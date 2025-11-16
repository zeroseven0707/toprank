<template>
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted fw-light">Blog /</span> Edit
            </h4>
            <Link href="/blogs" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back
            </Link>
        </div>

        <!-- Blog Form -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Edit Blog Post</h5>
            </div>

            <div class="card-body">
                <div v-if="$page.props.errors && Object.keys($page.props.errors).length"
                    class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        <li v-for="(msg, field) in $page.props.errors" :key="field">
                            {{ msg }}
                        </li>
                    </ul>
                </div>
                <form @submit.prevent="submit">
                    <div class="row g-3">
                        <!-- Title -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Title</label>
                            <input v-model="form.title" type="text" class="form-control"
                                placeholder="Enter blog title" />
                        </div>

                        <!-- Category (updated to match Create.vue style) -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category</label>
                            <select v-model="form.category" class="form-select">
                                <option value="">-- Pilih Kategori --</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat . name }}
                                </option>
                            </select>
                        </div>

                        <!-- Excerpt -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Excerpt</label>
                            <textarea v-model="form.excerpt" class="form-control" placeholder="Short description..." rows="3"></textarea>
                        </div>

                        <!-- Thumbnail, Tags & Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Thumbnail Image</label>
                            <input type="file" @change="handleFile" class="form-control" />
                            <div v-if="previewUrl || blog.image" class="mt-2 border rounded p-2">
                                <img :src="previewUrl || `/storage/${blog.image}`" alt="Preview"
                                    class="img-fluid rounded" style="max-height: 180px;" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tags</label>
                            <div class="form-control d-flex flex-wrap align-items-center gap-2 p-2"
                                @click="focusTagInput" style="min-height: 45px; cursor: text;">
                                <span v-for="(tag, index) in tagsList" :key="index"
                                    class="badge bg-light text-dark px-2 py-1 border rounded d-flex align-items-center">
                                    {{ tag }}
                                    <button type="button" class="btn-close btn-close-sm ms-2" aria-label="Remove"
                                        @click.stop="removeTag(index)" style="filter: invert(0.5);"></button>
                                </span>

                                <input ref="tagInput" v-model="newTag" @keydown.enter.prevent="addTag"
                                    @keydown="checkComma" type="text" class="border-0 flex-grow-1"
                                    placeholder="Ketik tag lalu tekan koma" style="outline: none; min-width: 150px;" />
                            </div>
                            <small class="text-muted">Pisahkan setiap tag dengan koma</small>

                            <label class="form-label fw-semibold mt-3">Status</label>
                            <select v-model="form.status" class="form-select">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                            </select>
                        </div>

                        <!-- Content -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Content</label>
                            <textarea v-model="form.content" class="form-control" rows="6" placeholder="Write your full blog content here..."></textarea>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Update Blog
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
    import {
        ref,
        onMounted
    } from "vue";
    import {
        router,
        Link
    } from "@inertiajs/vue3";

    const props = defineProps({
        blog: Object,
        categories: Array,
    });

    const form = ref({
        title: props.blog.title || "",
        category: props.blog.category_id || "",
        excerpt: props.blog.excerpt || "",
        content: props.blog.content || "",
        tags: props.blog.tags || "",
        image: null,
        status: props.blog.status || "draft",
    });

    const previewUrl = ref(null);

    // === File preview ===
    const handleFile = (e) => {
        const file = e.target.files[0];
        form.value.image = file;
        previewUrl.value = URL.createObjectURL(file);
    };

    // === TAGS (chip-style system) ===
    const tagsList = ref(
        props.blog.tags ? props.blog.tags.split(",").map((t) => t.trim()).filter(Boolean) : []
    );
    const newTag = ref("");
    const tagInput = ref(null);

    function checkComma(e) {
        if (e.key === ",") {
            e.preventDefault();
            addTag();
        }
    }

    function addTag() {
        const tag = newTag.value.trim().replace(",", "");
        if (tag && !tagsList.value.includes(tag)) {
            tagsList.value.push(tag);
            newTag.value = "";
            updateFormTags();
        }
    }

    function removeTag(index) {
        tagsList.value.splice(index, 1);
        updateFormTags();
    }

    function focusTagInput() {
        tagInput.value?.focus();
    }

    function updateFormTags() {
        form.value.tags = tagsList.value.join(",");
    }

    // === Submit ===
    const submit = () => {
        router.post(
            `/admin/blogs/${props.blog.id}`, {
                ...form.value,
                _method: "PUT"
            }, {
                forceFormData: true,
                onSuccess: () => {
                    window.toast && toast.success("✅ Blog updated successfully!");
                },
            }
        );
    };

    // === Default category ===
    onMounted(() => {
        if (!form.value.category && props.categories.length > 0) {
            form.value.category = props.categories[0].id;
        }
    });
</script>

<style scoped>
    .form-label {
        font-size: 0.9rem;
    }

    .badge {
        font-size: 0.85rem;
        background-color: #e9ecef !important;
    }

    .btn-close-sm {
        width: 0.6em;
        height: 0.6em;
    }

    .card {
        border-radius: 1rem;
    }

    input.form-control,
    select.form-select,
    textarea.form-control {
        border-radius: 0.5rem;
    }

    .hover-shadow:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }
</style>
