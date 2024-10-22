<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Navbar</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <!-- Branding Image -->
        <Link href="/shop">{{ appName }}</Link>
      </div>
      <nav-search :search-url="'/search'"></nav-search>
      <div class="collapse navbar-collapse" id="app-navbar-collapse">
        <div class="col-sm-4 col-lg-5 general-nav-col pull-right">
          <!-- Right Side Of Navbar -->
          <ul class="nav navbar-nav navbar-right general-nav">
            <!-- Authentication Links -->
            <template v-if="!userName">
              <li><Link href="/login">Login</Link></li>
              <li><Link href="/register">Register</Link></li>
            </template>
            <template v-else>
              <li><Link href="/orders">Orders</Link></li>

            <li class="dropdown">
              <a href="#"
                 class="dropdown-toggle"
                 data-toggle="dropdown"
                 role="button"
                 aria-expanded="false"
              >
                {{ userName }}
                <span class="caret"></span>
              </a>
              <ul class="dropdown-menu" role="menu">
                <li>
                  <li><Link href="/logout" method="post" as="button" type="button">Logout</Link></li>
                </li>
              </ul>
            </li>
            </template>
          </ul>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import { Link } from '@inertiajs/vue3'
import NavSearch from './NavSearch.vue'
export default {
  name: 'NavLayout',
  props: ['userName', 'appName'],
  components: { Link, NavSearch }
}
</script>
