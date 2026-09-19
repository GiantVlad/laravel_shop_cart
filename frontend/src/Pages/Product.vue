<template>
  <article class="product-card card h-100 border-0 shadow-sm">
    <a :href="`/shop/${product.id}`" class="product-card__media-link">
      <div class="product-card__media">
        <img
          class="product-card__image"
          :alt="product.name"
          :src="`/images/${product.image}`"
        >
      </div>
    </a>
      <div class="product-body p-3">
        <h3 class="product-card__title mb-2">
          <a :href="`/shop/${product.id}`" class="product-card__title-link text-decoration-none">{{ product.name }}</a>
        </h3>
        <p class="product-card__price mb-3">${{ formattedPrice }}</p>
        <Link
          href="/cart/add-to-cart"
          method="post"
          as="button"
          type="button"
          class="btn btn-primary btn-sm px-4 rounded-pill fw-semibold shadow-sm"
          :data="{ productId: product.id, isRelated: 0, productQty: 1 }"
        >
          Add to cart
        </Link>
      </div>
  </article>
</template>

<script>
import Layout from "../Layouts/MainLayout.vue";
import { Link } from '@inertiajs/vue3'

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
.product-card {
  border-radius: 1rem;
  overflow: hidden;
  background: linear-gradient(180deg, #ffffff 0%, #fbf6ef 100%);
}

.product-card__media-link {
  display: block;
  background:
    radial-gradient(circle at top left, rgba(207, 163, 94, 0.18), transparent 42%),
    linear-gradient(135deg, #fffaf2 0%, #f2eadf 100%);
}

.product-card__media {
  aspect-ratio: 4 / 3;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.25rem;
}

.product-card__image {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.product-card__title-link {
  color: #1c1a17;
}

.product-card__title {
  line-height: 1.35;
  min-height: 2.7em;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-card__description {
  font-size: 0.95rem;
  line-height: 1.55;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-card__price {
  font-size: 1.1rem;
  font-weight: 700;
  color: #201a12;
}
</style>
