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
    <section class="admin-card">
        <div class="admin-page-head">
            <div>
                <p class="admin-page-eyebrow">Catalog</p>
                <h2 class="admin-page-title">Categories</h2>
            </div>
            <a class="admin-btn admin-btn--primary" href="/admin/add-category">Add New Category</a>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
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
                            <img v-if="category.imageUrl" :src="category.imageUrl" class="admin-thumb" alt="" />
                            <span v-else class="admin-muted">No image</span>
                        </td>
                        <td>{{ category.name }}</td>
                        <td>{{ category.priority }}</td>
                        <td>{{ category.parentName || 'No parent' }}</td>
                        <td class="admin-actions">
                            <a class="admin-btn admin-btn--sm admin-btn--outline" :href="`/admin/edit-category/${category.id}`">Edit</a>
                            <button class="admin-btn admin-btn--sm admin-btn--danger" type="button" @click="removeCategory(category.id)">
                                Remove
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
