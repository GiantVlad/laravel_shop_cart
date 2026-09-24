<template>
  <Card class="product-card w-full h-full shadow-1 border-1 border-surface-200 border-round-xl overflow-hidden flex flex-column">
    <template #header>
      <a
        :href="`/shop/${product.id}`"
        target="_blank"
        rel="noopener"
        class="block bg-surface-50 no-underline overflow-hidden"
      >
        <div class="product-card__image-container flex align-items-center justify-content-center p-3">
          <img
            :alt="product.name"
            :src="`/images/${product.image}`"
            class="product-card__image"
            loading="lazy"
          />
        </div>
      </a>
    </template>
    
    <template #title>
      <a
        :href="`/shop/${product.id}`"
        target="_blank"
        rel="noopener"
        class="product-card__title text-900 no-underline font-semibold hover:text-primary transition-colors transition-duration-150 block"
      >
        {{ product.name }}
      </a>
    </template>

    <template #content>
      <div class="product-card__price font-bold text-xl text-900">
        ${{ formattedPrice }}
      </div>
    </template>

    <template #footer>
      <Link
        href="/cart/add-to-cart"
        method="post"
        as="button"
        type="button"
        class="add-to-cart-btn"
        :data="{ productId: product.id, isRelated: 0, productQty: 1 }"
      >
        <i class="pi pi-shopping-cart text-sm"></i>
        <span>Add to cart</span>
      </Link>
    </template>
  </Card>
</template>

<script>
import { Link } from '@inertiajs/vue3'
import Card from 'primevue/card'

export default {
  name: 'ProductCard',
  components: { Link, Card },
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
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.12) !important;
}

.product-card__image-container {
  height: 200px;
  background-color: #f8fafc;
}

.product-card__image {
  max-width: 100%;
  max-height: 100%;
  width: auto;
  height: auto;
  object-fit: contain;
  transition: transform 0.25s ease;
}

.product-card:hover .product-card__image {
  transform: scale(1.05);
}

.product-card__title {
  font-size: 0.95rem;
  line-height: 1.35;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 2.6em;
  color: #0f172a;
}

.product-card__price {
  color: #0f172a;
}

.add-to-cart-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  width: 100%;
  padding: 0.55rem 1rem;
  background-color: #0284c7;
  color: #ffffff;
  border: 1px solid #0284c7;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.2s ease;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  font-family: inherit;
}

.add-to-cart-btn:hover {
  background-color: #0369a1;
  border-color: #0369a1;
  box-shadow: 0 4px 6px -1px rgba(2, 132, 199, 0.2);
}

.add-to-cart-btn:active {
  transform: scale(0.98);
}

:deep(.p-card-body) {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  padding: 1.25rem;
}

:deep(.p-card-caption) {
  margin-bottom: 0.5rem;
}

:deep(.p-card-content) {
  padding: 0;
  margin-bottom: auto;
}

:deep(.p-card-footer) {
  padding: 0.75rem 0 0 0;
  margin-top: auto;
}
</style>
