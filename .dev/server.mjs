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

    // Preview runs inside an iframe (cross-site), so the browser treats any
    // request as third-party: SameSite=Lax cookies are NOT sent on POST →
    // Laravel sees a fresh session every time → 419 Page Expired.
    // SameSite=None + Secure is the only combination sent on cross-site POSTs.
    let env = readFileSync(envPath, 'utf8');
    const patch = (key, value) => {
        const re = new RegExp('^' + key + '=.*$', 'm');
        env = re.test(env) ? env.replace(re, key + '=' + value) : env + '\n' + key + '=' + value;
    };
    patch('SESSION_SAME_SITE', 'none');
    patch('SESSION_SECURE_COOKIE', 'true');
    writeFileSync(envPath, env);
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

function inlineAssets(html) {
    // Replace <link rel="stylesheet" href="...css"> with inline <style> and
    // <script type="module" src="...js"> with inline <script>, so the preview
    // renders the full design without a single extra request (bulletproof
    // against proxy, caching and service-worker quirks).
    const stripRegexes = [
        /<link\b[^>]*rel=["']preload["'][^>]*as=["']style["'][^>]*href=["'][^"']+["'][^>]*\/?>/g,
        /<link\b[^>]*rel=["']modulepreload["'][^>]*\/?>/g,
    ];
    const cssRegex = /<link\b[^>]*rel=["']stylesheet["'][^>]*href=["']([^"']+)["'][^>]*\/?>/g;
    const jsRegex = /<script\b[^>]*type=["']module["'][^>]*src=["']([^"']+)["'][^>]*>\s*<\/script>/g;
    const selfJsRegex = /\/[\w-]+\/[^"'`]*build\/assets\/app-[A-Za-z0-9_-]+\.js/g;

    let result = html;
    const replacements = [];

    for (const regex of stripRegexes) {
        result = result.replace(regex, '');
    }

    for (const m of html.matchAll(cssRegex)) {
        const path = m[1].replace(/^https?:\/\/[^/]+/, '').replace(/^\//, '');
        try {
            const css = readFileSync(join(PUBLIC_DIR, path), 'utf8');
            replacements.push([m[0], `<style>\n${css}\n</style>`]);
        } catch { /* keep original link */ }
    }

    for (const m of html.matchAll(jsRegex)) {
        const path = m[1].replace(/^https?:\/\/[^/]+/, '').replace(/^\//, '');
        try {
            const js = readFileSync(join(PUBLIC_DIR, path), 'utf8');
            replacements.push([m[0], `<script type="module">\n${js}\n</script>`]);
        } catch { /* keep original script */ }
    }

    for (const [original, replacement] of replacements) {
        // IMPORTANT: use a function replacement — String.replace() interprets
        // $&, $', $` etc. in the replacement string, and minified JS is full
        // of "$." sequences, which corrupted the HTML (stray JS rendered as
        // text). A callback performs a literal replacement.
        result = result.replace(original, () => replacement);
    }

    // Vite embeds a dynamic modulepreload of its own entry inside the bundle.
    // It resolves against the current origin (fine), but leave it untouched —
    // removing it risks breaking lazy chunks. Only strip plain <link> tags above.
    return result;
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

        // NOTE: we deliberately do NOT force X-Forwarded-Proto: https here.
        // Doing so made Laravel mark cookies as `Secure`, and the sandbox proxy
        // then mishandles them so the browser never keeps the session → 419 /
        // login loop. URLs are rewritten to relative by the response pass, so
        // the app works identically over plain HTTP internally.
        

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
            if (key === 'set-cookie') {
                const cookies = Array.isArray(values) ? values : [String(values)];
                // Keep ONLY the session cookie: one header survives proxy
                // header-merging; the XSRF-TOKEN cookie is a convenience only
                // (forms use the hidden _token, and our JS uses the meta
                // csrf-token header), and a merged Set-Cookie line would make
                // the browser drop BOTH cookies → 419 loop.
                const sessionOnly = cookies.filter((c) => /^ambarella-session=/.test(c));
                responseHeaders[key] = sessionOnly.length ? sessionOnly : cookies;
                continue;
            }
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

            // Strip ANY self-referencing host, not just localhost. The preview
            // proxy can rewrite Host to localhost:8080 or to any e2b host;
            // Laravel builds absolute URLs from the Host it saw, so links can
            // point to a host that differs from the browser's origin. Relative
            // URLs always resolve against the browser's current origin.
            const seenHost = headers['x-forwarded-host'] || headers.host || `localhost:${PORT}`;
            const selfPrefixes = [
                `http://${seenHost}`,
                `https://${seenHost}`,
                `//${seenHost}`,
                `http://localhost:${PORT}`,
                `https://localhost:${PORT}`,
                'http://localhost',
                'https://localhost',
            ];
            for (const prefix of selfPrefixes) {
                if (html.includes(prefix)) { html = html.split(prefix).join(''); }
            }

            // A stripped root URL (e.g. http://host → "") must become "/",
            // otherwise the logo/home links do nothing (empty href).
            html = html.replace(/href=""/g, 'href="/"');

            // Critical: inline CSS + JS so the browser renders the design
            // without any external asset requests.
            const before = html;
            html = inlineAssets(html);
            if (html !== before) {
                console.error(`[server] ${url.pathname}: CSS/JS inline qilindi (dizayn kafolatli)`);
            }
            body = Buffer.from(html, 'utf8');
        }

        // Redirect Location headers must not point at a stale host either —
        // make them relative so the browser stays on its own origin.
        if (status >= 300 && status < 400 && responseHeaders.location) {
            const loc = responseHeaders.location;
            const seenHost = headers['x-forwarded-host'] || headers.host || `localhost:${PORT}`;
            for (const host of [seenHost, `localhost:${PORT}`, 'localhost']) {
                if (loc.includes(host)) {
                    responseHeaders.location = loc.split(host).join('');
                    // May leave a dangling scheme like "http:///dashboard".
                    responseHeaders.location = responseHeaders.location.replace(/^[a-z]+:\/\/+/i, '/');
                    break;
                }
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
