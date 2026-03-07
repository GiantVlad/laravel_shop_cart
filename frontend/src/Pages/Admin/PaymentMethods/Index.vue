<script setup>
import axios from 'axios'
import { router } from '@inertiajs/vue3'
import AdminLayout from '../Shared/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    paymentMethods: {
        type: Array,
        default: () => [],
    },
    statuses: {
        type: Object,
        default: () => ({}),
    },
})

const actionLabel = (method) => {
    if (method.status === 0) return 'Enable'
    if (method.status === 1) return 'Disable'
    if (method.status === 2) return 'Remove from DB'
    return 'Add to DB'
}

const actionType = (method) => {
    if (method.status === 0) return 'enable'
    if (method.status === 1) return 'disable'
    if (method.status === 2) return 'remove_from_db'
    return 'add_to_db'
}

const triggerAction = async (method) => {
    await axios.put(`/admin/payment-method-action/${method.id}`, {
        action: actionType(method),
        payment_key: method.configKey,
    })

    router.reload({ only: ['paymentMethods', 'statuses'] })
}

const updatePriority = async (method, event) => {
    await axios.put(`/admin/payment-method-priority/${method.id}`, {
        val: event.target.value,
    })

    router.reload({ only: ['paymentMethods', 'statuses'] })
}
</script>

<template>
    <section class="content-card">
        <div class="page-header">
            <div>
                <p class="page-eyebrow">Checkout</p>
                <h2 class="page-title">Payment Methods</h2>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table admin-table align-middle">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Key</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Class Name</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="method in paymentMethods" :key="`${method.id}-${method.configKey}`">
                        <td>{{ method.label }}</td>
                        <td>{{ method.configKey }}</td>
                        <td>{{ statuses[method.status] }}</td>
                        <td>
                            <input
                                v-if="method.status !== 3"
                                :value="method.priority"
                                class="form-control"
                                type="number"
                                @change="updatePriority(method, $event)"
                            />
                            <span v-else>-</span>
                        </td>
                        <td>{{ method.className }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary" type="button" @click="triggerAction(method)">
                                {{ actionLabel(method) }}
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
