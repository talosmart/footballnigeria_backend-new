<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TournamentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
           
                'id' => $this['competition']['id'] ?? null,
                'name' => $this['competition']['name'] ?? null,
                'known_name' => $this['competition']['knownName'] ?? null,
                'code' => $this['competition']['competitionCode'] ?? null,
                'format' => $this['competition']['competitionFormat'] ?? null,
                'last_updated' => $this['competition']['lastUpdated'] ?? null,
          
            'tournament_calendar' => [
                'id' => $this['tournamentCalendar']['id'] ?? null,
                'name' => $this['tournamentCalendar']['name'] ?? null,
                'start_date' => $this['tournamentCalendar']['startDate'] ?? null,
                'end_date' => $this['tournamentCalendar']['endDate'] ?? null,
                'last_updated' => $this['tournamentCalendar']['lastUpdated'] ?? null,
            ],
            'match_dates' => collect($this['matchDate'] ?? [])->map(fn($date) => [
                'date' => $date['date'] ?? null,
                'number_of_games' => $date['numberOfGames'] ?? null,

                'data'=>MatchResource::collection($date['match'])
            ]),
        ];
    }
}
