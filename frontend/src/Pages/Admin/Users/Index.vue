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
    <section class="content-card">
        <div class="page-header">
            <div>
                <p class="page-eyebrow">Customers</p>
                <h2 class="page-title">Users</h2>
            </div>
        </div>

        <form class="search-row" @submit.prevent="submit">
            <input v-model="form.keyword" type="text" class="form-control" placeholder="Search by ID, name, or email" />
            <button class="btn btn-primary" type="submit" :disabled="form.processing">Search</button>
        </form>

        <div class="table-responsive">
            <table class="table admin-table align-middle">
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
                        <td class="text-end actions">
                            <a class="btn btn-sm btn-outline-primary" :href="`/admin/edit-user/${user.id}`">Edit</a>
                            <button class="btn btn-sm btn-outline-secondary" type="button" @click="clearCart(user.id)">
                                Logout / Clear Cart
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <AdminPagination :links="users.links" />
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

.actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}

@media (max-width: 767px) {
    .search-row,
    .actions {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>
