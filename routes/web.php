<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Rotas de autenticação (já vêm com o Laravel, mas precisamos adaptá-las)
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::get('/logout', [LoginController::class, 'destroy'])->name('logout'); // Importante para logout

# Rota socialite Google
// Rota para redirecionar para o Google
Route::get('/login/google', [SocialiteController::class, 'redirect_to_google'])->name('login.google');
// Rota de callback que o Google chamará após a autenticação
Route::get('/login/google/callback', [SocialiteController::class, 'handle_google_callback']);

Route::get('post/image/{filename}', [ImageController::class, 'show']);
Route::get('post/show/{slug}', [PostController::class, 'show'])->name('posts.show'); //Para exibir um post
Route::get('post/show/fix/{uuid}', [PostController::class, 'show'])->name('posts.show'); //Para exibir um post
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/home', [AdminController::class, 'index'])->name('admin.home');
    Route::prefix('/posts')->group(function () {
        Route::get('', [PostController::class, 'index'])->name('posts.index');
        Route::get('/create', [PostController::class, 'create'])->name('posts.create');
        Route::post('/store', [PostController::class, 'store'])->name('posts.store');
        Route::get('/edit/{uuid}', [PostController::class, 'edit'])->name('posts.edit');
        Route::post('/update', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/delete/{uuid}', [PostController::class, 'update'])->name('posts.destroy');
    });
});

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::prefix('/categories')->group(function () {
        Route::get('', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/store', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::get('/edit/{uuid}', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/update/{uuid}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/delete/{uuid}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    });
});
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');
