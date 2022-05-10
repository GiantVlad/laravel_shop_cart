<template>
  <div>
    <div class="product-row" v-for="(item, idx) in items">
      <div class="row">
        <input type="hidden" name="productId" :value="item.id">
        <div class="col-md-2">
          <a :href="'/product/'+ item.id">
            <img class="img-thumbnail"
                 :alt="'product id '+item.id"
                 height="240"
                 :src="'images/'+item.image">
          </a>
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
            <option v-for="method in shipping" :value="method.id">
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
            <option v-for="method in payments" :value="method.id">
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
          <a href="#">
            <img class="img-thumbnail" width="304" height="236"
                 :src="'images/'+relatedProduct.image">
          </a>
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
</template>

<script>
import axios from 'axios'

export default {
  name: "Cart",
  props: ['products', 'shipping', 'related-product', 'payments',],
  data() {
    return {
      baseUrl: '',
      total: 0,
      csrf: '',
      selectedShipping: null,
      selectedPayment: null,
      items: []
    }
  },
  watch: {
    selectedShipping(val) {
      this.subtotal(val)
      axios.post(this.baseUrl + '/cart/change-shipping', {
        shippingMethodId: val,
        subtotal: this.total,
      }).then(response => {
        this.$root.$emit('nav_cart', response.data.data)
      }).catch(e => {
        console.log(e)
        //this.errors.push(e)
      })
    },
    selectedPayment(val) {
      axios.post(this.baseUrl + '/cart/change-payment', {
        paymentMethodId: val,
      }).then(response => {
        this.$root.$emit('nav_cart', response.data.data)
      }).catch(e => {
        console.log(e)
        //this.errors.push(e)
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
        related_product_id: this.relatedProduct.id,
        paymentMethodId: this.selectedPayment,
        shippingMethodId: this.selectedShipping,
      };
      this.items.forEach(pr => {
        itemsInfo.product_ids.push(pr.id);
        itemsInfo.productQty.push(pr.qty);
        itemsInfo.isRelatedProduct.push(pr.is_related);
      })

      axios.post(this.baseUrl + '/checkout',
          itemsInfo
      ).then(response => {
        if (typeof response.data !== 'undefined') {
          //todo implement payment error
          if (typeof response.data.redirect_to !== 'undefined') {
            window.location.href = response.data.redirect_to;
          } else {
            window.location.href = this.baseUrl + '/orders';
          }
        }
        //window.location.reload()
      }).catch(e => {
        console.log(e);
        //window.location.reload()
      });
    },
    addRelated() {
      axios.post(this.baseUrl + '/cart/add-related', {
        id: this.relatedProduct.id,
      }).then(response => {
        window.location.reload()
      }).catch(e => {
        console.log(e)
        //this.errors.push(e)
      });
    },
    onChangeQty(idx, e) {
      let new_val = e.target.value;
      if (new_val < 1 || new_val > 99) {
        e.target.value = this.items[idx].qty;
        return;
      }
      axios.post(this.baseUrl + '/cart/add-to-cart', {
        productId: this.items[idx].id,
        productQty: new_val,
        subtotal: this.total,
        updateQty: true,
      }).then(response => {
        this.$root.$emit('nav_cart', response.data.data)
        this.items[idx].qty = new_val;
        this.subtotal(this.selectedShipping)
      }).catch(e => {
        console.log(e)
        //this.errors.push(e)
      })
    },
    subtotal(val) {
      this.total = 0;
      if (this.selectedShipping) {
        this.total += this.shipping.find(method => method.id === val).rate
      }

      this.items.forEach(item => {
        this.total += (+item.price * +item.qty)
        item.rowTotal = Math.round(+item.price * +item.qty * 100) / 100
      })
      this.total = Math.round(this.total * 100) / 100
      return this.total;
    },
    remove(item) {
      let total = this.total - (+item.price * +item.qty)
      axios.post(this.baseUrl + '/cart/remove-item', {
        productId: item.id,
        isRelated: Boolean(item.is_related),
        subtotal: total,
      }).then(response => {
        this.items = this.items.filter(i => i.id !== item.id)
        if (response.data.data.items === 0) {
          window.location.reload();
        }
        this.$root.$emit('nav_cart', response.data.data)
        this.subtotal(this.selectedShipping)
      }).catch(e => {
        console.log(e)
        //this.errors.push(e)
      });
    }
  },
  created() {
    const method = this.shipping.find(method => method.selected === true);
    this.selectedShipping = method !== undefined ? method.id : null;
    this.items = this.products;
    this.subtotal(this.selectedShipping);
    const payMethod = this.payments.find(item => item.selected === true);
    this.selectedPayment = payMethod !== undefined ? payMethod.id : null;
  },
  mounted() {
    this.baseUrl = window.location.origin;
    this.csrf = document.head.querySelector('meta[name="csrf-token"]').content;
  },
}
</script>

<style scoped>
</style>
