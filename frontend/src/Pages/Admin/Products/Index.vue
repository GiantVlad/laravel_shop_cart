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
    <section class="content-card">
        <div class="page-header">
            <div>
                <p class="page-eyebrow">Catalog</p>
                <h2 class="page-title">Products</h2>
            </div>
            <a class="btn btn-primary" href="/admin/add-product">Add New Product</a>
        </div>

        <div class="filters">
            <select v-model="selectedCategory" class="form-select filter-select" @change="filterByCategory">
                <option value="">All categories</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                </option>
            </select>
        </div>

        <div class="table-responsive">
            <table class="table admin-table align-middle">
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
                            <img v-if="product.imageUrl" :src="product.imageUrl" class="thumb" alt="" />
                        </td>
                        <td>{{ product.name }}</td>
                        <td class="description-cell">{{ product.description }}</td>
                        <td>{{ product.price }}</td>
                        <td>{{ product.categoryName }}</td>
                        <td class="text-end actions">
                            <a class="btn btn-sm btn-outline-primary" :href="`/admin/edit-product/${product.id}`">Edit</a>
                            <button class="btn btn-sm btn-outline-danger" type="button" @click="removeProduct(product.id)">
                                Remove
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <AdminPagination :links="products.links" />
    </section>
</template>

<style scoped>
.content-card {
    padding: 1.5rem;
    border-radius: 1.25rem;
    background: #fff;
    box-shadow: 0 20px 50px rgba(44, 62, 80, 0.08);
}

.page-header,
.filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.page-header {
    margin-bottom: 1.25rem;
}

.filters {
    margin-bottom: 1rem;
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

.filter-select {
    max-width: 320px;
}

.thumb {
    width: 88px;
    height: 68px;
    object-fit: cover;
    border-radius: 0.75rem;
}

.description-cell {
    max-width: 22rem;
}

.actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}

@media (max-width: 767px) {
    .page-header,
    .filters {
        flex-direction: column;
        align-items: stretch;
    }

    .filter-select {
        max-width: none;
    }
}
</style>
