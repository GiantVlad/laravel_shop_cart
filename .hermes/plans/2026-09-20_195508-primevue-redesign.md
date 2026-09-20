# Plan: PrimeVue Modern Minimalist Redesign (Preserving Architecture)

## Goal
Integrate PrimeVue across the frontend to achieve a cohesive, minimalist, modern e-commerce UI (Shop catalog, Single Product page, Cart, and unified AppLayout) while keeping backend controllers, Inertia routes, and business logic 100% intact.

---

## Current Context & Assumptions
- **Stack**: Laravel 10 (RoadRunner + Inertia) + Vue 3.5 + Vite + Bootstrap 5 + PrimeVue 4 (Aura preset).
- **Current State**:
  - `primevue`, `@primevue/themes`, `primeicons` are installed in `frontend/package.json` and registered in `frontend/src/main.js`.
  - An experimental `frontend/src/Layouts/AppLayout.vue` was created, but it stripped out navigation features (Search, Cart badge, Auth state/Orders dropdown).
  - `ProductList.vue` failed to render pagination because the controller passes links in `products.links`, while `ProductList.vue` bound to a legacy empty `links` prop.
  - `Product.vue` is currently serving as both a single-item card and the `/shop/{id}` page, rendering an undersized card on the full page view.
- **Design Principles**:
  - Surface archetype: **Explore** for Shop catalog (filters, clean product grid, fast glanceability), **Inspect / Operate** for Single Product & Cart.
  - Minimalist aesthetic: Off-white canvas (`#f8fafc`), clean surface cards (`#ffffff`), subtle borders (`#e2e8f0`), deep slate typography (`#0f172a`), refined neutral shadows (`shadow-sm` / `shadow-md`), and a single cohesive brand accent (`#0284c7` sky/cyan or primary Aura theme).
  - Zero disruption to backend logic: All controller contracts, props, and endpoints remain untouched.

---

## Architecture & Proposed Approach
We will establish a unified layout hierarchy centered around an enhanced `AppLayout.vue` featuring a full PrimeVue-styled navigation bar (brand, search input, cart badge, user menu). We will decompose product presentation into a reusable `ProductCard.vue` (for grids) and a dedicated high-fidelity `Product.vue` (for the `/shop/{id}` detail page), resolve pagination by reading `products.links`, and migrate `Cart.vue` and `Orders.vue` to the same unified shell. Verification will be executed at each step with Vite build checks, RoadRunner resets, and headless browser inspection.

---

## Step-by-Step Implementation Tasks

### Task 1: Fix Pagination & Bindings in `ProductList.vue`
**Context**: In `ShopController::list()`, `$products` is a `LengthAwarePaginator`. Laravel serializes paginator links into `products.links`. The template currently reads `links` directly, which evaluates to `{}` (object with no length).
**File**: `frontend/src/Pages/ProductList.vue`

1. Update `frontend/src/Pages/ProductList.vue`:
```vue
<template>
  <div class="shop-page container py-4">
    <!-- Header title and count -->
    <div class="flex items-center justify-between mb-4 pb-2 border-b border-surface-200">
      <div>
        <h1 class="text-2xl font-bold text-surface-900 tracking-tight m-0">Products</h1>
        <p class="text-sm text-surface-500 m-0 mt-1">
          Showing {{ products?.from || 1 }} - {{ products?.to || (products?.data || products)?.length }} of {{ products?.total || (products?.data || products)?.length }} items
        </p>
      </div>
    </div>

    <!-- Product Grid -->
    <div class="row g-4">
      <div
        v-for="product in (products?.data || products)"
        :key="product?.id"
        class="col-12 col-sm-6 col-lg-4 col-xl-3 d-flex align-items-stretch"
      >
        <ProductCard :product="product" />
      </div>
    </div>

    <!-- Pagination -->
    <div class="mt-5 flex justify-center">
      <AdminPagination :links="paginationLinks" />
    </div>
  </div>
</template>

<script>
import ProductCard from "../Components/ProductCard.vue";
import Layout from "../Layouts/AppLayout.vue";
import AdminPagination from "./Admin/Shared/AdminPagination.vue";

export default {
  name: 'ProductList',
  components: { ProductCard, AdminPagination },
  props: {
    keyword: String,
    category: [String, Number],
    products: [Object, Array],
    links: [Array, Object],
  },
  layout: Layout,
  computed: {
    paginationLinks() {
      if (Array.isArray(this.products?.links) && this.products.links.length > 0) {
        return this.products.links;
      }
      if (Array.isArray(this.links) && this.links.length > 0) {
        return this.links;
      }
      return [];
    }
  }
}
</script>

<style scoped>
.shop-page {
  max-width: 1280px;
  margin: 0 auto;
}
</style>
```

2. **Verification Command**:
```bash
cd /Users/mac_mac/projects/lara_shop/frontend && npm run build
```
*Expected output*: `✓ built in ...` with exit code 0.

---

### Task 2: Create Reusable Minimalist `ProductCard.vue`
**Context**: Separate the catalog card component from the single product detail page.
**File**: `frontend/src/Components/ProductCard.vue`

1. Create `frontend/src/Components/ProductCard.vue`:
```vue
<template>
  <article class="product-card surface-card border-1 border-surface-200 border-round-xl overflow-hidden w-full flex flex-column transition-all transition-duration-200 hover:shadow-3">
    <!-- Image Media Container -->
    <Link :href="`/shop/${product.id}`" class="product-card__media-link block relative bg-surface-50 p-4">
      <div class="product-card__media flex align-items-center justify-content-center">
        <img
          :alt="product.name"
          :src="`/images/${product.image}`"
          class="product-card__image"
          loading="lazy"
        />
      </div>
    </Link>

    <!-- Body Info -->
    <div class="p-4 flex flex-column flex-grow-1 justify-content-between">
      <div>
        <h3 class="product-card__title m-0 mb-2">
          <Link
            :href="`/shop/${product.id}`"
            class="text-surface-900 font-semibold no-underline text-base hover:text-primary transition-colors transition-duration-150"
          >
            {{ product.name }}
          </Link>
        </h3>
        <p v-if="product.description" class="product-card__description text-surface-500 text-xs m-0 mb-3">
          {{ product.description }}
        </p>
      </div>

      <div class="pt-2 flex align-items-center justify-content-between border-top-1 border-surface-100 mt-auto">
        <span class="text-xl font-bold text-surface-900">${{ formattedPrice }}</span>
        <Link
          href="/cart/add-to-cart"
          method="post"
          as="button"
          type="button"
          class="p-button p-button-sm p-button-rounded p-button-primary px-3 shadow-1 font-medium flex align-items-center gap-1"
          :data="{ productId: product.id, isRelated: 0, productQty: 1 }"
        >
          <i class="pi pi-shopping-cart text-xs"></i>
          <span>Add</span>
        </Link>
      </div>
    </div>
  </article>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
  name: 'ProductCard',
  components: { Link },
  props: {
    product: {
      type: Object,
      required: true
    }
  },
  computed: {
    formattedPrice() {
      return this.product.price ? parseFloat(this.product.price).toFixed(2) : '0.00';
    }
  }
}
</script>

<style scoped>
.product-card {
  background: #ffffff;
}

.product-card__media-link {
  aspect-ratio: 4 / 3;
  overflow: hidden;
  background: #f8fafc;
}

.product-card__media {
  width: 100%;
  height: 100%;
}

.product-card__image {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  transition: transform 0.25s ease;
}

.product-card:hover .product-card__image {
  transform: scale(1.04);
}

.product-card__title {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.35;
  min-height: 2.7em;
}

.product-card__description {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.4;
}
</style>
```

2. **Verification Command**:
```bash
cd /Users/mac_mac/projects/lara_shop/frontend && npm run build
```
*Expected output*: `✓ built in ...` exit code 0.

---

### Task 3: Enhance `AdminPagination.vue` with Clean PrimeVue Styling
**Context**: Ensure active/disabled links, arrows, and page numbers render cleanly as PrimeVue buttons.
**File**: `frontend/src/Pages/Admin/Shared/AdminPagination.vue`

1. Update `frontend/src/Pages/Admin/Shared/AdminPagination.vue`:
```vue
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
  <nav v-if="links && links.length > 3" aria-label="Pagination" class="flex items-center gap-1">
    <template v-for="link in links" :key="`${link.label}-${link.url}`">
      <Link
        v-if="link.url"
        :href="link.url"
        class="p-button p-button-sm p-button-rounded transition-colors duration-150 min-w-2rem h-2rem flex items-center justify-center text-xs font-semibold"
        :class="link.active ? 'p-button-primary shadow-sm' : 'p-button-text p-button-secondary'"
        v-html="cleanLabel(link.label)"
      />
      <span
        v-else
        class="p-button p-button-sm p-button-rounded p-button-text p-button-secondary opacity-40 pointer-events-none min-w-2rem h-2rem flex items-center justify-center text-xs"
        v-html="cleanLabel(link.label)"
      />
    </template>
  </nav>
</template>
```

2. **Verification Command**:
```bash
cd /Users/mac_mac/projects/lara_shop/frontend && npm run build
```
*Expected output*: `✓ built in ...` exit code 0.

---

### Task 4: Upgrade `AppLayout.vue` to Full PrimeVue Unified Header
**Context**: Bring back header brand link, search bar, cart item counter badge, and user authentication actions, styled with PrimeVue Aura minimalism.
**File**: `frontend/src/Layouts/AppLayout.vue`

1. Update `frontend/src/Layouts/AppLayout.vue`:
```vue
<script setup>
import { computed, ref } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

const page = usePage()
const appName = computed(() => page.props.appName || 'Lara Shop')
const userName = computed(() => page.props.userName)
const cart = computed(() => page.props.cart)

const searchQuery = ref('')

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.get('/search', { keyword: searchQuery.value.trim() })
  } else {
    router.get('/shop')
  }
}
</script>

<template>
  <div class="min-h-screen flex flex-column bg-surface-50">
    <!-- Navbar Header -->
    <header class="bg-white border-bottom-1 border-surface-200 sticky top-0 z-5 shadow-xs">
      <div class="container max-w-7xl mx-auto px-4 h-4rem flex align-items-center justify-content-between gap-3">
        
        <!-- Brand -->
        <Link href="/shop" class="flex align-items-center gap-2 no-underline text-surface-900">
          <i class="pi pi-box text-primary text-2xl"></i>
          <span class="text-xl font-bold tracking-tight">{{ appName }}</span>
        </Link>

        <!-- Search Bar -->
        <form @submit.prevent="handleSearch" class="flex-grow-1 max-w-28rem mx-3">
          <div class="p-inputgroup w-full">
            <span class="p-inputgroup-addon bg-surface-50 border-surface-300">
              <i class="pi pi-search text-surface-400"></i>
            </span>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search products..."
              class="p-inputtext p-component w-full text-sm border-surface-300"
            />
          </div>
        </form>

        <!-- Right Navigation: Cart + Auth -->
        <div class="flex align-items-center gap-2">
          <!-- Cart Button -->
          <Link
            href="/cart"
            class="p-button p-button-text p-button-rounded p-button-secondary relative p-2"
            aria-label="Shopping Cart"
          >
            <i class="pi pi-shopping-cart text-xl"></i>
            <span
              v-if="cart?.count"
              class="absolute -top-1 -right-1 bg-primary text-white text-xs font-bold border-circle w-1.25rem h-1.25rem flex align-items-center justify-center shadow-1"
            >
              {{ cart.count }}
            </span>
          </Link>

          <!-- Guest: Login / Register -->
          <template v-if="!userName">
            <Link
              href="/login"
              class="p-button p-button-sm p-button-text p-button-secondary font-medium"
            >
              Log in
            </Link>
            <Link
              href="/register"
              class="p-button p-button-sm p-button-primary p-button-rounded font-medium px-3 shadow-1"
            >
              Sign up
            </Link>
          </template>

          <!-- Authenticated: User Menu & Orders -->
          <template v-else>
            <Link
              href="/orders"
              class="p-button p-button-sm p-button-text p-button-secondary font-medium"
            >
              <i class="pi pi-list mr-1"></i> Orders
            </Link>
            <Link
              href="/logout"
              method="post"
              as="button"
              type="button"
              class="p-button p-button-sm p-button-text p-button-danger font-medium"
            >
              <i class="pi pi-sign-out mr-1"></i> Logout
            </Link>
          </template>
        </div>

      </div>
    </header>

    <!-- Main View Content -->
    <main class="flex-grow-1">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-white border-top-1 border-surface-200 py-4 mt-auto">
      <div class="container max-w-7xl mx-auto px-4 text-center text-sm text-surface-500">
        &copy; {{ new Date().getFullYear() }} {{ appName }}. Minimalist E-Commerce powered by PrimeVue.
      </div>
    </footer>
  </div>
</template>

<style scoped>
.shadow-xs {
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04);
}
</style>
```

2. **Verification Command**:
```bash
cd /Users/mac_mac/projects/lara_shop/frontend && npm run build
```
*Expected output*: `✓ built in ...` exit code 0.

---

### Task 5: Redesign Single Product Page (`Product.vue`)
**Context**: Transform `Product.vue` into a modern product detail view when navigated to directly via `/shop/{id}`.
**File**: `frontend/src/Pages/Product.vue`

1. Update `frontend/src/Pages/Product.vue`:
```vue
<template>
  <div class="product-detail-page container py-5">
    <!-- Breadcrumb & Back -->
    <div class="mb-4">
      <Link href="/shop" class="p-button p-button-text p-button-sm p-button-secondary flex align-items-center gap-2 no-underline">
        <i class="pi pi-arrow-left text-xs"></i>
        <span>Back to catalog</span>
      </Link>
    </div>

    <!-- Product Card Container -->
    <div class="surface-card border-1 border-surface-200 border-round-2xl p-4 md:p-6 shadow-2">
      <div class="grid">
        
        <!-- Media Column -->
        <div class="col-12 md:col-6 flex align-items-center justify-content-center bg-surface-50 border-round-xl p-5">
          <div class="product-media-wrapper w-full flex align-items-center justify-content-center">
            <img
              :src="`/images/${product.image}`"
              :alt="product.name"
              class="product-detail__image max-w-full max-h-30rem object-fit-contain"
            />
          </div>
        </div>

        <!-- Info Column -->
        <div class="col-12 md:col-6 p-4 md:pl-6 flex flex-column justify-content-between">
          <div>
            <div class="flex items-center gap-2 mb-2">
              <span class="inline-block px-2 py-1 text-xs font-semibold rounded bg-surface-100 text-surface-700">
                In Stock
              </span>
            </div>

            <h1 class="text-3xl font-bold text-surface-900 tracking-tight m-0 mb-3">
              {{ product.name }}
            </h1>

            <div class="text-3xl font-extrabold text-primary mb-4">
              ${{ formattedPrice }}
            </div>

            <p class="text-surface-600 line-height-3 text-base m-0 mb-5">
              {{ product.description }}
            </p>

            <!-- Specifications / Properties -->
            <div v-if="product.properties && product.properties.length" class="mb-5 border-top-1 border-surface-200 pt-4">
              <h4 class="text-sm font-semibold text-surface-900 uppercase tracking-wider mb-3">
                Specifications
              </h4>
              <dl class="grid text-sm m-0">
                <template v-for="prop in product.properties" :key="prop.id">
                  <dt class="col-5 font-medium text-surface-500 py-1">{{ prop.properties?.name || 'Property' }}:</dt>
                  <dd class="col-7 font-semibold text-surface-900 py-1 m-0">{{ prop.value }}</dd>
                </template>
              </dl>
            </div>
          </div>

          <!-- Add to Cart Form -->
          <div class="border-top-1 border-surface-200 pt-4 flex flex-wrap align-items-center gap-3">
            <div class="flex align-items-center gap-2">
              <label for="quantity" class="text-sm font-semibold text-surface-700">Qty:</label>
              <input
                id="quantity"
                v-model.number="quantity"
                type="number"
                min="1"
                max="99"
                class="p-inputtext p-component text-center w-5rem text-sm font-semibold border-surface-300"
              />
            </div>

            <Link
              href="/cart/add-to-cart"
              method="post"
              as="button"
              type="button"
              class="p-button p-button-primary p-button-rounded px-5 py-3 shadow-2 font-semibold flex align-items-center gap-2 flex-grow-1 justify-content-center"
              :data="{ productId: product.id, isRelated: 0, productQty: quantity }"
            >
              <i class="pi pi-shopping-bag"></i>
              <span>Add to Cart</span>
            </Link>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'
import Layout from "../Layouts/AppLayout.vue";

export default {
  name: 'Product',
  components: { Link },
  props: {
    product: {
      type: Object,
      required: true
    }
  },
  layout: Layout,
  data() {
    return {
      quantity: 1
    }
  },
  computed: {
    formattedPrice() {
      return this.product.price ? parseFloat(this.product.price).toFixed(2) : '0.00';
    }
  }
}
</script>

<style scoped>
.product-detail-page {
  max-width: 1100px;
  margin: 0 auto;
}

.product-detail__image {
  object-fit: contain;
}
</style>
```

2. **Verification Command**:
```bash
cd /Users/mac_mac/projects/lara_shop/frontend && npm run build
```
*Expected output*: `✓ built in ...` exit code 0.

---

### Task 6: Modernize `Cart.vue` to Match AppLayout & Aura Styling
**Context**: Apply `AppLayout` and clean table/card layout with PrimeVue buttons to the cart page.
**File**: `frontend/src/Pages/Cart.vue`

1. Update `frontend/src/Pages/Cart.vue`:
```vue
<template>
  <div class="cart-page container py-5">
    <h1 class="text-2xl font-bold text-surface-900 mb-4">Shopping Cart</h1>

    <!-- Empty State -->
    <div v-if="!items || items.length < 1" class="surface-card border-1 border-surface-200 border-round-2xl p-6 text-center shadow-1">
      <i class="pi pi-shopping-cart text-5xl text-surface-400 mb-3"></i>
      <h3 class="text-xl font-semibold text-surface-800 m-0 mb-2">Your cart is empty</h3>
      <p class="text-surface-500 text-sm m-0 mb-4">Looks like you haven't added anything to your cart yet.</p>
      <Link href="/shop" class="p-button p-button-primary p-button-rounded font-medium px-4 shadow-1">
        Start Shopping
      </Link>
    </div>

    <!-- Cart Items Grid -->
    <div v-else class="grid">
      <!-- Items list -->
      <div class="col-12 lg:col-8">
        <div class="surface-card border-1 border-surface-200 border-round-2xl p-4 shadow-1 mb-4">
          <div
            v-for="(item, idx) in items"
            :key="item.id"
            class="flex flex-column sm:flex-row align-items-center gap-4 py-3 border-bottom-1 border-surface-100 last:border-bottom-0"
          >
            <Link :href="`/shop/${item.id}`" class="w-5rem h-5rem bg-surface-50 border-round-lg flex align-items-center justify-content-center p-2 flex-shrink-0">
              <img :src="`/images/${item.image}`" :alt="item.name" class="max-w-full max-h-full object-fit-contain" />
            </Link>

            <div class="flex-grow-1 text-center sm:text-left">
              <h4 class="text-base font-semibold text-surface-900 m-0 mb-1">
                <Link :href="`/shop/${item.id}`" class="no-underline text-surface-900 hover:text-primary">
                  {{ item.name }}
                </Link>
              </h4>
              <p class="text-xs text-surface-500 m-0 mb-2">${{ item.price }} each</p>
              <button
                type="button"
                class="p-button p-button-text p-button-danger p-button-sm p-0 text-xs flex align-items-center gap-1"
                @click="remove(item)"
              >
                <i class="pi pi-trash text-xs"></i>
                <span>Remove</span>
              </button>
            </div>

            <div class="flex align-items-center gap-3">
              <div class="flex align-items-center gap-1">
                <label class="text-xs text-surface-600 font-medium">Qty:</label>
                <input
                  type="number"
                  min="1"
                  max="99"
                  class="p-inputtext p-component text-center w-4rem text-sm border-surface-300"
                  :value="item.qty"
                  @change="onChangeQty(idx, $event)"
                />
              </div>
              <span class="text-base font-bold text-surface-900 w-5rem text-right">${{ item.rowTotal }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Summary Sidebar -->
      <div class="col-12 lg:col-4">
        <div class="surface-card border-1 border-surface-200 border-round-2xl p-4 shadow-1">
          <h3 class="text-lg font-bold text-surface-900 m-0 mb-4 pb-2 border-bottom-1 border-surface-200">Order Summary</h3>
          <div class="flex justify-content-between text-sm text-surface-600 mb-2">
            <span>Subtotal</span>
            <span>${{ total }}</span>
          </div>
          <div class="flex justify-content-between text-sm text-surface-600 mb-3">
            <span>Shipping</span>
            <span class="text-green-600 font-medium">Free</span>
          </div>
          <div class="flex justify-content-between text-lg font-bold text-surface-900 pt-3 border-top-1 border-surface-200 mb-4">
            <span>Total</span>
            <span class="text-primary">${{ total }}</span>
          </div>
          <button
            type="button"
            class="p-button p-button-primary p-button-rounded w-full py-3 shadow-2 font-bold justify-content-center"
            @click="submitOrder"
          >
            Proceed to Checkout
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Layout from "../Layouts/AppLayout.vue";
import { Link, router } from '@inertiajs/vue3'

export default {
  name: 'Cart',
  components: { Link },
  layout: Layout,
  props: ['items', 'total'],
  methods: {
    onChangeQty(idx, event) {
      const qty = parseInt(event.target.value)
      if (qty > 0) {
        router.post('/cart/change-qty', { idx, qty })
      }
    },
    remove(item) {
      router.post('/cart/remove-from-cart', { productId: item.id })
    },
    submitOrder() {
      router.visit('/checkout')
    }
  }
}
</script>

<style scoped>
.cart-page {
  max-width: 1100px;
  margin: 0 auto;
}
</style>
```

2. **Verification Command**:
```bash
cd /Users/mac_mac/projects/lara_shop/frontend && npm run build
```
*Expected output*: `✓ built in ...` exit code 0.

---

### Task 7: Full Integration Verification (RoadRunner + Backend Tests + Browser Verification)
1. **Reset RoadRunner**:
```bash
docker compose exec roadrunner rr -c /etc/.rr.yaml reset http
```
*Expected output*: `resetting plugin: [http]` and `plugin reset: [http]`.

2. **Run Backend Tests**:
```bash
docker compose exec roadrunner php artisan test tests/Feature/HttpGetShopTest.php
```
*Expected output*: `PASS  Tests\Feature\HttpGetShopTest` (all assertions pass).

3. **Browser Smoke Check**:
Inspect live endpoints with `browser_exec`:
- `http://localhost/shop` (verify product grid, header search, cart count, pagination controls)
- `http://localhost/shop/1` (verify high-res product detail layout, specifications, Add to Cart)
- `http://localhost/cart` (verify cart summary and empty/filled state)

---

## Risks, Tradeoffs & Open Questions
- **CSS Precedence / Bootstrap Conflict**: Bootstrap CSS is currently imported alongside PrimeVue in `main.js`. If Bootstrap utility classes override PrimeVue button padding, we can scope or replace remaining Bootstrap classes with PrimeFlex/standard CSS utilities.
- **Cart API Routes**: Ensure all cart operations (`/cart/add-to-cart`, `/cart/remove-from-cart`, `/cart/change-qty`) keep exact parameter names (`productId`, `productQty`, `isRelated`) so backend sessions stay consistent.
- **Image Assets**: Verify all sample product images in `/images/prod*.jpg` exist and display cleanly in modern responsive aspect-ratio containers.
