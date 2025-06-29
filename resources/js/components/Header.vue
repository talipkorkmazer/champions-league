<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { TrophyIcon } from '@heroicons/vue/24/outline';
</script> 

<template>
  <header class="bg-white border-b border-gray-200 px-4 py-2 flex items-center justify-between">
    <Link :href="route('leagues.index')" class="flex items-center space-x-2 hover:opacity-80 transition-opacity">
      <TrophyIcon class="h-6 w-6 text-gray-500" />
      <span class="text-lg text-gray-700">Champions League</span>
    </Link>
    <nav class="space-x-4">
      <Link :href="route('leagues.index')" class="text-gray-600 hover:text-blue-600 transition-colors">Leagues</Link>
      <Link :href="route('leagues.create')" class="text-gray-600 hover:text-blue-600 transition-colors">Create League</Link>
    </nav>
  </header>
</template>
