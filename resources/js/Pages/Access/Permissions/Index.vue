<template>
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Admin /</span> Permission Management
        </h4>

        <form @submit.prevent="createPermission" class="d-flex flex-wrap gap-2 mb-4">
            <input v-model="form.name" placeholder="Permission name" class="form-control flex-grow-1" />
            <button type="submit" class="btn btn-primary">Add</button>
        </form>

        <div class="card">
            <div class="card-body">
                <ul class="list-group">
                    <li v-for="p in permissions" :key="p.id" class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-break">{{ p . name }}</span>
                        <button @click="deletePermission(p.id)" class="btn btn-sm btn-outline-danger">Delete</button>
                    </li>
                </ul>
                <div v-if="!permissions.length" class="text-center text-muted py-3">No permissions found.</div>
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
