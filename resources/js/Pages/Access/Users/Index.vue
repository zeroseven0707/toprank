<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Admin /</span> User Management
    </h4>

    <!-- User List -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">User List</h5>
      </div>

        <table class="table table-striped table-hover table-responsive align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="d-none d-sm-table-cell">No</th>
              <th>Nama</th>
              <th>Email</th>
              <th class="d-none d-md-table-cell">Role</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(user, index) in users" :key="user.id">
              <td class="d-none d-sm-table-cell">{{ index + 1 }}</td>
              <td>{{ user.name }}</td>
              <td class="text-break">{{ user.email }}</td>
              <td class="d-none d-md-table-cell">{{ user.roles.length ? user.roles.map(r => r.name).join(', ') : '-' }}</td>
                <td class="text-center text-nowrap position-relative">
                <div class="dropdown">
                    <!-- Tombol titik 3 -->
                    <button class="btn btn-sm btn-light" @click="toggleMenu(user.id)">
                    <i class="ti ti-dots-vertical"></i>
                    </button>

                    <!-- Menu dropdown -->
                    <div
                    v-if="openMenuId === user.id"
                    class="dropdown-menu show"
                    style="position:absolute; right:0; z-index:10;"
                    >
                    <a class="dropdown-item" href="#" @click.prevent="openAssignModal(user)">
                        <i class="ti ti-user-check me-1"></i> Assign
                    </a>
                    <a class="dropdown-item" href="#" @click.prevent="editUser(user)">
                        <i class="ti ti-edit me-1"></i> Edit
                    </a>
                    <a class="dropdown-item text-danger" href="#" @click.prevent="deleteUser(user.id)">
                        <i class="ti ti-trash me-1"></i> Hapus
                    </a>
                    </div>
                </div>
            </td>
            </tr>
          </tbody>
        </table>
    </div>

    <!-- Assign Role Modal -->
 <!-- Assign Role Modal -->
<transition name="fade">
  <div
    v-if="showAssignModal"
    class="modal fade show"
    style="display: block; background: rgba(0,0,0,0.5);"
    tabindex="-1"
  >
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            Assign Role to <span class="text-primary">{{ selectedUser?.name }}</span>
          </h5>
          <button type="button" class="btn-close" @click="closeAssignModal"></button>
        </div>

        <div class="modal-body">
          <label class="form-label">Select Role</label>
          <select v-model="selectedRole" class="form-select">
            <option value="" disabled>Pilih Role</option>
            <option v-for="r in roles" :key="r.id" :value="r.name">
              {{ r.name }}
            </option>
          </select>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" @click="closeAssignModal">Close</button>
          <button class="btn btn-primary" @click="assignRole">Save</button>
        </div>
      </div>
    </div>
  </div>
</transition>

  </div>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const openMenuId = ref(null);

const toggleMenu = (userId) => {
  // klik menu yang sama akan menutupnya, klik menu lain akan buka menu baru
  openMenuId.value = openMenuId.value === userId ? null : userId;
};

// opsional: tutup menu saat klik di luar
document.addEventListener('click', (e) => {
  const dropdowns = document.querySelectorAll('.dropdown');
  let clickedInside = false;
  dropdowns.forEach(drop => {
    if (drop.contains(e.target)) clickedInside = true;
  });
  if (!clickedInside) openMenuId.value = null;
});

const props = defineProps({
  users: Array,
  roles: Array,
});

const showAssignModal = ref(false);
const selectedUser = ref(null);
const selectedRole = ref("");

const openAssignModal = (user) => {
  selectedUser.value = user;
  selectedRole.value = user.role ? user.role.name : "";
  showAssignModal.value = true;
};

const closeAssignModal = () => {
  showAssignModal.value = false;
  selectedUser.value = null;
  selectedRole.value = "";
};

const assignRole = () => {
  if (!selectedRole.value) {
    alert("Pilih role terlebih dahulu!");
    return;
  }

  // Inertia post dengan onSuccess menangkap flash message
  router.post(route("assign.role"), {
    user_id: selectedUser.value.id,
    role: selectedRole.value,
  }, {
    onSuccess: (page) => {
      // Update role di front-end agar langsung terlihat
      if (selectedUser.value.roles) {
        const exists = selectedUser.value.roles.find(r => r.name === selectedRole.value);
        if (!exists) {
          selectedUser.value.roles.push({ name: selectedRole.value });
        }
      } else {
        selectedUser.value.roles = [{ name: selectedRole.value }];
      }

      closeAssignModal();

      if (page.props.flash?.success) {
        alert(page.props.flash.success);
      }
    },
    onError: (errors) => {
      console.error(errors);
      alert("❌ Gagal assign role!");
    }
  });
};


const editUser = (user) => {
  router.visit(route("users.edit", user.id));
};

const deleteUser = (id) => {
  if (confirm("Yakin ingin menghapus user ini?")) {
    router.delete(route("users.destroy", id));
  }
};
</script>

<style scoped>
.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.animate-fadeIn {
  animation: fadeIn 0.25s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
