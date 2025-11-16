<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="fw-bold">Blog Categories</h4>
      <Link href="/settings/blog-categories/create" class="btn btn-primary">Add Category</Link>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body">
        <table class="table table-striped">
          <thead class="table-light">
            <tr>
              <th>No</th>
              <th>Name</th>
              <th>Description</th>
              <th>Status</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(category, index) in categories.data" :key="category.id">
              <td>{{ index + 1 }}</td>
              <td>{{ category.name }}</td>
              <td>{{ category.description || '-' }}</td>
              <td>
                <span :class="category.status === 'active' ? 'badge bg-success' : 'badge bg-secondary'">
                  {{ category.status }}
                </span>
              </td>
              <td class="text-center">
                <Link :href="`/settings/blog-categories/${category.id}/edit`" class="btn btn-sm btn-warning me-2">Edit</Link>
                <button @click="destroy(category.id)" class="btn btn-sm btn-danger">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div v-if="categories && categories.data && categories.data.length" class="flex justify-center mt-4 space-x-2">
          <button v-if="categories.prev_page_url" @click="visitPage(categories.current_page - 1)"
            class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">← Prev</button>
          <button v-if="categories.next_page_url" @click="visitPage(categories.current_page + 1)"
            class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">Next →</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'

defineProps({ categories: Object })

const visitPage = (page) => {
  router.visit(route('blog-categories.index'), { data: { page }, preserveScroll: true, preserveState: true });
}

const destroy = (id) => {
  if (confirm('Are you sure to delete this category?')) {
    router.delete(route('blog-categories.destroy', id));
  }
}
</script>
