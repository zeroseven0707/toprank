<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Privacy Policy</h5>
      </div>

      <div class="card-body">
        <!-- Editable (for Admin) -->
        <div v-if="can.edit">
          <div class="mb-3">
            <label class="form-label fw-semibold">Content</label>
            <textarea
              v-model="form.content"
              class="form-control"
              rows="10"
              placeholder="Write your privacy policy here..."
            ></textarea>
          </div>

          <div class="text-end mt-3">
            <button
              @click="updatePolicy"
              class="btn btn-primary"
              :disabled="form.processing"
            >
              <i class="ti ti-device-floppy me-1"></i> Save Policy
            </button>
          </div>
        </div>

        <!-- Read Only (for Users) -->
        <div v-else>
          <div class="border rounded p-3 bg-light" v-html="policy?.content"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
  policy: Object,
  can: Object,
});

// Gunakan useForm dari Inertia
const form = useForm({
  content: props.policy?.content || "",
});

const updatePolicy = () => {
  form.put(route("privacy.update"), {
    preserveScroll: true,
    onSuccess: () => {
      alert("✅ Privacy Policy updated successfully!");
    },
    onError: () => {
      alert("❌ Failed to update Privacy Policy.");
    }
  });
};
</script>

<style scoped>
.card {
  border-radius: 1rem;
}

textarea.form-control {
  min-height: 300px;
  font-family: inherit;
  font-size: 0.95rem;
}

.bg-light {
  background-color: #f8f9fa !important;
}
</style>
