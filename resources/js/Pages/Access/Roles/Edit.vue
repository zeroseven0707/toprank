<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">✏️ Edit Role</h5>
        <Link href="/access/roles" class="btn btn-sm btn-outline-secondary">← Back</Link>
      </div>

      <div class="card-body">
        <form @submit.prevent="submit">
          <!-- Role name -->
          <div class="mb-4">
            <label class="form-label fw-semibold">Role Name</label>
            <input
              v-model="form.name"
              type="text"
              class="form-control"
              placeholder="Enter role name..."
            />
          </div>

          <!-- Permissions header -->
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0">Permissions</h6>
            <button type="button" class="btn btn-sm btn-outline-primary" @click="toggleAll">
              {{ allChecked ? 'Uncheck All' : 'Check All' }}
            </button>
          </div>

          <!-- Permissions grouped by module -->
          <div v-for="(group, groupName) in permissions" :key="groupName" class="mb-4 border rounded p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="checkbox"
                  :id="'group-' + groupName"
                  :checked="isGroupChecked(groupName)"
                  @change="toggleGroup(groupName)"
                />
                <label class="form-check-label fw-bold text-uppercase" :for="'group-' + groupName">
                  {{ groupName.replace('-', ' ') }}
                </label>
              </div>
              <small class="text-muted">
                ({{ group.length }} permissions)
              </small>
            </div>

            <div class="row">
              <div v-for="perm in group" :key="perm.id" class="col-md-3 mb-2">
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    :id="'perm-' + perm.id"
                    :value="perm.name"
                    v-model="form.permissions"
                  />
                  <label class="form-check-label" :for="'perm-' + perm.id">
                    {{ perm.name.split(' ')[0] }}
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Submit -->
          <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-success">
              💾 Update Role
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'

const props = defineProps({
  role: Object,
  permissions: Object,
  rolePermissions: Array,
})

const form = ref({
  name: props.role.name,
  permissions: [...props.rolePermissions],
})

// 🔘 Global Check/Uncheck All
const allChecked = computed(() => {
  const all = Object.values(props.permissions).flat().map(p => p.name)
  return all.every(p => form.value.permissions.includes(p))
})
const toggleAll = () => {
  if (allChecked.value) {
    form.value.permissions = []
  } else {
    form.value.permissions = Object.values(props.permissions).flat().map(p => p.name)
  }
}

// 🔘 Per-module Check/Uncheck
const isGroupChecked = (groupName) => {
  const group = props.permissions[groupName].map(p => p.name)
  return group.every(p => form.value.permissions.includes(p))
}
const toggleGroup = (groupName) => {
  const group = props.permissions[groupName].map(p => p.name)
  if (isGroupChecked(groupName)) {
    form.value.permissions = form.value.permissions.filter(p => !group.includes(p))
  } else {
    form.value.permissions = [...new Set([...form.value.permissions, ...group])]
  }
}

// 🔘 Submit ke backend
const submit = () => {
  router.put(route('roles.update', props.role.id), form.value, {
    onSuccess: () => alert('✅ Role updated successfully!'),
  })
}
</script>
