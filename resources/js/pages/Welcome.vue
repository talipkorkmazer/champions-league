<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { TrophyIcon } from '@heroicons/vue/24/outline';
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="px-4 py-6 sm:px-0">
        <div class="text-center">
          <div class="mx-auto h-16 w-16 text-gray-400 mb-4">
            <TrophyIcon class="h-16 w-16" />
          </div>
          <h1 class="text-3xl font-bold text-gray-900">Champions League Simulation</h1>
          <p class="mt-1 text-sm text-gray-500">
            Welcome to the Insider Champions League simulation project
          </p>
        </div>
      </div>

      <!-- Main Content -->
      <div class="px-4 py-6 sm:px-0">
        <div class="max-w-3xl mx-auto">
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
              <div class="text-center mb-6">
                <p class="text-lg text-gray-700">
                  This app simulates a mini football league with 4 teams, match results, and a live league table.
                </p>
              </div>

              <!-- Features List -->
              <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">League Features:</h3>
                <ul class="space-y-3 text-gray-600">
                  <li class="flex items-start">
                    <div class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center mr-3 mt-0.5">
                      <div class="h-2 w-2 rounded-full bg-blue-600"></div>
                    </div>
                    <span>4 teams compete in a round-robin league</span>
                  </li>
                  <li class="flex items-start">
                    <div class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center mr-3 mt-0.5">
                      <div class="h-2 w-2 rounded-full bg-blue-600"></div>
                    </div>
                    <span>Each team can have different strengths (affecting match results)</span>
                  </li>
                  <li class="flex items-start">
                    <div class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center mr-3 mt-0.5">
                      <div class="h-2 w-2 rounded-full bg-blue-600"></div>
                    </div>
                    <span>Premier League rules: 3 points for a win, 1 for a draw, 0 for a loss</span>
                  </li>
                  <li class="flex items-start">
                    <div class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center mr-3 mt-0.5">
                      <div class="h-2 w-2 rounded-full bg-blue-600"></div>
                    </div>
                    <span>League table updates after each week</span>
                  </li>
                  <li class="flex items-start">
                    <div class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center mr-3 mt-0.5">
                      <div class="h-2 w-2 rounded-full bg-blue-600"></div>
                    </div>
                    <span>After week 4, the app estimates championship probabilities for each team</span>
                  </li>
                  <li class="flex items-start">
                    <div class="flex-shrink-0 h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center mr-3 mt-0.5">
                      <div class="h-2 w-2 rounded-full bg-blue-600"></div>
                    </div>
                    <span>Extras: Auto-play all matches, see updated standings, and reset a league</span>
                  </li>
                </ul>
              </div>

              <!-- Action Button -->
              <div class="mt-8 text-center">
                <Link :href="route('leagues.index')">
                  <button class="inline-flex items-center px-6 py-3 cursor-pointer border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <TrophyIcon class="h-5 w-5 mr-2" />
                    Start Simulation
                  </button>
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
