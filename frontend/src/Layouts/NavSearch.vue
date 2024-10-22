<template>
  <div>
    <nav class="navbar bg-light">
      <div class="container-fluid">
        <form class="d-flex" role="search">
          <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" id="nav-search"
                 v-model="keyword">
            <Link class="btn btn-default" href="/search" method="get" as="button" :data="{ keyword }">
            <i class="bi-search"></i>
          </Link>
        </form>
      </div>
    </nav>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
  name: "NavSearch",
  props: ['searchUrl'],
  components: { Link },
  data() {
    return {
      keyword: '',
    }
  },
  mounted() {
  }
}
</script>

<style scoped>
</style>
