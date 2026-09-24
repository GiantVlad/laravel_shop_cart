<script setup>
import AdminLayout from './Shared/AdminLayout.vue'

defineOptions({ layout: AdminLayout })
</script>

<template>
    <section class="admin-card">
        <p class="admin-page-eyebrow">Overview</p>
        <h2 class="admin-page-title">Admin panel now runs on Vue 3 + Inertia.</h2>
        <p class="admin-muted" style="max-width: 52rem; margin-top: 0.75rem;">
            Categories, products, users, orders, payment methods and shipping methods are served by Inertia pages
            under the <code>/admin</code> guard.
        </p>
    </section>
</template>
