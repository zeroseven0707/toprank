<template>
  <div class="max-w-4xl mx-auto px-4 py-12">
    <img
      v-if="blog.image"
      :src="blog.image"
      class="w-full h-80 object-cover rounded-2xl shadow mb-8"
    />

    <h1 class="text-4xl font-bold mb-4 text-gray-800">{{ blog.title }}</h1>
    <p class="text-gray-500 text-sm mb-8">
      By {{ blog.author }} • {{ blog.created_at }}
    </p>

    <div class="prose prose-lg max-w-none" v-html="blog.content"></div>

    <div v-if="blog.tags.length" class="mt-8 flex flex-wrap gap-2">
      <span
        v-for="tag in blog.tags"
        :key="tag"
        class="px-3 py-1 bg-blue-100 text-blue-700 text-sm rounded-full"
      >
        #{{ tag }}
      </span>
    </div>

    <div class="mt-12">
      <Link href="/blogs" class="text-blue-600 hover:underline">← Back to Articles</Link>
    </div>
  </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
defineProps({ blog: Object });
</script>

<style scoped>
.prose img {
  border-radius: 0.75rem;
}
</style>
