<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { PlusIcon, EyeIcon, TrophyIcon } from '@heroicons/vue/24/outline'
import type { League, LeagueUtility } from '@/types/league'
import MainLayout from '@/layouts/MainLayout.vue'

interface Props {
  leagues: League[]
  leaguesUtility: Record<number, LeagueUtility>
}

defineProps<Props>()
</script>

<template>
  <MainLayout>
    <div class="min-h-screen bg-gray-50">
      <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Champions League</h1>
              <p class="mt-1 text-sm text-gray-500">
                Manage leagues and start simulations
              </p>
            </div>
            <div class="flex space-x-3">
              <Link
                :href="route('leagues.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <PlusIcon class="h-4 w-4 mr-2" />
                Create New League
              </Link>
            </div>
          </div>
        </div>

        <div class="px-4 py-6 sm:px-0">
          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div
              v-for="league in leagues"
              :key="league.id"
              class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200"
            >
              <div class="p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="text-lg font-medium text-gray-900">
                      {{ league.name }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                      {{ league.teams.length }} teams • {{ league.current_week }}/{{ leaguesUtility[league.id]?.totalWeeks }} weeks
                    </p>
                  </div>
                  <div class="flex flex-col items-end">
                    <span
                      :class="{
                        'bg-green-100 text-green-800': leaguesUtility[league.id]?.status === 'completed',
                        'bg-yellow-100 text-yellow-800': leaguesUtility[league.id]?.status === 'in_progress',
                        'bg-gray-100 text-gray-800': leaguesUtility[league.id]?.status === 'not_started'
                      }"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    >
                      {{ leaguesUtility[league.id]?.statusLabel }}
                    </span>
                  </div>
                </div>

                <div class="mt-4">
                  <h4 class="text-sm font-medium text-gray-700 mb-2">Teams:</h4>
                  <div class="flex flex-wrap gap-1">
                    <span
                      v-for="team in league.teams"
                      :key="team.id"
                      class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800"
                    >
                      {{ team.name }}
                    </span>
                  </div>
                </div>

                <div class="mt-4">
                  <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Progress</span>
                    <span>{{ leaguesUtility[league.id]?.progressPercentage }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2" :style="{'--progress': leaguesUtility[league.id]?.progressPercentage + '%'}">
                    <div
                      class="bg-blue-600 h-2 rounded-full transition-all duration-300 w-[var(--progress)]"
                    ></div>
                  </div>
                </div>

                <div class="mt-6 flex space-x-3">
                  <Link
                    :href="route('leagues.show', league.id)"
                    class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    <EyeIcon class="h-4 w-4 mr-2" />
                    View
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <div
            v-if="leagues.length === 0"
            class="text-center py-12"
          >
            <div class="mx-auto h-12 w-12 text-gray-400">
              <TrophyIcon class="h-12 w-12" />
            </div>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No leagues yet</h3>
            <p class="mt-1 text-sm text-gray-500">
              Create your first league to get started.
            </p>
            <div class="mt-6">
              <Link
                :href="route('leagues.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <PlusIcon class="h-4 w-4 mr-2" />
                Create First League
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
