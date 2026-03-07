<template>
  <section class="shop-page py-4 py-md-5">
    <div class="shop-page__header d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
      <div>
        <p class="shop-page__eyebrow mb-2">Storefront</p>
        <h1 class="shop-page__title mb-1">Shop</h1>
        <p class="shop-page__subtitle mb-0 text-body-secondary">
          Browse the catalog and add products directly from the listing.
        </p>
      </div>
      <p class="shop-page__count mb-0">{{ products.length }} products</p>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
      <div v-for="product in products" :key="product.id" class="col">
        <product :product="product" />
      </div>
    </div>
  </section>
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
