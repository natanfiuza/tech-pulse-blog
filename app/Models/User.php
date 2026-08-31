<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public const ROLE_LEITOR = 'leitor';
    public const ROLE_AUTOR = 'autor';
    public const ROLE_ADMIN = 'admin';

    public const ROLES = [
        self::ROLE_LEITOR,
        self::ROLE_AUTOR,
        self::ROLE_ADMIN,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Verifica se o usuário possui um dos papéis informados.
     */
    public function possui_papel(string ...$papeis): bool
    {
        return in_array($this->role, $papeis, true);
    }

    /**
     * Relacionamento: usuário tem vários posts.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
