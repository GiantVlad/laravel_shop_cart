<script setup>
import AdminLayout from './Shared/AdminLayout.vue'

defineOptions({ layout: AdminLayout })
</script>

<template>
    <section class="admin-card">
        <p class="section-eyebrow">Overview</p>
        <h2 class="section-title">Admin panel now runs on Vue 3 + Inertia.</h2>
        <p class="section-copy">
            Category, product, user, order, payment, and shipping screens have been moved off the legacy Blade admin shell.
        </p>
    </section>
</template>

<style scoped>
.admin-card {
    padding: 2rem;
    border-radius: 1.25rem;
    background: #fff;
    box-shadow: 0 20px 50px rgba(44, 62, 80, 0.08);
}

.section-eyebrow {
    margin: 0 0 0.5rem;
    color: #0d6efd;
    font-size: 0.85rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    font-weight: 700;
}

.section-title {
    margin: 0;
    font-size: 2rem;
}

.section-copy {
    margin: 1rem 0 0;
    max-width: 52rem;
    color: #4d6278;
    font-size: 1.05rem;
}
</style>
