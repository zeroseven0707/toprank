<template>
  <div class="tw-space-y-6">
    <h1 class="tw-text-2xl tw-font-semibold">Place Settings</h1>

    <Card>
      <ItemList
        :items="places"
        @delete-item="deletePlace"
        placeholder="Add new place"
        @add-item="addPlace"
      />
    </Card>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import ItemList from "@/components/ItemList.vue";

const places = ref([]);

onMounted(loadPlaces);

async function loadPlaces() {
  places.value = (await axios.get("/settings/places")).data;
}

async function addPlace(name) {
  await axios.post("/settings/places", { name });
  loadPlaces();
}

async function deletePlace(id) {
  await axios.delete(`/settings/places/${id}`);
  loadPlaces();
}
</script>
