<template>
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold mb-4">
            {{ isEdit ? 'Edit Konten' : 'Tambah Konten' }}
        </h4>

        <div class="card shadow-sm rounded-4">
            <div class="card-body">
                <form @submit.prevent="submit">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama</label>
                        <input v-model="form.name" type="text" class="form-control" required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori</label>
                        <select v-model="form.category_content_id" class="form-select" required>
                            <option value="">Pilih kategori</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat . name }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kota</label>
                        <select v-model="form.city" class="form-select" required>
                            <option value="">Pilih kota</option>
                            <option v-for="c in cities" :key="c.id" :value="c.id">
                                {{ c . name }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Maps Link</label>
                        <input v-model="form.maps_link" type="text" class="form-control" required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea v-model="form.description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="text-end">
                        <Link :href="route('content.index')" class="btn btn-secondary rounded-pill me-2">Batal</Link>
                        <button type="submit" class="btn btn-success rounded-pill">
                            {{ isEdit ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
    import {
        Link,
        useForm
    } from "@inertiajs/vue3";

    const props = defineProps({
        content: Object,
        cities: Array,
        categories: Array,
        isEdit: Boolean,
    });

    const form = useForm({
        name: props.content?.name || "",
        category_content_id: props.content?.category_content_id || "",
        city: props.content?.city || "",
        maps_link: props.content?.maps_link || "",
        description: props.content?.description || "",
        rank: props.content?.rank || "",
    });

    const submit = () => {
        if (props.isEdit) {
            form.put(route("content.update", props.content.id));
        } else {
            form.post(route("content.store"));
        }
    };
</script>
