<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         $data = $this->resource;

        return [
            'matchInfo' => [
                'id' => $data['matchInfo']['id'] ?? null,
                'coverageLevel' => $data['matchInfo']['coverageLevel'] ?? null,
                'date' => $data['matchInfo']['date'] ?? null,
                'time' => $data['matchInfo']['time'] ?? null,
                'week' => $data['matchInfo']['week'] ?? null,
                'numberOfPeriods' => $data['matchInfo']['numberOfPeriods'] ?? null,
                'periodLength' => $data['matchInfo']['periodLength'] ?? null,
                'lastUpdated' => $data['matchInfo']['lastUpdated'] ?? null,
                'description' => $data['matchInfo']['description'] ?? null,
                'sport' => [
                    'id' => $data['matchInfo']['sport']['id'] ?? null,
                    'name' => $data['matchInfo']['sport']['name'] ?? null,
                ],
                'ruleset' => [
                    'id' => $data['matchInfo']['ruleset']['id'] ?? null,
                    'name' => $data['matchInfo']['ruleset']['name'] ?? null,
                ],
                'competition' => [
                    'id' => $data['matchInfo']['competition']['id'] ?? null,
                    'name' => $data['matchInfo']['competition']['name'] ?? null,
                    'knownName' => $data['matchInfo']['competition']['knownName'] ?? null,
                    'competitionCode' => $data['matchInfo']['competition']['competitionCode'] ?? null,
                    'competitionFormat' => $data['matchInfo']['competition']['competitionFormat'] ?? null,
                    'country' => [
                        'id' => $data['matchInfo']['competition']['country']['id'] ?? null,
                        'name' => $data['matchInfo']['competition']['country']['name'] ?? null,
                    ],
                ],
                'tournamentCalendar' => [
                    'id' => $data['matchInfo']['tournamentCalendar']['id'] ?? null,
                    'startDate' => $data['matchInfo']['tournamentCalendar']['startDate'] ?? null,
                    'endDate' => $data['matchInfo']['tournamentCalendar']['endDate'] ?? null,
                    'name' => $data['matchInfo']['tournamentCalendar']['name'] ?? null,
                ],
                'stage' => [
                    'id' => $data['matchInfo']['stage']['id'] ?? null,
                    'formatId' => $data['matchInfo']['stage']['formatId'] ?? null,
                    'startDate' => $data['matchInfo']['stage']['startDate'] ?? null,
                    'endDate' => $data['matchInfo']['stage']['endDate'] ?? null,
                    'name' => $data['matchInfo']['stage']['name'] ?? null,
                ],
                'series' => [
                    'id' => $data['matchInfo']['series']['id'] ?? null,
                    'formatId' => $data['matchInfo']['series']['formatId'] ?? null,
                    'name' => $data['matchInfo']['series']['name'] ?? null,
                ],
                'contestant' => collect($data['matchInfo']['contestant'] ?? [])->map(function ($contestant) {
                    return [
                        'id' => $contestant['id'] ?? null,
                        'name' => $contestant['name'] ?? null,
                        'shortName' => $contestant['shortName'] ?? null,
                        'officialName' => $contestant['officialName'] ?? null,
                        'code' => $contestant['code'] ?? null,
                        'position' => $contestant['position'] ?? null,
                        'country' => [
                            'id' => $contestant['country']['id'] ?? null,
                            'name' => $contestant['country']['name'] ?? null,
                        ],
                    ];
                }),
            ],
            'liveData' => [
                'matchDetails' => [
                    'periodId' => $data['liveData']['matchDetails']['periodId'] ?? null,
                    'matchStatus' => $data['liveData']['matchDetails']['matchStatus'] ?? null,
                ],
            ],
        ];
    }
}
