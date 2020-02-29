<template>
    <div> <p>kkkkkkkkk</p>
        <!--<div class="jumbotron jumbotron-fluid">
            <div class="container">
                <h3 class="display-3">Order #{{order.label}}</h3>
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
                <div class="col-md-2">{{order.label}}</div>
                <div class="col-md-2">{{order.created_at}}</div>
                <div class="col-md-2">{{order.total}}</div>
                <div class="col-md-2">{{order.status}}</div>
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
                            <td><a :href=" product.product_id">{{product.product.name}}</a></td>
                            <td>{{product.price}}</td>
                            <td>{{product.qty}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>-->
    </div>
</template>

<script>
    export default {
        name: "Order1Info",
        //props: [/*'order', 'productRoute'*/],
        /*data() {
            return {
                //products: [] | format('Y-M-d h:i')
            }
        },*/
        created() {
            console.log('this.order')
        }
    }
</script>

<style scoped>

</style>