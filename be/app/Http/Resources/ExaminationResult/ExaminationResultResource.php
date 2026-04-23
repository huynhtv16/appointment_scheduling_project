<?php

namespace App\Http\Resources\ExaminationResult;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExaminationResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_appointment' => $this->id_appointment,
            'appointment' => [
                'id' => $this->appointment->id ?? null,
                'id_service' => $this->appointment->id_service ?? null,
                'patient' => [
                    'id' => $this->appointment->patient->id ?? null,
                    'name' => $this->appointment->patient->name ?? null,
                    'cccd' => $this->appointment->patient->cccd ?? null,
                    'gender' => $this->appointment->patient->gender ?? null,
                    'date' => $this->appointment->patient->date ?? null,
                ],
                'doctor' => [
                    'id' => $this->appointment->doctor->id ?? null,
                    'name' => $this->appointment->doctor->name ?? null,
                ],
            ],
            'diagnosis' => $this->diagnosis,
            'examination_time' => $this->examination_time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}