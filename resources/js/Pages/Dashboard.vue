<template>
    <div class="container-xxl flex-grow-1 container-p-y">
        <h2 class="text-3xl font-bold mb-6 flex items-center gap-2">
            <i class="ti ti-layout-dashboard text-primary"></i> Dashboard Overview
        </h2>

        <!-- Statistik Ringkas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6 mb-8">
            <div v-for="(count, key) in stats" :key="key"
                class="bg-white shadow-sm rounded-xl p-5 flex flex-col items-center justify-center hover:shadow-md transition">
                <i :class="getIcon(key)" class="text-3xl mb-2 text-indigo-600"></i>
                <p class="text-gray-600 capitalize">{{ formatLabel(key) }}</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ count }}</h3>
            </div>
        </div>

        <!-- Data Terbaru -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-md p-6 space-y-6">
                <div>
                    <h5 class="text-lg font-bold mb-3 flex items-center gap-2 text-gray-800">
                        <i class="ti ti-chart-area-line text-primary text-xl"></i>
                        <span>Trend Publikasi</span>
                    </h5>
                    <div ref="trendChartEl" class="w-full min-h-[260px]"></div>
                </div>

                <hr class="border-gray-200" />

                <div>
                    <h5 class="text-lg font-bold mb-3 flex items-center gap-2 text-gray-800">
                        <i class="ti ti-chart-bar text-success text-xl"></i>
                        <span>Distribusi Data</span>
                    </h5>
                    <div ref="distChartEl" class="w-full min-h-[260px]"></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-6 space-y-6">
                <div>
                    <h5 class="text-lg font-bold mb-3 flex items-center gap-2 text-gray-800">
                        <i class="ti ti-article text-primary text-xl"></i>
                        <span>Recent Blogs</span>
                    </h5>

                    <ul v-if="recentBlogs.length" class="divide-y divide-gray-100">
                        <li v-for="blog in recentBlogs" :key="blog.id" class="py-2 flex justify-between items-center hover:bg-gray-50 rounded-md transition">
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-700">{{ blog . title }}</span>
                                <small class="text-gray-500">Published on {{ formatDate(blog . created_at) }}</small>
                            </div>
                            <i class="ti ti-chevron-right text-gray-400"></i>
                        </li>
                    </ul>

                    <p v-else class="text-gray-500 text-sm italic mt-2">No recent blogs found.</p>
                </div>

                <hr class="border-gray-200" />

                <div>
                    <h5 class="text-lg font-bold mb-3 flex items-center gap-2 text-gray-800">
                        <i class="ti ti-database text-success text-xl"></i>
                        <span>Recent Crawl Data</span>
                    </h5>

                    <ul v-if="dataUrlCrawl.length" class="divide-y divide-gray-100">
                        <li v-for="urlCrawl in dataUrlCrawl" :key="urlCrawl.id" class="py-2 flex justify-between items-center hover:bg-gray-50 rounded-md transition">
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-700">{{ urlCrawl.url_crawl?.name ?? '-' }}</span>
                                <small class="text-gray-500">url: <span class="font-semibold text-gray-600">{{ urlCrawl . url }}</span></small>
                            </div>
                            <small class="text-gray-400">{{ formatDate(urlCrawl . scraped_at) }}</small>
                        </li>
                    </ul>

                    <p v-else class="text-gray-500 text-sm italic mt-2">No crawl data available.</p>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
    import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'
    import dayjs from 'dayjs'

    const props = defineProps({
        stats: Object,
        recentBlogs: Array,
        dataUrlCrawl: Array,
    })

    const trendChartEl = ref(null)
    const distChartEl = ref(null)
    let trendChart
    let distChart

    const formatLabel = (key) => {
        const labels = {
            users: 'Users',
            blogs: 'Blogs',
            contents: 'Contents',
            contentCategories: 'Content Categories',
            cities: 'cities',
            urlCrawls: 'URL Crawls',
        }
        return labels[key] || key
    }

    const getIcon = (key) => {
        const icons = {
            users: 'ti ti-users',
            blogs: 'ti ti-article',
            contents: 'ti ti-file-description',
            contentCategories: 'ti ti-category',
            cities: 'ti ti-map-pin',
            urlCrawls: 'ti ti-cloud-download',
        }
        return icons[key] || 'ti ti-dots'
    }

    const formatDate = (date) => dayjs(date).format('DD MMM YYYY')

    const buildBlogMonthlySeries = () => {
        const months = Array.from({ length: 12 }, (_, i) => dayjs().month(i).format('MMM'))
        const counts = Array(12).fill(0)
        ;(props.recentBlogs || []).forEach(b => {
            const m = dayjs(b.created_at).month()
            counts[m]++
        })
        return { categories: months, data: counts }
    }

    const buildStatsSeries = () => {
        const labels = Object.keys(props.stats || {})
        const data = labels.map(k => Number(props.stats?.[k] || 0))
        return { labels, data }
    }

    const renderCharts = () => {
        if (!window.ApexCharts) return
        const blogSeries = buildBlogMonthlySeries()
        const statsSeries = buildStatsSeries()

        trendChart = new window.ApexCharts(trendChartEl.value, {
            chart: { type: 'area', height: 260, toolbar: { show: false } },
            series: [{ name: 'Blogs', data: blogSeries.data }],
            xaxis: { categories: blogSeries.categories },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            colors: ['#696cff'],
            grid: { strokeDashArray: 4 }
        })
        trendChart.render()

        distChart = new window.ApexCharts(distChartEl.value, {
            chart: { type: 'bar', height: 260, toolbar: { show: false } },
            series: [{ name: 'Count', data: statsSeries.data }],
            xaxis: { categories: statsSeries.labels },
            plotOptions: { bar: { borderRadius: 6, columnWidth: '45%' } },
            dataLabels: { enabled: false },
            colors: ['#03c3ec'],
            grid: { strokeDashArray: 4 }
        })
        distChart.render()
    }

    onMounted(async () => {
        await nextTick()
        renderCharts()
    })

    onBeforeUnmount(() => {
        try { trendChart?.destroy() } catch {}
        try { distChart?.destroy() } catch {}
    })
</script>

<style scoped>
    .container-xxl {
        max-width: 1300px;
    }
</style>
