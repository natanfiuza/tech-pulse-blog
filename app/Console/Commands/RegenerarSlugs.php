<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Hashtag;
use App\Models\Post;
use Illuminate\Console\Command;

class RegenerarSlugs extends Command
{
    /**
     * O nome e a assinatura do comando.
     *
     * @var string
     */
    protected $signature = 'app:regenerar-slugs';

    /**
     * A descrição do comando.
     *
     * @var string
     */
    protected $description = 'Regenera slugs quebrados (com caracteres não-ASCII, hífens duplicados ou nas pontas) de Posts, Categorias e Hashtags';

    /**
     * Executa o comando.
     */
    public function handle(): int
    {
        $total = 0;

        foreach ([Post::class, Category::class, Hashtag::class] as $model) {
            $quebrados = $model::query()
                ->whereNotNull('slug')
                ->get()
                ->filter(function ($item) {
                    // Sintomas do bug de acentuação/remoção de stopwords
                    return preg_match('/[^a-z0-9-]|--|^-|-$/', $item->slug);
                });

            foreach ($quebrados as $item) {
                $this->regerar($item);
                $total++;
            }

            $this->info(sprintf('%s: %d slugs quebrados regenerados.', class_basename($model), $quebrados->count()));
        }

        $this->info(sprintf('Total: %d slugs regenerados.', $total));

        return self::SUCCESS;
    }

    /**
     * Regenera o slug do modelo (Posts/Categories reutilizam o save() com
     * loop de unicidade; Hashtags não têm save() customizado).
     *
     * @param  mixed  $item
     */
    private function regerar($item): void
    {
        if ($item instanceof Hashtag) {
            $item->slug = criar_slug($item->name);
            $item->save();

            return;
        }

        // Post/Category: slug nulo faz o save() regenerar a partir do título/nome
        $item->slug = null;
        $item->save();
    }
}
