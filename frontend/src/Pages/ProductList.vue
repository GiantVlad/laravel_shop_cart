<template>
  <div class="shop-page px-3 py-4">
    <!-- Header title and count -->
    <div class="flex align-items-center justify-content-between mb-4 pb-2 border-bottom-1 border-surface-200">
      <div>
        <h1 class="text-3xl font-bold text-900 m-0">Products</h1>
        <p class="text-sm text-500 m-0 mt-1">
          Showing {{ products?.from || 1 }} - {{ products?.to || (products?.data || products)?.length }} of {{ products?.total || (products?.data || products)?.length }} items
        </p>
      </div>
    </div>

    <!-- Product Grid -->
    <div class="grid">
      <div
        v-for="product in (products?.data || products)"
        :key="product?.id"
        class="col-12 sm:col-6 lg:col-4 xl:col-3 flex"
      >
        <ProductCard :product="product" />
      </div>
    </div>

    <!-- Pagination -->
    <div class="mt-5 flex justify-content-center">
      <ShopPagination :paginator="products" />
    </div>
  </div>
</template>

<script>
import ProductCard from "../Components/ProductCard.vue";
import Layout from "../Layouts/AppLayout.vue";
import ShopPagination from "../Components/ShopPagination.vue";

export default {
  name: 'ProductList',
  components: { ProductCard, ShopPagination },
  props: {
    keyword: String,
    category: [String, Number],
    products: [Object, Array],
    links: [Array, Object],
  },
  layout: Layout,
}
</script>

<style scoped>
.shop-page {
  max-width: 1280px;
  margin: 0 auto;
}
</style>
