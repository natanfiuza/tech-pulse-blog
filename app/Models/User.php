<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\AvatarService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
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
        'uuid',
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
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'avatar_url',
    ];

    /**
     * Boot do modelo para atribuição de UUID e criação de perfil.
     */
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });

        static::created(function (User $user) {
            $user->obter_ou_criar_perfil();

            // Se o usuário não veio com avatar do Google, gera o avatar padrão em disco
            if (empty($user->avatar)) {
                app(AvatarService::class)->gerar_e_salvar_avatar_padrao($user);
            }
        });
    }

    /**
     * Verifica se o usuário possui um dos papéis informados.
     */
    public function possui_papel(string ...$papeis): bool
    {
        return in_array($this->role, $papeis, true);
    }

    /**
     * Relacionamento: usuário tem um perfil.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Retorna o perfil associado ou cria um perfil inicial caso ainda não exista.
     */
    public function obter_ou_criar_perfil(): UserProfile
    {
        if ($this->profile) {
            return $this->profile;
        }

        $base = Str::slug($this->name);
        if (empty($base)) {
            $base = 'user-'.substr($this->uuid ?? (string) Str::uuid(), 0, 8);
        }

        $username = $base;
        $contador = 1;
        while (UserProfile::where('username', $username)->where('user_id', '!=', $this->id)->exists()) {
            $username = $base.'-'.$contador++;
        }

        return $this->profile()->firstOrCreate(
            ['user_id' => $this->id],
            [
                'username' => $username,
                'bio' => null,
                'social_links' => [],
                'public_profile_enabled' => true,
                'show_email' => false,
                'show_name' => true,
                'show_author_box' => true,
            ]
        );
    }

    /**
     * Acessor para a URL do avatar do usuário.
     */
    public function getAvatarUrlAttribute(): string
    {
        if (! $this->uuid) {
            return '';
        }

        $versao = $this->updated_at ? $this->updated_at->timestamp : time();

        return route('user.avatar', ['uuid' => $this->uuid]).'?v='.$versao;
    }

    /**
     * Relacionamento: usuário tem vários posts.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
