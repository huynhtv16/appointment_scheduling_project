<?php

namespace App\Http\Resources\Patient;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'gender'      => $this->gender,
            'date'        => $this->date,
            'address'     => $this->address,
            'gmail'       => $this->gmail,
            'cccd'        => $this->cccd,
            'bhyt'        => $this->bhyt,
            'prehistoric' => $this->prehistoric,
        ];
    }
}
