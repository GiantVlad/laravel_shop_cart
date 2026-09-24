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
    <section class="admin-card">
        <div class="admin-page-head">
            <div>
                <p class="admin-page-eyebrow">Logistics</p>
                <h2 class="admin-page-title">Shipping Methods</h2>
            </div>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
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
                        <td class="admin-actions">
                            <button class="admin-btn admin-btn--sm admin-btn--outline" type="button" @click="toggleStatus(method)">
                                {{ method.enabled ? 'Disable' : 'Enable' }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
