<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContentImageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserProfileImageController;
use App\Services\ImagensDeConteudo;
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

// Cadastro self-service (novos usuários nascem como 'leitor')
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

// Rota socialite Google
// Rota para redirecionar para o Google
Route::get('/login/google', [SocialiteController::class, 'redirect_to_google'])->name('login.google');
// Rota de callback que o Google chamará após a autenticação
Route::get('/login/google/callback', [SocialiteController::class, 'handle_google_callback']);

// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::post('/newsletter/send-cancel-link', [NewsletterController::class, 'send_cancel_link'])->name('newsletter.send-cancel-link');
Route::get('/newsletter/confirm/{uuid}', [NewsletterController::class, 'confirm'])->name('newsletter.confirm');
Route::post('/newsletter/confirm/{uuid}', [NewsletterController::class, 'confirm_submit'])->name('newsletter.confirm.submit');
Route::get('/newsletter/cancel/{uuid}', [NewsletterController::class, 'cancel'])->name('newsletter.cancel');
Route::post('/newsletter/unsubscribe/{uuid}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

Route::get('post/image/{filename}', [ImageController::class, 'show']);
Route::get('user/avatar/{uuid}', [UserProfileImageController::class, 'show'])
    ->name('user.avatar')
    ->where('uuid', '[0-9a-fA-F-]{36}');
Route::get('post/show/{slug}', [PostController::class, 'show'])->name('posts.show'); // Para exibir um post
Route::get('post/show/fix/{uuid}', [PostController::class, 'show'])->name('posts.show'); // Para exibir um post
Route::get('/tags/{slug}', [TagController::class, 'show'])->name('tags.show');

// Imagens de conteúdo do post (públicas — o conteúdo do blog é público).
// O {slug} é decorativo: a leitura resolve o arquivo apenas pelo {uuid},
// porque o slug do post muda quando o título muda.
Route::get(ImagensDeConteudo::ROTA.'/{slug}/{uuid}', [ContentImageController::class, 'show'])
    ->name('posts.content_image')
    ->where('slug', '[A-Za-z0-9-]*')
    ->where('uuid', '[0-9a-fA-F-]{36}');

// Comentários (somente usuários logados)
Route::middleware(['auth'])->group(function () {
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/upvote', [CommentController::class, 'vote'])->name('comments.vote');
});

// Dashboard pessoal do leitor
Route::get('/minha-conta', [DashboardController::class, 'index'])->middleware('auth')->name('minha_conta');
Route::get('/minha-conta/perfil', [UserProfileController::class, 'edit_reader'])->middleware('auth')->name('reader.profile.edit');
Route::match(['put', 'post'], '/minha-conta/perfil', [UserProfileController::class, 'update'])->middleware('auth')->name('reader.profile.update');

// Admin: autores e admins (posts são filtrados por dono no PostController)
Route::middleware(['auth', 'role:autor,admin'])->prefix('admin')->group(function () {
    Route::get('/home', [AdminController::class, 'index'])->name('admin.home');
    Route::get('/perfil', [UserProfileController::class, 'edit_admin'])->name('admin.profile.edit');
    Route::match(['put', 'post'], '/perfil', [UserProfileController::class, 'update'])->name('admin.profile.update');

    Route::prefix('/posts')->group(function () {
        Route::get('', [PostController::class, 'index'])->name('posts.index');
        Route::get('/create', [PostController::class, 'create'])->name('posts.create');
        Route::post('/store', [PostController::class, 'store'])->name('posts.store');
        Route::get('/edit/{uuid}', [PostController::class, 'edit'])->name('posts.edit');
        Route::post('/update', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/delete/{uuid}', [PostController::class, 'destroy'])->name('posts.destroy');

        // Upload imediato das imagens coladas/arrastadas no editor.
        Route::post('/content-images', [ContentImageController::class, 'store'])->name('posts.content_images.store');
    });
});

// Admin: categorias são estrutura compartilhada do blog — somente admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::prefix('/categories')->group(function () {
        Route::get('', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/store', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::get('/edit/{category}', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/update/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/delete/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    });

    // Admin: gestão de usuários e privilégios — somente admin
    Route::prefix('/users')->group(function () {
        Route::get('', [UserController::class, 'index'])->name('users.index');
        Route::put('/{user}/role', [UserController::class, 'update_role'])->name('users.update_role');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');

// Perfil público do autor/usuário (deve ser a última rota declarada para não conflitar com rotas de primeiro nível)
Route::get('/{username}', [PublicProfileController::class, 'show'])
    ->name('profiles.show')
    ->where('username', '[a-zA-Z0-9_\-\.]+');
