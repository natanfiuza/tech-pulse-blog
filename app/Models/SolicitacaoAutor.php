<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitacaoAutor extends Model
{
    /** @var string */
    protected $table = 'solicitacoes_autor';

    /** @var array<int, string> */
    protected $fillable = [
        'user_id',
        'motivacao',
        'status',
        'admin_id',
        'admin_nota',
    ];

    public const STATUS_PENDENTE   = 'pendente';
    public const STATUS_APROVADA   = 'aprovada';
    public const STATUS_REJEITADA  = 'rejeitada';

    /**
     * Usuário que fez a solicitação.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    /**
     * Admin que revisou a solicitação.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id')->withTrashed();
    }

    /**
     * Scope para solicitações ainda aguardando revisão.
     */
    public function scopePendente(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDENTE);
    }

    /**
     * Verifica se a solicitação está pendente de revisão.
     */
    public function esta_pendente(): bool
    {
        return $this->status === self::STATUS_PENDENTE;
    }
}

