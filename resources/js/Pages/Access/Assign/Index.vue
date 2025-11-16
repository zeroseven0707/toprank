<template>
    <div>
        <h1 class="text-2xl font-bold mb-4">User Role & Permission Assignment</h1>

        <div v-for="user in users" :key="user.id" class="border p-4 rounded mb-4">
            <h2 class="font-semibold mb-2">{{ user . name }} ({{ user . email }})</h2>

            <div class="mb-2">
                <label>Roles:</label>
                <select v-model="userRoles[user.id]" multiple class="border rounded p-1 w-full">
                    <option v-for="r in roles" :value="r.name" :key="r.id">{{ r . name }}</option>
                </select>
                <button @click="saveRoles(user.id)" class="bg-green-500 text-white px-3 py-1 mt-2 rounded">Save
                    Roles</button>
            </div>

            <div>
                <label>Permissions:</label>
                <select v-model="userPermissions[user.id]" multiple class="border rounded p-1 w-full">
                    <option v-for="p in permissions" :value="p.name" :key="p.id">{{ p . name }}
                    </option>
                </select>
                <button @click="savePermissions(user.id)" class="bg-green-500 text-white px-3 py-1 mt-2 rounded">Save
                    Permissions</button>
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
