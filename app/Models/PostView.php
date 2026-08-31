<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostView extends Model
{
    /**
     * Sem timestamps automáticos — usamos only viewed_at.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'post_id',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    /**
     * Relacionamento: visualização pertence a um usuário.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento: visualização pertence a um post.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
