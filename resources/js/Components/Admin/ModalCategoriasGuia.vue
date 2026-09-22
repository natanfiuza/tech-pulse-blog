<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="aberto" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm overflow-y-auto" role="dialog" aria-modal="true" @click.self="fechar">
        <Transition
          enter-active-class="transition duration-200 ease-out"
          enter-from-class="opacity-0 scale-95 translate-y-2"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition duration-150 ease-in"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 translate-y-2"
        >
          <div class="w-full max-w-2xl rounded-2xl bg-white dark:bg-surface-container-low shadow-2xl p-6 sm:p-7 space-y-4" v-if="aberto">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-bold">Guia de Categorias</h3>
              <button @click="fechar" class="text-gray-500 hover:text-gray-700">
                <span class="material-symbols-outlined" aria-hidden="true">close</span>
              </button>
            </div>
            <input v-model="busca" placeholder="Buscar categorias..." class="w-full px-3 py-2 border rounded" />
            <div class="space-y-3 max-h-96 overflow-y-auto">
              <template v-for="raiz in categoriasFiltradas" :key="raiz.id">
                <div class="border-b pb-2">
                  <div class="flex items-center justify-between cursor-pointer" @click="toggle(raiz.id)">
                    <div class="font-medium text-gray-800 dark:text-on-surface">{{ raiz.name }}</div>
                    <span class="material-symbols-outlined text-sm" v-if="raiz.children && raiz.children.length">
                      {{ abertoIds.includes(raiz.id) ? 'expand_less' : 'expand_more' }}
                    </span>
                  </div>
                  <div v-if="raiz.description" class="text-sm text-gray-600 dark:text-on-surface-variant mt-1">Descrição: {{ raiz.description }}</div>
                  <div v-if="raiz.scope" class="text-sm text-gray-600 dark:text-on-surface-variant">Abrangência: {{ raiz.scope }}</div>
                  <div v-if="raiz.possible_contents" class="text-sm text-gray-600 dark:text-on-surface-variant">Possíveis Conteúdos: {{ raiz.possible_contents }}</div>
                  <div v-if="raiz.post_suggestions" class="text-sm text-gray-600 dark:text-on-surface-variant">Sugestões de Postagens: {{ raiz.post_suggestions }}</div>
                  <div v-if="raiz.children && raiz.children.length && abertoIds.includes(raiz.id)" class="ml-4 mt-2 space-y-2">
                    <template v-for="filho in raiz.children" :key="filho.id">
                      <div class="border-l pl-2">
                        <div class="flex items-center justify-between cursor-pointer" @click="toggle(filho.id)">
                          <div class="font-medium text-gray-700 dark:text-on-surface">{{ filho.name }}</div>
                          <span class="material-symbols-outlined text-sm" v-if="filho.children && filho.children.length">
                            {{ abertoIds.includes(filho.id) ? 'expand_less' : 'expand_more' }}
                          </span>
                        </div>
                        <div v-if="filho.description" class="text-xs text-gray-600 dark:text-on-surface-variant mt-1">Descrição: {{ filho.description }}</div>
                        <div v-if="filho.scope" class="text-xs text-gray-600 dark:text-on-surface-variant">Abrangência: {{ filho.scope }}</div>
                        <div v-if="filho.possible_contents" class="text-xs text-gray-600 dark:text-on-surface-variant">Possíveis Conteúdos: {{ filho.possible_contents }}</div>
                        <div v-if="filho.post_suggestions" class="text-xs text-gray-600 dark:text-on-surface-variant">Sugestões: {{ filho.post_suggestions }}</div>
                        <!-- Neto level (optional) -->
                        <div v-if="filho.children && filho.children.length && abertoIds.includes(filho.id)" class="ml-4 mt-2 space-y-1">
                          <template v-for="neto in filho.children" :key="neto.id">
                            <div class="border-l pl-2">
                              <div class="font-medium text-gray-600 dark:text-on-surface">{{ neto.name }}</div>
                              <div v-if="neto.description" class="text-xs text-gray-600 dark:text-on-surface-variant mt-1">Descrição: {{ neto.description }}</div>
                              <div v-if="neto.scope" class="text-xs text-gray-600 dark:text-on-surface-variant">Abrangência: {{ neto.scope }}</div>
                              <div v-if="neto.possible_contents" class="text-xs text-gray-600 dark:text-on-surface-variant">Possíveis Conteúdos: {{ neto.possible_contents }}</div>
                              <div v-if="neto.post_suggestions" class="text-xs text-gray-600 dark:text-on-surface-variant">Sugestões: {{ neto.post_suggestions }}</div>
                            </div>
                          </template>
                        </div>
                      </div>
                    </template>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from "vue";

const props = defineProps({
  aberto: { type: Boolean, default: false },
  categorias: { type: Array, default: () => [] },
});

const emit = defineEmits(["fechar"]);

const abertoIds = ref([]);
const busca = ref("");

const fechar = () => {
  emit("fechar");
  abertoIds.value = [];
  busca.value = "";
};

const toggle = (id) => {
  const idx = abertoIds.value.indexOf(id);
  if (idx >= 0) abertoIds.value.splice(idx, 1);
  else abertoIds.value.push(id);
};

const categoriasFiltradas = computed(() => {
  if (!props.categorias) return [];
  const term = busca.value.toLowerCase();
  if (!term) return props.categorias;
  const matchCat = (cat) => {
    const match = cat.name?.toLowerCase().includes(term) ||
      (cat.description && cat.description.toLowerCase().includes(term)) ||
      (cat.scope && cat.scope.toLowerCase().includes(term)) ||
      (cat.possible_contents && cat.possible_contents.toLowerCase().includes(term)) ||
      (cat.post_suggestions && cat.post_suggestions.toLowerCase().includes(term));
    if (match) return true;
    return cat.children?.some(matchCat);
  };
  return props.categorias.filter(matchCat);
});

watch(() => props.aberto, (newVal) => {
  if (!newVal) {
    abertoIds.value = [];
    busca.value = "";
  }
});
</script>

<style scoped>
/* Optional custom styles */
</style>

