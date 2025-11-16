<template>
  <div>
    <ul>
      <li
        v-for="item in items"
        :key="item.id"
        class="tw-flex tw-justify-between tw-items-center tw-border-b tw-py-2"
      >
        <span>{{ item.name }}</span>
        <button class="btn btn-sm btn-danger" @click="$emit('delete-item', item.id)">Delete</button>
      </li>
    </ul>
    <form @submit.prevent="$emit('add-item', newItem)" class="tw-mt-3 tw-flex tw-gap-2">
      <input v-model="newItem" :placeholder="placeholder" class="form-control" />
      <button class="btn btn-primary">Add</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
const props = defineProps({
  items: Array,
  placeholder: String,
});
const newItem = ref("");
</script>
