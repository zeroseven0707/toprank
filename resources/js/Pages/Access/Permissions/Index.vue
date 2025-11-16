<template>
    <div>
        <h1 class="text-2xl font-bold mb-4">Permission Management</h1>
        <form @submit.prevent="createPermission" class="mb-5 flex gap-2">
            <input v-model="form.name" placeholder="Permission name" class="border px-2 py-1 rounded" />
            <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded">Add</button>
        </form>

        <ul>
            <li v-for="p in permissions" :key="p.id" class="flex justify-between border-b py-2">
                <span>{{ p . name }}</span>
                <button @click="deletePermission(p.id)" class="text-red-600">🗑️</button>
            </li>
        </ul>
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
        permissions: Array
    })
    const form = ref({
        name: ''
    })

    const createPermission = () => {
        router.post(route('permissions.store'), form.value)
        form.value.name = ''
    }

    const deletePermission = (id) => {
        if (confirm('Are you sure?')) router.delete(route('permissions.destroy', id))
    }
</script>
