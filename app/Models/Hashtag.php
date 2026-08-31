<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Hashtag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Relacionamento: uma hashtag pertence a vários posts.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_hashtag');
    }
}
