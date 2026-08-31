<template>
  <div
    class="min-h-screen bg-background text-on-background font-body selection:bg-primary/30"
  >
    <Head>
      <title>TechPulse - #{{ hashtag.name }}</title>
      <meta
        name="description"
        :content="`Todos os artigos publicados com a hashtag #${hashtag.name}.`"
      />
    </Head>

    <Navbar />

    <main class="pt-28 md:pt-32 pb-20 px-4 sm:px-8 max-w-screen-2xl mx-auto">
      <header class="mb-12">
        <span class="text-primary font-mono text-sm">HASHTAG</span>
        <h1 class="text-4xl md:text-5xl font-black tracking-tight mb-4">
          #{{ hashtag.name }}
        </h1>
        <p class="text-on-surface-variant">
          {{ posts.length }}
          {{ posts.length === 1 ? "artigo publicado" : "artigos publicados" }} com esta
          hashtag.
        </p>
      </header>

      <p v-if="posts.length === 0" class="text-on-surface-variant text-sm">
        Nenhum artigo publicado com esta hashtag ainda.
      </p>
      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <PostCard v-for="post in posts" :key="post.id" :post="post" />
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";
import PostCard from "@/Components/PostCard.vue";

defineProps({
  hashtag: {
    type: Object,
    required: true,
  },
  posts: {
    type: Array,
    default: () => [],
  },
});
</script>
