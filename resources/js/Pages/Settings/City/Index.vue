<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="fw-bold">City Management</h4>
      <Link href="/settings/cities/create" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Add City
      </Link>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead class="table-light">
              <tr>
                <th>No</th>
                <th>City</th>
                <th>Status</th>
                <th class="text-center" style="width: 150px">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(city, index) in cities.data" :key="city.id">
                <td>{{ index + 1 }}</td>
                <td>{{ city.name }}</td>
                <td>{{ city.status }}</td>
                <td class="text-center">
                  <Link :href="`/settings/cities/${city.id}/edit`" class="btn btn-sm btn-warning me-2">
                    Edit
                  </Link>
                  <button @click="deleteCity(city.id)" class="btn btn-sm btn-danger">
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>

          <div v-if="!cities.data.length" class="text-center text-muted py-4">
            No cities found.
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="cities.data.length" class="flex justify-center mt-4 space-x-2">
          <button v-if="cities.prev_page_url" @click="visitPage(cities.current_page - 1)" class="btn btn-light">
            ← Previous
          </button>
          <button v-if="cities.next_page_url" @click="visitPage(cities.current_page + 1)" class="btn btn-light">
            Next →
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from "@inertiajs/vue3";

defineProps({
  cities: Object,
});

const visitPage = (page) => {
  router.get("/cities", { page }, { preserveState: true });
};

const deleteCity = (id) => {
  if (confirm("Are you sure you want to delete this city?")) {
    router.delete(`/settings/cities/${id}`);
  }
};
</script>
