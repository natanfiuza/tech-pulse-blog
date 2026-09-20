<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'github',
        'phone',
        'lang',
        'is_canceled',
        'date_canceled',
    ];

    protected $casts = [
        'is_canceled' => 'boolean',
        'date_canceled' => 'datetime',
    ];

    /**
     * Scope para retornar apenas assinantes ativos (não cancelados).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_canceled', false);
    }
}

