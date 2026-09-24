<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import Paginator from 'primevue/paginator'

const props = defineProps({
  // A Laravel LengthAwarePaginator payload (products.total / per_page / current_page).
  paginator: {
    type: Object,
    default: null,
  },
})

const totalRecords = computed(() => Number(props.paginator?.total ?? 0))
const rows = computed(() => Number(props.paginator?.per_page ?? 20))
const currentPage = computed(() => Number(props.paginator?.current_page ?? 1))
const first = computed(() => (currentPage.value - 1) * rows.value)

const visible = computed(
  () => totalRecords.value > rows.value && rows.value > 0
)

function onPage(event) {
  const target = event.page + 1
  if (target === currentPage.value) return

  // Keep existing filters (keyword, category, ...) and only swap the page.
  const url = new URL(window.location.href)
  url.searchParams.set('page', String(target))
  router.get(url.pathname + url.search, {}, { preserveScroll: true, preserveState: true })
}
</script>

<template>
  <Paginator
    v-if="visible"
    class="shop-paginator"
    :first="first"
    :rows="rows"
    :totalRecords="totalRecords"
    :pageLinkSize="5"
    template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
    currentPageReportTemplate="{first}–{last} of {totalRecords}"
    @page="onPage"
  />
</template>

<style scoped>
.shop-paginator {
  background: transparent;
  border: none;
  padding: 0;
}

.shop-paginator :deep(.p-paginator-page),
.shop-paginator :deep(.p-paginator-first),
.shop-paginator :deep(.p-paginator-prev),
.shop-paginator :deep(.p-paginator-next),
.shop-paginator :deep(.p-paginator-last) {
  min-width: 2.75rem;
  height: 2.75rem;
  border-radius: 10px;
  font-weight: 600;
}

.shop-paginator :deep(.p-paginator-current) {
  font-size: 0.95rem;
  color: var(--p-text-muted-color, #6b7280);
}
</style>
