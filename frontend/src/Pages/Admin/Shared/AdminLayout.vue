<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'

defineProps({
  title: {
    type: String,
    default: 'Admin',
  },
})

const page = usePage()

const navigation = [
  { label: 'Dashboard', href: '/admin', match: ['/admin'] },
  { label: 'Categories', href: '/admin/categories', match: ['/admin/categories', '/admin/add-category', '/admin/edit-category'] },
  { label: 'Products', href: '/admin/products', match: ['/admin/products', '/admin/add-product', '/admin/edit-product'] },
  { label: 'Users', href: '/admin/users', match: ['/admin/users', '/admin/edit-user'] },
  { label: 'Orders', href: '/admin/orders', match: ['/admin/orders'] },
  { label: 'Payment Methods', href: '/admin/payment-methods', match: ['/admin/payment-methods'] },
  { label: 'Shipping', href: '/admin/shipping-methods', match: ['/admin/shipping-methods'] },
]

const currentPath = computed(() => page.url.split('?')[0])

// Edit/create screens keep their section highlighted (/admin/edit-product/3 -> Products).
const isActive = (item) =>
  item.match.some((prefix) =>
    prefix === '/admin' ? currentPath.value === '/admin' : currentPath.value.startsWith(prefix)
  )

const flashMessage = computed(() => page.props.flash?.message)
const flashError = computed(() => page.props.flash?.error)
const errors = computed(() => page.props.errors ?? {})
const adminName = computed(() => page.props.auth?.admin?.name ?? 'Admin')
</script>

<template>
  <Head :title="title" />

  <div class="admin-shell">
    <header class="admin-topbar">
      <div>
        <p class="admin-eyebrow">WG Shop</p>
        <h1 class="admin-title">{{ title }}</h1>
      </div>

      <div class="admin-user">
        <span>{{ adminName }}</span>
        <a class="admin-btn admin-btn--sm" href="/admin/logout">Logout</a>
      </div>
    </header>

    <div class="admin-body">
      <aside class="admin-sidebar">
        <Link
          v-for="item in navigation"
          :key="item.href"
          :href="item.href"
          class="admin-nav-link"
          :class="{ 'is-active': isActive(item) }"
        >
          {{ item.label }}
        </Link>
      </aside>

      <main class="admin-content">
        <div v-if="flashMessage" class="admin-alert admin-alert--success">{{ flashMessage }}</div>
        <div v-if="flashError" class="admin-alert admin-alert--danger">{{ flashError }}</div>

        <div v-if="Object.keys(errors).length" class="admin-alert admin-alert--danger">
          <div v-for="(message, key) in errors" :key="key">{{ message }}</div>
        </div>

        <slot />
      </main>
    </div>
  </div>
</template>
