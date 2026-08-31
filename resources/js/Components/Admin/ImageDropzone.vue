<template>
  <div>
    <div
      class="group flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed p-6 text-center transition-all"
      :class="
        dragging
          ? 'border-primary/60 bg-surface-container-high'
          : 'border-outline-variant/30 hover:border-primary/50 hover:bg-surface-container-high'
      "
      role="button"
      tabindex="0"
      aria-label="Enviar imagem de destaque"
      @click="abrir_seletor"
      @keydown.enter.prevent="abrir_seletor"
      @keydown.space.prevent="abrir_seletor"
      @dragover.prevent="dragging = true"
      @dragleave.prevent="dragging = false"
      @drop.prevent="ao_drop"
    >
      <template v-if="preview_url">
        <img
          :src="preview_url"
          alt="Pré-visualização da imagem de destaque"
          class="mb-3 max-h-40 w-full rounded-md object-cover"
        />
        <p class="text-xs text-on-surface-variant">Clique para trocar a imagem</p>
      </template>
      <template v-else-if="image_src">
        <img
          :src="image_src"
          alt="Imagem de destaque atual"
          class="mb-3 max-h-40 w-full rounded-md object-cover"
        />
        <p class="text-xs text-on-surface-variant">Clique para trocar a imagem</p>
      </template>
      <template v-else>
        <span
          class="material-symbols-outlined mb-2 text-3xl text-on-surface-variant transition-colors group-hover:text-primary"
          aria-hidden="true"
        >cloud_upload</span>
        <p class="text-sm font-medium text-on-surface-variant">
          Arraste a imagem ou <span class="text-primary">clique para upload</span>
        </p>
        <p class="mt-2 text-xs text-on-surface-variant/60">PNG, JPG ou WebP até 5MB</p>
      </template>
      <input
        ref="file_input"
        type="file"
        accept="image/png,image/jpeg,image/webp"
        class="hidden"
        aria-hidden="true"
        tabindex="-1"
        @change="ao_selecionar"
      />
    </div>
    <p v-if="error" class="mt-2 text-xs text-error">{{ error }}</p>
  </div>
</template>

<script>
import { onBeforeUnmount, ref, watch } from "vue";

export default {
    name: "ImageDropzone",
    props: {
        model_value: { type: [File, Object, null], default: null },
        image_src: { type: String, default: "" },
        error: { type: String, default: "" },
    },
    emits: ["update:model_value"],
    setup(props, { emit }) {
        const file_input = ref(null);
        const dragging = ref(false);
        const preview_url = ref("");

        function limpar_preview() {
            if (preview_url.value) {
                URL.revokeObjectURL(preview_url.value);
                preview_url.value = "";
            }
        }

        watch(
            () => props.model_value,
            (file) => {
                limpar_preview();
                if (file instanceof File) {
                    preview_url.value = URL.createObjectURL(file);
                }
            }
        );

        onBeforeUnmount(limpar_preview);

        function abrir_seletor() {
            file_input.value?.click();
        }

        function ao_selecionar(event) {
            const arquivo = event.target.files?.[0] || null;
            event.target.value = ""; // Permite selecionar o mesmo arquivo novamente
            emit_file(arquivo);
        }

        function ao_drop(event) {
            dragging.value = false;
            const arquivo = event.dataTransfer?.files?.[0] || null;
            emit_file(arquivo);
        }

        function emit_file(arquivo) {
            if (arquivo instanceof File) {
                emit("update:model_value", arquivo);
            } else {
                emit("update:model_value", null);
            }
        }

        return {
            file_input,
            dragging,
            preview_url,
            abrir_seletor,
            ao_selecionar,
            ao_drop,
        };
    },
};
</script>
