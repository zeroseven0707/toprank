<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'

import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'

// Props
defineProps({
    title: String,
})

// State
const showingNavigationDropdown = ref(false)
const isDark = ref(false)
function setHeaderOffsetVar() {
    const h = document.querySelector('header')
    const val = h ? h.offsetHeight + 'px' : '0px'
    document.documentElement.style.setProperty('--sticky-header-offset', val)
}
onMounted(() => {
    setHeaderOffsetVar()
    window.addEventListener('resize', setHeaderOffsetVar)
})
onUnmounted(() => {
    window.removeEventListener('resize', setHeaderOffsetVar)
})

// Logout
const logout = () => router.post(route('logout'))

// Share
const handleShare = async () => {
    if (navigator.share) {
        try {
            await navigator.share({
                title: document.title,
                url: window.location.href,
            })
        } catch {}
    } else {
        alert('Browser tidak mendukung fitur share.')
    }
}

// Help
const handleHelp = () => router.visit('/help')

// Toggle Dark Mode
const toggleDarkMode = () => {
    isDark.value = !isDark.value
    document.documentElement.classList.toggle('dark', isDark.value)
}
</script>


<template>
    <Head :title="title" />

    <div class="min-h-screen bg-[#f1f3f4] dark:bg-[#1e1e1e] font-inter transition-colors duration-300">

        <!-- HEADER -->
        <header class="bg-white dark:bg-[#2a2a2a] dark:border-gray-700 border-b shadow-sm fixed top-0 left-0 right-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex justify-between items-center">

                <!-- LOGO / TITLE -->
                <h1 class="flex items-center gap-2 text-xl md:text-2xl font-semibold text-[#202124] dark:text-gray-200">
                    <img src="/assets/img/icons/mappy-trends.png" alt="Trends Logo" class="w-10 h-10" />
                    <span>MTrends</span>
                </h1>

                <!-- DESKTOP MENU -->
                <nav class="hidden md:flex gap-8 text-sm text-gray-700 dark:text-gray-200 font-medium">
                    <Link href="/" class="hover:text-[#1a73e8]" active-class="text-[#1a73e8] font-semibold">
                        Beranda
                    </Link>
                    <Link href="/blogs" class="hover:text-[#1a73e8]" active-class="text-[#1a73e8] font-semibold">
                        Blog
                    </Link>
                    <Link href="/privacy-policy" class="hover:text-[#1a73e8]" active-class="text-[#1a73e8] font-semibold">
                        Privacy Policy
                    </Link>
                </nav>

                <!-- ACTION BUTTONS (DESKTOP) -->
                <div class="hidden md:flex items-center gap-4 text-gray-600 dark:text-gray-300">
                    <button @click="handleShare" class="hover:text-[#1a73e8] text-lg">
                        <i class="bi bi-share-fill"></i>
                    </button>

                    <button @click="handleHelp" class="hover:text-[#1a73e8] text-lg">
                        <i class="bi bi-question-circle"></i>
                    </button>

                    <button @click="toggleDarkMode" class="hover:text-[#1a73e8] text-lg">
                        <i v-if="!isDark" class="bi bi-moon-stars"></i>
                        <i v-else class="bi bi-brightness-high"></i>
                    </button>

                    <!-- USER -->
                    <div v-if="$page.props.auth.user">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="rounded-full w-8 h-8 bg-indigo-500 text-white font-semibold flex items-center justify-center">
                                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.show')">
                                    Profile
                                </DropdownLink>

                                <DropdownLink v-if="$page.props.jetstream?.hasApiFeatures" :href="route('api-tokens.index')">
                                    API Tokens
                                </DropdownLink>

                                <div class="border-t border-gray-200 my-2"></div>

                                <form @submit.prevent="logout">
                                    <DropdownLink as="button">
                                        Log Out
                                    </DropdownLink>
                                </form>
                            </template>
                        </Dropdown>
                    </div>

                    <div v-else>
                        <Link href="/login">
                            <button class="inline-flex items-center border border-gray-300 rounded-md px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Login</button>
                        </Link>
                    </div>
                </div>

                <!-- MOBILE HAMBURGER -->
                <button
                    class="md:hidden text-gray-700 dark:text-gray-200 text-2xl rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    @click="showingNavigationDropdown = !showingNavigationDropdown"
                >
                    <i :class="[showingNavigationDropdown ? 'bi bi-x-lg' : 'bi bi-list']"></i>
                </button>

            </div>

            <!-- MOBILE MENU -->
            <div
                class="md:hidden bg-white dark:bg-[#2a2a2a] border-t dark:border-gray-700 overflow-hidden transition-all duration-300"
                :class="showingNavigationDropdown ? 'max-h-96 py-3' : 'max-h-0 py-0'"
            >
                <div class="px-4 sm:px-6 flex flex-col gap-4 text-gray-700 dark:text-gray-200">

                    <Link href="/" class="hover:text-[#1a73e8]" @click="showingNavigationDropdown = false">
                        Beranda
                    </Link>

                    <Link href="/blogs" class="hover:text-[#1a73e8]" @click="showingNavigationDropdown = false">
                        Blog
                    </Link>

                    <Link href="/privacy-policy" class="hover:text-[#1a73e8]" @click="showingNavigationDropdown = false">
                        Privacy Policy
                    </Link>

                    <hr class="border-gray-300 dark:border-gray-600">

                    <div class="flex gap-4 text-lg">

                        <button @click="handleShare" class="hover:text-[#1a73e8]">
                            <i class="bi bi-share-fill"></i>
                        </button>

                        <button @click="handleHelp" class="hover:text-[#1a73e8]">
                            <i class="bi bi-question-circle"></i>
                        </button>

                        <button @click="toggleDarkMode" class="hover:text-[#1a73e8]">
                            <i v-if="!isDark" class="bi bi-moon-stars"></i>
                            <i v-else class="bi bi-brightness-high"></i>
                        </button>
                    </div>

                    <div v-if="$page.props.auth.user">
                        <button
                            @click="logout"
                            class="text-left text-red-500 font-medium">
                            Log Out
                        </button>
                    </div>

                    <div v-else>
                        <Link href="/login" class="inline-flex items-center justify-center w-full border border-gray-300 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Login
                        </Link>
                    </div>

                </div>
            </div>

        </header>

        <!-- PAGE CONTENT -->
        <main style="padding-top: var(--sticky-header-offset);">
            <slot />
        </main>

    </div>
</template>
