<template>
  <div class="row">
    <div v-for="product in products" :key="product.id" class="col-4">
      <product :product="product"/>
    </div>
  </div>
</template>

<script>
import Product from "./Product.vue";
import Layout from "../Layouts/MainLayout.vue";

export default {
  name: 'ProductList',
  components: { Product },
  props: ['keyword', 'category', 'products'],
  layout: Layout,
}
</script>

<style scoped>
.shop-page__eyebrow {
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: #8d5d22;
}

.shop-page__title {
  font-size: clamp(2rem, 4vw, 3rem);
  line-height: 1;
  color: #1c1a17;
}

.shop-page__count {
  font-size: 0.95rem;
  color: #6c675f;
  white-space: nowrap;
}
</style>
