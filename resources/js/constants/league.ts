// League configuration constants
export const LEAGUE_CONFIG = {
  TOTAL_WEEKS: 6,
  TEAMS_PER_LEAGUE: 4,
  PREDICTION_WEEKS_THRESHOLD: 3
} as const

// League status constants
export const LEAGUE_STATUS = {
  NOT_STARTED: 'not_started',
  IN_PROGRESS: 'in_progress',
  COMPLETED: 'completed'
} as const
