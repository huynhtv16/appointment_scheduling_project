<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_patient',
        'date',
        'symptom',
        'id_service',
        'id_doctor',
        'status',
    ];

    /**
     * Relationship: Appointment belongs to a Patient.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'id_patient');
    }

    /**
     * Relationship: Appointment belongs to a Doctor (User with doctor role).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'id_doctor');
    }

    /**
     * Relationship: Appointment has one ExaminationResult.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function examinationResult()
    {
        return $this->hasOne(ExaminationResult::class, 'id_appointment');
    }
}
