<template>
    <Head title="Home" />
    <main class="max-w-7xl mx-auto px-6 mt-8">
        <div class="w-full rounded-xl p-4">
            <div class="mt-4 flex flex-wrap gap-3 bg-white p-3 rounded-lg border border-gray-200">
                <select
                v-model="selectedCity"
                @change="applyFilter"
                class="flex-1 min-w-[180px] border border-gray-300 rounded-lg px-4 py-2 text-gray-700 text-sm focus:ring-2 focus:ring-[#1a73e8] cursor-pointer"
                >
                <option v-for="city in cities" :key="city.id" :value="city.id">
                    {{ city.name }}
                </option>
                </select>

                <select
                v-model="selectedMonth"
                class="flex-1 min-w-[180px] border border-gray-300 rounded-lg px-4 py-2 text-gray-700 text-sm focus:ring-2 focus:ring-[#1a73e8] cursor-pointer"
                >
                <option v-for="month in months" :key="month.value" :value="month.value">
                    {{ month.label }}
                </option>
                </select>

                <select
                v-model="selectedCategory"
                class="flex-1 min-w-[180px] border border-gray-300 rounded-lg px-4 py-2 text-gray-700 text-sm focus:ring-2 focus:ring-[#1a73e8] cursor-pointer"
                >
                <option value="">Semua Kategori</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                    {{ cat.name }}
                </option>
                </select>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-for="(section, index) in sections" :key="index" class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition p-6 flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-2 text-gray-800 font-semibold text-sm">
                <span>{{ section.content_category?.name || "Telusuri topik" }}</span>
                </div>
                <div class="flex items-center gap-3 text-gray-600">
                    <div class="relative">
                    <select
                    v-model="selectedMonth"
                    @change="applyFilter"
                    class="custom-select min-w-[160px] border border-gray-300 rounded-lg text-sm px-3 pr-8 py-1.5 text-gray-700 cursor-pointer bg-white"
                    >
                    <option v-for="month in months" :key="month.value" :value="month.value">
                        {{ month.label }}
                    </option>
                    </select>
                    <i class="bi bi-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none"></i>
                    </div>
                    <button @click="downloadData(section)" class="hover:text-gray-800 transition text-lg" title="Download"><i class="bi bi-download"></i></button>
                    <button @click="openEmbed(section)" class="hover:text-gray-800 transition text-lg" title="View code"><i class="bi bi-code"></i></button>
                    <button @click="shareData(section, index)" class="hover:text-gray-800 transition text-lg" title="Share data"><i class="bi bi-share-fill"></i></button>
                </div>
            </div>

            <!-- List -->
            <ul class="divide-y divide-gray-100 flex-grow overflow-auto">
                <li v-for="(content, idx) in section.content_category?.contents" :key="content.id" class="flex justify-between items-center py-3 hover:bg-gray-50 rounded-lg transition relative">
                <div class="flex items-center gap-4">
                    <span class="text-gray-500 font-semibold w-5 text-center select-none">{{ idx + 1 }}</span>
                    <div>
                    <p class="text-gray-800 font-medium leading-tight">{{ content.name }}</p>
                    </div>
                </div>

                <!-- 3 Dots Menu -->
                <div class="relative">
                    <button @click="toggleMenu(index, idx)" class="text-gray-400 hover:text-gray-600"><i class="bi bi-three-dots-vertical"></i></button>

                    <transition name="fade">
                    <div v-if="activeMenu?.section === index && activeMenu?.item === idx" class="absolute right-0 mt-2 w-36 bg-white border border-gray-200 rounded-md shadow-md z-50">
                        <ul class="text-sm text-gray-700">
                        <li>
                            <button @click="goToMaps(content)" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                            <i class="bi bi-geo-alt text-gray-500"></i>Go to Maps
                            </button>
                        </li>
                        <li>
                            <button @click="copyLink(content)" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                            <i class="bi bi-link-45deg text-gray-500"></i>Copy Link
                            </button>
                        </li>
                        </ul>
                    </div>
                    </transition>
                </div>
                </li>
            </ul>
            </div>
        </div>

        <!-- Popup SHARE -->
        <transition name="fade">
            <div v-if="sharePopup.show" class="fixed inset-0 bg-black/40 flex justify-center items-center z-50" @click.self="sharePopup.show = false">
            <div class="bg-white rounded-xl shadow-lg p-6 w-64">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 text-center">Bagikan ke</h3>
                <div class="flex justify-center gap-4 text-2xl text-gray-600">
                <button @click="shareTo('wa', sharePopup.section)" class="hover:text-green-500"><i class="bi bi-whatsapp"></i></button>
                <button @click="shareTo('telegram', sharePopup.section)" class="hover:text-sky-500"><i class="bi bi-telegram"></i></button>
                <button @click="shareTo('twitter', sharePopup.section)" class="hover:text-blue-400"><i class="bi bi-twitter"></i></button>
                <button @click="shareTo('copy', sharePopup.section)" class="hover:text-gray-800"><i class="bi bi-link-45deg"></i></button>
                </div>
                <button @click="sharePopup.show = false" class="mt-6 w-full py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 font-medium">Tutup</button>
            </div>
            </div>
        </transition>

        <transition name="fade">
        <div
            v-if="embedPopup.show"
            class="fixed inset-0 bg-black/40 flex justify-center items-center z-50"
            @click.self="embedPopup.show = false"
        >
            <div
            class="bg-white rounded-lg shadow-lg flex flex-col md:flex-row overflow-hidden font-inter w-full max-w-[880px] max-h-[90vh]"
            >
                <!-- ░ Panel kiri: SEMATKAN ░ -->
                <div class="w-full md:w-[35%] bg-[#f8f9fa] border-b md:border-b-0 md:border-r border-gray-200 p-4 md:p-5 flex flex-col overflow-auto">
                    <h3 class="text-base font-semibold text-gray-600 mb-2">Sematkan</h3>
                    <p class="text-sm text-gray-400 mb-4 leading-snug">
                    Data ini dinamis dan akan terus diperbarui.
                    </p>

                    <div class="mt-auto">
                    <label class="text-sm font-medium text-gray-400 mb-2 block">
                    Salin kode ini ke halaman HTML Anda:
                    </label>
                    <div class="relative">
                    <textarea
                        readonly
                        rows="8"
                        class="w-full border border-gray-300 rounded-md p-2 text-xs font-mono text-gray-700 bg-white resize-none"
                        :value="generateEmbedCode(embedPopup.section)"
                    ></textarea>
                    <button
                        @click="copyEmbedCode(embedPopup.section)"
                        class="absolute top-1 right-1 bg-secondary text-white text-[11px] px-3 py-1 rounded"
                    >
                        Salin
                    </button>
                    </div>

                    <p class="text-xs text-gray-500 pt-4">
                    Dibuat oleh <span class="font-semibold text-gray-700">MTrends</span>
                    </p>
                    </div>

                </div>
                <!-- ░ Panel kanan: PRATINJAU ░ -->
                <div class="flex-1 flex flex-col bg-white overflow-auto">
                    <!-- Header -->
                    <div class="border-b border-gray-200 flex justify-between items-center px-5 py-2">
                    <div class="flex items-center gap-5">
                        <h3 class="text-base font-semibold text-gray-800">Pratinjau</h3>
                        <div class="flex items-center gap-3 text-sm">
                        <button
                            :class="[
                            embedPopup.device === 'desktop'
                                ? 'text-[#1a73e8] border-b-2 border-[#1a73e8]'
                                : 'text-gray-500',
                            'pb-[2px] font-medium',
                            ]"
                            @click="embedPopup.device = 'desktop'"
                        >
                            DESKTOP
                        </button>
                        <button
                            :class="[
                            embedPopup.device === 'mobile'
                                ? 'text-[#1a73e8] border-b-2 border-[#1a73e8]'
                                : 'text-gray-500',
                            'pb-[2px] font-medium',
                            ]"
                            @click="embedPopup.device = 'mobile'"
                        >
                            SELULER
                        </button>
                        </div>
                    </div>

                    <button
                        @click="embedPopup.show = false"
                        class="text-gray-400 hover:text-gray-700 text-base"
                    >
                        <i class="bi bi-x-lg"></i>
                    </button>
                    </div>

                    <!-- Preview area -->
                    <div class="flex-grow bg-[#f8f9fa] flex items-center justify-center py-6">
                    <div
                        class="bg-white border border-gray-300 rounded-md shadow-inner overflow-hidden"
                        :class="embedPopup.device === 'desktop' ? 'w-[640px] h-[360px]' : 'w-[360px] h-[640px]'"
                    >

                        <iframe
                        :src="getEmbedUrl(embedPopup.section)"
                        class="w-full h-full"
                        frameborder="0"
                        allowfullscreen
                        ></iframe>

                        <div
                        class="border-t border-gray-200 px-4 py-1.5 text-[11px] text-gray-500 text-left bg-gray-50"
                        >
                        Indonesia. 12 bulan terakhir. Penelusuran Web.
                        </div>
                    </div>
                    </div>


                    <!-- Footer -->
                    <div class="border-t border-gray-200 px-5 py-2 text-right">
                    <button
                        @click="embedPopup.show = false"
                        class="text-[#1a73e8] text-sm font-semibold hover:underline"
                    >
                        SELESAI
                    </button>
                    </div>
                </div>
            </div>
        </div>
        </transition>
    </main>
</template>

<script setup>
import * as XLSX from "xlsx";
import { Link, router, useRemember, Head } from "@inertiajs/vue3";
import { ref, onMounted, watch } from "vue";


const props = defineProps({
  sections: Array,
  categories: Array,
  cities: Array,
  months: Array,
  filters: Object,
});

// Default dari server
const selectedCategory = ref(props.filters?.category || "");
const selectedCity = ref(props.filters?.city || "");
const selectedMonth = ref(props.filters?.month || (new Date().getMonth() + 1));

watch([selectedCategory, selectedCity, selectedMonth], ([category, city, month]) => {
  router.post(
    route("home"),
    { category, city, month },
    { preserveScroll: true, preserveState: true, replace: true }
  );
});
function applyFilter() {
  router.post(
    route("home"),
    {
      category: selectedCategory.value,
      city: selectedCity.value,
      month: selectedMonth.value,
    },
    { preserveScroll: true, preserveState: true, replace: true }
  );
}


const sharePopup = ref({ show: false, section: null });
const embedPopup = useRemember({ show: false, section: null, device: "desktop" }, "embedPopup");
const activeMenu = ref(null);

function toggleMenu(sectionIndex, itemIndex) {
  if (activeMenu.value?.section === sectionIndex && activeMenu.value?.item === itemIndex) {
    activeMenu.value = null;
  } else {
    activeMenu.value = { section: sectionIndex, item: itemIndex };
  }
}
onMounted(() => {
    if (!selectedCity.value && props.cities.length > 0) {
        selectedCity.value = props.cities[0].id;
        applyFilter();
    }
  document.addEventListener("click", (e) => {
    if (!e.target.closest(".relative")) activeMenu.value = null;
  });
});

/* === SHARE === */
function shareData(section) {
  sharePopup.value = { show: true, section };
}
function shareTo(platform, section) {
  const title = encodeURIComponent(`Lihat tren ${section.content_category?.name || 'konten menarik'} di situs ini!`);
  const url = window.location.href;
  let shareUrl = "";
  switch (platform) {
    case "wa":
      shareUrl = `https://wa.me/?text=${title}%20${url}`;
      break;
    case "twitter":
      shareUrl = `https://twitter.com/intent/tweet?text=${title}&url=${url}`;
      break;
    case "telegram":
      shareUrl = `https://t.me/share/url?url=${url}&text=${title}`;
      break;
    case "copy":
      navigator.clipboard.writeText(`${title} ${url}`);
      alert("📋 Link disalin!");
      return;
  }
  window.open(shareUrl, "_blank");
  sharePopup.value = { show: false };
}

function openEmbed(section) {
  embedPopup.value.show = true;
  embedPopup.value.section = section;
}
function getEmbedUrl(section) {
  const base = `/embed/${section.content_category?.id}`;
  const params = new URLSearchParams();
  if (selectedCity.value) params.append("city", selectedCity.value);
  if (selectedMonth.value) params.append("month", selectedMonth.value);
  const qs = params.toString();
  return qs ? `${base}?${qs}` : base;
}
function generateEmbedCode(section) {
  const url = window.location.origin + getEmbedUrl(section);
  return `<iframe src="${url}" width="800" height="450" frameborder="0" style="border:0;" allowfullscreen></iframe>`;
}
function copyEmbedCode(section) {
  navigator.clipboard.writeText(generateEmbedCode(section));
  alert("✅ Kode embed disalin ke clipboard!");
}

function downloadData(section) {
  const contents = section.content_category?.contents || [];
  if (!contents.length) return alert("Tidak ada data untuk diunduh.");

  const data = contents.map((c, i) => ({
    No: i + 1,
    Nama: c.name,
    Deskripsi: c.description || "",
    Kota: c.city?.name || "-",
    Maps: c.maps_link || "-",
  }));

  const worksheet = XLSX.utils.json_to_sheet(data);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, "Konten");

  const fileName = `${section.content_category?.name || "data"}_${new Date().toISOString().slice(0, 10)}.xlsx`;
  XLSX.writeFile(workbook, fileName);
}

function goToMaps(content) {
  if (content.maps_link) window.open(content.maps_link, "_blank");
  else alert("Tautan maps tidak tersedia.");
}
function copyLink(content) {
  navigator.clipboard.writeText(content.maps_link || "");
  alert("🔗 Link disalin ke clipboard!");
}
</script>

<style scoped>
@import "https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css";
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

select.custom-select {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  background-image: none !important;
}
select.custom-select::-ms-expand { display: none; }
</style>
