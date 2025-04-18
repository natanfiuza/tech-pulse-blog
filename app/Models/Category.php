<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

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
     * Define o atributo 'slug'.
     *
     * Este método define o valor do atributo 'slug' da instância do modelo.
     * Se um valor para o slug for fornecido, ele será atribuído diretamente.
     * Caso contrário, um slug será gerado automaticamente a partir do título
     * (assumindo que o modelo possui um atributo 'name') usando a função helper `criar_slug()`.
     *
     * @param  string|null  $value  O valor do slug a ser definido.  Se for nulo ou uma string vazia,
     *                                 um slug será gerado a partir do título.
     * @return void
     *
     * @example
     * // Exemplo 1: Definindo um slug manualmente.
     * $model->setSlugAttribute('meu-slug-personalizado'); // Atribui 'meu-slug-personalizado' ao atributo slug.
     *
     * @example
     * // Exemplo 2: Gerando um slug automaticamente a partir do título.
     * $model->name = "Nome cateogoria";
     * $model->setSlugAttribute(null);  // Atribui um slug gerado (e.g., 'nome-categoria') ao atributo slug.
     * $model->setSlugAttribute('');    // Atribui um slug gerado (e.g., 'nome-categoria') ao atributo slug.
     *
     * @uses \Illuminate\Support\Str::slug() Para gerar o slug a partir do título se nenhum valor for fornecido.  Esta função
     *       normalmente converte a string para minúsculas, substitui espaços por hífens e remove caracteres especiais.
     *       (Verifica documentação da classe Str para comportamento exato.)
     */
    public function setSlugAttribute($value)
    {
        // Se o slug não for fornecido (ex: ao criar um novo post),
        // gera um slug a partir do nome da categoria
        if (empty(trim($value))) {
            $this->attributes['slug'] = criar_slug($this->name);
        } else {
            // Se um valor for fornecido, usa ele mesmo
            $this->attributes['slug'] = $value;
        }
    }
    /**
     * Sobrescreve o método `save` do modelo base.
     *
     * Este método estende o comportamento padrão do método `save` para:
     * 1. Gerar automaticamente um slug se ele não estiver definido.
     * 2. Garantir que o slug gerado ou fornecido seja único no banco de dados.
     * 3. Chamar o método `save` original da classe pai (Eloquent Model) para persistir os dados.
     *
     * @param  array  $options  Opções a serem passadas para o método `save` da classe pai.
     *                           Veja a documentação do Eloquent para as opções disponíveis.
     * @return bool Retorna o resultado do método save() original.
     *
     * @throws \Exception Se ocorrer uma exceção durante o processo de salvamento do modelo pai.  A exceção original
     *                   será relançada.
     *
     * @example
     *  //Exemplo 3: Salvando um modelo existente com possivel conflito de slug
     *  $model = MyModel::find(1);
     *  $model->name = "Nome que causa conflito de slug";
     *  $model->save(); //Modifica o slug, adicionando um contador, e salva.
     *
     * @uses \Illuminate\Database\Eloquent\Model::save() Chama o método save() original para a persistência.
     * @uses \Illuminate\Database\Eloquent\Builder::where() Usado na consulta para verificar slugs duplicados.
     * @uses \Illuminate\Database\Eloquent\Builder::exists() Usado na consulta para verificar slugs duplicados.
     */
    public function save(array $options = [])
    {
        if (empty(trim($this->slug)) || $this->isDirty('name')) { //Slug vazio ou título foi alterado
            $this->slug = criar_slug($this->name);
        }

        //Garantir que o slug seja único
        $originalSlug = $this->slug;
        $counter      = 2;

        while (static::where('slug', $this->slug)->where('id', '!=', $this->id)->exists()) {
            $this->slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        parent::save($options);
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
