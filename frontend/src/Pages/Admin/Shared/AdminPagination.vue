<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    links: {
        type: Array,
        default: () => [],
    },
})
</script>

<template>
    <nav v-if="links.length" aria-label="Pagination" class="mt-5 flex justify-center gap-2">
        <Link
            v-for="link in links"
            :key="`${link.label}-${link.url}`"
            :href="link.url || '#'"
            class="p-button p-button-text p-button-rounded px-3 py-2 text-sm"
            :class="{ 'p-button-primary': link.active, 'opacity-50 pointer-events-none': !link.url }"
            v-html="link.label"
        />
    </nav>
</template>
