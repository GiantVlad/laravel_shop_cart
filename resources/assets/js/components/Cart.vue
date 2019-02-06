<template>
    <div>
        <div class="product-row" v-for="item in items">
            <div class="row">
                <input type="hidden" name="productId" :value="item.id">
                <div class="col-md-2"><a :href="'/product/'+ item.id"><img class="img-thumbnail" :alt="'product id '+item.id"
                    height="240" :src="'images/'+item.image"></a>
                </div>
                <div class="col-md-5">
                    <h4>{{item.name}}</h4>
                    <p>{{item.description}}</p>
                    <button class="btn btn-link" type="button" @click="remove(item.id, item.is_related)">
                        Remove
                    </button>
                </div>
                <div class="col-md-2 form-group">
                    <label class="control-label" :for="'productQty'+item.id">QTY</label>
                    <input type="number" class="form-control" :name="'productQty'+item.id"
                           :id="'productQty'+item.id" placeholder="QTY" :value="item.qty"
                           min="1" max="99" required>
                </div>
                <div class="col-md-1"><p>Price: <span :id="'price'+item.id">{{item.price}}</span></p>
                </div>
                <div class="col-md-1"><p>Total: <span :id="'total'+item.id"></span></p></div>
                <input type="hidden" name="isRelatedProduct" :value="item.is_related">
            </div>
            <hr/>
        </div>
        <div class="row">
            <div class="col-md-4 col-md-offset-7">
                <div class="form-group">
                    <label for="shipping-select">Select shipping method: </label>
                    <select class="form-control" id="shipping-select" v-model="selected_shipping">
                        <option v-if="!selected_shipping" :value="null">Select shipping method...</option>
                        <option v-for="method in shipping" :value="method.id">
                            {{method.label + ', ' + method.time + ', ' + method.rate}}
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10">
                <p class="text-right">Subtotal: <span id="subtotal">{{total}}</span></p>
                <input type="hidden" name="subtotal" value="">
            </div>
            <div class="col-md-2">
                <button type="submit" id="checkout" class="btn btn-primary" :disabled="disableSubmit">Pay</button>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        name: "Cart",
        props: ['products', 'shipping'],
        data() {
            return {
                baseUrl: '',
                total: 0,
                disableSubmit: true,
                csrf: '',
                selected_shipping: null,
                items: []
            }
        },
        watch: {
            selected_shipping(val) {

                this.subtotal(val)

                axios.post(this.baseUrl + '/cart', {
                    input: "changeShipping",
                    shippingMethodId: val,
                    subtotal: this.total,
                    _token: this.csrf
                }).then(response => {
                    this.$root.$emit('nav_cart', response.data)
                    console.log(response.data)
                    //$('input[name=subtotal]').val(data.total);
                }).catch(e => {
                    console.log(e)
                    //this.errors.push(e)
                })
            },
        },
        methods: {
            subtotal(val) {
                this.total = 0;
                if (this.selected_shipping) {
                    this.total += this.shipping.find(method=>method.id === val).rate
                }

                this.items.forEach(item=>{
                    this.total += +item.price
                })
                this.total = Math.round(this.total*100)/100
                return this.total;
            },

            remove(item_id, isRelated) {
                axios.post(this.baseUrl + '/cart', {
                    input: "removeRow",
                    productId: item_id,
                    isRelated: isRelated,
                    subtotal: this.total,
                    _token: this.csrf
                }).then(response => {
                    console.log(response.data)
                    this.$root.$emit('nav_cart', response.data)
                    this.subtotal(this.selected_shipping)
                    arr = arr.filter(item => item !== value)
                   // $('input[name=subtotal]').val(data.total);
                }).catch(e => {
                    console.log(e)
                    //this.errors.push(e)
                })
            }
        },
        created() {
            this.selected_shipping = !this.shipping.selected ? null : this.shipping.selected
            this.items = Object.assign({}, this.products)
            this.subtotal(this.selected_shipping)
        },
        mounted() {
            this.baseUrl = window.location.origin;
            this.csrf = document.head.querySelector('meta[name="csrf-token"]').content;
            console.log(this.items)
        },
    }
</script>

<style scoped>

</style>