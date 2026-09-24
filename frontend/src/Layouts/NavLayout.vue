<template>
  <nav class="navbar navbar-expand-lg shop-nav py-3">
    <div class="container-fluid align-items-center">
      <Link class="navbar-brand shop-nav__brand" href="/">Lara-shop</Link>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNav"
        aria-controls="navbarNav"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <Link class="navbar-text text-decoration-none me-3" href="/shop">{{ appName }}</Link>
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <nav-cart v-if="userName" :count="cart?.count" :total="cart?.total" />
          <template v-if="!userName">
            <li class="nav-item"><Link class="nav-link" href="/login">Login</Link></li>
            <li class="nav-item"><Link class="nav-link" href="/register">Register</Link></li>
          </template>
          <template v-else>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{ userName }}
              </a>
              <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li><Link class="dropdown-item" href="/orders">Orders</Link></li>
                <li><hr class="dropdown-divider"></li>
                <li><Link class="dropdown-item" href="/logout" method="post" as="button" type="button">Logout</Link></li>
              </ul>
            </li>
          </template>
        </ul>
      </div>
      <nav-search :search-url="'/search'"></nav-search>
    </div>
  </nav>
</template>

<script>
import { Link } from '@inertiajs/vue3'
import NavSearch from './NavSearch.vue'
import NavCart from "./NavCart.vue";

export default {
  name: 'NavLayout',
  props: ['userName', 'appName', 'cart'],
  components: { NavCart, Link, NavSearch },
}
</script>

<style scoped>
.shop-nav {
  position: fixed;
  top: 0; left: 0; right: 0;
  z-index: 1030;
  background:
    linear-gradient(135deg, #fcf5e8 0%, #f4ece0 55%, #efe8db 100%);
  border-bottom: 1px solid rgba(116, 88, 47, 0.14);
}

.shop-nav__brand {
  font-size: 1.35rem;
  font-weight: 700;
  letter-spacing: 0.02em;
  color: #1f1a15;
}

.shop-nav__section-link {
  font-size: 0.95rem;
  font-weight: 600;
  color: #6b4a21;
}

.shop-nav :deep(.nav-link) {
  color: #433225;
}

.shop-nav :deep(.nav-link:hover),
.shop-nav__section-link:hover,
.shop-nav__brand:hover {
  color: #8d5d22;
}

.shop-nav__search {
  width: 100%;
}

@media (min-width: 992px) {
  .shop-nav__search {
    max-width: 24rem;
  }
}
</style>
