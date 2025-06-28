import { LEAGUE_CONFIG, LEAGUE_STATUS } from '../constants/league'
import type { LeagueStatus } from '../types/league'

export function useLeague() {
  const getLeagueStatus = (currentWeek: number): LeagueStatus => {
    if (currentWeek === 0) return LEAGUE_STATUS.NOT_STARTED as LeagueStatus
    if (currentWeek >= LEAGUE_CONFIG.TOTAL_WEEKS) return LEAGUE_STATUS.COMPLETED as LeagueStatus
    return LEAGUE_STATUS.IN_PROGRESS as LeagueStatus
  }

  const getProgressPercentage = (currentWeek: number): number => {
    return Math.round((currentWeek / LEAGUE_CONFIG.TOTAL_WEEKS) * 100)
  }

  const getStatusLabel = (currentWeek: number): string => {
    const status = getLeagueStatus(currentWeek)
    const statusLabels: Record<LeagueStatus, string> = {
      [LEAGUE_STATUS.NOT_STARTED]: 'Not Started',
      [LEAGUE_STATUS.IN_PROGRESS]: 'In Progress',
      [LEAGUE_STATUS.COMPLETED]: 'Completed'
    }
    return statusLabels[status]
  }

  const getStatusClasses = (currentWeek: number): string => {
    const status = getLeagueStatus(currentWeek)
    const statusClassMap: Record<LeagueStatus, string> = {
      [LEAGUE_STATUS.NOT_STARTED]: 'bg-gray-100 text-gray-800',
      [LEAGUE_STATUS.IN_PROGRESS]: 'bg-yellow-100 text-yellow-800',
      [LEAGUE_STATUS.COMPLETED]: 'bg-green-100 text-green-800'
    }
    return statusClassMap[status]
  }

  return {
    getLeagueStatus,
    getProgressPercentage,
    getStatusLabel,
    getStatusClasses,
  }
} 