import { ref } from "vue";

/**
 * Estado reativo compartilhado do tema atual ('dark' | 'light').
 */
const tema_atual = ref("dark");

/**
 * Lê o estado do tema a partir do DOM ou localStorage.
 */
const sincronizar_tema_do_dom = () => {
    if (typeof document !== "undefined") {
        tema_atual.value = document.documentElement.classList.contains("dark") ? "dark" : "light";
    }
};

/**
 * Composable para gerenciamento e alternância do tema da aplicação.
 *
 * @returns {Object} Funções e estado reativo do tema.
 */
export function use_theme() {
    const alternar_tema = () => {
        if (typeof document === "undefined") {
            return;
        }

        const proximo_tema = tema_atual.value === "dark" ? "light" : "dark";
        definir_tema(proximo_tema);
    };

    const definir_tema = (novo_tema) => {
        tema_atual.value = novo_tema;

        if (typeof document !== "undefined") {
            if (novo_tema === "dark") {
                document.documentElement.classList.add("dark");
            } else {
                document.documentElement.classList.remove("dark");
            }
        }

        if (typeof localStorage !== "undefined") {
            try {
                localStorage.setItem("techpulse_tema", novo_tema);
            } catch (e) {
                // Modo restrito de armazenamento ignorado silenciosamente
            }
        }
    };

    const inicializar_tema = () => {
        sincronizar_tema_do_dom();
    };

    return {
        tema_atual,
        alternar_tema,
        definir_tema,
        inicializar_tema,
    };
}

