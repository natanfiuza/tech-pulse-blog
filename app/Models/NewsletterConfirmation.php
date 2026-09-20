<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterConfirmation extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'email',
        'is_confirmed',
        'date_confirmed',
    ];

    protected $casts = [
        'is_confirmed' => 'boolean',
        'date_confirmed' => 'datetime',
    ];

    /**
     * Scope para filtrar registros pendentes criados há menos de 24 horas.
     */
    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where('created_at', '>=', now()->subHours(24));
    }
}

