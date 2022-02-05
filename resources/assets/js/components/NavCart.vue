<template>
  <li><a :href="baseUrl +'/cart'"><img height="22" width="25" :src="baseUrl + '/images/cart-icon.png'">
    Items: <span
        id="nav-items">{{ count }}</span>
    Total: <span
        id="nav-total">{{ total }}</span>
  </a>
  </li>
</template>

<script>
import axios from "axios";

export default {
  name: "NavCart",
  data() {
    return {
      baseUrl: '',
      count: 0,
      total: 0,
    }
  },
  methods: {},
  mounted() {
    this.baseUrl = window.location.origin;

    this.$root.$on('nav_cart', data => {
      this.count = data.items
      this.total = Math.round(data.total * 100) / 100
    });

    axios.get(this.baseUrl + '/cart/content')
        .then(response => {
          this.count = response.data.data.items;
          this.total = Math.round(response.data.data.total * 100) / 100;
        })
        .catch(e => {
          if (e.response && e.response.status === 401) {
            console.log(e.response)
          } else {
            console.log(e)
          }
        });
  },
}
</script>

<style scoped>

</style>
