<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  links: {
    type: Array,
    default: () => [],
  },
})

function cleanLabel(label) {
  if (!label) return ''
  return label
    .replace('&laquo; Previous', '‹')
    .replace('Next &raquo;', '›')
    .replace('&laquo;', '‹')
    .replace('&raquo;', '›')
}
</script>

<template>
  <nav v-if="links && links.length > 3" aria-label="Pagination" class="d-flex align-items-center gap-1">
    <template v-for="link in links" :key="`${link.label}-${link.url}`">
      <Link
        v-if="link.url"
        :href="link.url"
        class="p-button p-button-sm p-button-rounded transition-colors duration-150 min-w-2rem h-2rem d-flex align-items-center justify-content-center text-xs font-semibold"
        :class="link.active ? 'p-button-primary shadow-sm' : 'p-button-text p-button-secondary text-surface-700'"
        v-html="cleanLabel(link.label)"
      />
      <span
        v-else
        class="p-button p-button-sm p-button-rounded p-button-text p-button-secondary opacity-40 pointer-events-none min-w-2rem h-2rem d-flex align-items-center justify-content-center text-xs"
        v-html="cleanLabel(link.label)"
      />
    </template>
  </nav>
</template>

<style scoped>
.min-w-2rem { min-width: 2.25rem; }
.h-2rem { height: 2.25rem; }
</style>
