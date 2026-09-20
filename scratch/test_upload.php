<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

$user = App\Models\User::first();
$avatar_service = app(App\Services\AvatarService::class);

echo 'Initial file content: '.Storage::disk('local')->get($avatar_service->caminho($user->uuid)).PHP_EOL;

$fake_png = UploadedFile::fake()->image('avatar.png', 100, 100);
$avatar_service->salvar_upload($user, $fake_png);

$new_content = Storage::disk('local')->get($avatar_service->caminho($user->uuid));
echo 'New size: '.strlen($new_content).' bytes'.PHP_EOL;
echo 'Is still SVG? '.(str_contains($new_content, '<svg') ? 'YES' : 'NO').PHP_EOL;

// Test controller again:
$controller = app(App\Http\Controllers\UserProfileImageController::class);
$response = $controller->show($user->uuid, request());
echo 'Response content-type: '.$response->headers->get('Content-Type').PHP_EOL;
