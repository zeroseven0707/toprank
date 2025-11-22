<template>
  <Head title="Blog" />
  <div class="bg-gray-50 min-h-screen">
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 mt-10 mb-20">
      <h2 class="text-3xl font-bold mb-8 text-gray-800">Artikel Terbaru</h2>

      <!-- Grid Artikel -->
      <div v-if="blogs && blogs.data && blogs.data.length"
           class="grid lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1 gap-8">
        <div v-for="blog in blogs.data" :key="blog.id"
             class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden group flex flex-col">

          <!-- Image -->
          <div class="overflow-hidden rounded-t-2xl">
            <img v-if="blog.image" :src="blog.image" alt="Blog Image"
                 class="w-full h-52 object-cover group-hover:scale-105 transition-transform duration-700" />
          </div>

          <!-- Content -->
          <div class="p-6 flex flex-col flex-grow">
            <p v-if="blog.category"
               class="text-xs font-semibold uppercase tracking-wide text-blue-600 mb-2">
              {{ blog.category.name }}
            </p>
            <h2 class="text-xl font-semibold text-gray-800 group-hover:text-blue-600 transition mb-2 leading-tight">
              {{ blog.title }}
            </h2>
            <div class="text-gray-400 text-xs mb-3">
              {{ blog.author || 'Admin' }} • {{ new Date(blog.created_at).toLocaleDateString() }}
            </div>
            <p class="text-gray-600 text-sm line-clamp-3 flex-grow">
              {{ blog.excerpt }}
            </p>
            <div class="mt-4">
              <Link :href="route('blogs.front.show', blog.slug)"
                    class="text-blue-600 hover:text-blue-800 font-medium transition-all">
                Baca Selengkapnya →
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="flex flex-col items-center justify-center py-24 text-center text-gray-500">
        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" alt="No articles"
             class="w-40 h-40 mb-6 opacity-70" />
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum ada artikel</h3>
        <p class="text-gray-500 max-w-md mb-6">
          Sepertinya belum ada artikel yang dipublikasikan. Silakan cek kembali nanti
          atau tambahkan artikel baru dari panel admin.
        </p>
        <Link href="/" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all">
          Kembali ke Beranda
        </Link>
      </div>

      <!-- Pagination -->
      <div v-if="blogs && blogs.data && blogs.data.length" class="flex justify-center mt-12 space-x-2">
        <button v-if="blogs.prev_page_url" @click="visitPage(blogs.current_page - 1)"
                class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
          ← Sebelumnya
        </button>

        <button v-if="blogs.next_page_url" @click="visitPage(blogs.current_page + 1)"
                class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
          Selanjutnya →
        </button>
      </div>
    </main>
  </div>
</template>

<script setup>
import { Link, router, Head } from "@inertiajs/vue3";

defineProps({
  blogs: { type: Object, required: true },
});

function visitPage(page) {
  router.visit(route("blogs.front.index"), {
    data: { page },
    preserveScroll: true,
    preserveState: true,
  });
}
</script>
