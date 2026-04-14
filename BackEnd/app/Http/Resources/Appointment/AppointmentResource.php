<?php

namespace App\Http\Resources\Appointment;

use App\Http\Resources\Patient\PatientResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'id'        => $this->id,
            'patient_id'=> $this->id_patient,
            'doctor_id' => $this->id_doctor,
            'service_id'=> $this->id_service,
            'date'      => $this->date,
            'symptom'   => $this->symptom,
            'status'    => $this->status,
            'patient'   => new PatientResource($this->whenLoaded('patient')),
            'doctor'    => new UserResource($this->whenLoaded('doctor')),
        ];
    }
}
