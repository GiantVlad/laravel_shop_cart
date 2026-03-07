<script setup>
import axios from 'axios'
import { ref } from 'vue'
import AdminLayout from '../Shared/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    shippingMethods: {
        type: Array,
        default: () => [],
    },
})

const shippingMethods = ref(props.shippingMethods)

const toggleStatus = async (method) => {
    const { data } = await axios.put('/admin/shipping-method', {
        method_id: method.id,
        status: method.enabled ? 0 : 1,
    }, {
        headers: { Accept: 'application/json' },
    })

    shippingMethods.value = data.shippingMethods
}
</script>

<template>
    <section class="content-card">
        <div class="page-header">
            <div>
                <p class="page-eyebrow">Logistics</p>
                <h2 class="page-title">Shipping Methods</h2>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table admin-table align-middle">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="method in shippingMethods" :key="method.id">
                        <td>{{ method.label }}</td>
                        <td>{{ method.priority }}</td>
                        <td>{{ method.enabled ? 'Enabled' : 'Disabled' }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary" type="button" @click="toggleStatus(method)">
                                {{ method.enabled ? 'Disable' : 'Enable' }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
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
