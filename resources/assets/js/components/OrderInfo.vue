<template>
  <div>
    <div class="jumbotron jumbotron-fluid">
      <div class="container">
        <h3 class="display-3">Order #{{ order.id }}</h3>
      </div>
    </div>
    <div class="row orders-list">
      <div class="row" style="margin-bottom: 5px;">
        <div class="col-md-2"><strong>ID</strong></div>
        <div class="col-md-2"><strong>Created</strong></div>
        <div class="col-md-2"><strong>Total</strong></div>
        <div class="col-md-2"><strong>Status</strong></div>
      </div>
      <div class="row" style="margin-bottom: 5px;">
        <div class="col-md-2">{{ order.id }}</div>
        <div class="col-md-2">{{ order.createdAt | formatDate }}</div>
        <div class="col-md-2">{{ order.total }}</div>
        <div class="col-md-2">{{ order.status }}</div>
      </div>

      <div class="row" style="margin-bottom: 5px;">
        <table class="table table-striped">
          <thead>
          <tr>
            <th class="col-sm-8">Product</th>
            <th class="col-sm-2">Price</th>
            <th class="col-sm-2">QTY</th>
          </tr>
          </thead>

          <tbody>
          <tr v-for="product in order.orderData">
            <td><a :href="productRoute + '/' + product.product_id">{{ product.product.name }}</a></td>
            <td>{{ product.price }}</td>
            <td>{{ product.qty }}</td>
          </tr>
          </tbody>
        </table>
      </div>
      <div class="row" style="margin-bottom: 5px;">
        <div class="col-md-5">
          <table class="table table-striped">
            <thead>
            <tr>
              <th>Shipping method</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="item in order.dispatches">
              <td>{{ item.label }}</td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="row" style="margin-bottom: 5px;">
        <div class="col-md-5">
          <table class="table table-striped">
            <thead>
            <tr>
              <th>Payment method</th>
              <th>Payment ID</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="item in order.payments">
              <td>{{ item.method.label }}</td>
              <td>{{ item.external_id }}</td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Order from "../models/Order";

export default {
  name: "OrderInfo",
  props: ['productRoute', 'orderId'],
  data() {
    return {
      order: Order(),
      baseUrl: '',
    }
  },
  created() {
    // add columns payment status and shipping code
    this.baseUrl = window.location.origin;
    axios.get(this.baseUrl + '/order-data/' + this.orderId)
        .then(response => {
          this.order = new Order(response.data.data);
        })
        .catch(e => {
          if (e.response && e.response.status === 401) {
            console.log(e.response)
          } else {
            console.log(e)
          }
        });
  }
}
</script>

<style scoped>
</style>
