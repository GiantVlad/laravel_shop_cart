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
    <section class="admin-card">
        <div class="admin-page-head">
            <div>
                <p class="admin-page-eyebrow">Checkout</p>
                <h2 class="admin-page-title">Payment Methods</h2>
            </div>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
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
                                class="admin-input"
                                type="number"
                                @change="updatePriority(method, $event)"
                            />
                            <span v-else>-</span>
                        </td>
                        <td>{{ method.className }}</td>
                        <td class="admin-actions">
                            <button class="admin-btn admin-btn--sm admin-btn--outline" type="button" @click="triggerAction(method)">
                                {{ actionLabel(method) }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
