<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\Team;
use App\Http\Requests\StoreLeagueRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\LeagueService;

class LeagueController extends Controller
{
    public function __construct(
        private LeagueService $leagueService
    ) {
    }

    public function index(): Response
    {
        $leagues = League::with(['teams', 'matches'])->get();

        return Inertia::render('Leagues/Index', [
            'leagues' => $leagues
        ]);
    }

    public function create(): Response
    {
        $teams = Team::all();

        return Inertia::render('Leagues/Create', [
            'teams' => $teams
        ]);
    }

    public function store(StoreLeagueRequest $request): RedirectResponse
    {
        $this->leagueService->createLeague($request->name, $request->team_ids);

        return redirect()->route('leagues.index')->with('success', 'League created successfully!');
    }

    public function show(League $league): Response
    {
        $data = $this->leagueService->getLeagueWithStandings($league);

        return Inertia::render('Leagues/Show', $data);
    }

    public function reset(League $league): RedirectResponse
    {
        $this->leagueService->resetLeague($league);

        return redirect()->back();
    }
}
