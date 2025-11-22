<template>
    <Head title="Privacy Policy" />
    <div class="bg-gray-50 min-h-screen">
        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-6 mt-6">
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-xl font-bold mb-4">Privacy Policy</h2>
                <p class="text-gray-700" v-html="content"></p>
            </div>
        </main>
    </div>
</template>
<script setup>
    import {
        Link, Head
    } from '@inertiajs/vue3'
    import {
        defineProps
    } from 'vue'

    const props = defineProps({
        content: String
    })
</script>
