<template>
  <article class="surface-card shadow-2 border-round-xl overflow-hidden h-full" style="background: linear-gradient(180deg, #fff 0%, #fbf6ef 100%);">
    <a :href="`/shop/${product.id}`" class="block" style="background: linear-gradient(135deg,#fffaf2 0%,#f2eadf 100%);">
      <div class="flex align-items-center justify-content-center" style="aspect-ratio:4/3;padding:1.25rem;">
        <img
          :alt="product.name"
          :src="`/images/${product.image}`"
          class="w-full h-full object-fit-contain"
        />
      </div>
    </a>
    <div class="p-4">
      <h3 class="mb-2">
        <a :href="`/shop/${product.id}`" class="no-underline text-900 font-semibold text-lg line-height-normal">{{ product.name }}</a>
      </h3>
      <p class="text-xl font-bold mb-3" style="color:#201a12;">${{ formattedPrice }}</p>
      <Link
        href="/cart/add-to-cart"
        method="post"
        as="button"
        class="p-button p-button-sm p-button-rounded p-button-primary shadow-2 font-semibold px-4"
        :data="{ productId: product.id, isRelated: 0, productQty: 1 }"
      >
        Add to cart
      </Link>
    </div>
  </article>
</template>

<script>
import { Link } from '@inertiajs/vue3'
import Layout from "../Layouts/AppLayout.vue";

export default {
  name: 'Product',
  components: { Link },
  props: ['product'],
  layout: Layout,
  computed: {
    formattedPrice() {
      return this.product.price ? parseFloat(this.product.price).toFixed(2) : '0.00';
    }
  },
}
</script>

<style scoped>
.object-fit-contain { object-fit: contain; }
.no-underline { text-decoration: none; }
</style>
