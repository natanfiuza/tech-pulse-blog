<template>
  <textarea ref="editor" class="form-control"></textarea>
</template>

<script>
import EasyMDE from "easymde";
import "easymde/dist/easymde.min.css"; // Importante!

export default {
  props: {
    modelValue: {
      // Use modelValue para v-model
      type: String,
      default: "",
    },
  },
  mounted() {
    this.editor = new EasyMDE({
      element: this.$refs.editor,
      initialValue: this.modelValue,
    });

    this.editor.codemirror.on("change", () => {
      this.$emit("update:modelValue", this.editor.value());
    });
  },

  beforeUnmount() {
    // Limpa o editor ao desmontar
    this.editor.toTextArea();
    this.editor = null;
  },
  watch: {
    modelValue(newValue) {
      if (this.editor && this.editor.value() !== newValue) {
        this.editor.value(newValue);
      }
    },
  },
};
</script>
