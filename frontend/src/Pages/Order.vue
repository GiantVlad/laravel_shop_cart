<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '../Layouts/MainLayout.vue'

defineOptions({ layout: Layout })

const props = defineProps({
  order: {
    type: Object,
    required: true,
  },
})

const formatDate = (value) => {
  if (!value) {
    return ''
  }

  return new Intl.DateTimeFormat('en-US', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(new Date(value))
}
</script>

<template>
  <div>
    <Head :title="`Order #${order.label}`" />
    <div class="py-4">
      <h1 class="h3">Order #{{ order.label }}</h1>
    </div>

    <div class="row mb-4">
      <div class="col-md-3"><strong>ID</strong></div>
      <div class="col-md-3"><strong>Created</strong></div>
      <div class="col-md-3"><strong>Total</strong></div>
      <div class="col-md-3"><strong>Status</strong></div>
    </div>

    <div class="row mb-4">
      <div class="col-md-3">{{ order.id }}</div>
      <div class="col-md-3">{{ formatDate(order.createdAt) }}</div>
      <div class="col-md-3">{{ order.total }}</div>
      <div class="col-md-3">{{ order.status }}</div>
    </div>

    <div class="mb-4 table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th class="col-sm-8">Product</th>
            <th class="col-sm-2">Price</th>
            <th class="col-sm-2">QTY</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in order.orderData" :key="item.id">
            <td><Link :href="`/shop/${item.product_id}`">{{ item.product?.name }}</Link></td>
            <td>{{ item.price }}</td>
            <td>{{ item.qty }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="row mb-4">
      <div class="col-md-6">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Shipping method</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in order.dispatches" :key="item.id">
              <td>{{ item.label }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Payment method</th>
              <th>Payment ID</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in order.payments" :key="item.id">
              <td>{{ item.method?.label }}</td>
              <td>{{ item.external_id }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
