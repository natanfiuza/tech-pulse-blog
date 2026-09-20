<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$jpg_path = storage_path('app/test_jpg');
file_put_contents($jpg_path, base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA='));

echo 'JPEG MIME: '.Illuminate\Support\Facades\File::mimeType($jpg_path).PHP_EOL;

$first_bytes = substr(ltrim(file_get_contents($jpg_path, false, null, 0, 50)), 0, 5);
echo 'Is SVG? '.((strtolower($first_bytes) === '<svg' || str_contains($first_bytes, '<?xml')) ? 'YES' : 'NO').PHP_EOL;

unlink($jpg_path);
