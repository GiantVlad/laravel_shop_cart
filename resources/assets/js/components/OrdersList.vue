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
      <div v-for="(order, idx) in ordersList" class="row" style="margin-bottom: 5px;">
        <div class="col-md-2">{{order.label}}</div>
        <div class="col-md-2">{{order.created_at}}</div>
        <div class="col-md-2">{{order.total}}</div>
        <div class="col-md-2">{{order.status}}</div>
        <div class="col-md-1"><a :href="order.uri">details</a></div>
        <div class="col-md-2">
          <select class="form-control" id="order-status-actions" v-model="orderAction">
            <option v-if="!orderAction" :value="null" >select action</option>
            <template v-if="order.status === 'pending payment'">
              <option :value="{idx: idx, order: order.id, action: 'undo'}">undo order</option>
              <option :value="{idx: idx, order: order.id, action: 're_payment'}">repeat payment</option>
            </template>
            <option v-else :value="{idx: idx, order: order.id, action: 'repeat'}">repeat order</option>
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
      },
      watch: {
        orderAction(val) {
          axios.post(this.baseUrl + '/order/action', {
            id: val.order,
            action: val.action,
          }).then(response => {
            if (response.data.hasOwnProperty('redirect_to')) {
              window.location.href = response.data.redirect_to;
            }
            if (response.data.hasOwnProperty('status')) {
              this.ordersList[val.idx].status = response.data.status;
            }
          }).catch(e => {
            console.log(e)
            //this.errors.push(e)
          }).finally(() => {
            this.orderAction = null;
          })
        },
      },
    }
</script>

<style scoped>

</style>
