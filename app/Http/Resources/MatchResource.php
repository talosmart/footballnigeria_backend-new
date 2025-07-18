<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'] ?? null,
            'date' => $this['date'] ?? null,
            'time' => $this['time'] ?? null,
            'local_date' => $this['localDate'] ?? null,
            'local_time' => $this['localTime'] ?? null,
            'home_team' => [
                'id' => $this['homeContestantId'] ?? null,
                'name' => $this['homeContestantName'] ?? null,
                'official_name' => $this['homeContestantOfficialName'] ?? null,
                'short_name' => $this['homeContestantShortName'] ?? null,
                'code' => $this['homeContestantCode'] ?? null,
            ],
            'away_team' => [
                'id' => $this['awayContestantId'] ?? null,
                'name' => $this['awayContestantName'] ?? null,
                'official_name' => $this['awayContestantOfficialName'] ?? null,
                'short_name' => $this['awayContestantShortName'] ?? null,
                'code' => $this['awayContestantCode'] ?? null,
            ],
            'coverage_level' => $this['coverageLevel'] ?? null,
            'number_of_periods' => $this['numberOfPeriods'] ?? null,
            'period_length' => $this['periodLength'] ?? null,
        ];
    }
}
