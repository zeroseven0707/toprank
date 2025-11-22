<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <h4 class="fw-bold mb-0">Content Categories</h4>
      <form class="d-flex flex-wrap gap-2" @submit.prevent="addCategory(newName.value)">
        <input v-model="newName" class="form-control" placeholder="Add new category" />
        <button class="btn btn-primary">Add</button>
      </form>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="d-none d-sm-table-cell" style="width:80px">No</th>
                <th>Name</th>
                <th class="text-center" style="width:120px">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(cat, idx) in categories" :key="cat.id">
                <td class="d-none d-sm-table-cell">{{ idx + 1 }}</td>
                <td class="text-break">{{ cat.name }}</td>
                <td class="text-center position-relative">
                  <div class="dropdown">
                    <button class="btn btn-sm btn-light" @click.stop="toggleMenu(cat.id)">
                      <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div v-if="openMenuId === cat.id" class="dropdown-menu show" style="position:absolute; right:0; z-index:10;">
                      <button class="dropdown-item text-danger" @click.prevent="deleteCategory(cat.id)">
                        <i class="ti ti-trash me-1"></i> Delete
                      </button>
                    </div>
                  </div>
                </td>
              </tr>
              <tr v-if="!categories.length">
                <td colspan="3" class="text-center text-muted py-4">No categories found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";


const categories = ref([]);
const newName = ref("");
const openMenuId = ref(null);
const toggleMenu = (id) => { openMenuId.value = openMenuId.value === id ? null : id };
document.addEventListener('click', (e) => { if (!e.target.closest('.dropdown')) openMenuId.value = null });

onMounted(loadCategories);

async function loadCategories() {
  categories.value = (await axios.get("/settings/categories")).data;
}

async function addCategory(name) {
  await axios.post("/settings/categories", { name });
  newName.value = "";
  loadCategories();
}

async function deleteCategory(id) {
  await axios.delete(`/settings/categories/${id}`);
  loadCategories();
}
</script>
