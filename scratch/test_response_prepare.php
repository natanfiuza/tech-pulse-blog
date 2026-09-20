<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Http\Request;

$controller = app(App\Http\Controllers\UserProfileImageController::class);
$user = App\Models\User::first();
$req = Request::create('/user/avatar/'.$user->uuid);

$response = $controller->show($user->uuid, $req);
// Prepare the response as Symfony does before sending
$response->prepare($req);

echo 'Final Content-Type: '.$response->headers->get('Content-Type').PHP_EOL;
echo 'Final Cache-Control: '.$response->headers->get('Cache-Control').PHP_EOL;
echo 'File size: '.filesize($response->getFile()->getPathname()).PHP_EOL;
