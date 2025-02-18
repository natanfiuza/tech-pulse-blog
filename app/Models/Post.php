<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'image',
        'excerpt',
        'content',
    ];

    /**
     * Define o atributo 'slug'.
     *
     * Este método define o valor do atributo 'slug' da instância do modelo.
     * Se um valor para o slug for fornecido, ele será atribuído diretamente.
     * Caso contrário, um slug será gerado automaticamente a partir do título
     * (assumindo que o modelo possui um atributo 'title') usando a função helper `Str::slug()`.
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
     * $model->title = "Meu Título Incrível";
     * $model->setSlugAttribute(null);  // Atribui um slug gerado (e.g., 'meu-titulo-incrivel') ao atributo slug.
     * $model->setSlugAttribute('');    // Atribui um slug gerado (e.g., 'meu-titulo-incrivel') ao atributo slug.
     *
     * @uses \Illuminate\Support\Str::slug() Para gerar o slug a partir do título se nenhum valor for fornecido.  Esta função
     *       normalmente converte a string para minúsculas, substitui espaços por hífens e remove caracteres especiais.
     *       (Verifica documentação da classe Str para comportamento exato.)
     */
    public function setSlugAttribute($value)
    {
        // Se o slug não for fornecido (ex: ao criar um novo post),
        // gera um slug a partir do título.
        if (empty($value)) {
            //$this->attributes['slug'] = Str::slug($this->title, '-', 'pt-br', ['@' => 'at']);
            $this->attributes['slug'] = criar_slug($this->title);
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
     * // Exemplo 1: Salvando um novo modelo com título (slug gerado automaticamente).
     * $model = new MyModel();
     * $model->title = "Meu Novo Post";
     * $model->content = "Conteúdo do post...";
     * $model->save(); // Gera um slug único (e.g., 'meu-novo-post') e salva.
     *
     * @example
     * // Exemplo 2: Salvando um modelo com slug predefinido (ainda garante a unicidade).
     * $model = new MyModel();
     * $model->title = "Outro Post";
     * $model->slug = "meu-slug"; //Slug definido manualmente.
     * $model->content = "Conteúdo...";
     * $model->save(); // Verifica a unicidade de 'meu-slug'. Se existir, gera um novo (e.g., 'meu-slug-2').
     *
     * @example
     *  //Exemplo 3: Salvando um modelo existente com possivel conflito de slug
     *  $model = MyModel::find(1);
     *  $model->title = "Título que causa conflito de slug";
     *    $model->save(); //Modifica o slug, adicionando um contador, e salva.
     *
     * @uses \Illuminate\Support\Str::slug() Para gerar o slug a partir do título se nenhum slug for definido inicialmente.
     * @uses \Illuminate\Database\Eloquent\Model::save() Chama o método save() original para a persistência.
     * @uses \Illuminate\Database\Eloquent\Builder::where() Usado na consulta para verificar slugs duplicados.
     * @uses \Illuminate\Database\Eloquent\Builder::exists() Usado na consulta para verificar slugs duplicados.
     */
    public function save(array $options = [])
    {
        if (empty($this->slug) || $this->isDirty('title')) { //Slug vazio ou título foi alterado
            //$this->slug = Str::slug($this->title, '-', 'pt-br', ['@' => 'at']);
            $this->slug = criar_slug($this->title);
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
}
