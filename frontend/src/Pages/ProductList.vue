<template>
  <div class="shop-page px-3 py-4">
    <!-- Header title and count -->
    <div class="flex align-items-center justify-content-between mb-4 pb-2 border-bottom-1 border-surface-200">
      <div>
        <h1 class="text-3xl font-bold text-900 m-0">Products</h1>
        <p class="text-sm text-500 m-0 mt-1">
          Showing {{ products?.from || (productItems.length ? 1 : 0) }} -
          {{ products?.to || productItems.length }} of
          {{ products?.total || productItems.length }} items
        </p>
      </div>
    </div>

    <!-- Left column (categories + filters) + product grid -->
    <div class="shop-layout">
      <ShopSidebar :categories="categories" :properties="properties" />

      <div class="shop-layout__main">
        <div v-if="productItems.length" class="grid">
          <div
            v-for="product in productItems"
            :key="product.id"
            class="col-12 sm:col-6 xl:col-4 flex"
          >
            <ProductCard :product="product" />
          </div>
        </div>

        <div v-else class="shop-empty">
          <i class="pi pi-search text-3xl text-400"></i>
          <p class="m-0 mt-2 text-500">No products match the selected filters.</p>
        </div>

        <!-- Pagination -->
        <div class="mt-5 flex justify-content-center">
          <ShopPagination :paginator="products" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ProductCard from "../Components/ProductCard.vue";
import Layout from "../Layouts/AppLayout.vue";
import ShopPagination from "../Components/ShopPagination.vue";
import ShopSidebar from "../Components/ShopSidebar.vue";

export default {
  name: 'ProductList',
  components: { ProductCard, ShopPagination, ShopSidebar },
  props: {
    keyword: String,
    category: [String, Number],
    products: [Object, Array],
    categories: { type: Array, default: () => [] },
    properties: { type: Array, default: () => [] },
  },
  computed: {
    // /shop and /search both render this page; /search passes a plain array.
    productItems() {
      return this.products?.data || this.products || [];
    },
  },
  layout: Layout,
}
</script>

<style scoped>
.shop-page {
  max-width: 1280px;
  margin: 0 auto;
}

.shop-layout {
  display: grid;
  grid-template-columns: 260px minmax(0, 1fr);
  gap: 2rem;
  align-items: start;
}

.shop-layout__main {
  min-width: 0;
}

.shop-empty {
  text-align: center;
  padding: 4rem 1rem;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
}

@media (max-width: 991px) {
  .shop-layout {
    grid-template-columns: minmax(0, 1fr);
    gap: 1.25rem;
  }
}
</style>
