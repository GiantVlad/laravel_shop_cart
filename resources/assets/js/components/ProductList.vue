<template>
  <div>
    <div v-for="product in products">
      <product :product="product"/>
    </div>
  </div>
</template>

<script>
import VueSlideUpDown from 'vue-slide-up-down'
import axios from 'axios'
import Product from "./Product";

export default {
  name: 'ProductList',
  components: {Product, VueSlideUpDown},
  props: ['keyword'],
  data() {
    return {
      products: [],
      baseUrl: '',
      csrf: '',
    }
  },
  computed: {},
  methods: {
    searchProducts(keyword) {
      axios.get(this.baseUrl + '/search', {params: {keyword}})
          .then(response => {
            this.$root.$emit('product_filter', {property_id: null, option: null, value: null});
            this.products = response.data.data;
          })
          .catch(e => {
            if (e.response && e.response.status === 401) {
              console.log(e.response)
            } else {
              console.log(e)
            }
          });
    },
    filterProducts(data) {
      axios.get(this.baseUrl + '/filter', {params: data})
          .then(response => {
            this.products = response.data.data;
          })
          .catch(e => {
            if (e.response && e.response.status === 401) {
              console.log(e.response)
            } else {
              console.log(e)
            }
          });
    },
    parseFilterValues(filters) {
      const query = {};
      filters.forEach(filter => {
        if (Array.isArray(filter.value)) {
          let values = [];
          if (filter.option === 'checked') {
            filter.value.forEach(item => {
              if (item.value) {
                values.push(item.id);
              }
            });
          } else {
            values = filter.value;
          }
          query['values_' + filter.property_id] = values.join(',');
        }
      });

      return query;
    },
    parseCategory() {
      let category_id = null;
      const path = window.location.pathname.split( '/' );
      const idx = path.indexOf('category');
      if (idx >= 0 && parseInt(path[idx + 1]) > 0) {
        category_id = parseInt(path[idx + 1]);
      }

      return category_id;
    }
  },
  created() {
    this.baseUrl = window.location.origin;
    this.csrf = document.head.querySelector('meta[name="csrf-token"]').content;
    this.$root.$on('product_search', data => {
      this.searchProducts(data.keyword);
    });
    this.$root.$on('product_filters', data => {
      const filters = this.parseFilterValues(data.filters);
      filters.category_id = this.parseCategory();
      this.filterProducts(filters);
    });
  },
  mounted() {
    const category_id = this.parseCategory();
    if (category_id) {
      this.filterProducts({category_id: this.parseCategory()});
    } else {
      this.searchProducts(this.keyword);
    }
  }
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
