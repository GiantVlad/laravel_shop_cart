<script setup>
import axios from 'axios'
import { computed, reactive, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Layout from '../Layouts/MainLayout.vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Tag from 'primevue/tag'
import Button from 'primevue/button'
import Select from 'primevue/select'
import Card from 'primevue/card'

defineOptions({ layout: Layout })

const props = defineProps({
  orders: {
    type: Object,
    required: true,
  },
})

const statusOverrides = reactive({})
const rowActions = reactive({})
const busyRow = ref(null)

// Derive rows from props (not a ref snapshot): with preserveState the component
// is not recreated on page change, so a ref would keep showing the old page.
const ordersList = computed(() =>
  (props.orders.data ?? []).map((order) =>
    statusOverrides[order.id] ? { ...order, status: statusOverrides[order.id] } : order
  )
)

const hasOrders = computed(() => ordersList.value.length > 0)

const meta = computed(() => props.orders.meta ?? {})

const totalRecords = computed(() => Number(meta.value.total ?? ordersList.value.length))
const perPage = computed(() => Number(meta.value.per_page ?? 15))
const first = computed(() => (Number(meta.value.current_page ?? 1) - 1) * perPage.value)

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

const actionOptions = (order) => {
  if (order.status === 'pending payment') {
    return [
      { label: 'Undo order', value: 'undo', icon: 'pi pi-undo' },
      { label: 'Repeat payment', value: 're_payment', icon: 'pi pi-credit-card' },
    ]
  }

  return [{ label: 'Repeat order', value: 'repeat', icon: 'pi pi-replay' }]
}

const submitAction = async (order, action) => {
  if (!order || !action) {
    return
  }

  const index = ordersList.value.findIndex((item) => String(item.id) === String(order.id))
  busyRow.value = order.id

  try {
    const response = await axios.post('/order/action', {
      id: order.id,
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
      statusOverrides[order.id] = response.data.status
    }
  } catch (error) {
    console.log(error)
  } finally {
    rowActions[order.id] = null
    busyRow.value = null
  }
}

const onPage = (event) => {
  router.get('/orders', { page: event.page + 1 }, { preserveScroll: true, preserveState: true })
}
</script>

<template>
  <div class="orders-page">
    <Head title="Orders" />

    <!-- Header -->
    <div class="flex align-items-center justify-content-between mb-4 pb-2 border-bottom-1 border-surface-200">
      <div>
        <h1 class="text-3xl font-bold text-900 m-0">Orders</h1>
        <p class="text-sm text-500 m-0 mt-1">
          {{ totalRecords }} {{ totalRecords === 1 ? 'order' : 'orders' }}
        </p>
      </div>
      <Link href="/shop" class="no-underline">
        <Button label="Continue shopping" icon="pi pi-arrow-left" severity="secondary" outlined size="small" />
      </Link>
    </div>

    <!-- Empty state -->
    <Card v-if="!hasOrders" class="border-1 border-surface-200 border-round-xl shadow-1">
      <template #content>
        <div class="text-center py-6 px-3">
          <div class="inline-flex align-items-center justify-content-center bg-primary-50 border-circle mb-3"
               style="width: 72px; height: 72px;">
            <i class="pi pi-receipt text-primary text-3xl"></i>
          </div>
          <h2 class="text-xl font-bold text-900 m-0 mb-1">No orders yet</h2>
          <p class="text-500 text-sm m-0 mb-4">Orders you place will show up here.</p>
          <Link href="/shop" class="no-underline">
            <Button label="Browse products" icon="pi pi-shopping-bag" />
          </Link>
        </div>
      </template>
    </Card>

    <!-- Orders table -->
    <Card v-else class="border-1 border-surface-200 border-round-xl shadow-1">
      <template #content>
        <DataTable
          :value="ordersList"
          dataKey="id"
          :rows="perPage"
          :first="first"
          :totalRecords="totalRecords"
          :lazy="true"
          paginator
          paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
          currentPageReportTemplate="{first}–{last} of {totalRecords}"
          :pageLinkSize="5"
          responsiveLayout="scroll"
          scrollable
          class="orders-table"
          @page="onPage"
        >
          <Column field="label" header="Order">
            <template #body="{ data }">
              <Link :href="data.uri" class="font-semibold text-900 no-underline hover:text-primary transition-colors">
                {{ data.label }}
              </Link>
            </template>
          </Column>

          <Column field="created_at" header="Created" style="min-width: 12rem;">
            <template #body="{ data }">
              <span class="text-600">{{ formatDate(data.created_at) }}</span>
            </template>
          </Column>

          <Column field="total" header="Total" style="min-width: 8rem;">
            <template #body="{ data }">
              <span class="font-semibold text-900">{{ money(data.total) }}</span>
            </template>
          </Column>

          <Column field="status" header="Status" style="min-width: 9rem;">
            <template #body="{ data }">
              <Tag :value="statusLabel(data.status)" :severity="statusSeverity(data.status)" />
            </template>
          </Column>

          <Column header="Actions" style="min-width: 20rem;">
            <template #body="{ data }">
              <div class="flex align-items-center gap-2 justify-content-end">
                <Link :href="data.uri" class="no-underline">
                  <Button label="Details" icon="pi pi-eye" severity="secondary" outlined size="small" />
                </Link>
                <Select
                  v-model="rowActions[data.id]"
                  :options="actionOptions(data)"
                  optionLabel="label"
                  optionValue="value"
                  placeholder="Select action"
                  size="small"
                  class="order-action-select"
                  :loading="busyRow === data.id"
                  :disabled="busyRow === data.id"
                  @update:modelValue="value => submitAction(data, value)"
                />
              </div>
            </template>
          </Column>
        </DataTable>
      </template>
    </Card>
  </div>
</template>

<style scoped>
.orders-page {
  max-width: 1280px;
  margin: 0 auto;
}

.order-action-select {
  min-width: 11rem;
}

:deep(.orders-table .p-datatable-header-cell) {
  font-weight: 600;
  color: #0f172a;
  background: #f8fafc;
}

:deep(.orders-table .p-datatable-tbody > tr > td) {
  vertical-align: middle;
}

:deep(.p-card-body) {
  padding: 1.25rem;
}

:deep(.p-card-content) {
  padding: 0;
}
</style>
