<script setup lang="ts">
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'
import { LEAGUE_CONFIG } from '@/constants/league'
import type { Team } from '@/types/league'
import Spinner from '@/components/ui/Spinner.vue'

interface Props {
  teams: Team[]
}

const props = defineProps<Props>()

const form = useForm({
  name: '',
  team_ids: [] as number[]
})

const submit = () => {
  form.post(route('leagues.store'))
}

const canSubmit = computed(() => {
  return form.name.trim() && form.team_ids.length === LEAGUE_CONFIG.TEAMS_PER_LEAGUE
})
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="px-4 py-6 sm:px-0">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('leagues.index')"
            class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700"
          >
            <ArrowLeftIcon class="h-4 w-4 mr-1" />
            Back to Leagues
          </Link>
        </div>
        <div class="mt-4">
          <h1 class="text-3xl font-bold text-gray-900">Create New League</h1>
          <p class="mt-1 text-sm text-gray-500">
            Select {{ LEAGUE_CONFIG.TEAMS_PER_LEAGUE }} teams to create a new league
          </p>
        </div>
      </div>

      <!-- Form -->
      <div class="px-4 py-6 sm:px-0">
        <div class="bg-white shadow rounded-lg">
          <form @submit.prevent="submit" class="p-6 space-y-6">
            <!-- League Name -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                League Name
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                class="mt-1 block w-full bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base font-medium px-4 py-3 min-h-[48px] text-gray-900 placeholder-gray-400 appearance-none"
                placeholder="Enter league name"
                autocomplete="off"
              />
              <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                {{ form.errors.name }}
              </div>
            </div>

            <!-- Team Selection -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3">
                Select {{ LEAGUE_CONFIG.TEAMS_PER_LEAGUE }} Teams
                <span class="text-gray-500">
                  ({{ form.team_ids.length }}/{{ LEAGUE_CONFIG.TEAMS_PER_LEAGUE }})
                </span>
              </label>
              
              <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <div
                  v-for="team in teams"
                  :key="team.id"
                  class="relative"
                >
                  <input
                    :id="`team-${team.id}`"
                    v-model="form.team_ids"
                    :value="team.id"
                    type="checkbox"
                    class="sr-only"
                    :disabled="form.team_ids.length >= LEAGUE_CONFIG.TEAMS_PER_LEAGUE && !form.team_ids.includes(team.id)"
                  />
                  <label
                    :for="`team-${team.id}`"
                    class="flex items-center justify-between p-6 h-24 border rounded-xl cursor-pointer transition-colors select-none group"
                    :class="{
                      'border-blue-500 bg-blue-50': form.team_ids.includes(team.id),
                      'border-gray-300 hover:border-gray-400 bg-white': !form.team_ids.includes(team.id),
                      'opacity-50 cursor-not-allowed': form.team_ids.length >= LEAGUE_CONFIG.TEAMS_PER_LEAGUE && !form.team_ids.includes(team.id)
                    }"
                  >
                    <div class="flex flex-col">
                      <span class="font-semibold text-gray-900 text-base">{{ team.name }}</span>
                      <span class="text-sm text-gray-500">Strength: {{ team.strength }}</span>
                    </div>
                    <span class="flex items-center justify-center w-5 h-5 ml-4">
                      <template v-if="form.team_ids.includes(team.id)">
                        <span class="flex items-center justify-center w-5 h-5 rounded-full bg-blue-500 text-white">
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                          </svg>
                        </span>
                      </template>
                      <template v-else>
                        <span class="flex items-center justify-center w-5 h-5 rounded-full border border-gray-300 bg-white">
                          <svg class="w-2.5 h-2.5 text-gray-300" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" /></svg>
                        </span>
                      </template>
                    </span>
                  </label>
                </div>
              </div>
              
              <div v-if="form.errors.team_ids" class="mt-2 text-sm text-red-600">
                {{ form.errors.team_ids }}
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3">
              <Link
                :href="route('leagues.index')"
                class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Cancel
              </Link>
              <button
                type="submit"
                :disabled="!canSubmit || form.processing"
                class="inline-flex justify-center py-2 px-4 border border-transparent cursor-pointer shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <Spinner v-if="form.processing" size="sm" class="mr-2" />
                Create League
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template> 