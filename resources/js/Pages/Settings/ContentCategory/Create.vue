<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Add Content Category</h4>

    <form @submit.prevent="submit">
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" v-model="form.name" class="form-control" required />
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea v-model="form.description" class="form-control"></textarea>
      </div>

      <button type="submit" class="btn btn-primary">Save</button>
      <Link href="/settings/content-categories" class="btn btn-secondary ms-2">Cancel</Link>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'

const form = ref({
  name: '',
  description: ''
})

const submit = () => {
  router.post(route('content-categories.store'), form.value)
}
</script>
