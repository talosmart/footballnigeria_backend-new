<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContestantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this;

        return [
            'contestantId' => $data['contestantId'] ?? null,
            'contestantName' => $data['contestantName'] ?? null,
            'contestantShortName' => $data['contestantShortName'] ?? null,
            'contestantClubName' => $data['contestantClubName'] ?? null,
            'contestantCode' => $data['contestantCode'] ?? null,
            'tournamentCalendar' => [
                'id' => $data['tournamentCalendarId'] ?? null,
                'startDate' => $data['tournamentCalendarStartDate'] ?? null,
                'endDate' => $data['tournamentCalendarEndDate'] ?? null,
            ],
            'competition' => [
                'id' => $data['competitionId'] ?? null,
                'name' => $data['competitionName'] ?? null,
            ],
            'venue' => [
                'id' => $data['venueId'] ?? null,
                'name' => $data['venueName'] ?? null,
            ],
            'persons' => collect($data['person'] ?? [])->map(function ($person) {
                return [
                    'id' => $person['id'] ?? null,
                    'firstName' => $person['firstName'] ?? null,
                    'lastName' => $person['lastName'] ?? null,
                    'shortFirstName' => $person['shortFirstName'] ?? null,
                    'shortLastName' => $person['shortLastName'] ?? null,
                    'gender' => $person['gender'] ?? null,
                    'matchName' => $person['matchName'] ?? null,
                    'nationality' => $person['nationality'] ?? null,
                    'nationalityId' => $person['nationalityId'] ?? null,
                    'secondNationality' => $person['secondNationality'] ?? null,
                    'secondNationalityId' => $person['secondNationalityId'] ?? null,
                    'position' => $person['position'] ?? null,
                    'type' => $person['type'] ?? null,
                    'placeOfBirth' => $person['placeOfBirth'] ?? null,
                    'startDate' => $person['startDate'] ?? null,
                    'endDate' => $person['endDate'] ?? null,
                    'active' => $person['active'] ?? null,
                ];
            })
        ];
    }
}
