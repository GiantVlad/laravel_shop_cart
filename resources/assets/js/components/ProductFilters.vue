<template>
  <div>
    <h4 class="header">Filters:</h4>
    <div v-for="property in properties">
      <product-filter :property="property"/>
    </div>
    <button class="btn btn-block btn-sm btn-primary" @click="apply">Apply</button>
  </div>
</template>

<script>
import ProductFilter from './ProductFilter'
import axios from "axios";

export default {
  name: "ProductFilters",
  props: [],
  components: {ProductFilter},
  data() {
    return {
      filters: [],
      properties: [],
    }
  },
  created() {
    this.getProperties()
    this.$root.$on('product_filter', data => {
      if (data.property_id) {
        this.filters = this.filters.filter(el => el.property_id !== data.property_id);
        this.filters.push(data);
      }
    });
  },
  methods: {
    apply() {
      this.$root.$emit('product_filters', {filters: this.filters});
    },
    getProperties() {
      axios.get(this.$baseUrl + '/filter/properties/')
          .then(res => {
            this.properties = res.data?.data ?? []
          })
          .catch(e => (console.log(e)))
    }
  },
}
</script>

<style scoped>
</style>
