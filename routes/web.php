<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
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
// Route::get('/login', [LoginController::class, 'create'])->name('login');
// Route::post('/login', [LoginController::class, 'store']);
// Route::post('/logout', [LoginController::class, 'destroy'])->name('logout'); //Rota de Logout

// // Rotas de posts (precisamos criar o PostController)
// Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create')->middleware('auth'); //Protege com Middleware
// Route::post('/posts', [PostController::class, 'store'])->name('posts.store')->middleware('auth');


Route::get('post/show/{slug}', [PostController::class, 'show'])->name('posts.show'); //Para exibir um post
Route::middleware(['auth'])->prefix('/post')->group(function () {
    Route::get('', [PostController::class, 'index'])->name('post.index');
    Route::post('/store', [PostController::class, 'store'])->name('post.store');
    Route::get('/create', [PostController::class, 'create'])->name('post.create');
});

Route::middleware(['auth'])->prefix('/categories')->group(function () {
    Route::get('', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/store', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::get('/{category:slug}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/{category:slug}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/{category:slug}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::get('/{slug}', [CategoryController::class, 'show'])->name('categories.show');

});


