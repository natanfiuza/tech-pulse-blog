<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

$user = User::where('email', 'natan.fiuza@gmail.com')->first() ?? User::first();
echo "Testing with user: " . $user->name . " (id: " . $user->id . ", uuid: " . $user->uuid . ")" . PHP_EOL;

// Authenticate user
auth()->login($user);

// Create fake uploaded image
$temp_file = UploadedFile::fake()->image('meu_novo_perfil.jpg', 400, 400);

$req = Request::create('/admin/perfil', 'POST', [
    '_method' => 'PUT',
    'name' => $user->name,
    'username' => $user->profile ? $user->profile->username : 'natanfiuza',
    'bio' => 'Test bio',
], [], [
    'avatar' => $temp_file,
], [
    'HTTP_X_INERTIA' => 'true',
    'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
]);

$response = $kernel->handle($req);
echo "Status code: " . $response->getStatusCode() . PHP_EOL;
echo "Location header: " . $response->headers->get('Location') . PHP_EOL;

$user->refresh();
echo "User avatar column in DB: " . $user->avatar . PHP_EOL;
echo "User avatar_url: " . $user->avatar_url . PHP_EOL;

$avatar_service = app(App\Services\AvatarService::class);
echo "File on disk exists: " . ($avatar_service->existe($user->uuid) ? 'YES' : 'NO') . PHP_EOL;
if ($avatar_service->existe($user->uuid)) {
    echo "File size on disk: " . filesize($avatar_service->caminho_absoluto($user->uuid)) . " bytes" . PHP_EOL;
    echo "File MIME: " . Illuminate\Support\Facades\File::mimeType($avatar_service->caminho_absoluto($user->uuid)) . PHP_EOL;
}

