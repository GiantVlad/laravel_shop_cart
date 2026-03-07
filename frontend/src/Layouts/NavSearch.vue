<template>
  <nav class="navbar bg-light">
    <div class="container-fluid">
      <form class="d-flex" role="search" @submit.prevent="submit">
        <input
          id="nav-search"
          v-model.trim="keyword"
          class="form-control me-2"
          type="search"
          placeholder="Search"
          aria-label="Search"
        >
        <button class="btn btn-outline-secondary" type="submit">
          <i class="bi-search"></i>
        </button>
      </form>
    </div>
  </nav>
</template>

<script>
import { router } from '@inertiajs/vue3'

export default {
  name: "NavSearch",
  props: ['searchUrl'],
  data() {
    return {
      keyword: '',
    }
  },
  methods: {
    submit() {
      router.get(this.searchUrl, { keyword: this.keyword || null })
    },
  },
}
</script>

<style scoped>
</style>
