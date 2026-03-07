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
    <nav v-if="links.length > 3" aria-label="Pagination">
        <div class="admin-pagination">
            <Link
                v-for="link in links"
                :key="`${link.label}-${link.url}`"
                :href="link.url || '#'"
                class="admin-pagination-link"
                :class="{ 'is-active': link.active, 'is-disabled': !link.url }"
                v-html="link.label"
            />
        </div>
    </nav>
</template>

<style scoped>
.admin-pagination {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 1.5rem;
}

.admin-pagination-link {
    padding: 0.65rem 0.95rem;
    border-radius: 999px;
    background: #fff;
    color: #1b4965;
    text-decoration: none;
    box-shadow: 0 10px 25px rgba(27, 73, 101, 0.08);
}

.admin-pagination-link.is-active {
    background: #0d6efd;
    color: #fff;
}

.admin-pagination-link.is-disabled {
    pointer-events: none;
    opacity: 0.45;
}
</style>
