<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Edit Blog Category</h4>

    <form @submit.prevent="submit">
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" v-model="form.name" class="form-control" required />
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea v-model="form.description" class="form-control"></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Status</label>
        <select v-model="form.status" class="form-select">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary">Update</button>
      <Link href="/settings/blog-categories" class="btn btn-secondary ms-2">Cancel</Link>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'

const props = defineProps({ category: Object })

const form = ref({
  name: props.category.name,
  description: props.category.description,
  status: props.category.status
})

const submit = () => router.put(route('blog-categories.update', props.category.id), form.value)
</script>
