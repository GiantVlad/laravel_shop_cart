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
    <section class="admin-card">
        <div class="admin-page-head">
            <div>
                <p class="admin-page-eyebrow">Commerce</p>
                <h2 class="admin-page-title">Orders</h2>
            </div>
        </div>

        <form class="admin-search-row" @submit.prevent="submit">
            <input v-model="form.keyword" type="text" class="admin-input" placeholder="Search by order label" />
            <button class="admin-btn admin-btn--primary" type="submit" :disabled="form.processing">Search</button>
        </form>

        <div class="admin-table-wrapper">
            <table class="admin-table">
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
                            <div class="admin-muted">{{ order.userEmail }}</div>
                        </td>
                        <td>{{ order.total }}</td>
                        <td>{{ order.status }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <AdminPagination :paginator="orders" />
    </section>
</template>
