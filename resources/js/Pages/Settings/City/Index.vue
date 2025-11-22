<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="fw-bold">City Management</h4>
      <Link href="/settings/cities/create" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Add City
      </Link>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">City List</h5>
      </div>
      <div class="card-body">
        <div>
          <table class="table table-striped table-responsive align-middle">
            <thead class="table-light">
              <tr>
                <th class="d-none d-sm-table-cell">No</th>
                <th>City</th>
                <th class="d-none d-md-table-cell">Status</th>
                <th class="text-center" style="width: 120px">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(city, index) in cities.data" :key="city.id">
                <td class="d-none d-sm-table-cell">{{ index + 1 }}</td>
                <td class="text-break">{{ city.name }} <i v-if="city.is_current" class="ti ti-star me-1 text-warning"></i></td>
                <td class="d-none d-md-table-cell">{{ city.status }}</td>
                <td class="text-center position-relative">
                  <div class="dropdown">
                    <button class="btn btn-sm btn-light" @click.stop="toggleMenu(city.id)">
                      <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div v-if="openMenuId === city.id" class="dropdown-menu show" style="position:absolute; right:0; z-index:10;">
                      <Link :href="`/settings/cities/${city.id}/edit`" class="dropdown-item">
                        <i class="ti ti-edit me-1"></i> Edit
                      </Link>
                      <button class="dropdown-item" @click.prevent="makeDefault(city.id)">
                        <i class="ti ti-star me-1"></i> Jadikan Default
                      </button>
                      <button class="dropdown-item text-danger" @click.prevent="deleteCity(city.id)">
                        <i class="ti ti-trash me-1"></i> Delete
                      </button>
                    </div>
                  </div>
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
import { ref } from "vue";

defineProps({
  cities: Object,
});

const visitPage = (page) => {
  router.get("/cities", { page }, { preserveState: true });
};

const openMenuId = ref(null);
const toggleMenu = (id) => { openMenuId.value = openMenuId.value === id ? null : id };
const deleteCity = (id) => {
  if (confirm("Are you sure you want to delete this city?")) {
    router.delete(`/settings/cities/${id}`);
  }
};
const makeDefault = (id) => {
  router.post(`/settings/cities/${id}/default`);
};
document.addEventListener('click', (e) => { if (!e.target.closest('.dropdown')) openMenuId.value = null; });
</script>
