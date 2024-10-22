<script setup>
import NavLayout from "./NavLayout.vue";
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const appName = computed(() => page.props.appName)
const userName = computed(() => page.props.userName)
</script>

<template>
  <main>
    <header>
      <nav-layout :app-name="appName" :user-name="userName"></nav-layout>
    </header>
    <article>
      <div class="container">
        <slot />
      </div>
    </article>
  </main>
</template>
