import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { gravar_consentimento } from '@/lib/analytics';

/**
 * Composable para gerenciamento do estado de consentimento (ADR 0003).
 */
const consentimento = ref(null);
const banner_reaberto = ref(false);
let props_atuais = null;

export function use_consent() {
    const page = usePage();
    const analytics = page.props.analytics ?? null;

    if (analytics !== props_atuais) {
        props_atuais = analytics;
        consentimento.value = analytics?.consent ?? null;
        banner_reaberto.value = false;
    }

    function decidir(decisao) {
        consentimento.value = decisao;
        banner_reaberto.value = false;

        if (props_atuais) {
            gravar_consentimento(props_atuais.cookie, decisao, props_atuais.days);
        }
    }

    return {
        consentimento,
        banner_aberto: computed(() => consentimento.value === null || banner_reaberto.value),
        aceitar: () => decidir('granted'),
        recusar: () => decidir('denied'),
        reabrir: () => {
            banner_reaberto.value = true;
        },
    };
}
