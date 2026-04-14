<?php

namespace App\Http\Controllers;

use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Patient\StorePatientRequest;
use App\Http\Resources\Patient\PatientCollection;
use App\Http\Resources\Patient\PatientResource;
use App\Http\Resources\LoginResource;
use App\Repositories\Patient\PatientRepositoryInterface;
use App\Http\Resources\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
class PatientController extends Controller
{
    protected $patientRepo;
    use ApiResponse;

    /**
     * Construct.
     */
    public function __construct(PatientRepositoryInterface $patient)
    {
        $this->patientRepo = $patient;
    }

    /**
     * Get all patients.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $patients = $this->patientRepo->paginate(
            $request->input('per_page', 10),
            $request->input('search'),
            $request->input('role')
        );

        return new PatientCollection($patients);
    }

    /**
     * Authenticate patient login.
     * @param StorePatientRequest $request (gmail, password)
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(StorePatientRequest $request)
    {
        try {
            $validated = $request->validated();
            $patient = $this->patientRepo->getByEmail($validated['gmail']);

            if (!$patient || !Hash::check($validated['password'], $patient->password)) {
                return $this->error("Tài khoản hoặc mật khẩu không chính xác");
            }

            $token = $patient->createToken('auth_token')->plainTextToken;
            $response = [
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'patient'         => new LoginResource($patient),
            ];

            return $this->success($response, " Đăng nhập thành công");
        } catch (\Exception $e) {
            return $this->error($e->getMessage() ?: "Đăng nhập thất bại");
        }
    }

    /**
     * Store a new patient.
     * @param StorePatientRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePatientRequest $request)
    {
        try {
            $validated = $request->validated();
            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }
            $patient = $this->patientRepo->create($validated);
            return $this->success(new PatientResource($patient), "success");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Get patient by ID.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $patient = $this->patientRepo->find($id);
        if ($patient) {
            return $this->success($patient, "success");
        }
        return $this->error("Không tìm thấy người dùng");
    }

    /**
     * Update patient by ID.
     * @param StorePatientRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StorePatientRequest $request, int $id)
    {
        $check = $this->patientRepo->find($id);
        if (!$check) {
            return $this->error("not found", 404);
        }

        try {
            $validated = $request->validated();
            $patient = $this->patientRepo->update($id, $validated);
            return $this->success(new PatientResource($patient), "success");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }


}
