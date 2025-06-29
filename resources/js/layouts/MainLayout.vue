<script setup lang="ts">
import Header from '@/components/Header.vue';
</script> 

<template>
  <div>
    <Header />
    <main class="p-4 bg-gray-50 min-h-screen">
      <slot />
    </main>
  </div>
</template>
