<template>
  <div>
    <div class="col-sm-4 col-md-5 col-lg-4 general-nav-col">
      <form role="search" class="navbar-form">
        <div class="input-group col-xs-12">
          <input type="text" class="form-control" placeholder="Search" id="nav-search"
                 v-model="keyword">
          <div class="input-group-btn">
            <button class="btn btn-default" type="button" id="nav-search-btn"
                    @click.prevent="search">
              <i class="glyphicon glyphicon-search"></i>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>

export default {
  name: "NavSearch",
  props: ['searchUrl'],
  data() {
    return {
      keyword: '',
    }
  },
  methods: {
    search() {
      if (this.keyword.trim()) {
        if (window.location.pathname === '/shop' || window.location.pathname === '/') {
          this.$root.$emit('product_search', {keyword: this.keyword});

          return;
        }

        window.location = window.location.origin + '/shop?keyword=' + this.keyword;
      }
    }
  },
  mounted() {
  }
}
</script>

<style scoped>

</style>
