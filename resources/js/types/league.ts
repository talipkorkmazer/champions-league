export interface Team {
    id: number;
    name: string;
    strength: number;
}

export interface Match {
    id: number;
    week: number;
    home_team: Team;
    away_team: Team;
    home_score: number | null;
    away_score: number | null;
    is_played: boolean;
}

export interface Standing {
    team: Team;
    played: number;
    won: number;
    drawn: number;
    lost: number;
    goals_for: number;
    goals_against: number;
    goal_difference: number;
    points: number;
    championship_percentage?: number;
}

export interface LeagueUtility {
    status: LeagueStatus;
    progressPercentage: number;
    statusLabel: string;
    statusClasses: string;
    canSimulate: boolean;
    remainingWeeks: number;
    totalWeeks: number;
}

export interface League {
    id: number;
    name: string;
    current_week: number;
    teams: Team[];
    matches: Match[];
}

export type LeagueStatus = 'not_started' | 'in_progress' | 'completed';
