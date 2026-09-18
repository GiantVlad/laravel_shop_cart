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
.nav-search {
  width: 100%;
  gap: 0.75rem;
}

.nav-search .form-control {
  min-width: 0;
  border-color: rgba(95, 73, 45, 0.16);
  background-color: rgba(255, 255, 255, 0.72);
}

.nav-search__button {
  white-space: nowrap;
}

@media (max-width: 991.98px) {
  .nav-search {
    margin: 0.25rem 0 0.5rem;
  }
}
</style>
