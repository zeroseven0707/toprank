<template>
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">
                <i class="ti ti-shield me-2"></i> Role Management
            </h4>
            <Link href="/access/roles/create" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i> Add Role
            </Link>
        </div>

        <!-- Roles Table -->
       <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Role List</h5>
      </div>
            <table class="table table-striped table-hover table-responsive align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="w-25">Role</th>
                        <th>Permissions</th>
                        <th class="text-center" style="width: 150px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="role in roles" :key="role.id">
                        <!-- Role Name -->
                        <td class="fw-semibold">
                            <i class="ti ti-lock me-2 text-primary"></i>
                            {{ role . name }}
                        </td>

                        <!-- Permissions -->
                        <td>
                            <div class="d-none d-md-flex flex-wrap align-items-center gap-1">
                                <template v-for="perm in limitedPermissions(role.permissions)" :key="perm.id">
                                    <span class="badge bg-label-info text-capitalize">{{ perm . name }}</span>
                                </template>
                                <span v-if="role.permissions.length > 10" class="badge bg-label-secondary">
                                    +{{ role . permissions . length - 10 }}
                                </span>
                            </div>
                            <span class="d-inline d-md-none badge bg-label-info">{{ role . permissions . length }} perms</span>
                        </td>

                        <!-- Actions Dropdown -->
                        <td class="text-center text-nowrap position-relative">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" @click.stop="toggleMenu(role.id)">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>

                                <div v-if="openMenuId === role.id" class="dropdown-menu show"
                                    style="position:absolute; right:0; z-index:10;">
                                    <Link :href="`/access/roles/${role.id}/edit`" class="dropdown-item">
                                    <i class="ti ti-edit me-1"></i> Edit
                                    </Link>
                                    <button class="dropdown-item text-danger"
                                        @click.prevent="deleteRole(role.id)">
                                        <i class="ti ti-trash me-1"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="!roles.length" class="text-center text-muted py-4">
                <i class="ti ti-info-circle me-1"></i> No roles found.
            </div>
        </div>
    </div>
</template>

<script setup>
    import {
        ref
    } from "vue";
    import {
        router,
        Link
    } from '@inertiajs/vue3';

    const props = defineProps({
        roles: Array,
    });

    const openMenuId = ref(null);

    // Toggle menu per role
    const toggleMenu = (roleId) => {
        openMenuId.value = openMenuId.value === roleId ? null : roleId;
    };

    // Batasi permission yang tampil (10 max)
    const limitedPermissions = (permissions) => permissions.slice(0, 10);

    // Hapus Role
    const deleteRole = (id) => {
        if (confirm('Are you sure you want to delete this role?')) {
            router.delete(route('roles.destroy', id), {
                onSuccess: () => alert('🗑️ Role deleted successfully!'),
            });
        }
    };

    // Tutup menu saat klik di luar dropdown
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) openMenuId.value = null;
    });
</script>
