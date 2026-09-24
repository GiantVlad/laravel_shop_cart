<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '../Shared/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
})

const form = useForm({
    id: props.user.id,
    name: props.user.name,
    email: props.user.email,
})

const submit = () => {
    form.put('/admin/user')
}
</script>

<template>
    <section class="admin-card">
        <div class="admin-page-head">
            <div>
                <p class="admin-page-eyebrow">Customers</p>
                <h2 class="admin-page-title">Edit User</h2>
            </div>
        </div>

        <form class="admin-form-grid" @submit.prevent="submit">
            <div class="admin-col-12">
                <label class="admin-label">Name</label>
                <input v-model="form.name" type="text" class="admin-input" required />
            </div>

            <div class="admin-col-12">
                <label class="admin-label">Email</label>
                <input v-model="form.email" type="email" class="admin-input" required />
            </div>

            <div class="admin-col-12">
                <button class="admin-btn admin-btn--primary" type="submit" :disabled="form.processing">Save User</button>
            </div>
        </form>
    </section>
</template>
