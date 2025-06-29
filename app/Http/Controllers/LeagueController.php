<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\Team;
use App\Http\Requests\StoreLeagueRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\LeagueService;
use App\DTOs\LeagueDTO;

/**
 * Controller for managing league operations
 */
class LeagueController extends Controller
{
    /**
     * Create a new LeagueController instance
     *
     * @param LeagueService $leagueService Service for league operations
     */
    public function __construct(
        private LeagueService $leagueService
    ) {
    }

    /**
     * Display a listing of leagues
     *
     * @return Response The leagues index page
     */
    public function index(): Response
    {
        $leagues = League::with(['teams', 'matches'])->get();

        // Get utility data for each league
        $leaguesUtility = $leagues->mapWithKeys(function ($league) {
            return [$league->id => $this->leagueService->getLeagueUtilityData($league)->toArray()];
        });

        return Inertia::render('Leagues/Index', [
            'leagues' => $leagues,
            'leaguesUtility' => $leaguesUtility
        ]);
    }

    /**
     * Show the form for creating a new league
     *
     * @return Response The league creation form
     */
    public function create(): Response
    {
        $teams = Team::all();

        return Inertia::render('Leagues/Create', [
            'teams' => $teams,
            'leagueConfig' => [
                'teamsPerLeague' => config('league.teams_per_league'),
                'totalWeeks' => config('league.total_weeks'),
            ]
        ]);
    }

    /**
     * Store a newly created league in storage
     *
     * @param StoreLeagueRequest $request The validated request data
     * @return RedirectResponse Redirect to leagues index with success message
     */
    public function store(StoreLeagueRequest $request): RedirectResponse
    {
        $this->leagueService->createLeague(LeagueDTO::fromRequest($request));

        return redirect()->route('leagues.index')->with('success', 'League created successfully!');
    }

    /**
     * Display the specified league with standings
     *
     * @param League $league The league to display
     * @return Response The league show page with standings
     */
    public function show(League $league): Response
    {
        $data = $this->leagueService->getLeagueWithStandings($league);

        // Add utility data as separate field
        $data['leagueUtility'] = $this->leagueService->getLeagueUtilityData($league)->toArray();

        return Inertia::render('Leagues/Show', $data);
    }

    /**
     * Reset a league to its initial state
     *
     * @param League $league The league to reset
     * @return RedirectResponse Redirect back to previous page
     */
    public function reset(League $league): RedirectResponse
    {
        $this->leagueService->resetLeague($league);

        return redirect()->back();
    }
}
