<?php
namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, Sluggable; // Use o trait Sluggable

    protected $fillable = [
        'name',
        'slug',
        'description',
        'scope',
        'possible_contents',
        'post_suggestions',
        'parent_id',
    ];

    /**
     * Configuração do Sluggable.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name', // Usa o nome para gerar o slug
            ],
        ];
    }

    // Relacionamento: Uma categoria PODE ter um pai (categoria pai)
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Relacionamento: Uma categoria PODE ter várias filhas (subcategorias)
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    //Relacionamento com posts (se a sua tabela posts tiver uma coluna category_id)
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    // Método para obter *todas* as categorias descendentes (filhas, netas, etc.) - RECURSIVO
    public function descendants()
    {
        return $this->children()->with('descendants');
        // Eager loading recursivo!  Evita o problema N+1.
    }
}
