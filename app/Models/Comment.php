<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content',
    ];

    /**
     * Relacionamento: comentário pertence a um post.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Relacionamento: comentário pertence a um usuário.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento: comentário pode responder outro comentário.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Relacionamento: respostas deste comentário (threads recursivas).
     */
    public function children(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Relacionamento: votos (upvotes) recebidos.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(CommentVote::class);
    }
}
