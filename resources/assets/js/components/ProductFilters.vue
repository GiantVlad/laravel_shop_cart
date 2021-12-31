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

export default {
  name: "ProductFilters",
  props: ['properties'],
  components: {ProductFilter},
  data() {
    return {
      filters: [],
    }
  },
  created() {
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
  }
}
</script>

<style scoped>
</style>
