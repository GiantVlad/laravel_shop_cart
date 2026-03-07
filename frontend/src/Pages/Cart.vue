<template>
  <div>
    <div v-if="items.length < 1">
      <div class="py-5 text-center">
        <h4>Your cart is empty</h4>
      </div>
    </div>
    <div v-else>
      <div v-for="(item, idx) in items" :key="item.id" class="product-row">
        <div class="row">
          <input type="hidden" name="productId" :value="item.id">
          <div class="col-md-2">
            <Link :href="`/shop/${item.id}`">
              <img class="img-thumbnail"
                   :alt="'product id '+item.id"
                   height="240"
                   :src="'images/'+item.image">
            </Link>
          </div>
          <div class="col-md-5">
            <h4>{{ item.name }}</h4>
            <p>{{ item.description }}</p>
            <button class="btn btn-link" :id="'remove-'+item.id" type="button" @click="remove(item)">
              Remove
            </button>
          </div>
          <div class="col-md-2 form-group">
            <label class="control-label" :for="'productQty'+item.id">QTY</label>
            <input type="number" class="form-control" :name="'productQty'+item.id"
                   :id="'productQty'+item.id" placeholder="QTY" :value="item.qty"
                   min="1" max="99" required @change="onChangeQty(idx, $event)">
          </div>
          <div class="col-md-1"><p>Price: <span :id="'price'+item.id">{{ item.price }}</span></p>
          </div>
          <div class="col-md-1"><p :id="'row-total-'+item.id">Total: {{ item.rowTotal }}</p></div>
          <input type="hidden" name="isRelatedProduct" v-model="item.is_related">
        </div>
        <hr/>
      </div>
      <div class="row">
        <div class="col-md-4 col-md-offset-7">
          <div class="form-group">
            <label for="shipping-select">Select shipping method: </label>
            <select class="form-control" id="shipping-select" v-model="selectedShipping">
              <option v-if="!selectedShipping" :value="null">Select shipping method...</option>
              <option v-for="method in shippingMethods" :key="method.id" :value="method.id">
                {{ method.label + ', ' + method.time + ', ' + method.rate }}
              </option>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4 col-md-offset-7">
          <div class="form-group">
            <label for="payment-select">Select payment method: </label>
            <select class="form-control" id="payment-select" v-model="selectedPayment">
              <option v-if="!selectedPayment" :value="null">Select payment method...</option>
              <option v-for="method in payments" :key="method.id" :value="method.id">
                {{ method.label }}
              </option>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-10">
          <p class="text-right">Subtotal: <span id="subtotal">{{ total }}</span></p>
          <input type="hidden" name="subtotal" :value="total">
        </div>
        <div class="col-md-2">
          <button type="button" id="checkout" class="btn btn-primary"
                  :disabled="isPayDisabled"
                  @click="pay"
          >
            Pay
          </button>
        </div>
      </div>
      <template v-if="Object.keys(relatedProduct).length > 0">
        <hr/>
        <h3 class="text-center">We also recommend:</h3>
        <div class="row">
          <div class="col-md-2">
            <Link :href="`/shop/${relatedProduct.id}`">
              <img class="img-thumbnail" width="304" height="236"
                   :src="'images/'+relatedProduct.image">
            </Link>
          </div>
          <div class="col-md-5">
            <h4>{{ relatedProduct.name }}</h4>
            <p>{{ relatedProduct.description }}</p>
          </div>

          <div class="col-md-2"><p>Price: {{ relatedProduct.price }}</p></div>
          <div class="col-md-2">
            <button type="button" class="btn btn-primary add-related" @click="addRelated">
              Add to Cart
            </button>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import Layout from "../Layouts/MainLayout.vue";
import { Link, router } from '@inertiajs/vue3'

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
  components: { Link },
  data() {
    return {
      total: 0,
      selectedShipping: null,
      selectedPayment: null,
      items: [],
    }
  },
  watch: {
    selectedShipping(val) {
      if (!val) {
        return
      }
      this.subtotal(val)
      axios.post('/cart/change-shipping', {
        shippingMethodId: val,
        subtotal: this.total,
      }).then(() => {
        this.refreshSharedCart()
      }).catch(e => {
        console.log(e)
      })
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
  },
  methods: {
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
    onChangeQty(idx, e) {
      const newVal = Number(e.target.value);
      if (newVal < 1 || newVal > 99) {
        e.target.value = this.items[idx].qty;
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
      })
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
      axios.post(this.$baseUrl + '/cart/remove-item', {
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
    if (this.items.length > 0) {
      this.subtotal(this.selectedShipping);
      const payMethod = this.payments.find(item => item.selected === true);
      this.selectedPayment = payMethod !== undefined ? payMethod.id : null;
    }
  },
}
</script>

<style scoped>
</style>
