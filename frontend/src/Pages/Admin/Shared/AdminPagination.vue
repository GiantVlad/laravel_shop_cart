<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import Paginator from 'primevue/paginator'

const props = defineProps({
  // A Laravel LengthAwarePaginator payload (total / per_page / current_page).
  paginator: {
    type: Object,
    default: null,
  },
})

const totalRecords = computed(() => Number(props.paginator?.total ?? 0))
const rows = computed(() => Number(props.paginator?.per_page ?? 15))
const currentPage = computed(() => Number(props.paginator?.current_page ?? 1))
const first = computed(() => (currentPage.value - 1) * rows.value)

const visible = computed(() => totalRecords.value > rows.value && rows.value > 0)

function onPage(event) {
  const target = event.page + 1
  if (target === currentPage.value) return

  const url = new URL(window.location.href)
  url.searchParams.set('page', String(target))
  router.get(url.pathname + url.search, {}, { preserveScroll: true, preserveState: true })
}
</script>

<template>
  <Paginator
    v-if="visible"
    class="admin-paginator"
    :first="first"
    :rows="rows"
    :totalRecords="totalRecords"
    :pageLinkSize="5"
    template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
    currentPageReportTemplate="{first}–{last} of {totalRecords}"
    @page="onPage"
  />
</template>
