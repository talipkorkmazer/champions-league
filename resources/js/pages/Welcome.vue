<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 p-6">
    <div class="w-full max-w-2xl bg-white dark:bg-gray-900 rounded-xl shadow-lg p-8 flex flex-col items-center">
      <h1 class="text-3xl font-bold mb-4 text-indigo-700 dark:text-indigo-300">Insider Champions League Simulation</h1>
      <p class="mb-4 text-lg text-gray-700 dark:text-gray-200 text-center">
        Welcome to the Insider Champions League simulation project!<br>
        This app simulates a mini football league with 4 teams, match results, and a live league table.
      </p>
      <ul class="mb-6 text-left text-gray-600 dark:text-gray-300 list-disc pl-6">
        <li>4 teams compete in a round-robin league.</li>
        <li>Each team can have different strengths (affecting match results).</li>
        <li>Premier League rules: 3 points for a win, 1 for a draw, 0 for a loss.</li>
        <li>League table updates after each week.</li>
        <li>After week 4, the app estimates championship probabilities for each team.</li>
        <li>Extras: Auto-play all matches, see updated standings, and reset a league.</li>
      </ul>
      <div class="flex flex-col items-center gap-4 w-full">
        <Link :href="route('leagues.index')">
          <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold shadow hover:bg-indigo-700 transition">
            Start Simulation
          </button>
        </Link>
      </div>
    </div>
  </div>
</template>
