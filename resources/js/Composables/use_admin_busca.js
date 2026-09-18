import { ref } from "vue";
import { normalizar_texto } from "@/helpers";

/**
 * Estado reativo compartilhado do termo de busca no painel administrativo.
 */
const termo_busca = ref("");

/**
 * Composable para gerenciamento e filtragem de busca no painel administrativo.
 *
 * @returns {Object} Estado reativo e métodos de busca.
 */
export function use_admin_busca() {
    /**
     * Limpa o termo de busca atual.
     */
    const limpar_busca = () => {
        termo_busca.value = "";
    };

    /**
     * Filtra a lista de posts com base no termo de busca atual.
     * A busca estende-se para título, tags, resumo (excerpt) e corpo do post (content).
     *
     * @param {Array} posts - Lista de posts a ser filtrada.
     * @returns {Array} Lista filtrada de posts.
     */
    const filtrar_posts = (posts) => {
        if (!posts || !Array.isArray(posts)) {
            return [];
        }

        const termo = normalizar_texto(termo_busca.value);
        if (!termo) {
            return posts;
        }

        return posts.filter((post) => {
            const titulo = normalizar_texto(post?.title);
            const resumo = normalizar_texto(post?.excerpt);
            const conteudo = normalizar_texto(post?.content);

            const match_tags = Array.isArray(post?.hashtags) && post.hashtags.some((tag) => {
                return normalizar_texto(tag?.name).includes(termo) || normalizar_texto(tag?.slug).includes(termo);
            });

            return (
                titulo.includes(termo) ||
                resumo.includes(termo) ||
                conteudo.includes(termo) ||
                match_tags
            );
        });
    };

    /**
     * Filtra a lista de categorias com base no termo de busca atual.
     * A busca em categorias restringe-se ao nome da categoria.
     *
     * @param {Array} categories - Lista de categorias a ser filtrada.
     * @returns {Array} Lista filtrada de categorias.
     */
    const filtrar_categorias = (categories) => {
        if (!categories || !Array.isArray(categories)) {
            return [];
        }

        const termo = normalizar_texto(termo_busca.value);
        if (!termo) {
            return categories;
        }

        return categories.filter((category) => {
            const nome = normalizar_texto(category?.name);
            return nome.includes(termo);
        });
    };

    return {
        termo_busca,
        limpar_busca,
        filtrar_posts,
        filtrar_categorias,
    };
}

