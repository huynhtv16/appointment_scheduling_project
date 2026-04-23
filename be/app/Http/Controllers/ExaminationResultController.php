<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExaminationResult\StoreExaminationResultRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\ExaminationResult\ExaminationResultResource;
use App\Repositories\ExaminationResult\ExaminationResultRepositoryInterface;
use App\Repositories\Appointment\AppointmentRepositoryInterface;
use Carbon\Carbon;

class ExaminationResultController extends Controller
{
    protected $appointmentRepo;
    protected $examinationRepo;
    use ApiResponse;

    /**
     * Contruct.
     */
    public function __construct(
        AppointmentRepositoryInterface $appointmentRepo,
        ExaminationResultRepositoryInterface $resultRepo
    ) {
        $this->appointmentRepo = $appointmentRepo;
        $this->examinationRepo = $resultRepo;
    }

    /**
     * Submit or update a diagnosis for an appointment.
     *
     * @param StoreExaminationResultRequest $request
     * @param int $id Appointment ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreExaminationResultRequest $request, $id)
    {
        $examination = $request->validated();
        $examination['id_appointment'] = $id;

        $existing = $this->examinationRepo->find($id);

        if ($existing) {
            $result = $this->examinationRepo->update($id, $examination);
        } else {
            $result = $this->examinationRepo->create($examination);
        }

        // Update appointment status to completed
        $this->appointmentRepo->update($id, ['status' => 2]);
        return $this->success($result, "Diagnosis submitted successfully", 201);
    }


    /**
     * Show examination result by ID.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $examination = $this->examinationRepo->find($id);
        return $this->success(new ExaminationResultResource($examination), "success", 200);
    }
}
