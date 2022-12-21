<template>
  <div class="container">
    <div class="row">
      <div class="col-xs-12">
        <a v-if="!isRoot" :href="$baseUrl + '/shop'">Shop</a>
        <template v-for="(parentCatalog, idx) in parentCatalogs">
          &nbsp;<small class="glyphicon glyphicon-arrow-right"></small>&nbsp;
          <a :class="idx < 1 ? 'active-catalog': ''" :data-id="parentCatalog.id"
             v-on:click.stop.preven="toCategory(parentCatalog.id)"
          >
            {{ parentCatalog.name }}
          </a>
        </template>
      </div>
    </div>
    <div class="row">
      <template v-for="(catalog, idx) in catalogs">
        <div class="col-xs-3 col-sm-2 col-md-1">
          <a v-on:click.stop.preven="toCategory(catalog.id)">{{ catalog.name}}</a>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import axios from "axios"

export default {
  name: "Categories",
  props: [],
  data() {
    return {
      parentCatalogs: [],
      catalogs: [],
    }
  },
  methods: {
    toCategory(id) {
      this.getCategories(id)
      this.$root.$emit('category_changed', id)
    },
    getCategories(id) {
      axios.get(this.$baseUrl + '/shop/category/' + id)
          .then(res => {
            this.parentCatalogs = res.data.data?.parentCatalogs ?? []
            this.catalogs = res.data.data?.catalogs ?? []
          })
          .catch(e => (console.log(e)))
    }
  },
  created() {
    this.getCategories(0)
  },
  computed: {
    isRoot() {
      return this.parentCatalogs.length === 0
    },
    lastElemFlag() {
      return this.parentCatalogs.length
    }
  }
}
</script>

<style scoped>

</style>
