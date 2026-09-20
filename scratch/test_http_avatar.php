<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$kernel->bootstrap();

$user = App\Models\User::first();

$request = Illuminate\Http\Request::create('/user/avatar/'.$user->uuid, 'GET');
$response = $kernel->handle($request);

ob_start();
$response->sendContent();
$output = ob_get_clean();

echo 'HTTP Status: '.$response->getStatusCode().PHP_EOL;
echo 'Content-Type: '.$response->headers->get('Content-Type').PHP_EOL;
echo 'Body length: '.strlen($output).PHP_EOL;
echo 'First 50 chars: '.bin2hex(substr($output, 0, 16)).PHP_EOL;
