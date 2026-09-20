<template>
  <div class="product-detail-page px-3 py-5">
    <!-- Back Navigation -->
    <div class="mb-4">
      <Link href="/shop" class="p-button p-button-sm p-button-text p-button-secondary inline-flex align-items-center gap-2 no-underline">
        <i class="pi pi-arrow-left text-xs"></i>
        <span>Back to catalog</span>
      </Link>
    </div>

    <!-- Product Card Container -->
    <div class="surface-card border-1 border-surface-200 border-round-2xl p-4 md:p-6 shadow-1">
      <div class="grid align-items-center">
        
        <!-- Media Column -->
        <div class="col-12 md:col-6 flex align-items-center justify-content-center bg-surface-50 border-round-xl p-4 md:p-5" style="min-height: 380px;">
          <img
            :src="`/images/${product.image}`"
            :alt="product.name"
            class="max-w-full max-h-30rem object-fit-contain"
            style="max-height: 400px;"
          />
        </div>

        <!-- Info Column -->
        <div class="col-12 md:col-6 p-3 md:pl-5 flex flex-column justify-content-between">
          <div>
            <div class="flex align-items-center gap-2 mb-2">
              <span class="inline-block px-2 py-1 text-xs font-semibold border-round bg-surface-100 text-surface-700">In Stock</span>
            </div>

            <h1 class="text-3xl font-bold text-900 tracking-tight m-0 mb-3">
              {{ product.name }}
            </h1>

            <div class="text-3xl font-bold text-primary mb-3">
              ${{ formattedPrice }}
            </div>

            <p class="text-600 line-height-3 text-base m-0 mb-4">
              {{ product.description }}
            </p>

            <!-- Specifications / Properties -->
            <div v-if="product.properties && product.properties.length" class="mb-4 pt-3 border-top-1 border-surface-200">
              <h5 class="text-xs uppercase text-500 font-bold tracking-wider mb-3">Specifications</h5>
              <div class="grid text-sm m-0">
                <template v-for="prop in product.properties" :key="prop.id">
                  <div class="col-5 text-500 py-1 font-medium">{{ prop.properties?.name || 'Specification' }}:</div>
                  <div class="col-7 text-900 py-1 font-semibold">{{ prop.value }}</div>
                </template>
              </div>
            </div>
          </div>

          <!-- Add to Cart Form -->
          <div class="pt-3 border-top-1 border-surface-200 flex flex-wrap align-items-center gap-3">
            <div class="flex align-items-center gap-2">
              <label for="quantity" class="text-sm font-semibold text-600">Qty:</label>
              <input
                id="quantity"
                v-model.number="quantity"
                type="number"
                min="1"
                max="99"
                class="p-inputtext p-component text-center font-bold"
                style="width: 70px;"
              />
            </div>

            <Link
              href="/cart/add-to-cart"
              method="post"
              as="button"
              type="button"
              class="p-button p-button-primary p-button-rounded px-4 py-2 font-semibold shadow-1 flex align-items-center justify-content-center gap-2 flex-grow-1"
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
  max-width: 1140px;
  margin: 0 auto;
}
.object-fit-contain {
  object-fit: contain;
}
</style>
