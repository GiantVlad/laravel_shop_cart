<script setup>
import axios from 'axios'
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Layout from '../Layouts/MainLayout.vue'

defineOptions({ layout: Layout })

const props = defineProps({
  orders: {
    type: Object,
    required: true,
  },
})

const ordersList = ref(props.orders.data ?? [])

const hasOrders = computed(() => ordersList.value.length > 0)

const formatDate = (value) => {
  if (!value) {
    return ''
  }

  return new Intl.DateTimeFormat('en-US', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(new Date(value))
}

const submitAction = async (event) => {
  const [orderId, action] = event.target.value.split(':')
  if (!orderId || !action) {
    return
  }

  const index = ordersList.value.findIndex((order) => String(order.id) === orderId)
  try {
    const response = await axios.post('/order/action', {
      id: orderId,
      action,
    })

    if (response.data?.redirect_to) {
      const target = response.data.redirect_to
      if (target.startsWith('http')) {
        window.location.href = target
      } else {
        router.visit(target)
      }
    }

    if (index >= 0 && response.data?.status) {
      ordersList.value[index].status = response.data.status
    }
  } catch (error) {
    console.log(error)
  } finally {
    event.target.value = ''
  }
}
</script>

<template>
  <div>
    <Head title="Orders" />
    <div class="py-4">
      <h1 class="h3">Orders</h1>
    </div>

    <div v-if="!hasOrders" class="alert alert-light border">
      No orders found.
    </div>

    <div v-else class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Created</th>
            <th>Total</th>
            <th>Status</th>
            <th>Details</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in ordersList" :key="order.id">
            <td>{{ order.label }}</td>
            <td>{{ formatDate(order.created_at) }}</td>
            <td>{{ order.total }}</td>
            <td>{{ order.status }}</td>
            <td><Link :href="order.uri">Details</Link></td>
            <td>
              <select
                class="form-control"
                @change="submitAction"
              >
                <option value="">Select action</option>
                <template v-if="order.status === 'pending payment'">
                  <option :value="`${order.id}:undo`">Undo order</option>
                  <option :value="`${order.id}:re_payment`">Repeat payment</option>
                </template>
                <option v-else :value="`${order.id}:repeat`">Repeat order</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
