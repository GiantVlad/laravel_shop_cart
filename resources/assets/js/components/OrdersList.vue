<template>
  <div>
    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h3 class="display-3">Orders</h3>
        </div>
    </div>
    <div class="row orders-list">
      <div v-if="orders.left > 0" class="row" style="margin-bottom: 5px;">
        <div class="col-md-2">ID</div>
        <div class="col-md-2">Created</div>
        <div class="col-md-2">Total</div>
        <div class="col-md-2">Status</div>
      </div>
      <div v-else class="row" style="margin-bottom: 5px;">
        <div class="col-md-8">No orders found</div>
      </div>
    </div>
      <div v-for="order in ordersList" class="row" style="margin-bottom: 5px;">
        <div class="col-md-2">{{order.label}}</div>
        <div class="col-md-2">{{order.created_at}}</div>
        <div class="col-md-2">{{order.total}}</div>
        <div class="col-md-2">{{order.status}}</div>
        <div class="col-md-1"><a :href="order.uri">details</a></div>
        <div class="col-md-2">
          <select class="form-control" id="order-status-actions" v-model="orderAction">
            <option v-if="!orderAction" :value="null" >select action ..</option>
            <option
              v-if="order.status !== 'pending payment'"
              :value="{order: order.id, action: 'repeat'}">
              repeat order
            </option>
<!--            <template v-else>-->
<!--            <option :value="{order: order.id, action: 'replay payment'}">replay payment</option>-->
<!--            <option :value="{order: order.id, action: 'undo'}">undo order</option>-->
<!--            </template>-->
          </select>
        </div>
      </div>
  </div>
</template>

<script>
    import axios from "axios";

    export default {
        name: "OrdersList",
        props: ['orders',],
        data() {
            return {
              baseUrl: '',
              ordersList: [],
              orderAction: null,
            }
        },
        created() {
            this.ordersList = this.orders.data;
        },
      mounted() {
        this.baseUrl = window.location.origin;
        this.csrf = document.head.querySelector('meta[name="csrf-token"]').content;
      },
      watch: {
        orderAction(val) {
          axios.post(this.baseUrl + '/order/action', {
            id: val.order,
            action: val.action,
            _token: this.csrf
          }).then(response => {
            console.log(response);
            if (response.data === 'redirect_to_cart') {
              window.location.href = this.baseUrl + '/cart';
            }
            // if delete remove row
          }).catch(e => {
            console.log(e)
            //this.errors.push(e)
          })
        },
      },
    }
</script>

<style scoped>

</style>
