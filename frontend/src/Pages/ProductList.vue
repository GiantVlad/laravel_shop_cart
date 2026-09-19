<template>
  <div class="shop-page container py-5">
    <div class="row g-4">
      <div v-for="product in (products?.data || products)" :key="product?.id" class="col-sm-6 col-lg-4 col-xl-3">
        <Product :product="product"/>
      </div>
    </div>
    <AdminPagination :links="links" />
  </div>
</template>

<script>
import Product from "./Product.vue";
import Layout from "../Layouts/MainLayout.vue";
import AdminPagination from "./Admin/Shared/AdminPagination.vue";

export default {
  name: 'ProductList',
  components: { Product, AdminPagination },
  props: ['keyword', 'category', 'products', 'links'],
  layout: Layout,
}
</script>

<style scoped>
.shop-page {
  max-width: 1200px;
  margin: 0 auto;
}
.shop-page__header {
  padding-top: 2rem;
}
.shop-page__title {
  font-size: clamp(2rem, 5vw, 3.5rem);
  font-weight: 800;
  letter-spacing: -0.03em;
  color: #111;
}
.shop-page__count {
  color: #777;
  font-size: 0.95rem;
  margin-top: 0.25rem;
}
</style>
