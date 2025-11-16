<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="fw-bold mb-0">
        <span class="text-muted fw-light">Admin /</span> Blog Management
      </h4>
      <Link href="/admin/blogs/create" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> New Blog
      </Link>
    </div>

    <!-- Blog Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">All Blogs</h5>
      </div>

      <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Title</th>
              <!-- <th>Category</th> -->
              <th>Author</th>
              <th>Status</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="blog in blogs.data" :key="blog.id">
              <td>
                <div class="d-flex align-items-center">
                  <img
                    v-if="blog.image"
                    :src="`/storage/${blog.image}`"
                    alt="Blog Thumbnail"
                    class="rounded me-3"
                    width="50"
                    height="50"
                  />
                  <div>
                    <strong>{{ blog.title }}</strong>
                    <div class="text-muted small">ID: {{ blog.id }}</div>
                  </div>
                </div>
              </td>
              <!-- <td>{{ blog.category }}</td> -->
              <td>
                <div class="d-flex align-items-center">
                  <img
                    src="/assets/img/avatars/1.png"
                    alt="Author Avatar"
                    class="rounded-circle me-2"
                    width="32"
                    height="32"
                  />
                  {{ blog.user.name }}
                </div>
              </td>
              <td>
                <span
                  class="badge"
                  :class="{
                    'bg-success': blog.status === 'published',
                    'bg-warning': blog.status === 'draft',
                    'bg-secondary': blog.status === 'archived',
                  }"
                >
                  {{ blog.status }}
                </span>
              </td>
              <td class="text-center">
                <Link
                  :href="`/admin/blogs/${blog.id}/edit`"
                  class="btn btn-sm btn-icon btn-outline-primary me-1"
                >
                  <i class="ti ti-pencil"></i>
                </Link>
                <button
                  @click="deleteBlog(blog.id)"
                  class="btn btn-sm btn-icon btn-outline-danger"
                >
                  <i class="ti ti-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="card-footer d-flex justify-content-between">
        <button
          @click="previousPage"
          :disabled="!blogs.prev_page_url"
          class="btn btn-outline-secondary"
        >
          <i class="ti ti-chevron-left me-1"></i> Prev
        </button>

        <button
          @click="nextPage"
          :disabled="!blogs.next_page_url"
          class="btn btn-outline-secondary"
        >
          Next <i class="ti ti-chevron-right ms-1"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router, Link } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";
const props = defineProps({
  blogs: Object,
});

const page = usePage();
console.log('Auth from Inertia:', page.props.auth);

const deleteBlog = (id) => {
  if (confirm("Are you sure you want to delete this blog?")) {
    router.delete(`/blogs/${id}`);
  }
};

const nextPage = () => {
  if (props.blogs.next_page_url) router.visit(props.blogs.next_page_url);
};

const previousPage = () => {
  if (props.blogs.prev_page_url) router.visit(props.blogs.prev_page_url);
};

</script>

<style scoped>
.badge {
  text-transform: capitalize;
}
</style>
