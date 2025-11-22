# TopRank

Aplikasi untuk eksplorasi dan publikasi ranking konten per kota dan bulan, dengan panel admin modern berbasis Vuexy dan halaman publik minimalis.

## Fitur Utama
- Home publik dengan filter `kota` (default kota berstatus current) dan `bulan` (default bulan ini)
- Embed pratinjau yang bisa disematkan di situs lain dengan opsi perangkat desktop/seluler
- Manajemen konten: publish per-bulan, publish terpilih (modal pilih Publish/Draft), drag & drop sections
- Blog publik + detail artikel
- Settings: Cities (dengan `is_current` sebagai default), Content Categories, Blog Categories, URL Crawls (run per-item / run-all), Data Crawling
- Access: Users, Roles, Permissions dengan dropdown tindakan dan tampilan responsif konsisten
- Dashboard dengan grafik (ApexCharts)

## Teknologi
- Backend: Laravel + Inertia
- Frontend: Vue 3, Tailwind (guest), Vuexy Bootstrap (admin), Bootstrap Icons
- Utilitas: Ziggy (route), dayjs (tanggal), ApexCharts (grafik)

## Struktur Penting
- Layout admin: `resources/views/admin.blade.php` + `resources/js/Layouts/AdminLayout.vue`
- Layout guest: `resources/views/app.blade.php` + `resources/js/Layouts/GuestLayout.vue`
- Halaman publik: `resources/js/Pages/Welcome.vue`, `Blog.vue`, `BlogDetail.vue`, `PrivacyPolicy.vue`, `Help.vue`
- Admin Settings: `resources/js/Pages/Settings/*`
- Akses: `resources/js/Pages/Access/*`

## Instalasi
1. Salin `.env` dan set koneksi database
2. Composer dan NPM
   - `composer install`
   - `npm install`
3. Migrasi
   - `php artisan migrate`
4. Jalankan
   - `php artisan serve`
   - `npm run dev`

## Build Produksi
- `npm run build`

## Konvensi & Catatan
- Judul halaman dinamis via Inertia `<Head>`
- Dropdown tindakan memakai tombol titik-3, dengan penutupan saat klik di luar
- Sidebar admin dapat di-scroll tipis vertikal
- Penentuan kota default: hanya satu `City.is_current = true` (menu “Jadikan Default” di daftar Cities)
- Publish konten dapat per-bulan atau per-seleksi (modal Publish/Draft)

## Lisensi
Proyek ini menggunakan lisensi yang sesuai dengan komponen open-source terkait. Lihat header lisensi tiap dependency untuk detail.
