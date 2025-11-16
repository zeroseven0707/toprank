<template>
  <div class="min-h-screen bg-[#f1f3f4] flex justify-center items-center font-inter p-4">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col w-full max-w-2xl relative">
      <!-- Header -->
      <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-2 text-gray-800 font-semibold text-sm">
          <span>{{ category.name || "Telusuri topik" }}</span>
        </div>
        <div class="flex items-center gap-3 text-gray-600">
          <!-- Dropdown Bulan -->
          <select
            v-model="selectedMonth"
            @change="applyFilter"
            class="border border-gray-300 rounded-lg text-sm px-3 py-1.5 text-gray-700 cursor-pointer"
          >
            <option value="">Bulan ini</option>
            <option v-for="month in months" :key="month.value" :value="month.value">
              {{ month.label }}
            </option>
          </select>

          <!-- Download -->
          <button
            @click="downloadData"
            class="hover:text-gray-800 transition text-lg"
            title="Download Data"
          >
            <i class="bi bi-download"></i>
          </button>

          <!-- Share -->
          <button
            @click="shareData"
            class="hover:text-gray-800 transition text-lg"
            title="Bagikan"
          >
            <i class="bi bi-share-fill"></i>
          </button>
        </div>
      </div>

      <!-- List -->
      <ul class="divide-y divide-gray-100 flex-grow overflow-auto">
        <li
          v-for="(content, idx) in category.contents"
          :key="content.id"
          class="flex justify-between items-center py-3 hover:bg-gray-50 rounded-lg transition relative"
        >
          <div class="flex items-center gap-4">
            <span class="text-gray-500 font-semibold w-5 text-center select-none">
              {{ idx + 1 }}
            </span>
            <div>
              <p class="text-gray-800 font-medium leading-tight">{{ content.name }}</p>
              <p class="text-sm text-gray-500">
                {{ idx === 0 ? "Pesat" : "+" + (Math.random() * 3 + 1).toFixed(3) + "%" }}
              </p>
            </div>
          </div>

          <!-- 3 Dots Menu -->
          <div class="relative">
            <button @click="toggleMenu(idx)" class="text-gray-400 hover:text-gray-600">
              <i class="bi bi-three-dots-vertical"></i>
            </button>

            <transition name="fade">
              <div
                v-if="activeMenu === idx"
                class="absolute right-0 mt-2 w-36 bg-white border border-gray-200 rounded-md shadow-md z-50"
              >
                <ul class="text-sm text-gray-700">
                  <li>
                    <button
                      @click="goToMaps(content)"
                      class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2"
                    >
                      <i class="bi bi-geo-alt text-gray-500"></i>Go to Maps
                    </button>
                  </li>
                  <li>
                    <button
                      @click="copyLink(content)"
                      class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2"
                    >
                      <i class="bi bi-link-45deg text-gray-500"></i>Copy Link
                    </button>
                  </li>
                </ul>
              </div>
            </transition>
          </div>
        </li>
      </ul>

      <div class="mt-3 text-xs text-gray-400 text-center">
        <p>Ranking bulan <span class="font-semibold text-gray-600">{{ currentMonthLabel }}</span></p>
        <p>Data oleh <span class="font-semibold text-gray-600">TopRank</span></p>
      </div>
    </div>

    <!-- 🟦 Popup SHARE (sama seperti Home) -->
    <transition name="fade">
      <div
        v-if="sharePopup.show"
        class="fixed inset-0 bg-black/40 flex justify-center items-center z-50"
        @click.self="sharePopup.show = false"
      >
        <div class="bg-white rounded-xl shadow-lg p-6 w-64">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 text-center">Bagikan ke</h3>
          <div class="flex justify-center gap-4 text-2xl text-gray-600">
            <button @click="shareTo('wa')" class="hover:text-green-500">
              <i class="bi bi-whatsapp"></i>
            </button>
            <button @click="shareTo('telegram')" class="hover:text-sky-500">
              <i class="bi bi-telegram"></i>
            </button>
            <button @click="shareTo('twitter')" class="hover:text-blue-400">
              <i class="bi bi-twitter"></i>
            </button>
            <button @click="shareTo('copy')" class="hover:text-gray-800">
              <i class="bi bi-link-45deg"></i>
            </button>
          </div>
          <button
            @click="sharePopup.show = false"
            class="mt-6 w-full py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 font-medium"
          >
            Tutup
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import * as XLSX from "xlsx";
import { ref, watch, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import dayjs from "dayjs";
import "dayjs/locale/id";

// === Props dari backend ===
const props = defineProps({
  category: Object,
  months: Array,
  filters: Object,
});

// === State ===
const selectedMonth = ref(props.filters?.month || "");
const activeMenu = ref(null);
const currentMonthLabel = ref("");
const sharePopup = ref({ show: false });

// === Watch bulan ===
watch(selectedMonth, (month) => {
  if (month) {
    currentMonthLabel.value =
      props.months.find((m) => m.value === Number(month))?.label || "Bulan ini";
  } else {
    currentMonthLabel.value = dayjs().locale("id").format("MMMM");
  }
});

// === Filter reload data ===
function applyFilter() {
  router.get(
    route("embed.view", { id: props.category.id }),
    { month: selectedMonth.value },
    { preserveScroll: true, preserveState: true, replace: true }
  );
}

// === Download Excel ===
function downloadData() {
  const contents = props.category.contents || [];
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

  const fileName = `${props.category.name || "data"}_${new Date()
    .toISOString()
    .slice(0, 10)}.xlsx`;
  XLSX.writeFile(workbook, fileName);
}

// === Menu tiga titik ===
function toggleMenu(idx) {
  activeMenu.value = activeMenu.value === idx ? null : idx;
}

function goToMaps(content) {
  if (content.maps_link) window.open(content.maps_link, "_blank");
  else alert("Tautan maps tidak tersedia.");
}

function copyLink(content) {
  navigator.clipboard.writeText(content.maps_link || "");
  alert("🔗 Link disalin ke clipboard!");
}

// === SHARE (identik dengan Home) ===
function shareData() {
  sharePopup.value = { show: true };
}

function shareTo(platform) {
  const title = encodeURIComponent(`Lihat tren ${props.category.name || 'konten menarik'} di situs ini!`);
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
      sharePopup.value = { show: false };
      return;
  }

  window.open(shareUrl, "_blank");
  sharePopup.value = { show: false };
}

// === Inisialisasi bulan ===
onMounted(() => {
  currentMonthLabel.value =
    props.months.find((m) => m.value === Number(selectedMonth.value))?.label ||
    dayjs().locale("id").format("MMMM");
});
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
</style>
