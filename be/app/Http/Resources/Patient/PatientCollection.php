<?php

namespace App\Http\Resources\Patient;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PatientCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
         return $this->collection->map(function ($patient){        
            return [
                'id'          => $patient->id,
                'name'        => $patient->name,
                'gender'      => $patient->gender,
                'date'        => $patient->date,
                'address'     => $patient->address,
                'gmail'       => $patient->gmail,
                'cccd'        => $patient->cccd,
                'bhyt'        => $patient->bhyt,
                'prehistoric' => $patient->prehistoric,
            ];
        })->all();
    }
}
