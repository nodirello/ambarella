// Dev tool: PHP 8.4 (WebAssembly) CLI against the host filesystem.
// Usage: node .dev/phpw.mjs <cwd> <php args...>
import { createRequire } from 'node:module';

const require = createRequire(import.meta.url);
const { PHP } = require('@php-wasm/universal');
const { loadNodeRuntime, useHostFilesystem } = require('@php-wasm/node');

const [cwd, ...args] = process.argv.slice(2);
const processId = Number(process.env.PHP_PROCESS_ID ?? Math.floor(Math.random() * 1e9));

const php = new PHP(await loadNodeRuntime('8.4', { emscriptenOptions: { processId } }));
useHostFilesystem(php);

const argv = ['php', ...(args.length ? args : ['-v'])];
const res = await php.cli(argv, { cwd: cwd ?? process.cwd() });
await res.exitCode;
const text = await res.stdoutText;
if (text) process.stdout.write(text);
try { php[Symbol.dispose](); } catch { /* noop */ }
process.exit(0);
