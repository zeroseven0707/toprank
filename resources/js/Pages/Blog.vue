<template>
  <Head title="Blog" />
  <div class="bg-gray-50 min-h-screen">
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 mt-10 mb-20">
      <div v-if="blogs && blogs.data && blogs.data.length">
        <div class="mb-8">
          <Link v-if="featuredBlog" :href="route('blogs.front.show', featuredBlog.slug)" class="relative block rounded-2xl overflow-hidden bg-white/80 border border-gray-100 shadow-sm hover:shadow-md transition group">
            <img v-if="featuredBlog.image" :src="featuredBlog.image" class="w-full h-72 md:h-96 object-cover group-hover:scale-105 transition" />
            <div v-else class="w-full h-72 md:h-96 bg-gray-200"></div>
            <div class="absolute inset-x-0 bottom-0 p-6 bg-gradient-to-t from-black/60 via-black/20 to-transparent text-white">
              <p v-if="featuredBlog.category" class="text-xs uppercase tracking-wide mb-2">{{ featuredBlog.category.name }}</p>
              <h2 class="text-2xl md:text-3xl font-bold mb-2">{{ featuredBlog.title }}</h2>
              <div class="text-sm opacity-90">{{ new Date(featuredBlog.created_at).toLocaleDateString() }}</div>
            </div>
          </Link>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <div class="md:col-span-2 space-y-6">
            <Link v-for="blog in otherBlogs" :key="blog.id" :href="route('blogs.front.show', blog.slug)" class="bg-white/80 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden flex">
              <img v-if="blog.image" :src="blog.image" class="w-40 h-32 object-cover hidden sm:block" />
              <div class="p-5 flex-1">
                <p v-if="blog.category" class="text-xs font-semibold uppercase text-blue-600/80 mb-1">{{ blog.category.name }}</p>
                <h3 class="text-lg font-semibold text-gray-800">{{ blog.title }}</h3>
                <div class="text-gray-400 text-xs mb-2">{{ new Date(blog.created_at).toLocaleDateString() }}</div>
                <p class="text-gray-600 text-sm line-clamp-2">{{ blog.excerpt }}</p>
              </div>
            </Link>
          </div>
          <aside class="space-y-4">
            <h4 class="text-sm font-semibold text-gray-700">Trending</h4>
            <Link v-for="blog in otherBlogs.slice(0,5)" :key="blog.id" :href="route('blogs.front.show', blog.slug)" class="flex gap-3 bg-white/80 rounded-xl border border-gray-100 p-3 hover:bg-gray-50">
              <img v-if="blog.image" :src="blog.image" class="w-14 h-14 object-cover rounded-md" />
              <div class="flex-1">
                <span class="text-sm font-medium text-gray-800 line-clamp-2">{{ blog.title }}</span>
              </div>
            </Link>
          </aside>
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
import { computed } from "vue";

const { blogs } = defineProps({
  blogs: { type: Object, required: true },
});

const featuredBlog = computed(() => blogs?.data?.[0] || null)
const otherBlogs = computed(() => (blogs?.data || []).slice(1))

function visitPage(page) {
  router.visit(route("blogs.front.index"), {
    data: { page },
    preserveScroll: true,
    preserveState: true,
  });
}
</script>
