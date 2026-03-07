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
    <section class="content-card">
        <div class="page-header">
            <div>
                <p class="page-eyebrow">Customers</p>
                <h2 class="page-title">Edit User</h2>
            </div>
        </div>

        <form class="row g-4" @submit.prevent="submit">
            <div class="col-12">
                <label class="form-label">Name</label>
                <input v-model="form.name" type="text" class="form-control" required />
            </div>

            <div class="col-12">
                <label class="form-label">Email</label>
                <input v-model="form.email" type="email" class="form-control" required />
            </div>

            <div class="col-12">
                <button class="btn btn-primary" type="submit" :disabled="form.processing">Save User</button>
            </div>
        </form>
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
</style>
