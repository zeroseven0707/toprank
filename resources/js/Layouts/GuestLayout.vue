<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'

import ApplicationMark from '@/Components/ApplicationMark.vue'
import Banner from '@/Components/Banner.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'

// Props
defineProps({
    title: String,
})

// State
const showingNavigationDropdown = ref(false)
const isDark = ref(false)

// ----------------------------------------------------
// FUNCTIONS
// ----------------------------------------------------

// Logout
const logout = () => {
    router.post(route('logout'))
}

// Share
const handleShare = async () => {
    if (navigator.share) {
        try {
            await navigator.share({
                title: document.title,
                url: window.location.href,
            })
        } catch (error) {
            console.log('Share cancelled')
        }
    } else {
        alert('Browser kamu tidak mendukung fitur share.')
    }
}

// Go to Help Page
const handleHelp = () => {
    router.visit('/help')
}

// Toggle Dark Mode
const toggleDarkMode = () => {
    isDark.value = !isDark.value

    if (isDark.value) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }
}
</script>

<template>
    <Head :title="title" />

    <div class="min-h-screen bg-[#f1f3f4] dark:bg-[#1e1e1e] font-inter transition-colors duration-300">

        <!-- Header -->
        <header class="bg-white dark:bg-[#2a2a2a] dark:border-gray-700 border-b shadow-sm transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-6 py-3 flex justify-between items-center">

                <!-- Logo + Title -->
                <h1 class="text-2xl font-semibold text-[#202124] tracking-tight flex items-center gap-1">
                    Top Ranks
                    <!-- <img src="https://upload.wikimedia.org/wikipedia/commons/2/2f/Google_2015_logo.svg"
                         alt="Google"
                         class="h-6" /> -->
                    <!-- <span>Trends</span> -->
                </h1>

                <!-- Navigation -->
                <nav class="flex gap-8 text-sm text-gray-700 dark:text-gray-200 font-medium">
                    <Link href="/" class="hover:text-[#1a73e8] transition"
                          active-class="text-[#1a73e8] border-b-2 border-[#1a73e8] pb-1">
                        Beranda
                    </Link>
                    <Link href="/blogs" class="hover:text-[#1a73e8] transition"
                          active-class="text-[#1a73e8] border-b-2 border-[#1a73e8] pb-1">
                        Blog
                    </Link>
                    <Link href="/privacy-policy" class="hover:text-[#1a73e8] transition"
                          active-class="text-[#1a73e8] border-b-2 border-[#1a73e8] pb-1">
                        Privacy Policy
                    </Link>
                </nav>

                <!-- Right Buttons -->
                <div class="flex items-center gap-4">

                    <!-- Share -->
                    <button @click="handleShare"
                            aria-label="Share" title="Share"
                            class="text-gray-600 hover:text-[#1a73e8] transition text-lg">
                        <i class="bi bi-share-fill"></i>
                    </button>

                    <!-- Help -->
                    <button @click="handleHelp"
                            aria-label="Help" title="Help"
                            class="text-gray-600 hover:text-[#1a73e8] transition text-lg">
                        <i class="bi bi-question-circle"></i>
                    </button>

                    <!-- Dark Mode -->
                    <button @click="toggleDarkMode"
                            aria-label="Dark Mode" title="Toggle Dark Mode"
                            class="text-gray-600 hover:text-[#1a73e8] transition text-lg">
                        <i v-if="!isDark" class="bi bi-moon-stars"></i>
                        <i v-else class="bi bi-brightness-high"></i>
                    </button>

                    <!-- User Dropdown -->
                    <div v-if="$page.props.auth.user">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="rounded-full w-8 h-8 bg-[#6c63ff] text-white font-semibold flex justify-center items-center cursor-pointer select-none">
                                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                </button>
                            </template>

                            <template #content>
                                <div class="px-4 py-2 text-xs text-gray-400">
                                    Manage Account
                                </div>

                                <DropdownLink :href="route('profile.show')">
                                    Profile
                                </DropdownLink>

                                <DropdownLink
                                    v-if="$page.props.jetstream?.hasApiFeatures"
                                    :href="route('api-tokens.index')"
                                >
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

                    <!-- If guest -->
                    <div v-else>
                        <Link href="/login" class="text-sm text-gray-700 underline">
                            <button class="btn btn-outline-dark text-primary">Login</button>
                        </Link>
                    </div>

                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>
