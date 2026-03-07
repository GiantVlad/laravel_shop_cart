<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '../Shared/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
})

const removeCategory = (id) => {
    if (!window.confirm('Remove this category?')) {
        return
    }

    router.delete(`/admin/categories/${id}`, {
        data: { id },
        preserveScroll: true,
    })
}
</script>

<template>
    <section class="content-card">
        <div class="page-header">
            <div>
                <p class="page-eyebrow">Catalog</p>
                <h2 class="page-title">Categories</h2>
            </div>
            <a class="btn btn-primary" href="/admin/add-category">Add New Category</a>
        </div>

        <div class="table-responsive">
            <table class="table admin-table align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Priority</th>
                        <th>Parent</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(category, index) in categories" :key="category.id">
                        <td>{{ index + 1 }}</td>
                        <td>
                            <img v-if="category.imageUrl" :src="category.imageUrl" class="thumb" alt="" />
                            <span v-else class="text-muted">No image</span>
                        </td>
                        <td>{{ category.name }}</td>
                        <td>{{ category.priority }}</td>
                        <td>{{ category.parentName || 'No parent' }}</td>
                        <td class="text-end actions">
                            <a class="btn btn-sm btn-outline-primary" :href="`/admin/edit-category/${category.id}`">Edit</a>
                            <button class="btn btn-sm btn-outline-danger" type="button" @click="removeCategory(category.id)">
                                Remove
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>

<style scoped>
.content-card,
.page-header {
    background: #fff;
}

.content-card {
    padding: 1.5rem;
    border-radius: 1.25rem;
    box-shadow: 0 20px 50px rgba(44, 62, 80, 0.08);
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.page-eyebrow {
    margin: 0 0 0.35rem;
    color: #0d6efd;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    font-size: 0.8rem;
    font-weight: 700;
}

.page-title {
    margin: 0;
}

.admin-table {
    margin-bottom: 0;
}

.thumb {
    width: 88px;
    height: 68px;
    object-fit: cover;
    border-radius: 0.75rem;
}

.actions {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}

@media (max-width: 767px) {
    .page-header {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>
