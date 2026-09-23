<script setup>
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '../Shared/AdminLayout.vue'
import AdminPagination from '../Shared/AdminPagination.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    users: {
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
    form.post('/admin/users')
}

const clearCart = (id) => {
    if (!window.confirm('Force logout this user and clear the cart?')) {
        return
    }

    router.put('/admin/users/', { id })
}
</script>

<template>
    <section class="admin-card">
        <div class="admin-page-head">
            <div>
                <p class="admin-page-eyebrow">Customers</p>
                <h2 class="admin-page-title">Users</h2>
            </div>
        </div>

        <form class="admin-search-row" @submit.prevent="submit">
            <input v-model="form.keyword" type="text" class="admin-input" placeholder="Search by ID, name, or email" />
            <button class="admin-btn admin-btn--primary" type="submit" :disabled="form.processing">Search</button>
        </form>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users.data" :key="user.id">
                        <td>{{ user.id }}</td>
                        <td>{{ user.name }}</td>
                        <td>{{ user.email }}</td>
                        <td class="admin-actions">
                            <a class="admin-btn admin-btn--sm admin-btn--outline" :href="`/admin/edit-user/${user.id}`">Edit</a>
                            <button class="admin-btn admin-btn--sm" type="button" @click="clearCart(user.id)">
                                Logout / Clear Cart
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <AdminPagination :paginator="users" />
    </section>
</template>
