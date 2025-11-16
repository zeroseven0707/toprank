<script setup>
import { Head } from '@inertiajs/vue3'
</script>

<template>
    <Head title="Help" />

    <div class="max-w-3xl mx-auto py-10 px-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Help Center</h1>

        <p class="text-gray-600 mb-6">
            Selamat datang di halaman bantuan. Di sini kamu bisa menemukan jawaban singkat atas fitur-fitur utama yang tersedia di website ini.
        </p>

        <!-- FAQ -->
        <div class="space-y-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Bagaimana cara menggunakan website ini?</h2>
                <p class="text-gray-600">
                    Kamu bisa mulai dari menu Beranda untuk melihat konten utama, lalu navigasi ke Blog untuk membaca artikel.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-gray-800">Bagaimana cara login?</h2>
                <p class="text-gray-600">
                    Klik tombol Login di pojok kanan atas, lalu masukkan email dan password yang sudah terdaftar.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-gray-800">Saya mengalami error, apa yang harus saya lakukan?</h2>
                <p class="text-gray-600">
                    Kamu bisa mencoba refresh halaman terlebih dahulu. Jika masih error, hubungi admin melalui kontak yang disediakan.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-gray-800">Bagaimana menghubungi admin?</h2>
                <p class="text-gray-600">
                    Kamu dapat menghubungi admin melalui email: <strong>support@meatmap.com</strong>.
                </p>
            </div>
        </div>
    </div>
</template>
