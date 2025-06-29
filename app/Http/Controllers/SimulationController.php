<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Services\Interfaces\SimulationServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

/**
 * Controller for managing league simulation operations
 */
class SimulationController extends Controller
{
    /**
     * Create a new SimulationController instance
     *
     * @param SimulationServiceInterface $simulationService Service for simulation operations
     */
    public function __construct(
        private SimulationServiceInterface $simulationService
    ) {
    }

    /**
     * Simulate the next week of matches for a league
     *
     * @param League $league The league to simulate matches for
     * @return JsonResponse|RedirectResponse Response indicating simulation result
     */
    public function simulateWeek(League $league): JsonResponse|RedirectResponse
    {
        $currentWeek = $league->current_week + 1;

        if ($currentWeek > config('league.total_weeks')) {
            return $this->createResponse('League completed!', 400);
        }

        $this->simulationService->simulateWeek($league);

        return $this->createResponse("Week $currentWeek completed!", 200, $league);
    }

    /**
     * Simulate all remaining matches for a league
     *
     * @param League $league The league to simulate all matches for
     * @return JsonResponse|RedirectResponse Response indicating simulation result
     */
    public function simulateAll(League $league): JsonResponse|RedirectResponse
    {
        $this->simulationService->simulateAll($league);

        return $this->createResponse('League completed!', 200, $league);
    }

    /**
     * Create a response based on the request type
     *
     * @param string $message The response message
     * @param int $statusCode The HTTP status code
     * @param League|null $league The league data to include in response
     * @return JsonResponse|RedirectResponse The appropriate response type
     */
    private function createResponse(string $message, int $statusCode = 200, ?League $league = null): JsonResponse|RedirectResponse
    {
        if (request()->expectsJson()) {
            $response = ['message' => $message];

            if ($league) {
                $response['league'] = $league->load(['teams', 'matches.homeTeam', 'matches.awayTeam']);
            }

            return response()->json($response, $statusCode);
        }

        return redirect()->back();
    }
}