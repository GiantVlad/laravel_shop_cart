<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '../Shared/AdminLayout.vue'
import AdminPagination from '../Shared/AdminPagination.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    products: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    selectedCategoryId: {
        type: [Number, String, null],
        default: null,
    },
})

const selectedCategory = ref(props.selectedCategoryId ?? '')

watch(
    () => props.selectedCategoryId,
    (value) => {
        selectedCategory.value = value ?? ''
    }
)

const filterByCategory = () => {
    if (!selectedCategory.value) {
        router.get('/admin/products')
        return
    }

    router.get(`/admin/products/category/${selectedCategory.value}`)
}

const removeProduct = (id) => {
    if (!window.confirm('Remove this product?')) {
        return
    }

    router.delete('/admin/products', {
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
                <h2 class="admin-page-title">Products</h2>
            </div>
            <a class="admin-btn admin-btn--primary" href="/admin/add-product">Add New Product</a>
        </div>

        <div class="admin-search-row">
            <select v-model="selectedCategory" class="admin-select" style="max-width: 320px;" @change="filterByCategory">
                <option value="">All categories</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                </option>
            </select>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="product in products.data" :key="product.id">
                        <td>
                            <img v-if="product.imageUrl" :src="product.imageUrl" class="admin-thumb" alt="" />
                        </td>
                        <td>{{ product.name }}</td>
                        <td class="admin-description-cell">{{ product.description }}</td>
                        <td>{{ product.price }}</td>
                        <td>{{ product.categoryName }}</td>
                        <td class="admin-actions">
                            <a class="admin-btn admin-btn--sm admin-btn--outline" :href="`/admin/edit-product/${product.id}`">Edit</a>
                            <button class="admin-btn admin-btn--sm admin-btn--danger" type="button" @click="removeProduct(product.id)">
                                Remove
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <AdminPagination :paginator="products" />
    </section>
</template>
