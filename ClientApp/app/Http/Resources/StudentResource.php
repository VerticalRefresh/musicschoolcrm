<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'first_name'   => $this->first_name,
            'last_name'    => $this->last_name,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'birthday'     => $this->birthday?->toDateString(),
            'subscription' => (float) $this->subscription,
            'balance'      => (float) $this->balance,
            'tutor_id'     => $this->tutor_id,
            'guardian_id'  => $this->guardian_id,
            'franchise_id' => $this->franchise_id,

            // Optional embeds if preloaded:
            'tutor'        => $this->whenLoaded('tutor', fn() => [
                                'id' => $this->tutor->id,
                                'first_name' => $this->tutor->first_name,
                                'last_name' => $this->tutor->last_name,
                              ]),
            'guardian'     => $this->whenLoaded('guardian', fn() => [
                                'id' => $this->guardian->id,
                                'first_name' => $this->guardian->first_name,
                                'last_name' => $this->guardian->last_name,
                              ]),
            'franchise'    => $this->whenLoaded('franchise', fn() => [
                                'id' => $this->franchise->id,
                                'email' => $this->franchise->email,
                              ]),
            'instruments'  => $this->whenLoaded('instruments', function () {
                                return $this->instruments->map(fn($i) => [
                                    'id' => $i->id, 'name' => $i->name,
                                    'level' => $i->pivot->level,
                                    'is_primary' => (bool) $i->pivot->is_primary,
                                    'started_on' => $i->pivot->started_on,
                                ]);
                              }),
            'address' => $this->whenLoaded('address', fn () => [
              'line1'   => $this->address->line1,
              'line2'   => $this->address->line2,
              'city'    => $this->address->city,
              'state'   => $this->address->state,
              'zip'     => $this->address->zip,
              'country' => $this->address->country,
              ]),

            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->updated_at?->toISOString(),
        ];
    }
}
