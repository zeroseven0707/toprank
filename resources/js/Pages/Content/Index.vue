<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="card shadow-sm rounded-4">
      <!-- Header -->
      <div class="card-header">
        <div class="row g-3 align-items-center">
          <div class="col-12 col-lg">
            <div class="d-flex flex-wrap align-items-center gap-2">
              <!-- Periode -->
              <div class="position-relative">
                <i class="bi bi-calendar3 position-absolute text-muted" style="top:9px;left:12px;"></i>
                <select v-model="selectedPeriod" class="form-select rounded-pill ps-5 border-0 shadow-sm text-secondary fw-semibold small" style="min-width:160px;" @change="applyFilters">
                  <option v-for="m in monthsOfYear" :key="m.value" :value="m.value">{{ m.label }}</option>
                </select>
              </div>
              <!-- Kategori -->
              <div class="position-relative">
                <i class="bi bi-tags position-absolute text-muted" style="top:9px;left:12px;"></i>
                <select v-model="selectedCategory" class="form-select rounded-pill ps-5 border-0 shadow-sm text-secondary fw-semibold small" style="min-width:160px;" @change="applyFilters">
                  <option value="">Semua Kategori</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
              </div>
              <!-- Kota -->
              <div class="position-relative">
                <i class="bi bi-geo-alt position-absolute text-muted" style="top:9px;left:12px;"></i>
                <select v-model="selectedCity" class="form-select rounded-pill ps-5 border-0 shadow-sm text-secondary fw-semibold small" style="min-width:160px;" @change="applyFilters">
                  <option value="">Semua Kota</option>
                  <option v-for="city in cities" :key="city.id" :value="city.id">{{ city.name }}</option>
                </select>
              </div>
              <!-- Reset -->
              <button class="btn btn-light border rounded-pill shadow-sm d-flex align-items-center gap-1 text-secondary fw-semibold" @click="resetFilters">
                <i class="bi bi-arrow-clockwise"></i> Reset
              </button>
            </div>
          </div>
          <div class="col-12 col-lg-auto">
            <div class="d-flex flex-wrap align-items-center gap-2 justify-content-lg-end">
              <button class="btn btn-outline-primary rounded-pill d-flex align-items-center" @click="openStatusModal" :disabled="!selectedIds.length" :title="!selectedIds.length ? 'Pilih konten terlebih dulu' : 'Ubah status konten terpilih'">
                <i class="bi bi-toggle2-on me-2"></i> Ubah Status
              </button>
              <!-- <button class="btn btn-primary rounded-pill d-flex align-items-center" @click="publishAll" :disabled="!isCurrentMonth" :title="!isCurrentMonth ? 'Hanya bisa publish untuk bulan ini' : 'Publish semua konten bulan ini'">
                <i class="bi bi-upload me-2"></i> Publish Konten Bulan Ini
              </button> -->
            </div>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:36px">
                <input type="checkbox" :checked="isAllVisibleSelected" @change="toggleSelectAllVisible($event.target.checked)">
              </th>
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
              <td style="width:36px">
                <input type="checkbox" :checked="isSelected(c.id)" @change="toggleSelect(c.id, $event.target.checked)">
              </td>
              <td>{{ i + 1 + (contents.current_page - 1) * contents.per_page }}</td>

              <!-- Nama -->
              <td>{{ c.name?.trim() || '-' }}</td>

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
                  <span class="text-muted fst-italic">-</span>
                </template>
              </td>

              <!-- Deskripsi -->
              <td>
                <template v-if="c.description?.trim()">
                  {{ c.description }}
                </template>
                <template v-else>
                  <span class="text-muted fst-italic">-</span>
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
              <td class="text-center position-relative">
                <div class="dropdown">
                  <button class="btn btn-sm btn-light" @click.stop="toggleMenu(c.id)">
                    <i class="ti ti-dots-vertical"></i>
                  </button>
                  <div v-if="openMenuId === c.id" class="dropdown-menu show" style="position:absolute; right:0; z-index:10;">
                    <a v-if="c.url?.trim()" :href="c.url" target="_blank" class="dropdown-item">
                      <i class="ti ti-link me-1"></i> Lihat
                    </a>
                    <span v-else class="dropdown-item text-muted">Belum ada URL</span>
                    <Link :href="route('content.create', c.id)" class="dropdown-item">
                      <i class="ti ti-edit me-1"></i> Edit
                    </Link>
                  </div>
                </div>
              </td>
            </tr>

            <!-- Jika kosong -->
            <tr v-if="!contents.data.length">
              <td colspan="9" class="text-center py-4 text-muted fst-italic">
                Belum ada konten untuk filter ini.
              </td>
            </tr>
          </tbody>
          </table>
        </div>
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

  <transition name="fade">
    <div v-if="showStatusModal" class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center" style="z-index:1050;">
      <div class="card shadow-sm" style="max-width:420px; width:100%;">
        <div class="card-body">
          <h6 class="fw-bold mb-2">Ubah status konten terpilih</h6>
          <p class="text-muted mb-3">Pilih tindakan untuk konten yang dipilih.</p>
          <div class="d-flex justify-content-end gap-2">
            <button class="btn btn-secondary" @click="applyStatus('draft')"><i class="bi bi-file-earmark me-1"></i> Draft</button>
            <button class="btn btn-success" @click="applyStatus('publish')"><i class="bi bi-upload me-1"></i> Publish</button>
            <button class="btn btn-light" @click="closeStatusModal">Batal</button>
          </div>
        </div>
      </div>
    </div>
  </transition>
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
const defaultPeriod = (() => { const now = new Date(); return `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}` })();
const selectedPeriod = ref(props.filters?.period || props.period || defaultPeriod)
const selectedCategory = ref(props.filters?.category || "")
const selectedCity = ref(props.filters?.city || "")
const selectedIds = ref([])
const isSelected = (id) => selectedIds.value.includes(id)
const visibleIds = computed(() => props.contents.data.map(c => c.id))
const isAllVisibleSelected = computed(() => visibleIds.value.length && visibleIds.value.every(id => selectedIds.value.includes(id)))
const toggleSelect = (id, checked) => { if (checked) { if (!selectedIds.value.includes(id)) selectedIds.value.push(id) } else { selectedIds.value = selectedIds.value.filter(x => x !== id) } }
const toggleSelectAllVisible = (checked) => { if (checked) { const add = visibleIds.value.filter(id => !selectedIds.value.includes(id)); selectedIds.value = selectedIds.value.concat(add) } else { selectedIds.value = selectedIds.value.filter(id => !visibleIds.value.includes(id)) } }

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
const showStatusModal = ref(false)
const openStatusModal = () => { if (!selectedIds.value.length) return; showStatusModal.value = true }
const closeStatusModal = () => { showStatusModal.value = false }
const applyStatus = (action) => {
  const published = action === 'publish'
  router.post(route("content.publish.selected"), { ids: selectedIds.value, published }, {
    onSuccess: () => { selectedIds.value = []; showStatusModal.value = false; alert(published ? "✅ Konten terpilih berhasil dipublish!" : "✅ Konten terpilih diubah ke draft!") },
    onError: () => alert("❌ Gagal mengubah status konten."),
  })
}

const publishAll = () => {
  if (!isCurrentMonth.value) return
  if (confirm("Yakin ingin mem-publish semua konten bulan ini?")) {
    router.post(route("content.publish"), {}, {
      onSuccess: () => alert("✅ Semua konten bulan ini berhasil dipublish!"),
      onError: () => alert("❌ Gagal mem-publish konten."),
    })
  }
}

const openMenuId = ref(null)
const toggleMenu = (id) => { openMenuId.value = openMenuId.value === id ? null : id }
document.addEventListener('click', (e) => { if (!e.target.closest('.dropdown')) openMenuId.value = null })
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
