<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);
$session = $request->session();
echo json_encode(['sid' => $session->getId(), 'token' => $session->get('_token'), 'cookie' => (string) $request->cookies->get('ambarella-session')]);
$kernel->terminate($request, $response);
