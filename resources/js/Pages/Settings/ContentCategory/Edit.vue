<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Edit Content Category</h4>

    <form @submit.prevent="submit">
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" v-model="form.name" class="form-control" required />
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea v-model="form.description" class="form-control"></textarea>
      </div>

      <button type="submit" class="btn btn-primary">Update</button>
      <Link href="/settings/content-categories" class="btn btn-secondary ms-2">Cancel</Link>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'

const props = defineProps({
  category: {
    type: Object,
    required: true
  }
})

const form = ref({
  name: props.category.name,
  description: props.category.description
})

const submit = () => {
  router.put(route('content-categories.update', props.category.id), form.value)
}
</script>
