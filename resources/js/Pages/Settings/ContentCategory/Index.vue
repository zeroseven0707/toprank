<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="fw-bold">Content Categories</h4>
      <Link href="/settings/content-categories/create" class="btn btn-primary">Add Category</Link>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Category List</h5>
        </div>
      <div class="card-body">
          <table class="table table-striped table-hover table-responsive align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="d-none d-sm-table-cell" style="width:80px">No</th>
                <th>Name</th>
                <th class="d-none d-md-table-cell">Description</th>
                <th class="text-center" style="width:120px">Action</th>
              </tr>
            </thead>
            <tbody>
            <tr v-for="(category, index) in categories.data" :key="category.id">
              <td class="d-none d-sm-table-cell">{{ index + 1 }}</td>
              <td class="text-break">{{ category.name }}</td>
              <td class="d-none d-md-table-cell">{{ category.description || '-' }}</td>
              <td class="text-center position-relative">
                <div class="dropdown">
                  <button class="btn btn-sm btn-light" @click.stop="toggleMenu(category.id)">
                    <i class="ti ti-dots-vertical"></i>
                  </button>
                  <div v-if="openMenuId === category.id" class="dropdown-menu show" style="position:absolute; right:0; z-index:10;">
                    <Link :href="`/settings/content-categories/${category.id}/edit`" class="dropdown-item">
                      <i class="ti ti-edit me-1"></i> Edit
                    </Link>
                    <button @click.prevent="destroy(category.id)" class="dropdown-item text-danger">
                      <i class="ti ti-trash me-1"></i> Delete
                    </button>
                  </div>
                </div>
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
import { ref } from 'vue'

defineProps({
  categories: Object
})

const openMenuId = ref(null)
const toggleMenu = (id) => { openMenuId.value = openMenuId.value === id ? null : id }
document.addEventListener('click', (e) => { if (!e.target.closest('.dropdown')) openMenuId.value = null })

const visitPage = (page) => {
  router.visit(route('content-categories.index'), {
    data: { page },
    preserveScroll: true,
    preserveState: true,
  })
}

const destroy = (id) => {
  if (confirm('Are you sure to delete this category?')) {
    router.delete(route('content-categories.destroy', id))
  }
}
</script>
