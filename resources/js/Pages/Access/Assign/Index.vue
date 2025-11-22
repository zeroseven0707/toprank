<template>
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Admin /</span> Assign Roles & Permissions
        </h4>

        <div v-for="user in users" :key="user.id" class="card mb-4">
            <div class="card-body">
                <h2 class="h6 mb-3">{{ user . name }} ({{ user . email }})</h2>

                <div class="mb-3">
                    <label class="form-label">Roles</label>
                    <select v-model="userRoles[user.id]" multiple class="form-select">
                        <option v-for="r in roles" :value="r.name" :key="r.id">{{ r . name }}</option>
                    </select>
                    <div class="mt-2 d-flex justify-content-end">
                        <button @click="saveRoles(user.id)" class="btn btn-success btn-sm">Save Roles</button>
                    </div>
                </div>

                <div>
                    <label class="form-label">Permissions</label>
                    <select v-model="userPermissions[user.id]" multiple class="form-select">
                        <option v-for="p in permissions" :value="p.name" :key="p.id">{{ p . name }}</option>
                    </select>
                    <div class="mt-2 d-flex justify-content-end">
                        <button @click="savePermissions(user.id)" class="btn btn-success btn-sm">Save Permissions</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import {
        ref
    } from 'vue'
    import {
        router
    } from '@inertiajs/vue3'

    const props = defineProps({
        users: Array,
        roles: Array,
        permissions: Array
    })

    const userRoles = ref({})
    const userPermissions = ref({})

    const saveRoles = (userId) => {
        router.post(route('assign.role'), {
            user_id: userId,
            roles: userRoles.value[userId] || []
        })
    }
    const savePermissions = (userId) => {
        router.post(route('assign.permission'), {
            user_id: userId,
            permissions: userPermissions.value[userId] || []
        })
    }
</script>
