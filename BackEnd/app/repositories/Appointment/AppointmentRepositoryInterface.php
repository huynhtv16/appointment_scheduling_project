<?php

namespace App\Repositories\Appointment;

use App\Repositories\RepositoryInterface;

interface AppointmentRepositoryInterface extends RepositoryInterface
{
    /**
     * Get total number of appointments
     *
     * @return int
     */
    public function getTotalAppointments();

    /**
     * Get total successful appointments
     *
     * @return int
     */
    public function getSuccessAppointments();

    /**
     * Get total failed appointments
     *
     * @return int
     */
    public function getFailedAppointments();

    /**
     * Get top symptoms for a given date
     *
     * @param string $date
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getTopSymptoms($date, $limit = 5);

    /**
     * Get top doctors for a given date
     *
     * @param string $date
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getTopDoctors($date, $limit = 7);

    /**
     * Get all appointments of a patient
     *
     * @param int $id
     * @return \Illuminate\Support\Collection
     */
    public function getByUserId(int $id, int $perPage);

    /**
     * Get total appointments grouped by day between two dates (inclusive)
     *
     * @param string $fromDate  format: Y-m-d
     * @param string $toDate    format: Y-m-d
     * @return \Illuminate\Support\Collection
     */
    public function getTotalAppointmentsByRange($fromDate, $toDate);

    /**
     * Get doctor appointment statistics for today
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDoctorAppointmentStatsToday();

    /**
     * Find appointment by ID
     *
     * @param int $id
     * @return \App\Models\Appointment|null
     */
    public function find($id);

    /**
     * Update status of an appointment
     *
     * @param int $id
     * @param int $status
     * @return \App\Models\Appointment|null
     */
    public function updateStatus($id, $status);

    /**
     * Get appointments of a doctor on a specific date
     *
     * @param string $date
     * @param int $doctorId
     * @return \Illuminate\Support\Collection
     */
    public function getByDoctorAndDate($date, $doctorId);

    /**
     * Check if a patient has double booking
     *
     * @param int $patientId
     * @return bool
     */
    public function checkExistBooking($patientId);

    /**
     * Get today's appointments for a doctor
     *
     * @param int $id_doctor 
     * @param string|null $date (format: Y-m-d), if null, use today's date
     * @return \Illuminate\Support\Collection
     */
    public function getTodayAppointments($id_doctor, $date = null, $perPage = 10, $search = null);

    /**
     * Get latest or specific patient appointment
     *
     * @param int $id_patient
     * @return \App\Models\Appointment|null
     */
    public function getPatientAppointment($id_patient);

    /**
     * Get all patients with assigned staff
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllPatientsWithStaff($perPage = 10);

    /**
     * Update doctor assigned to an appointment
     *
     * @param int $id
     * @param int $doctorId
     * @return \App\Models\Appointment|null
     */
    public function updateDoctor($id, $doctorId);

    /**
     * Get all appointments of a doctor
     *
     * @param int $id
     * @return \Illuminate\Support\Collection
     */
    public function getByDoctorId($id, $perPage = 10, $search = null);
}
