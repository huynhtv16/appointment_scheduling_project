<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use Illuminate\Http\Request;
use App\Repositories\Appointment\AppointmentRepositoryInterface;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\Appointment\AppointmentResource;

class AppointmentController extends Controller
{
    protected $appointmentRepo;
    use ApiResponse;

    public function __construct(AppointmentRepositoryInterface $appointmentRepo)
    {
        $this->appointmentRepo = $appointmentRepo;
    }

    /**
     * Store a new appointment.
     * @param StoreAppointmentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreAppointmentRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['status'] = 0;
            $exists = $this->appointmentRepo->checkExistBooking($validated['id_patient']);
            if ($exists) {
                return $this->error("Bạn đã có lịch hẹn chưa xử lý. Vui lòng kiểm tra lại lịch sử", 500);
            }
            $appointment = $this->appointmentRepo->create($validated);
            return $this->success(new AppointmentResource($appointment), "success", 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Get appointment history by user ID.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function historyByUser(Request $request, int $id)
    {
        $perPage = $request->get('per_page', 10);
        $status  = $request->get('status');
        $date    = $request->get('date');
        
        return  AppointmentResource::collection($this->appointmentRepo->getByUserId($id, $perPage, $status, $date));

    }

    /**
     * Get total appointments.
     * @return \Illuminate\Http\JsonResponse
     */
    public function total()
    {
        return response()->json([
            'total' => $this->appointmentRepo->getTotalAppointments()
        ]);
    }

    /**
     * Get successful appointments count.
     * @return \Illuminate\Http\JsonResponse
     */
    public function successAppointments()
    {
        return response()->json([
            'success' => $this->appointmentRepo->getSuccessAppointments()
        ]);
    }

    /**
     * Get failed appointments count.
     * @return \Illuminate\Http\JsonResponse
     */
    public function failed()
    {
        return response()->json([
            'failed' => $this->appointmentRepo->getFailedAppointments()
        ]);
    }

    /**
     * Get top symptoms by date.
     * @param Request $request (date optional)
     * @return \Illuminate\Http\JsonResponse
     */
    public function topSymptoms(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        return response()->json(
            $this->appointmentRepo->getTopSymptoms($date)
        );
    }

    /**
     * Get top doctors by date.
     * @param Request $request (date optional)
     * @return \Illuminate\Http\JsonResponse
     */
    public function topDoctors(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        return response()->json(
            $this->appointmentRepo->getTopDoctors($date)
        );
    }

    /**
     * Get doctor statistics for today.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDoctorAppointmentStatsToday()
    {
        $stats = $this->appointmentRepo->getDoctorAppointmentStatsToday();

        return response()->json([
            'status' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get total appointments by date range.
     * @param Request $request (from, to)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTotalAppointmentsByRange(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        if (!$from || !$to) {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng cung cấp from và to theo định dạng Y-m-d'
            ], 400);
        }

        try {
            $total = $this->appointmentRepo->getTotalAppointmentsByRange($from, $to);
            return response()->json($total);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get today's appointments for a doctor.
     * @param int $id_doctor 
     * @param Request $request (date optional)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTodayAppointments($id_doctor, Request $request)
    {
        $date = $request->input('date');
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);
        return AppointmentResource::collection($this->appointmentRepo->getTodayAppointments($id_doctor, $date, $search, $perPage));

    }
    public function getAll(){
    }
    /**
     * Get appointment by patient ID.
     * @param int|null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPatientAppointment($id = null)
    {
        $appointment = $this->appointmentRepo->getPatientAppointment($id);
        if (!$appointment) {
            return $this->error("Không tìm thấy lịch hẹn cho bệnh nhân này", 404);
        }
        return $this->success(new AppointmentResource($appointment), "success", 200);
    }

    /**
     * Get appointments by doctor and date.
     * @param Request $request (date)
     * @param int $doctorId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByDoctorAndDate(Request $request, $doctorId)
    {
        $date = $request->query('date');
        $appointments = $this->appointmentRepo->getByDoctorAndDate($date, $doctorId);
        return $this->success($appointments, "success");
    }

    /**
     * Get all patients with staff info.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllPatientsWithStaff(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $status     = $request->input('status', null);
        $service_id = $request->input('service_id', null);
        return  AppointmentResource::collection($this->appointmentRepo->getAllPatientsWithStaff($perPage, $status, $service_id));
    }


    /**
     * Update appointment status.
     * @param Request $request (status)
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $appointment = $this->appointmentRepo->find($id);
        if ($appointment->status == 2) {
            return $this->error("Bạn Không thể chuyển trạng thái đối với các ca hẹn đã khám", 500);
        }
        $appointment = $this->appointmentRepo->updateStatus($id, $request->status);
        return $this->success(new AppointmentResource($appointment), "Cập nhật thành công ", 200);
    }

    /**
     * Update appointment doctor.
     * @param Request $request (id_doctor)
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDoctor(Request $request, $id)
    {
        $appointment = $this->appointmentRepo->find($id);
        if ($appointment->status == 2) {
            return $this->error("Quá trình khám đã hoàn thành, không thể thay đổi", 500);
        }
        $appointment = $this->appointmentRepo->updateDoctor($id, $request->id_doctor);
        if (!$appointment) {
            return response()->json(['status' => false, 'message' => 'Appointment not found']);
        }
        return response()->json(['status' => true, 'message' => 'Doctor updated successfully']);
    }

    /**
     * Get appointment history by doctor ID.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistoryByDoctor($id, Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

       return  AppointmentResource::collection($this->appointmentRepo->getByDoctorId($id, $perPage, $search));
    }
}
