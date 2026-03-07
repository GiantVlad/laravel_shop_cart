<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '../Shared/AdminLayout.vue'
import AdminPagination from '../Shared/AdminPagination.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    orders: {
        type: Object,
        required: true,
    },
    keyword: {
        type: String,
        default: null,
    },
})

const form = useForm({
    keyword: props.keyword ?? '',
})

const submit = () => {
    form.post('/admin/orders')
}
</script>

<template>
    <section class="content-card">
        <div class="page-header">
            <div>
                <p class="page-eyebrow">Commerce</p>
                <h2 class="page-title">Orders</h2>
            </div>
        </div>

        <form class="search-row" @submit.prevent="submit">
            <input v-model="form.keyword" type="text" class="form-control" placeholder="Search by order label" />
            <button class="btn btn-primary" type="submit" :disabled="form.processing">Search</button>
        </form>

        <div class="table-responsive">
            <table class="table admin-table align-middle">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Created</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="order in orders.data" :key="order.id">
                        <td>{{ order.label }}</td>
                        <td>{{ order.createdAt }}</td>
                        <td>
                            <div>{{ order.userName }}</div>
                            <div class="text-muted">{{ order.userEmail }}</div>
                        </td>
                        <td>{{ order.total }}</td>
                        <td>{{ order.status }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <AdminPagination :links="orders.links" />
    </section>
</template>

<style scoped>
.content-card {
    padding: 1.5rem;
    border-radius: 1.25rem;
    background: #fff;
    box-shadow: 0 20px 50px rgba(44, 62, 80, 0.08);
}

.page-header {
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

.search-row {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

@media (max-width: 767px) {
    .search-row {
        flex-direction: column;
    }
}
</style>
