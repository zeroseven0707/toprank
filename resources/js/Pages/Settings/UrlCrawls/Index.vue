<template>
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold mb-4 text-2xl">URL Crawl Manager</h4>

        <!-- Form Tambah Config -->
        <form @submit.prevent="addUrlCrawl" class="card p-4 shadow-sm mb-4 rounded-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Nama</label>
                    <input v-model="form.name" type="text" class="form-control"
                        placeholder="Misal: Fashion Anak Bandung" required />
                </div>

                <div class="col-md-3">
                    <label class="form-label">Kategori</label>
                    <select v-model="form.category" class="form-select">
                        <option value="">Pilih Kategori</option>
                        <option v-for="cat in contentCategory" :key="cat.id" :value="cat.id">
                            {{ cat . name }}
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Kota</label>
                    <select v-model="form.city" class="form-select">
                        <option value="">Pilih Kota</option>
                        <option v-for="c in city" :key="c.id" :value="c.id">{{ c . name }}
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Hastag</label>
                    <input v-model="form.tag" type="text" class="form-control" placeholder="cth: fashionanak" />
                </div>

                <div class="col-md-12 text-end mt-3">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-plus"></i> Tambah
                    </button>
                </div>
            </div>
        </form>
        <!-- Data Table -->
        <div class="card shadow-sm rounded-3 p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Kota</th>
                            <th>Tag</th>
                            <th>Status</th>
                            <th>Last Run</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="item in urlCrawls" :key="item.id">
                            <td>{{ item . name }}</td>
                            <td>{{ item . category ? item . category . name : '-' }}</td>
                            <td>{{ item . city ? item . city . name : '-' }}</td>
                            <td>{{ item . tag || '-' }}</td>
                            <td>
                                <span class="badge text-uppercase px-3 py-2" :class="statusClass(item.status)">
                                    {{ item . status }}
                                </span>
                            </td>
                            <td class="d-md-table-cell">{{ item . last_run_at ? formatDate(item . last_run_at) : '-' }}</td>
                            <td class="text-end position-relative">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" @click.stop="toggleMenu(item.id)">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div v-if="openMenuId === item.id" class="dropdown-menu show" style="position:absolute; right:0; z-index:10;">
                                        <button @click.prevent="runCrawl(item.id)" class="dropdown-item" :disabled="item.status === 'running'">
                                            <i class="ti ti-player-play me-1"></i> Run
                                        </button>
                                        <button @click.prevent="deleteCrawl(item.id)" class="dropdown-item text-danger">
                                            <i class="ti ti-trash me-1"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="!urlCrawls.length">
                            <td colspan="7" class="text-center py-4 text-muted">
                                Belum ada data URL Crawl
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end align-items-center mt-4">
                <button class="btn btn-success rounded-pill" @click="runAll">
                    <i class="ti ti-player-play me-1"></i> Jalankan Semua
                </button>
            </div>
        </div>

    </div>
</template>

<script setup>
    import {
        router
    } from '@inertiajs/vue3'
    import {
        ref
    } from 'vue'
    import dayjs from 'dayjs'
    import 'dayjs/locale/id'

    dayjs.locale('id')

    const props = defineProps({
        urlCrawls: Array,
        contentCategory: Array,
        city: Array,
    })

    const form = ref({
        name: '',
        category: '',
        city: '',
        tag: '',
    })

    const addUrlCrawl = () => {
        router.post('/settings/url-crawls', form.value, {
            onSuccess: () => {
                form.value = {
                    name: '',
                    category: '',
                    city: '',
                    tag: ''
                }
            },
        })
    }

    const runCrawl = (id) => {
        if (confirm('Jalankan crawl sekarang?')) {
            router.post(`/settings/url-crawls/${id}/run`)
        }
    }

    const runAll = () => {
        if (confirm('Yakin ingin menjalankan semua URL Crawl sekaligus?')) {
            router.post('/settings/url-crawls/run-all', {}, {
                onSuccess: () => alert('Semua URL Crawl sedang dijalankan'),
                onError: (err) => {
                    console.error(err)
                    alert('Gagal menjalankan semua crawl.')
                },
            })
        }
    }

    const deleteCrawl = (id) => {
        if (confirm('Hapus data ini?')) {
            router.delete(`/settings/url-crawls/${id}`)
        }
    }

    const statusClass = (status) => {
        return {
            idle: 'bg-secondary text-white',
            running: 'bg-warning text-dark',
            done: 'bg-success text-white',
            failed: 'bg-danger text-white',
        } [status]
    }

    const formatDate = (date) => dayjs(date).format('DD MMM YYYY HH:mm')

    const openMenuId = ref(null)
    const toggleMenu = (id) => { openMenuId.value = openMenuId.value === id ? null : id }
    document.addEventListener('click', (e) => { if (!e.target.closest('.dropdown')) openMenuId.value = null })
</script>
