<template>
  <li class="nav-item">
    <Link class="nav-link" href="/cart">
      <img height="22" width="25" src="/images/cart-icon.png" alt="Cart icon">
      Items: <span id="nav-items">{{ count ?? 0 }}</span>
      Total: <span id="nav-total">{{ total ?? 0 }}</span>
    </Link>
  </li>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
  name: "NavCart",
  props: ['count', 'total'],
  components: { Link },
}
</script>

<style scoped>

</style>
