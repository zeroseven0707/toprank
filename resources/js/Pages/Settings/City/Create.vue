<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-4">Add City</h4>

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

      <button type="submit" class="btn btn-primary">Save</button>
      <Link href="/cities" class="btn btn-secondary ms-2">Cancel</Link>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { router, Link } from "@inertiajs/vue3";

defineProps({
  provinces: Array,
});

const form = ref({
  name: "",
  status: "active",
});

const submit = () => {
  router.post("/settings/cities", form.value);
};
</script>
