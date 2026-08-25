/**
 * AMBARELLA dev server (local preview only).
 *
 * Runs Laravel on PHP 8.4 (WebAssembly inside Node) with the host filesystem
 * mounted. Each request gets a fresh PHP runtime AND a fresh PHPRequestHandler
 * with a stateless client-cookie store, so no PHP state leaks between requests
 * and the browser owns its cookies (real session semantics).
 *
 * Usage: node .dev/server.mjs [port]   (or: npm run serve --workspace .dev)
 */
import { createServer } from 'node:http';
import { createRequire } from 'node:module';
import { readFileSync, statSync, existsSync, writeFileSync, copyFileSync } from 'node:fs';
import { extname, join, normalize } from 'node:path';
import { spawnSync } from 'node:child_process';

const require = createRequire(import.meta.url);
const { PHP, PHPRequestHandler } = require('@php-wasm/universal');
const { loadNodeRuntime, createNodeFsMountHandler } = require('@php-wasm/node');

const ROOT = process.cwd();
const PUBLIC_DIR = join(ROOT, 'public');
const PORT = Number(process.env.PORT ?? process.argv[2] ?? 8080);

/**
 * Self-healing bootstrap (fresh checkout / sandbox reset friendly):
 *  1. .env — created from .env.example with a fresh APP_KEY (git-ignored).
 *  2. database/database.sqlite — seeded with migrations+seeders if missing
 *     or broken, so the preview always boots with demo data.
 */
function ensureEnv() {
    const envPath = join(ROOT, '.env');
    if (!existsSync(envPath)) {
        const example = join(ROOT, '.env.example');
        if (!existsSync(example)) {
            console.error('[server] .env.example topilmadi — loyiha to‘liq emas.');
            process.exit(1);
        }
        copyFileSync(example, envPath);
        const key = 'base64:' + Buffer.from(Array.from({ length: 32 }, () => Math.floor(Math.random() * 256))).toString('base64');
        let env = readFileSync(envPath, 'utf8');
        env = env
            .replace(/^APP_KEY=.*$/m, 'APP_KEY=' + key)
            .replace('SESSION_DRIVER=file', 'SESSION_DRIVER=database')
            .replace('CACHE_STORE=file', 'CACHE_STORE=database');
        writeFileSync(envPath, env);
        console.error('[server] .env (APP_KEY bilan) avtomatik yaratildi.');
    }
}

function ensureDatabase() {
    const dbPath = join(ROOT, 'database', 'database.sqlite');
    const valid = existsSync(dbPath) && statSync(dbPath).size > 0;
    if (valid) return;

    console.error('[server] database.sqlite yo‘q — migratsiya va seed ishga tushirilmoqda…');
    const result = spawnSync(
        process.execPath,
        [join(ROOT, '.dev', 'phpw.mjs'), ROOT, 'artisan', 'migrate', '--seed', '--force'],
        { encoding: 'utf8', timeout: 180_000 }
    );
    if (result.status !== 0) {
        console.error('[server] DB bootstrap xatosi:', result.stderr?.slice(-800) ?? result.stdout?.slice(-800));
        process.exit(1);
    }
    console.error('[server] DB tayyor (migrate + seed).');
}

ensureEnv();
ensureDatabase();

const MIME = {
    '.html': 'text/html; charset=utf-8',
    '.css': 'text/css; charset=utf-8',
    '.js': 'text/javascript; charset=utf-8',
    '.json': 'application/json',
    '.svg': 'image/svg+xml',
    '.png': 'image/png',
    '.jpg': 'image/jpeg',
    '.webp': 'image/webp',
    '.txt': 'text/plain; charset=utf-8',
    '.webmanifest': 'application/manifest+json',
    '.woff2': 'font/woff2',
    '.woff': 'font/woff',
};

let loadCount = 0;

/**
 * php-wasm's PHPRequestHandler overwrites the request Cookie header with its
 * internal cookie store. We seed that store with the real client cookies and
 * make it stateless — the client owns its cookies, sessions live in the DB.
 */
function makeClientCookieStore(cookieHeader) {
    const cookies = {};
    for (const pair of (cookieHeader ?? '').split(';')) {
        const [name, ...rest] = pair.trim().split('=');
        if (name) cookies[name] = rest.join('=');
    }

    return {
        getCookieRequestHeader: () => Object.entries(cookies).map(([k, v]) => `${k}=${v}`).join('; '),
        rememberCookiesFromResponseHeaders: () => {},
    };
}

async function createPhpAndHandler(cookieHeader) {
    const php = new PHP(
        await loadNodeRuntime('8.4', {
            emscriptenOptions: { processId: 5000 + (loadCount++ % 1000) },
        })
    );

    // Mount the project root on the host so PHP sees real files.
    await php.mount(ROOT, createNodeFsMountHandler(ROOT)).catch(() => {});
    await php.mount('/', createNodeFsMountHandler('/')).catch(() => {});

    const handler = new PHPRequestHandler({
        php,
        cookieStore: makeClientCookieStore(cookieHeader),
        documentRoot: PUBLIC_DIR,
        absoluteUrl: `http://localhost:${PORT}`,
        // Frontier routing: anything that is not a real static file goes to Laravel.
        getFileNotFoundAction: () => ({ type: 'internal-redirect', uri: '/index.php' }),
    });

    return { php, handler };
}

function serveStatic(urlPath, res) {
    const safePath = normalize(urlPath.replace(/^\/+/, ''));
    const filePath = join(PUBLIC_DIR, safePath);

    if (!filePath.startsWith(PUBLIC_DIR)) {
        res.writeHead(403).end('Forbidden');
        return false;
    }

    try {
        const stat = statSync(filePath);
        if (!stat.isFile()) return false;

        const ext = extname(filePath);
        res.writeHead(200, {
            'Content-Type': MIME[ext] ?? 'application/octet-stream',
            'Cache-Control': ext === '.html' ? 'no-cache' : 'public, max-age=3600',
        });
        res.end(readFileSync(filePath));
        return true;
    } catch {
        return false;
    }
}

async function toArrayBuffer(body) {
    const chunks = [];
    for await (const chunk of body) chunks.push(chunk);
    return Buffer.concat(chunks);
}

const server = createServer(async (req, res) => {
    const url = new URL(req.url, `http://localhost:${PORT}`);

    // 1) Static pass.
    if (req.method === 'GET' || req.method === 'HEAD') {
        const staticCandidate = url.pathname === '/' ? '/index.php' : url.pathname;
        if (serveStatic(staticCandidate, req, res)) return;
    }

    try {
        const rawBody = await toArrayBuffer(req);
        // Pass the real client headers through (host + forwarded proto are
        // critical: Laravel generates absolute URLs from them, and the preview
        // is reached through a TLS proxy on a different host).
        const headers = Object.fromEntries(
            Object.entries(req.headers)
                .filter(([name]) => ['host', 'cookie', 'accept', 'accept-language', 'user-agent', 'content-type', 'x-telegram-bot-api-secret-token', 'x-telegram-init-data', 'authorization', 'x-requested-with', 'x-forwarded-host', 'x-forwarded-proto', 'x-forwarded-port'].includes(name))
                .map(([name, value]) => [name, String(value)])
        );

        // Everything reaching this server from outside goes through the TLS
        // proxy; if the proxy omitted the scheme header, default to https so
        // Laravel never emits mixed-content (http) URLs in the browser.
        if (!headers['x-forwarded-proto'] && !(headers.host ?? '').startsWith('localhost')) {
            headers['x-forwarded-proto'] = 'https';
        }

        // Diagnose proxy behaviour on the dev server console.
        console.error(`[server] ${url.pathname} ← host=${headers.host ?? '-'} xfh=${headers['x-forwarded-host'] ?? '-'} xfp=${headers['x-forwarded-proto'] ?? '-'}`);
        const { php, handler } = await createPhpAndHandler(headers.cookie);
        const response = await handler.request({
            method: req.method || 'GET',
            url: `http://localhost:${PORT}${url.pathname}${url.search}`,
            headers,
            body: rawBody.length ? rawBody : undefined,
        });

        const status = response.httpStatusCode ?? 200;
        // StreamedPHPResponse.headers is a Promise<Record<string, string[]>>.
        const headerMap = await response.headers;
        const responseHeaders = {};
        let contentType = '';
        for (const [name, values] of Object.entries(headerMap ?? {})) {
            const key = name.toLowerCase();
            if (key === 'content-type') contentType = Array.isArray(values) ? values.join(', ') : String(values);
            if (key === 'content-length') continue; // body may be rewritten below
            // Set-Cookie must remain separate header lines (never comma-joined).
            responseHeaders[key] = key === 'set-cookie' && Array.isArray(values) ? values : String(values);
        }

        let body = response.bytes ?? new Uint8Array(0);

        // Bulletproof preview fix: the TLS proxy may rewrite Host back to
        // localhost:8080, which would make Laravel emit http://localhost:8080
        // absolute URLs — the visitor's browser then cannot reach them.
        // For HTML/XML we rewrite every self-referencing absolute URL to a
        // RELATIVE one, so assets and links always resolve against the real
        // preview origin regardless of Host/X-Forwarded handling.
        if (/text\/html|application\/xml/i.test(contentType) && body.length) {
            let html = Buffer.from(body).toString('utf8');
            const selfPrefixes = [
                `http://localhost:${PORT}`,
                `https://localhost:${PORT}`,
                'http://localhost',
                'https://localhost',
            ];
            let changed = false;
            for (const prefix of selfPrefixes) {
                if (html.includes(prefix)) { html = html.split(prefix).join(''); changed = true; }
            }
            if (changed) {
                body = Buffer.from(html, 'utf8');
                console.error(`[server] ${url.pathname}: mutlaq URL'lar relative qilindi`);
            }
        }

        res.writeHead(status, responseHeaders);
        res.end(Buffer.from(body));
    } catch (error) {
        console.error('[server] error:', error);
        if (!res.headersSent) {
            res.writeHead(500, { 'Content-Type': 'text/plain; charset=utf-8' });
        }
        res.end('Server error (dev preview). See logs.');
    }
});

server.listen(PORT, '0.0.0.0', () => {
    console.error(`[server] AMBARELLA preview: http://localhost:${PORT}`);
});
