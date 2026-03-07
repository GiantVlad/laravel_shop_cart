<script setup>
import NavLayout from "./NavLayout.vue";
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const appName = computed(() => page.props.appName)
const userName = computed(() => page.props.userName)
const cart = computed(() => page.props.cart)
</script>

<template>
  <main>
    <header>
      <nav-layout :app-name="appName" :user-name="userName" :cart="cart"></nav-layout>
    </header>
    <article>
      <div class="container">
        <slot />
      </div>
    </article>
  </main>
</template>
