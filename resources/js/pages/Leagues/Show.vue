<script setup lang="ts">
import { computed, ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { ArrowLeftIcon, PlayIcon, TrophyIcon, ChartBarIcon, ChevronDoubleRightIcon } from '@heroicons/vue/24/outline'
import Spinner from '@/components/ui/Spinner.vue'
import type { League, Standing, LeagueUtility } from '@/types/league'
import MainLayout from '@/layouts/MainLayout.vue'

interface Props {
  league: League
  standings: Standing[]
  leagueUtility: LeagueUtility
}

const props = defineProps<Props>()

const isSimulating = ref(false)

const showChampionshipColumn = computed(() => {
  return props.league.current_week >= 4;
});

const simulateWeek = async () => {
  if (!props.leagueUtility.canSimulate || isSimulating.value) return
  isSimulating.value = true
  try {
    router.post(route('simulation.run', props.league.id), {}, {
      onSuccess: () => router.reload()
    })
  } finally {
    isSimulating.value = false
  }
}

const simulateAll = async () => {
  if (!props.leagueUtility.canSimulate || isSimulating.value) return
  isSimulating.value = true
  try {
    router.post(route('simulation.runAll', props.league.id), {}, {
      onSuccess: () => router.reload()
    })
  } finally {
    isSimulating.value = false
  }
}

const resetLeague = async () => {
  if (isSimulating.value) return
  isSimulating.value = true
  try {
    router.post(route('leagues.reset', props.league.id), {}, {
      onSuccess: () => router.reload()
    })
  } finally {
    isSimulating.value = false
  }
}
</script>

<template>
  <MainLayout>
    <div class="min-h-screen bg-gray-50">
      <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <Link
                :href="route('leagues.index')"
                class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700"
              >
                <ArrowLeftIcon class="h-4 w-4 mr-1" />
                Back to Leagues
              </Link>
              <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ league.name }}</h1>
                <p class="mt-1 text-sm text-gray-500">
                  Week {{ league.current_week }} of {{ leagueUtility.totalWeeks }}
                </p>
              </div>
            </div>
            <div class="flex space-x-3">
              <span
                :class="leagueUtility.statusClasses"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
              >
                {{ leagueUtility.statusLabel }}
              </span>
              <button @click="simulateWeek"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                v-if="leagueUtility.canSimulate"
                :disabled="isSimulating"
              >
                <PlayIcon class="h-4 w-4 mr-2" />
                Run Next Week
                <Spinner v-if="isSimulating" size="sm" class="ml-2" />
              </button>
              <button @click="simulateAll"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                v-if="leagueUtility.canSimulate"
                :disabled="isSimulating"
              >
                <ChevronDoubleRightIcon class="h-4 w-4 mr-2" />
                Run All Weeks
                <Spinner v-if="isSimulating" size="sm" class="ml-2" />
              </button>
              <button @click="resetLeague"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                :disabled="isSimulating"
              >
                Reset League
                <Spinner v-if="isSimulating" size="sm" class="ml-2" />
              </button>
            </div>
          </div>
        </div>

        <div class="px-4 py-6 sm:px-0">
          <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between text-sm text-gray-600 mb-2">
              <span>League Progress</span>
              <span>{{ leagueUtility.progressPercentage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3" :style="{'--progress': leagueUtility.progressPercentage + '%'}">
              <div
                class="bg-blue-600 h-3 rounded-full transition-all duration-300 w-[var(--progress)]"
              ></div>
            </div>
          </div>
        </div>

        <div class="px-4 py-6 sm:px-0">
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <TrophyIcon class="h-5 w-5 mr-2 text-yellow-500" />
                League Standings
              </h2>
            </div>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Position
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Team
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                      P
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                      W
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                      D
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                      L
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                      GF
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                      GA
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                      GD
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Pts
                    </th>
                    <th v-if="showChampionshipColumn" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Championship %
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr
                    v-for="(team, index) in standings"
                    :key="team.team.id"
                    :class="{
                      'bg-yellow-50': index === 0,
                      'bg-gray-50': index === 1,
                      'bg-orange-50': index === 2
                    }"
                  >
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ index + 1 }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ team.team.name }}</div>
                      <div class="text-sm text-gray-500">Strength: {{ team.team.strength }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                      {{ team.played }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                      {{ team.won }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                      {{ team.drawn }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                      {{ team.lost }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                      {{ team.goals_for }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                      {{ team.goals_against }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                      {{ team.goal_difference }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">
                      {{ team.points }}
                    </td>
                    <td v-if="showChampionshipColumn" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                      {{ team.championship_percentage }}%
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="px-4 py-6 sm:px-0">
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <ChartBarIcon class="h-5 w-5 mr-2 text-blue-500" />
                Match Results
              </h2>
            </div>
            <div class="p-6">
              <div v-if="league.matches && league.matches.length > 0" class="space-y-4">
                <div
                  v-for="match in league.matches"
                  :key="match.id"
                  class="flex items-center justify-between p-4 border rounded-lg"
                >
                  <div class="flex items-center space-x-4 flex-1">
                    <div class="text-sm font-medium text-gray-900 text-right flex-1">
                      {{ match.home_team.name }}
                    </div>
                    <div class="text-lg font-bold text-gray-900">
                      {{ match.home_score }} - {{ match.away_score }}
                    </div>
                    <div class="text-sm font-medium text-gray-900 text-left flex-1">
                      {{ match.away_team.name }}
                    </div>
                  </div>
                  <div class="text-xs text-gray-500 ml-4">
                    Week {{ match.week }}
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-8 text-gray-500">
                No matches played yet.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
