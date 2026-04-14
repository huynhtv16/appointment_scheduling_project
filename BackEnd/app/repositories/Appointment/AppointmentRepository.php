<?php

namespace App\Repositories\Appointment;

use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use App\Repositories\Appointment\AppointmentRepositoryInterface;
use Carbon\Carbon;

class AppointmentRepository extends BaseRepository implements AppointmentRepositoryInterface
{
    /**
     * Get the model class
     *
     * @return string
     */
    public function getModel()
    {
        return Appointment::class;
    }

    /**
     * Get total number of appointments
     *
     * @return int
     */
    public function getTotalAppointments()
    {
        return $this->model->count();
    }

    /**
     * Get total successful appointments
     *
     * @return int
     */
    public function getSuccessAppointments()
    {
        return $this->model->where('status', 2)->count();
    }

    /**
     * Get total failed appointments
     *
     * @return int
     */
    public function getFailedAppointments()
    {
        return $this->model->where('status', -1)->count();
    }

    /**
     * Get top symptoms for a given date
     *
     * @param string $date
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getTopSymptoms($date, $limit = 5)
    {
        return $this->model->select(
            'id_service',
            DB::raw('DATE(date) as booking_date'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as success'),
            DB::raw('SUM(CASE WHEN status = -1 THEN 1 ELSE 0 END) as failed')
        )
            ->whereDate('date', $date)
            ->groupBy('id_service', DB::raw('DATE(date)'))
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top doctors for a given date
     *
     * @param string $date
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getTopDoctors($date, $limit = 7)
    {
        return $this->model->select('id_doctor', DB::raw('COUNT(*) as total'))
            ->groupBy('id_doctor')
            ->orderByDesc('total')
            ->limit($limit)
            ->with('doctor')
            ->get();
    }

    /**
     * Get doctor appointment statistics in a date range
     *
     * @param string $fromDate
     * @param string $toDate
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getDoctorAppointmentStats($fromDate, $toDate, $limit = 10)
    {
        return $this->model->select(
            'id_doctor',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as success'),
            DB::raw('SUM(CASE WHEN status = -1 THEN 1 ELSE 0 END) as failed')
        )
            ->whereBetween('date', [$fromDate, $toDate])
            ->groupBy('id_doctor')
            ->orderByDesc('total')
            ->limit($limit)
            ->with('doctor')
            ->get();
    }

    /**
     * Get total appointments grouped by day in a month
     *
     * @param int $year
     * @param int $month
     * @return \Illuminate\Support\Collection
     */

    /**
     * Get total appointments grouped by day between two dates (inclusive)
     *
     * @param string $fromDate  format: Y-m-d
     * @param string $toDate    format: Y-m-d
     * @return \Illuminate\Support\Collection
     */
    public function getTotalAppointmentsByRange($fromDate, $toDate)
    {
        return $this->model
            ->selectRaw('DATE(date) as day, COUNT(*) as total')
            ->whereBetween('date', [$fromDate, $toDate])
            ->groupBy('day')
            ->orderBy('day')
            ->get();
    }

    /**
     * Get doctor appointment statistics for today
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDoctorAppointmentStatsToday()
    {
        return $this->model->select(
            'id_doctor',
            DB::raw('COUNT(*) as total')
        )
            ->whereDate('date', today())
            ->groupBy('id_doctor')
            ->orderByDesc('total')
            ->with('doctor')
            ->get();
    }

    /**
     * Get appointments by patient ID with optional status and date filter
     *
     * @param int $id
     * @param int $perPage
     * @param string|null $status
     * @param string|null $date (format: Y-m-d)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByUserId(int $id, int $perPage = 10, $status = null, $date = null)
    {
        return $this->model
            ->with(['doctor', 'patient'])
            ->where('id_patient', $id)
            ->when($status !== null, function ($q) use ($status) {
            $q->where('status', $status);
            })
            ->when($date, function ($q) use ($date) {
                $q->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->paginate($perPage);
    }

    /**
     * Find appointment by ID
     *
     * @param int $id
     * @return Appointment|null
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Update status of an appointment
     *
     * @param int $id
     * @param int $status
     * @return Appointment|null
     */
    public function updateStatus($id, $status)
    {
        $appointment = $this->model->find($id);
        if ($appointment) {
            $appointment->status = $status;
            $appointment->save();
        }
        return $appointment;
    }

    /**
     * Get appointments of a doctor on a specific date
     *
     * @param string $date
     * @param int $doctorId
     * @return \Illuminate\Support\Collection
     */
    public function getByDoctorAndDate($date, $doctorId)
    {
        return $this->model::where('id_doctor', $doctorId)
            ->whereDate('date', $date)
            ->get(['id', 'date']);
    }

    /**
     * Check if patient has a double booking
    
     * @param int $patientId
     * @return bool
     */
    function checkExistBooking($patientId)
    {
        return $this->model->query()
            ->where('id_patient', $patientId)
            ->whereIn('status', [0, 1])
            ->where('date', '>=', now())
            ->exists();
    }

    /**
     * @param int $id_doctor
     * @param string|null $date (format: Y-m-d), if null, use today's date
     * @return \Illuminate\Support\Collection
     */
    public function getTodayAppointments($id_doctor, $date = null, $search = null, $perPage = 10)
    {
        return $this->model->with('patient')
            ->select('id', 'id_patient', 'date', 'id_service', 'status', 'id_doctor')
            ->whereDate('date', $date)
            ->where('status', 1)
            ->where('id_doctor', $id_doctor)
            ->when($search, function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            })
            ->orderBy('date', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get latest or specific patient appointment
     * @param int|null $id
     * @return Appointment|\Illuminate\Database\Eloquent\Model|null
     */
    public function getPatientAppointment($id = null)
    {
        if ($id) {
            return $this->model->with(['patient'])->find($id);
        } else {
            return $this->model->with(['patient'])
                ->where('status', 2)
                ->orderBy('date', 'desc')
                ->first();
        }
    }

    /**
     * Get all patients with assigned staff
     * @return \Illuminate\Support\Collection
     */
    public function getAllPatientsWithStaff($perPage = 10, $status = null, $service_id = null)
    {
        return $this->model->with(['patient', 'doctor'])
            ->select('id_patient', 'date', 'id_service', 'status', 'id_doctor', 'id')
            ->when($status !== null, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($service_id !== null, function ($query) use ($service_id) {
                return $query->where('id_service', $service_id);
            })
            ->orderBy('date', 'desc')
            ->distinct()
            ->paginate($perPage);
    }

    /**
     * Update doctor assigned to an appointment
     * @param int $id
     * @param int $doctorId
     * @return Appointment|null
     */
    public function updateDoctor($id, $doctorId)
    {
        $appointment = $this->model->find($id);
        if ($appointment) {
            $appointment->id_doctor = $doctorId;
            $appointment->save();
        }
        return $appointment;
    }

    /**
     * Get all appointments of a doctor
     * @param int $id
     * @return \Illuminate\Support\Collection
     */
    public function getByDoctorId($id, $perPage = 10, $search = null)
    {
        return $this->model
            ->with(['doctor', 'patient'])
            ->when($id != 0, function ($q) use ($id) {
                $q->where('id_doctor', $id);
            })
            ->where('status', 2)
            ->when($search, function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            })
            ->orderBy('date', 'desc')
            ->paginate($perPage);
    }

}
