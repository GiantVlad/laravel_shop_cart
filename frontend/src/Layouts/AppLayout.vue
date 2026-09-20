<script setup>
</script>

<template>
  <div class="min-h-screen bg-surface-50">
    <header class="shadow-1 bg-white px-4 py-3 flex items-center justify-between sticky top-0 z-20">
      <h1 class="text-xl font-semibold text-primary">Lara Shop</h1>
    </header>
    <main class="p-4 max-w-5xl mx-auto">
      <Card class="shadow-md">
        <template #content>
          <slot />
        </template>
      </Card>
    </main>
  </div>
</template>

<style scoped>
.bg-surface-50 { background-color: #f8f9fa; }
</style>
