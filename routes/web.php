<?php

use App\Http\Controllers\HomeController;
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
// Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show'); //Para exibir um post
