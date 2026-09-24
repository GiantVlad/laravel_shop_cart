<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import Layout from '../Layouts/MainLayout.vue'
import Card from 'primevue/card'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import Divider from 'primevue/divider'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'

defineOptions({ layout: Layout })

const props = defineProps({
  order: {
    type: Object,
    required: true,
  },
})

// The detail resource serialises relations through JsonResource::collection(),
// so each one arrives as { data: [...] } — unwrap it (and tolerate a plain array).
const asList = (value) => (Array.isArray(value) ? value : Array.isArray(value?.data) ? value.data : [])

const items = computed(() => asList(props.order.orderData))
const payments = computed(() => asList(props.order.payments))
const dispatches = computed(() => asList(props.order.dispatches))

const itemCount = computed(() =>
  items.value.reduce((sum, item) => sum + Number(item.qty ?? 0), 0)
)

const formatDate = (value) => {
  if (!value) {
    return ''
  }

  return new Intl.DateTimeFormat('en-US', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(new Date(value))
}

const money = (value) => '$' + Number(value ?? 0).toFixed(2)

const rowTotal = (item) => Number(item.price ?? 0) * Number(item.qty ?? 0)

const statusSeverity = (status) => {
  switch (status) {
    case 'completed':
      return 'success'
    case 'process':
      return 'info'
    case 'pending payment':
      return 'warn'
    case 'deleted':
      return 'danger'
    default:
      return 'secondary'
  }
}

const statusLabel = (status) => {
  if (!status) {
    return ''
  }

  return status.charAt(0).toUpperCase() + status.slice(1)
}
</script>

<template>
  <div class="order-page px-3 py-4">
    <Head :title="`Order #${order.label}`" />

    <!-- Header -->
    <div class="flex align-items-center justify-content-between mb-4 pb-2 border-bottom-1 border-surface-200">
      <div>
        <h1 class="text-3xl font-bold text-900 m-0">Order #{{ order.label }}</h1>
        <p class="text-sm text-500 m-0 mt-1">Placed on {{ formatDate(order.createdAt) }}</p>
      </div>
      <Link href="/orders" class="no-underline">
        <Button label="All orders" icon="pi pi-arrow-left" severity="secondary" outlined size="small" />
      </Link>
    </div>

    <!-- Summary -->
    <Card class="mb-3 border-1 border-surface-200 border-round-xl shadow-1">
      <template #content>
        <div class="grid">
          <div class="col-12 sm:col-6 lg:col-3">
            <div class="text-xs font-semibold text-500 uppercase mb-1">Order ID</div>
            <div class="text-lg font-semibold text-900">{{ order.id }}</div>
          </div>
          <div class="col-12 sm:col-6 lg:col-3">
            <div class="text-xs font-semibold text-500 uppercase mb-1">Created</div>
            <div class="text-lg font-semibold text-900">{{ formatDate(order.createdAt) }}</div>
          </div>
          <div class="col-12 sm:col-6 lg:col-3">
            <div class="text-xs font-semibold text-500 uppercase mb-1">Total</div>
            <div class="text-lg font-bold text-primary">{{ money(order.total) }}</div>
          </div>
          <div class="col-12 sm:col-6 lg:col-3">
            <div class="text-xs font-semibold text-500 uppercase mb-1">Status</div>
            <Tag :value="statusLabel(order.status)" :severity="statusSeverity(order.status)" />
          </div>
        </div>
      </template>
    </Card>

    <!-- Items -->
    <Card class="mb-3 border-1 border-surface-200 border-round-xl shadow-1">
      <template #title>
        <div class="flex align-items-center justify-content-between">
          <span class="text-lg font-bold text-900">Items</span>
          <span class="text-sm text-500">
            {{ itemCount }} {{ itemCount === 1 ? 'unit' : 'units' }}
          </span>
        </div>
      </template>
      <template #content>
        <DataTable
          :value="items"
          dataKey="id"
          responsiveLayout="scroll"
          scrollable
          class="order-items-table"
        >
          <Column header="Product" style="min-width: 18rem;">
            <template #body="{ data }">
              <div class="flex align-items-center gap-3">
                <Link
                  v-if="data.product"
                  :href="`/shop/${data.product_id}`"
                  class="order-item__thumb flex align-items-center justify-content-center no-underline flex-shrink-0"
                >
                  <img
                    :alt="data.product.name"
                    :src="`/images/${data.product.image}`"
                    class="order-item__image"
                    loading="lazy"
                  />
                </Link>
                <Link
                  :href="`/shop/${data.product_id}`"
                  class="font-semibold text-900 no-underline hover:text-primary transition-colors"
                >
                  {{ data.product?.name ?? `Product #${data.product_id}` }}
                </Link>
              </div>
            </template>
          </Column>

          <Column header="Price" style="min-width: 7rem;">
            <template #body="{ data }">
              <span class="text-600">{{ money(data.price) }}</span>
            </template>
          </Column>

          <Column field="qty" header="Qty" style="min-width: 5rem;">
            <template #body="{ data }">
              <span class="text-600">{{ data.qty }}</span>
            </template>
          </Column>

          <Column header="Subtotal" style="min-width: 8rem;">
            <template #body="{ data }">
              <span class="font-semibold text-900 text-right block">{{ money(rowTotal(data)) }}</span>
            </template>
          </Column>
        </DataTable>

        <Divider class="my-3" />

        <div class="flex justify-content-end">
          <div class="flex align-items-center gap-3">
            <span class="font-bold text-900">Order total</span>
            <span class="font-bold text-2xl text-primary">{{ money(order.total) }}</span>
          </div>
        </div>
      </template>
    </Card>

    <!-- Shipping / payment -->
    <div class="grid">
      <div class="col-12 lg:col-6">
        <Card class="h-full border-1 border-surface-200 border-round-xl shadow-1">
          <template #title>
            <div class="flex align-items-center gap-2">
              <i class="pi pi-truck text-primary"></i>
              <span class="text-lg font-bold text-900">Shipping method</span>
            </div>
          </template>
          <template #content>
            <div v-if="dispatches.length > 0" class="flex flex-column gap-2">
              <div
                v-for="item in dispatches"
                :key="item.id"
                class="flex align-items-center gap-2 text-900"
              >
                <i class="pi pi-check-circle text-primary text-sm"></i>
                <span>{{ item.label }}</span>
              </div>
            </div>
            <p v-else class="text-500 text-sm m-0">No shipping method recorded.</p>
          </template>
        </Card>
      </div>

      <div class="col-12 lg:col-6">
        <Card class="h-full border-1 border-surface-200 border-round-xl shadow-1">
          <template #title>
            <div class="flex align-items-center gap-2">
              <i class="pi pi-credit-card text-primary"></i>
              <span class="text-lg font-bold text-900">Payment method</span>
            </div>
          </template>
          <template #content>
            <div v-if="payments.length > 0" class="flex flex-column gap-3">
              <div
                v-for="item in payments"
                :key="item.id"
                class="flex flex-column gap-1"
              >
                <span class="text-900 font-medium">{{ item.method?.label ?? '—' }}</span>
                <span class="text-xs text-500">
                  Payment ID: <span class="font-mono text-600">{{ item.external_id ?? '—' }}</span>
                </span>
              </div>
            </div>
            <p v-else class="text-500 text-sm m-0">No payment recorded.</p>
          </template>
        </Card>
      </div>
    </div>
  </div>
</template>

<style scoped>
.order-page {
  max-width: 1100px;
  margin: 0 auto;
}

.order-item__thumb {
  width: 48px;
  height: 48px;
  background-color: #f8fafc;
  border: 1px solid var(--p-surface-200, #e2e8f0);
  border-radius: 10px;
  overflow: hidden;
}

.order-item__image {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

:deep(.p-card-body) {
  padding: 1.25rem;
}

:deep(.p-card-content) {
  padding: 0;
}

:deep(.p-card-title) {
  margin-bottom: 0.75rem;
}

:deep(.order-items-table .p-datatable-header-cell) {
  font-weight: 600;
  color: #0f172a;
  background: #f8fafc;
}

:deep(.order-items-table .p-datatable-tbody > tr > td) {
  vertical-align: middle;
}
</style>