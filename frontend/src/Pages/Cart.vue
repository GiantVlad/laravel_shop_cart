<template>
  <div class="cart-page px-3 py-4">
    <!-- Header -->
    <div class="flex align-items-center justify-content-between mb-4 pb-2 border-bottom-1 border-surface-200">
      <div>
        <h1 class="text-3xl font-bold text-900 m-0">Your cart</h1>
        <p class="text-sm text-500 m-0 mt-1">
          {{ itemCount }} {{ itemCount === 1 ? 'item' : 'items' }} in your cart
        </p>
      </div>
      <Link href="/shop" class="no-underline">
        <Button label="Continue shopping" icon="pi pi-arrow-left" severity="secondary" outlined size="small" />
      </Link>
    </div>

    <!-- Empty state -->
    <Card v-if="items.length < 1" class="border-1 border-surface-200 border-round-xl shadow-1">
      <template #content>
        <div class="empty-state text-center py-6 px-3">
          <div class="inline-flex align-items-center justify-content-center bg-primary-50 border-circle mb-3"
               style="width: 72px; height: 72px;">
            <i class="pi pi-shopping-cart text-primary text-3xl"></i>
          </div>
          <h2 class="text-xl font-bold text-900 m-0 mb-1">Your cart is empty</h2>
          <p class="text-500 text-sm m-0 mb-4">Browse the catalogue and add something you like.</p>
          <Link href="/shop" class="no-underline">
            <Button label="Browse products" icon="pi pi-shopping-bag" />
          </Link>
        </div>
      </template>
    </Card>

    <div v-else class="grid">
      <!-- Items -->
      <div class="col-12 lg:col-8">
        <Card v-for="(item, idx) in items" :key="item.id"
              class="cart-item border-1 border-surface-200 border-round-xl shadow-1 mb-3">
          <template #content>
            <div class="flex flex-column sm:flex-row gap-3">
              <!-- Thumbnail -->
              <Link :href="`/shop/${item.id}`" class="cart-item__thumb flex align-items-center justify-content-center no-underline flex-shrink-0">
                <img :alt="item.name" :src="`/images/${item.image}`" class="cart-item__image" loading="lazy" />
              </Link>

              <!-- Details -->
              <div class="flex flex-column flex-grow-1 min-w-0">
                <div class="flex align-items-start justify-content-between gap-2">
                  <Link :href="`/shop/${item.id}`" class="cart-item__title no-underline font-semibold text-900 hover:text-primary transition-colors">
                    {{ item.name }}
                  </Link>
                  <Tag v-if="item.is_related" value="Recommended" severity="info" class="flex-shrink-0" />
                </div>

                <p class="cart-item__desc text-sm text-600 mt-1 mb-3">{{ item.description }}</p>

                <div class="flex flex-wrap align-items-end justify-content-between gap-3 mt-auto">
                  <!-- Quantity -->
                  <div class="flex flex-column gap-1">
                    <label class="text-xs font-semibold text-600" :for="`productQty${item.id}`">Quantity</label>
                    <InputNumber
                      :inputId="`productQty${item.id}`"
                      :modelValue="item.qty"
                      :min="1"
                      :max="99"
                      :step="1"
                      :useGrouping="false"
                      showButtons
                      buttonLayout="horizontal"
                      :allowEmpty="false"
                      size="small"
                      class="cart-qty"
                      @update:modelValue="value => onChangeQty(idx, value)"
                    />
                  </div>

                  <!-- Unit + row total -->
                  <div class="flex align-items-end gap-4">
                    <div class="text-right">
                      <div class="text-xs text-500">Unit price</div>
                      <div class="font-semibold text-900">{{ money(item.price) }}</div>
                    </div>
                    <div class="text-right">
                      <div class="text-xs text-500">Subtotal</div>
                      <div class="font-bold text-lg text-900">{{ money(rowTotal(item)) }}</div>
                    </div>
                  </div>

                  <Button
                    label="Remove"
                    icon="pi pi-trash"
                    severity="danger"
                    text
                    size="small"
                    :aria-label="`Remove ${item.name}`"
                    @click="remove(item)"
                  />
                </div>
              </div>
            </div>
          </template>
        </Card>

        <!-- Recommendation -->
        <template v-if="Object.keys(relatedProduct).length > 0">
          <div class="flex align-items-center gap-3 mt-5 mb-3">
            <i class="pi pi-sparkles text-primary text-xl"></i>
            <span class="font-bold text-lg text-900 white-space-nowrap flex-shrink-0">We also recommend</span>
            <Divider class="flex-grow-1" />
          </div>

          <Card class="border-1 border-surface-200 border-round-xl shadow-1">
            <template #content>
              <div class="flex flex-column sm:flex-row align-items-center gap-3">
                <Link :href="`/shop/${relatedProduct.id}`" class="cart-item__thumb flex align-items-center justify-content-center no-underline flex-shrink-0">
                  <img :alt="relatedProduct.name" :src="`/images/${relatedProduct.image}`" class="cart-item__image" loading="lazy" />
                </Link>
                <div class="flex flex-column flex-grow-1 min-w-0">
                  <Link :href="`/shop/${relatedProduct.id}`" class="cart-item__title no-underline font-semibold text-900 hover:text-primary transition-colors">
                    {{ relatedProduct.name }}
                  </Link>
                  <p class="cart-item__desc text-sm text-600 mt-1 mb-0">{{ relatedProduct.description }}</p>
                </div>
                <div class="flex flex-column align-items-end gap-2 flex-shrink-0">
                  <span class="font-bold text-lg text-900">{{ money(relatedProduct.price) }}</span>
                  <Button label="Add to cart" icon="pi pi-shopping-cart" size="small" @click="addRelated" />
                </div>
              </div>
            </template>
          </Card>
        </template>
      </div>

      <!-- Summary -->
      <div class="col-12 lg:col-4">
        <Card class="summary-card border-1 border-surface-200 border-round-xl shadow-1 lg:sticky" style="top: 6rem;">
          <template #title>
            <span class="text-lg font-bold text-900">Order summary</span>
          </template>
          <template #content>
            <div class="flex flex-column gap-4">
              <!-- Shipping -->
              <div class="flex flex-column gap-1">
                <label for="shipping-select" class="text-sm font-semibold text-900">Shipping method</label>
                <Select
                  inputId="shipping-select"
                  v-model="selectedShipping"
                  :options="shippingOptions"
                  optionLabel="display"
                  optionValue="id"
                  placeholder="Select shipping method"
                  class="w-full"
                >
                  <template #option="slotProps">
                    <div class="flex flex-column">
                      <span class="font-medium">{{ slotProps.option.label }}</span>
                      <span class="text-xs text-500">
                        {{ slotProps.option.time }} · {{ shippingRateLabel(slotProps.option) }}
                      </span>
                    </div>
                  </template>
                </Select>
              </div>

              <!-- Payment -->
              <div class="flex flex-column gap-1">
                <label for="payment-select" class="text-sm font-semibold text-900">Payment method</label>
                <Select
                  inputId="payment-select"
                  v-model="selectedPayment"
                  :options="paymentOptions"
                  optionLabel="label"
                  optionValue="id"
                  :disabled="!selectedShipping"
                  :placeholder="selectedShipping ? 'Select payment method' : 'Select a shipping method first'"
                  class="w-full"
                />
                <small v-if="!selectedShipping" class="text-500 text-xs">
                  Payment options depend on the shipping method.
                </small>
              </div>

              <Divider class="my-0" />

              <!-- Totals -->
              <div class="flex flex-column gap-2">
                <div class="flex justify-content-between text-sm">
                  <span class="text-600">Subtotal ({{ itemCount }} {{ itemCount === 1 ? 'item' : 'items' }})</span>
                  <span class="text-900 font-medium">{{ money(itemsTotal) }}</span>
                </div>
                <div class="flex justify-content-between text-sm">
                  <span class="text-600">Shipping</span>
                  <span class="text-900 font-medium">{{ shippingRate ? money(shippingRate) : '—' }}</span>
                </div>
                <Divider class="my-1" />
                <div class="flex justify-content-between align-items-center">
                  <span class="font-bold text-900">Total</span>
                  <span class="font-bold text-2xl text-primary">{{ money(total) }}</span>
                </div>
              </div>

              <Button
                label="Pay"
                icon="pi pi-credit-card"
                class="w-full"
                size="large"
                :loading="paying"
                :disabled="isPayDisabled"
                @click="pay"
              />
              <small v-if="isPayDisabled" class="text-500 text-center block">
                Choose a shipping and payment method to continue.
              </small>
            </div>
          </template>
        </Card>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import Layout from "../Layouts/MainLayout.vue";
import { Link, router } from '@inertiajs/vue3'
import Card from 'primevue/card'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import Divider from 'primevue/divider'
import Select from 'primevue/select'
import InputNumber from 'primevue/inputnumber'

export default {
  name: "Cart",
  props: {
    products: {
      type: Array,
      default: () => [],
    },
    shippingMethods: {
      type: Array,
      default: () => [],
    },
    relatedProduct: {
      type: Object,
      default: () => ({}),
    },
    payments: {
      type: Array,
      default: () => [],
    },
  },
  layout: Layout,
  components: { Link, Card, Button, Tag, Divider, Select, InputNumber },
  data() {
    return {
      total: 0,
      selectedShipping: null,
      selectedPayment: null,
      paymentOptions: [],
      items: [],
      paying: false,
    }
  },
  watch: {
    // Inertia reloads keep this component alive (preserveState: true), so a cart
    // change made server-side - adding the recommended product, removing an item -
    // only reaches the rendered list if the local copy is re-synced from the props.
    products: {
      handler(products) {
        this.items = [...products]
        this.subtotal(this.selectedShipping)
      },
    },
    async selectedShipping(val) {
      // Payment options belong to the shipping method: drop the previous choice
      // and ask the server which methods the new shipping method accepts.
      this.selectedPayment = null
      this.paymentOptions = []

      if (!val) {
        return
      }

      this.subtotal(val)
      try {
        await axios.post('/cart/change-shipping', {
          shippingMethodId: val,
          subtotal: this.total,
        })
        const { data } = await axios.get(`/cart/payment-methods/${val}`)
        this.paymentOptions = data?.payments ?? []
        this.refreshSharedCart()
      } catch (e) {
        console.log(e)
      }
    },
    selectedPayment(val) {
      if (!val) {
        return
      }
      axios.post('/cart/change-payment', {
        paymentMethodId: val,
      }).then(() => {
        this.refreshSharedCart()
      }).catch(e => {
        console.log(e)
      })
    },
  },
  computed: {
    isPayDisabled() {
      return !this.selectedShipping || !this.selectedPayment
    },
    itemCount() {
      return this.items.reduce((sum, item) => sum + Number(item.qty || 0), 0)
    },
    itemsTotal() {
      return Math.round(this.items.reduce((sum, item) => sum + (+item.price * +item.qty), 0) * 100) / 100
    },
    shippingRate() {
      if (!this.selectedShipping) {
        return 0
      }
      const method = this.shippingMethods.find(method => method.id === this.selectedShipping)
      return Number(method?.rate ?? 0)
    },
    shippingOptions() {
      return this.shippingMethods.map(method => ({
        ...method,
        display: `${method.label} · ${method.time} · ${this.shippingRateLabel(method)}`,
      }))
    },
  },
  methods: {
    money(value) {
      const amount = Number(value ?? 0)
      return '$' + amount.toFixed(2)
    },
    rowTotal(item) {
      return Math.round(+item.price * +item.qty * 100) / 100
    },
    shippingRateLabel(method) {
      const rate = Number(method?.rate ?? 0)
      return rate > 0 ? '$' + rate.toFixed(2) : 'Free'
    },
    pay() {
      let itemsInfo = {
        product_ids: [],
        productQty: [],
        isRelatedProduct: [],
        subtotal: this.total,
        related_product_id: this.relatedProduct?.id,
        paymentMethodId: this.selectedPayment,
        shippingMethodId: this.selectedShipping,
      };
      this.items.forEach(pr => {
        itemsInfo.product_ids.push(pr.id);
        itemsInfo.productQty.push(pr.qty);
        itemsInfo.isRelatedProduct.push(pr.is_related);
      })

      this.paying = true
      axios.post('/checkout',
          itemsInfo
      ).then(response => {
        if (response.data?.redirect_to) {
          window.location.href = response.data.redirect_to;
        } else {
          router.visit('/orders');
        }
      }).catch(e => {
        console.log(e);
      }).finally(() => {
        this.paying = false
      });
    },
    addRelated() {
      axios.post('/cart/add-related', {
        id: this.relatedProduct.id,
      }).then(() => {
        router.reload({ preserveScroll: true })
      }).catch(e => {
        console.log(e)
      });
    },
    onChangeQty(idx, value) {
      const newVal = Number(value);
      if (!Number.isFinite(newVal) || newVal < 1 || newVal > 99) {
        // revert the control to the stored value
        this.items[idx].qty = this.items[idx].qty;
        return;
      }
      if (newVal === this.items[idx].qty) {
        return;
      }
      axios.post('/cart/add-to-cart', {
        productId: this.items[idx].id,
        productQty: newVal,
        subtotal: this.total,
        updateQty: true,
      }).then(() => {
        this.items[idx].qty = newVal;
        this.subtotal(this.selectedShipping)
        this.refreshSharedCart()
      }).catch(e => {
        console.log(e)
      });
    },
    subtotal(val) {
      this.total = 0;
      if (this.selectedShipping) {
        const shippingMethod = this.shippingMethods.find(method => method.id === val)
        this.total += Number(shippingMethod?.rate ?? 0)
      }

      this.items.forEach(item => {
        this.total += (+item.price * +item.qty)
        item.rowTotal = Math.round(+item.price * +item.qty * 100) / 100
      })
      this.total = Math.round(this.total * 100) / 100
      return this.total;
    },
    remove(item) {
      const total = this.total - (+item.price * +item.qty)
      axios.post('/cart/remove-item', {
        productId: item.id,
        isRelated: Boolean(item.is_related),
        subtotal: total,
      }).then(response => {
        this.items = this.items.filter(i => i.id !== item.id)
        if (response.data.data.items === 0) {
          router.reload({ preserveScroll: true });
          return;
        }
        this.subtotal(this.selectedShipping)
        this.refreshSharedCart()
      }).catch(e => {
        console.log(e)
      });
    },
    refreshSharedCart() {
      router.reload({ preserveScroll: true, preserveState: true })
    }
  },
  created() {
    const method = this.shippingMethods.find(method => method.selected === true);
    this.selectedShipping = method !== undefined ? method.id : null;
    this.items = [...this.products];
    // The server sends the payment methods of the already selected shipping method.
    this.paymentOptions = method !== undefined ? [...this.payments] : [];
    if (this.items.length > 0) {
      this.subtotal(this.selectedShipping);
      const payMethod = this.paymentOptions.find(item => item.selected === true);
      this.selectedPayment = payMethod !== undefined ? payMethod.id : null;
    }
  },
}
</script>

<style scoped>
.cart-page {
  max-width: 1280px;
  margin: 0 auto;
}

.cart-item__thumb {
  width: 104px;
  height: 104px;
  background-color: #f8fafc;
  border: 1px solid var(--p-surface-200, #e2e8f0);
  border-radius: 12px;
  overflow: hidden;
}

.cart-item__image {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.cart-item__title {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.cart-item__desc {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.4;
}

.cart-qty {
  width: 9.5rem;
}

:deep(.cart-item .p-card-body),
:deep(.summary-card .p-card-body) {
  padding: 1.25rem;
}

:deep(.cart-item .p-card-content),
:deep(.summary-card .p-card-content) {
  padding: 0;
}

:deep(.summary-card .p-card-title) {
  margin-bottom: 0.75rem;
}
</style>