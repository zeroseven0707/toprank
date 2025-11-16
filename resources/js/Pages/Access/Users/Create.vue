<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Admin /</span> Create User
    </h4>

    <div class="card">
      <div class="card-body">
        <form @submit.prevent="submit">
          <!-- Name -->
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input
              type="text"
              v-model="form.name"
              class="form-control"
              :class="{ 'is-invalid': form.errors.name }"
            />
            <div class="invalid-feedback">{{ form.errors.name }}</div>
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input
              type="email"
              v-model="form.email"
              class="form-control"
              :class="{ 'is-invalid': form.errors.email }"
            />
            <div class="invalid-feedback">{{ form.errors.email }}</div>
          </div>

          <!-- Password -->
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input
              type="password"
              v-model="form.password"
              class="form-control"
              :class="{ 'is-invalid': form.errors.password }"
            />
            <div class="invalid-feedback">{{ form.errors.password }}</div>
          </div>

          <!-- Confirm Password -->
          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input
              type="password"
              v-model="form.password_confirmation"
              class="form-control"
              :class="{ 'is-invalid': form.errors.password_confirmation }"
            />
            <div class="invalid-feedback">{{ form.errors.password_confirmation }}</div>
          </div>

          <!-- Role -->
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select v-model="form.role" class="form-select" :class="{ 'is-invalid': form.errors.role }">
              <option value="" disabled>Pilih Role</option>
              <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
            </select>
            <div class="invalid-feedback">{{ form.errors.role }}</div>
          </div>

          <button type="submit" class="btn btn-primary" :disabled="form.processing">
            Save User
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  roles: Array,
})

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: '', // single role
})

const submit = () => {
  form.post(route('users.store'), {
    onSuccess: () => {
      alert('✅ User created successfully!')
    },
    onError: () => {
      alert('❌ Failed to create user!')
    }
  })
}
</script>
