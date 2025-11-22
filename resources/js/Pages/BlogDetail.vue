<template>
  <Head :title="blog?.title || 'Article'" />
  <div class="sticky top-0 z-20 bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3">
      <h1 class="text-xl md:text-2xl font-semibold text-[#1a73e8] tracking-tight">{{ blog.title }}</h1>
    </div>
  </div>
  <div class="max-w-4xl mx-auto px-4 md:px-6 py-8 md:py-12">


    <article class="bg-white/90 border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
      <img v-if="blog.image" :src="blog.image" class="w-full h-56 md:h-80 object-cover" />
      <div class="p-5 md:p-6">
        <div class="prose prose-lg max-w-none" v-html="blog.content"></div>
      </div>
    </article>

    <div v-if="blog.tags.length" class="mt-8 md:mt-10 flex flex-wrap gap-2">
      <span
        v-for="tag in blog.tags"
        :key="tag"
        class="px-3 py-1 bg-blue-50 text-blue-700 text-sm rounded-full border border-blue-100"
      >
        #{{ tag }}
      </span>
    </div>

    <div class="mt-12">
      <Link href="/blogs" class="text-[#1a73e8] hover:underline">← Back to Articles</Link>
    </div>
  </div>
</template>

<script setup>
import { Link, Head } from "@inertiajs/vue3";
defineProps({ blog: Object });
</script>

<style scoped>
.prose img {
  border-radius: 0.75rem;
}
</style>
