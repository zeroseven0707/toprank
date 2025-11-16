<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Edit City</h4>

    <form @submit.prevent="submit">
      <div class="mb-3">
        <label class="form-label">City Name</label>
        <input type="text" v-model="form.name" class="form-control" required />
      </div>

      <div class="mb-3">
        <label class="form-label">Status</label>
        <select v-model="form.status" class="form-select">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary">Update</button>
      <Link href="/settings/cities" class="btn btn-secondary ms-2">Cancel</Link>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { router, Link } from "@inertiajs/vue3";

// **Define props sebelum digunakan**
const props = defineProps({
  city: {
    type: Object,
    required: true,
  },
});

// Buat reactive form berdasarkan props.city
const form = ref({
  name: props.city.name,
  status: props.city.status,
});

// Submit form update
const submit = () => {
  router.put(`/settings/cities/${props.city.id}`, form.value);
};
</script>
