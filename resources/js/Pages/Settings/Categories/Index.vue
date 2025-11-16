<template>
  <div class="tw-space-y-6">
    <h1 class="tw-text-2xl tw-font-semibold">Content Categories</h1>

    <Card>
      <ItemList
        :items="categories"
        @delete-item="deleteCategory"
        placeholder="Add new category"
        @add-item="addCategory"
      />
    </Card>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import ItemList from "@/components/ItemList.vue";

const categories = ref([]);

onMounted(loadCategories);

async function loadCategories() {
  categories.value = (await axios.get("/settings/categories")).data;
}

async function addCategory(name) {
  await axios.post("/settings/categories", { name });
  loadCategories();
}

async function deleteCategory(id) {
  await axios.delete(`/settings/categories/${id}`);
  loadCategories();
}
</script>
