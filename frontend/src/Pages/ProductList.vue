<template>
  <div class="row">
    <div v-for="product in products" :key="product.id" class="col-4">
      <product :product="product"/>
    </div>
  </div>
</template>

<script>
import Product from "./Product.vue";
import Layout from "../Layouts/MainLayout.vue";

export default {
  name: 'ProductList',
  components: { Product },
  props: ['keyword', 'category', 'products'],
  layout: Layout,
}
</script>

<style lang="scss" scoped>
.product-cart {
  margin-bottom: 15px;

  .cart-wrapper {
    border: 1px solid gray;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    text-align: center;

    .cart-header {
      height: 6em;
      position: relative;
    }

    .effect {
      margin-top: 5px;

      .glyphicon {
        vertical-align: top;
        -webkit-transition: 0.6s ease-out;
        -moz-transition: 0.6s ease-out;
        transition: 0.6s ease-out;
      }

      .glyphicon.up {
        -webkit-transform: rotateZ(180deg);
        -moz-transform: rotateZ(180deg);
        transform: rotateZ(180deg);
      }

      .glyphicon-triangle-bottom {
        cursor: pointer;
      }

    }

    .thumbnail {
      a:hover {
        text-decoration: none;
      }

      .img-wrapper {
        width: 100%;
        padding-bottom: 100%; /* your aspect ratio here! */
        position: relative;
        height: 20em;

        .product-shop-desc {
          text-align: justify;
          padding: 0px 7px;
        }

        img {
          position: relative;
          top: 0;
          bottom: 0;
          left: 0;
          right: 0;
          max-width: 100%;
        }
      }
    }
  }
}
</style>
