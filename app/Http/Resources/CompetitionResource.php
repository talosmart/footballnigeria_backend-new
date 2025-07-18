<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Find the active tournament calendar
        $activeTournament = collect($this['tournamentCalendar'])
            ->firstWhere('active', 'yes');

        return [
            'competition_id' => $this['id'],
            'competition_name' => $this['name'],
            'competition_type' => $this['type'],
            'active_tournament' => $activeTournament ? [
                'id'=>$activeTournament['id'],
                'start_date' => $activeTournament['startDate'],
                'end_date' => $activeTournament['endDate'],
                'name' => $activeTournament['name'],
            ] : null,
            'meta' => [
                'country' => $this['country'],
                'format' => $this['competitionFormat'],
                'known_name' => $this['knownName'],
            ]
        ];
    }
}
