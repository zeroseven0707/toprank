<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold mb-6 text-2xl">Manage Sections</h4>

    <!-- Tambah Section -->
    <form @submit.prevent="addSection" class="mb-6 flex flex-wrap gap-3 items-center">
      <select v-model="newSectionCategory" class="form-select w-full md:w-64">
        <option value="">Pilih Content Category</option>
        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
          {{ cat.name }}
        </option>
      </select>
      <select v-model="newSectionStatus" class="form-select w-full md:w-32">
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
      <button type="submit" class="btn btn-primary px-6">Add Section</button>
    </form>

    <!-- Drag & Drop List -->
    <draggable v-model="sections" item-key="id" @end="onReorder" class="space-y-3">
      <template #item="{ element, index }">
        <div
          class="p-4 bg-white rounded-xl shadow-sm flex justify-between items-center hover:shadow-md transition cursor-move"
        >
          <div>
            <span class="font-semibold">{{ index + 1 }}.</span>
            <span class="ml-2 text-gray-800">{{ element.content_category.name }}</span>
            <span
              class="ml-3 px-2 py-0.5 rounded text-xs font-medium"
              :class="element.status === 'active'
                ? 'bg-green-100 text-green-700'
                : 'bg-red-100 text-red-700'"
            >
              {{ element.status }}
            </span>
          </div>
          <div class="flex gap-2">
            <button
              @click="editSection(element)"
              class="btn btn-sm btn-warning flex items-center gap-1"
            >
              <i class="ti ti-edit"></i> Edit
            </button>
            <button
              @click="deleteSection(element.id)"
              class="btn btn-sm btn-danger flex items-center gap-1"
            >
              <i class="ti ti-trash"></i> Delete
            </button>
          </div>
        </div>
      </template>
    </draggable>

    <!-- Edit Modal -->
    <transition name="fade">
      <div
        v-if="editingSection"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div class="bg-white p-6 rounded-xl shadow-lg w-96">
          <h5 class="mb-4 font-bold text-lg">Edit Section</h5>

          <label class="block mb-2 font-medium text-gray-700">Content Category</label>
          <select v-model="editingSection.content_category_id" class="form-select mb-4 w-full">
            <option
              v-for="cat in categories"
              :key="cat.id"
              :value="cat.id"
            >
              {{ cat.name }}
            </option>
          </select>

          <label class="block mb-2 font-medium text-gray-700">Status</label>
          <select v-model="editingSection.status" class="form-select mb-6 w-full">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>

          <div class="flex justify-end gap-3">
            <button @click="updateSection" class="btn btn-primary px-4 flex items-center gap-1">
              <i class="ti ti-check"></i> Save
            </button>
            <button @click="editingSection = null" class="btn btn-secondary px-4 flex items-center gap-1">
              <i class="ti ti-x"></i> Cancel
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import draggable from "vuedraggable";

const props = defineProps({
  sections: Array,
  categories: Array,
});

const sections = ref([...props.sections]);
const categories = ref(props.categories);

const newSectionCategory = ref("");
const newSectionStatus = ref("active");
const editingSection = ref(null);

const reloadSections = () => {
  router.reload({
    only: ["sections"],
    onSuccess: (page) => {
      sections.value = [...page.props.sections];
    },
  });
};

const addSection = () => {
  if (!newSectionCategory.value) return alert("Pilih content category");

  router.post(
    "/settings/sections",
    {
      content_category_id: newSectionCategory.value,
      status: newSectionStatus.value,
    },
    {
      onSuccess: () => {
        newSectionCategory.value = "";
        newSectionStatus.value = "active";
        reloadSections();
      },
    }
  );
};

const editSection = (section) => {
  editingSection.value = { ...section };
};

const updateSection = () => {
  router.put(`/settings/sections/${editingSection.value.id}`, editingSection.value, {
    onSuccess: () => {
      editingSection.value = null;
      reloadSections();
    },
  });
};

const deleteSection = (id) => {
  if (confirm("Are you sure?")) {
    router.delete(`/settings/sections/${id}`, {
      onSuccess: reloadSections,
    });
  }
};

const onReorder = () => {
  const order = sections.value.map((s) => s.id);
  router.post("/settings/sections/reorder", { order }, { onSuccess: reloadSections });
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
