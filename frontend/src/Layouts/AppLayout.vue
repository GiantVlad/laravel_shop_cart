<script setup>
import { computed, ref } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

const page = usePage()
const appName = computed(() => page.props.appName || 'Lara Shop')
const userName = computed(() => page.props.userName)
const cart = computed(() => page.props.cart)

const searchQuery = ref('')

const handleSearch = () => {
  if (searchQuery.value && searchQuery.value.trim()) {
    router.get('/search', { keyword: searchQuery.value.trim() })
  } else {
    router.get('/shop')
  }
}
</script>

<template>
  <div class="app-layout min-h-screen flex flex-column bg-surface-50">
    <!-- Navbar Header -->
    <header class="app-header bg-white border-bottom-1 sticky top-0 z-5 shadow-1">
      <div class="layout-container mx-auto px-4 flex align-items-center justify-content-between py-2 gap-3">
        
        <!-- Brand -->
        <Link href="/shop" class="flex align-items-center gap-2 no-underline text-900 flex-shrink-0">
          <i class="pi pi-box text-primary text-2xl"></i>
          <span class="text-xl font-bold tracking-tight text-900">{{ appName }}</span>
        </Link>

        <!-- Search Bar (Icon strictly in one line with input) -->
        <form @submit.prevent="handleSearch" class="flex-grow-1 mx-3" style="max-width: 480px;">
          <div class="search-input-wrapper">
            <i class="pi pi-search search-icon"></i>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search products..."
              class="search-input"
            />
          </div>
        </form>

        <!-- Right Actions: Cart & Auth -->
        <div class="flex align-items-center gap-2 flex-shrink-0">
          <!-- Cart Link with Badge -->
          <Link
            href="/cart"
            class="cart-btn"
            aria-label="Shopping Cart"
          >
            <i class="pi pi-shopping-cart text-xl"></i>
            <span
              v-if="cart?.count"
              class="cart-badge"
            >
              {{ cart.count }}
            </span>
          </Link>

          <!-- Guest Actions -->
          <template v-if="!userName">
            <Link
              href="/login"
              class="auth-btn auth-btn-ghost"
            >
              Log in
            </Link>
            <Link
              href="/register"
              class="auth-btn auth-btn-primary shadow-1"
            >
              Sign up
            </Link>
          </template>

          <!-- User Menu -->
          <template v-else>
            <Link
              href="/orders"
              class="auth-btn auth-btn-outline"
            >
              <i class="pi pi-list mr-1"></i> Orders
            </Link>
            <Link
              href="/logout"
              method="post"
              as="button"
              type="button"
              class="auth-btn auth-btn-danger"
            >
              <i class="pi pi-sign-out"></i>
            </Link>
          </template>
        </div>

      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow-1">
      <slot />
    </main>

    <!-- Minimalist Footer -->
    <footer class="bg-white border-top-1 border-surface-200 py-4 mt-auto">
      <div class="layout-container mx-auto px-4 text-center text-500 text-sm">
        &copy; {{ new Date().getFullYear() }} {{ appName }}. Minimalist design with PrimeVue & Inertia.
      </div>
    </footer>
  </div>
</template>

<style scoped>
.bg-surface-50 {
  background-color: #f8fafc;
}
.layout-container {
  max-width: 1280px;
}
.app-header {
  border-color: #e2e8f0;
}

/* Search Bar in One Line */
.search-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  width: 100%;
}
.search-icon {
  position: absolute;
  left: 0.85rem;
  color: #94a3b8;
  font-size: 0.95rem;
  pointer-events: none;
}
.search-input {
  width: 100%;
  height: 38px;
  padding: 0.5rem 1rem 0.5rem 2.4rem;
  font-size: 0.875rem;
  line-height: 1.25rem;
  color: #0f172a;
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 9999px;
  outline: none;
  transition: all 0.2s ease;
  font-family: inherit;
}
.search-input:focus {
  background-color: #ffffff;
  border-color: #0284c7;
  box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}

/* Cart Button */
.cart-btn {
  position: relative;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #334155;
  text-decoration: none;
  background: transparent;
  transition: background-color 0.15s ease;
}
.cart-btn:hover {
  background-color: #f1f5f9;
}
.cart-badge {
  position: absolute;
  top: -2px;
  right: -2px;
  background-color: #0284c7;
  color: #ffffff;
  font-size: 0.65rem;
  font-weight: 700;
  border-radius: 9999px;
  min-width: 18px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 4px;
}

/* Auth Buttons */
.auth-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.45rem 1rem;
  font-size: 0.875rem;
  font-weight: 600;
  border-radius: 9999px;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: all 0.15s ease;
  font-family: inherit;
}
.auth-btn-ghost {
  color: #334155;
  background: transparent;
}
.auth-btn-ghost:hover {
  color: #0f172a;
  background-color: #f1f5f9;
}
.auth-btn-primary {
  background-color: #0284c7;
  color: #ffffff;
}
.auth-btn-primary:hover {
  background-color: #0369a1;
}
.auth-btn-outline {
  color: #334155;
  background: transparent;
  border: 1px solid #cbd5e1;
}
.auth-btn-outline:hover {
  background-color: #f8fafc;
  color: #0f172a;
}
.auth-btn-danger {
  color: #ef4444;
  background: transparent;
}
.auth-btn-danger:hover {
  background-color: #fee2e2;
}
</style>
