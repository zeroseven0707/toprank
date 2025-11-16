<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="card shadow-sm rounded-4">
      <!-- Header -->
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <!-- Filter Section -->
        <div class="d-flex align-items-center flex-wrap gap-2 p-2 px-3 rounded-4">
        <!-- Filter Periode -->
        <div class="position-relative">
            <i class="bi bi-calendar3 position-absolute text-muted" style="top: 9px; left: 12px;"></i>
            <select
            v-model="selectedPeriod"
            class="form-select rounded-pill ps-5 border-0 shadow-sm text-secondary fw-semibold small"
            style="min-width: 160px;"
            @change="applyFilters"
            >
            <option v-for="m in monthsOfYear" :key="m.value" :value="m.value">
                {{ m.label }}
            </option>
            </select>
        </div>

        <!-- Filter Kategori -->
        <div class="position-relative">
            <i class="bi bi-tags position-absolute text-muted" style="top: 9px; left: 12px;"></i>
            <select
            v-model="selectedCategory"
            class="form-select rounded-pill ps-5 border-0 shadow-sm text-secondary fw-semibold small"
            style="min-width: 160px;"
            @change="applyFilters"
            >
            <option value="">Semua Kategori</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ cat.name }}
            </option>
            </select>
        </div>

        <!-- Filter Kota -->
        <div class="position-relative">
            <i class="bi bi-geo-alt position-absolute text-muted" style="top: 9px; left: 12px;"></i>
            <select
            v-model="selectedCity"
            class="form-select rounded-pill ps-5 border-0 shadow-sm text-secondary fw-semibold small"
            style="min-width: 160px;"
            @change="applyFilters"
            >
            <option value="">Semua Kota</option>
            <option v-for="city in cities" :key="city.id" :value="city.id">
                {{ city.name }}
            </option>
            </select>
        </div>

        <!-- Tombol Reset -->
        <button
            class="btn btn-light border rounded-pill shadow-sm d-flex align-items-center gap-1 text-secondary fw-semibold"
            @click="resetFilters"
        >
            <i class="bi bi-arrow-clockwise"></i>
            Reset
        </button>
        </div>

        <!-- Publish Semua -->
        <button
          class="btn btn-primary rounded-pill d-flex align-items-center"
          @click="publishAll"
          :disabled="!isCurrentMonth"
          :title="!isCurrentMonth ? 'Hanya bisa publish untuk bulan ini' : 'Publish semua konten bulan ini'"
        >
          <i class="bi bi-upload me-2"></i> Publish Konten Bulan Ini
        </button>
      </div>

      <!-- Table -->
      <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Nama</th>
              <th>Kategori</th>
              <th>Kota</th>
              <th>Maps</th>
              <th>Deskripsi</th>
              <th>Status</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(c, i) in contents.data" :key="c.id">
              <td>{{ i + 1 + (contents.current_page - 1) * contents.per_page }}</td>

              <!-- Nama -->
              <td>{{ c.name?.trim() || 'Belum diisi' }}</td>

              <!-- Kategori -->
              <td>{{ c.category?.name ?? '-' }}</td>

              <!-- Kota -->
              <td>{{ c.city?.name ?? '-' }}</td>

              <!-- Maps -->
              <td>
                <template v-if="c.maps_link?.trim()">
                  <a :href="c.maps_link" target="_blank" class="text-primary">Lihat Maps</a>
                </template>
                <template v-else>
                  <span class="text-muted fst-italic">Belum diisi</span>
                </template>
              </td>

              <!-- Deskripsi -->
              <td>
                <template v-if="c.description?.trim()">
                  {{ c.description }}
                </template>
                <template v-else>
                  <span class="text-muted fst-italic">Belum diisi</span>
                </template>
              </td>

              <!-- Status -->
              <td>
                <span
                  class="badge px-3 py-2 rounded-pill text-uppercase"
                  :class="c.published ? 'bg-success text-white' : 'bg-secondary text-white'"
                >
                  {{ c.published ? 'Published' : 'Draft' }}
                </span>
              </td>

              <!-- Aksi -->
              <td class="text-center">
                <template v-if="c.url?.trim()">
                  <a
                    :href="c.url"
                    target="_blank"
                    class="btn btn-sm btn-outline-primary rounded-pill me-2"
                  >
                    Lihat
                  </a>
                </template>
                <template v-else>
                  <span class="text-muted fst-italic me-2">Belum ada URL</span>
                </template>

                <Link
                  :href="route('content.create', c.id)"
                  class="btn btn-sm btn-outline-warning rounded-pill"
                >
                  Edit
                </Link>
              </td>
            </tr>

            <!-- Jika kosong -->
            <tr v-if="!contents.data.length">
              <td colspan="8" class="text-center py-4 text-muted fst-italic">
                Belum ada konten untuk filter ini.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="card-footer bg-white border-0 mt-3 d-flex justify-content-center">
        <nav v-if="contents.links.length > 3">
          <ul class="pagination mb-0">
            <li
              v-for="(link, index) in contents.links"
              :key="index"
              class="page-item"
              :class="{ active: link.active, disabled: !link.url }"
            >
              <Link
                v-if="link.url"
                :href="link.url"
                class="page-link"
                v-html="link.label"
              />
              <span v-else class="page-link" v-html="link.label"></span>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from "@inertiajs/vue3"
import { ref, computed } from "vue"

const props = defineProps({
  contents: Object,
  period: String,
  categories: Array,
  cities: Array,
  filters: Object,
})

// === State filters ===
const selectedPeriod = ref(props.filters?.period || props.period)
const selectedCategory = ref(props.filters?.category || "")
const selectedCity = ref(props.filters?.city || "")

// === Bulan ===
const monthsOfYear = computed(() => {
  const now = new Date()
  const year = now.getFullYear()
  const currentMonth = now.getMonth() + 1
  const months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
  ]
  return months.slice(0, currentMonth).map((label, i) => ({
    label: `${label} ${year}`,
    value: `${year}-${String(i + 1).padStart(2, '0')}`,
  }))
})

// === Apakah bulan ini ===
const isCurrentMonth = computed(() => {
  const now = new Date()
  const current = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`
  return selectedPeriod.value === current
})

// === Apply filters ke backend ===
const applyFilters = () => {
  router.get(
    route("content.index"),
    {
      period: selectedPeriod.value,
      category: selectedCategory.value,
      city: selectedCity.value,
    },
    { preserveScroll: true, preserveState: true, replace: true }
  )
}

// === Reset semua filter ===
const resetFilters = () => {
  const now = new Date()
  const current = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`
  selectedPeriod.value = current
  selectedCategory.value = ""
  selectedCity.value = ""
  applyFilters()
}

// === Publish semua konten bulan ini ===
const publishAll = () => {
  if (!isCurrentMonth.value) return
  if (confirm("Yakin ingin mem-publish semua konten bulan ini?")) {
    router.post(route("content.publish"), {}, {
      onSuccess: () => alert("✅ Semua konten bulan ini berhasil dipublish!"),
      onError: () => alert("❌ Gagal mem-publish konten."),
    })
  }
}
</script>

<style scoped>
.text-muted {
  color: #6c757d !important;
}
.fst-italic {
  font-style: italic;
}
.table th,
.table td {
  vertical-align: middle;
}
.card-header select {
  min-width: 140px;
}
.badge {
  font-size: 0.75rem;
  letter-spacing: 0.3px;
}
</style>
