<script setup>
</script>

<template>
  <div class="min-h-screen" style="background:#f8f9fa;">
    <header class="shadow-sm bg-white px-4 py-3 flex items-center justify-between sticky top-0 z-20">
      <h1 class="text-xl font-semibold" style="color:#0ea5e9;">Lara Shop</h1>
    </header>
    <main class="p-4" style="max-width:1200px;margin:0 auto;">
      <div class="bg-white rounded-xl shadow-md p-4">
        <slot />
      </div>
    </main>
  </div>
</template>
