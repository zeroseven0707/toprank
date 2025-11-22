<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header + Filter -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="d-none d-md-block fw-bold text-2xl mb-0">Crawl Data</h4>

      <!-- ✅ Filter Bulan -->
      <div class="d-flex align-items-center gap-2">
        <select v-model="selectedPeriod" class="form-select w-auto" @change="filterByPeriod">
          <option v-for="m in monthsOfYear" :key="m.value" :value="m.value">
            {{ m.label }}
          </option>
        </select>

        <button class="btn btn-outline-secondary rounded-pill" @click="resetToCurrentMonth">
          Reset
        </button>
      </div>
    </div>

    <!-- Table Section -->
    <div class="card shadow-sm rounded-4">
      <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Nama Crawl</th>
                  <th>Jumlah Data</th>
                  <th>Scraped At (Terakhir)</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="(group, index) in groupedCrawls"
                  :key="group.id"
                  @click="toggleGroup(group.id)"
                  style="cursor: pointer;"
                >
                  <td>{{ index + 1 + (crawls.current_page - 1) * crawls.per_page }}</td>
                  <td>{{ group.name ?? group.url ?? '-' }}</td>
                  <td>{{ group.items.length }}</td>
                  <td>{{ formatDate(group.last_scraped_at) }}</td>
                  <td class="text-center">
                    <button
                      class="btn btn-sm btn-outline-primary rounded-pill px-3"
                      @click.stop="toggleGroup(group.id)"
                    >
                      {{ expandedGroups.includes(group.id) ? 'Tutup' : 'Lihat Detail' }}
                    </button>
                  </td>
                </tr>

                <!-- Detail -->
                <tr
                  v-for="group in groupedCrawls"
                  v-show="expandedGroups.includes(group.id)"
                  :key="'details-' + group.id"
                >
                  <td colspan="6" class="bg-light">
                    <div class="p-3 border rounded">
                      <h6 class="fw-bold mb-3 text-primary">
                        Detail Data Crawl ({{ group.items.length }} items)
                      </h6>
                      <div class="table-responsive">
                        <table class="table table-sm table-striped">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Owner</th>
                              <th>Username</th>
                              <th>Caption</th>
                              <th>Likes</th>
                              <th>Komentar</th>
                              <th>Aksi</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="(item, i) in group.items" :key="item.id">
                              <td>{{ i + 1 }}</td>
                              <td>{{ item.ownerFullName ?? '-' }}</td>
                              <td>{{ item.ownerUsername ?? '-' }}</td>
                              <td class="truncate" :title="item.caption">
                                {{ item.caption?.substring(0, 70) ?? '-' }}
                                {{ item.caption?.length > 70 ? '...' : '' }}
                              </td>
                              <td>{{ item.likesCount ?? 0 }}</td>
                              <td>{{ item.commentsCount ?? 0 }}</td>
                              <td>
                                <button
                                  class="btn btn-sm btn-outline-info rounded-pill px-3 me-2"
                                  @click="showMetadata(item)"
                                >
                                  Metadata
                                </button>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </td>
                </tr>

                <tr v-if="!crawls.data.length">
                  <td colspan="6" class="text-center py-4 text-muted">
                    Belum ada data crawl untuk periode ini.
                  </td>
                </tr>
              </tbody>
            </table>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
      <nav v-if="crawls.links.length > 3">
        <ul class="pagination mb-0">
          <li
            v-for="(link, index) in crawls.links"
            :key="index"
            class="page-item"
            :class="{ active: link.active, disabled: !link.url }"
          >
            <Link v-if="link.url" :href="link.url" class="page-link" v-html="link.label" />
            <span v-else class="page-link" v-html="link.label"></span>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  crawls: Object,
  categories: Array,
  period: String,
})

// ====== FILTER BY PERIOD ======
const selectedPeriod = ref(props.period)

const monthsOfYear = computed(() => {
  const now = new Date()
  const year = now.getFullYear()
  const currentMonth = now.getMonth() + 1 // 1-12
  const months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
  ]

  return months.slice(0, currentMonth).map((label, i) => ({
    label: `${label} ${year}`,
    value: `${year}-${String(i + 1).padStart(2, '0')}`,
  }))
})


const filterByPeriod = () => {
  router.get(route('settings.crawls.index'), { period: selectedPeriod.value }, { preserveScroll: true })
}

const resetToCurrentMonth = () => {
  const now = new Date()
  const current = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`
  selectedPeriod.value = current
  router.get(route('settings.crawls.index'), { period: current }, { preserveScroll: true })
}

const groupedCrawls = computed(() => {
  const groups = {}
  props.crawls.data.forEach(item => {
    const id = item.url_crawl_id
    if (!groups[id]) {
      groups[id] = {
        id,
        name: item.url_crawl?.name ?? `Crawl #${id}`,
        last_scraped_at: item.scraped_at,
        items: [],
      }
    }
    groups[id].items.push(item)
    if (item.scraped_at > groups[id].last_scraped_at)
      groups[id].last_scraped_at = item.scraped_at
  })
  return Object.values(groups)
})

const expandedGroups = ref([])
const toggleGroup = id => {
  if (expandedGroups.value.includes(id))
    expandedGroups.value = expandedGroups.value.filter(g => g !== id)
  else expandedGroups.value.push(id)
}

const formatDate = date =>
  date ? new Date(date).toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' }) : '-'

const modalMetadataOpen = ref(false)
const selectedMetadata = ref(null)
const showMetadata = crawl => {
  try {
    selectedMetadata.value = typeof crawl.metadata === 'string'
      ? JSON.parse(crawl.metadata)
      : crawl.metadata
  } catch {
    selectedMetadata.value = crawl.metadata
  }
  modalMetadataOpen.value = true
}
const closeMetadataModal = () => {
  modalMetadataOpen.value = false
  selectedMetadata.value = null
}
</script>

<style scoped>
.modal {
  background-color: rgba(0, 0, 0, 0.5);
}
.truncate {
  max-width: 300px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>
