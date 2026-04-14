<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserCollection;
use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    protected $userRepository;
    use ApiResponse;

    /**
     * Construct.
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get paginated list of users.
     * @return UserCollection
     */
    public function index(Request $request)
    {
        $users = $this->userRepository->paginate(
            $request->input('per_page', 10),
            $request->input('search'),
            $request->input('role')
        );
        return new UserCollection($users);
    }

    /**
     * Store a new user.
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = $this->userRepository->create($data);

        return response()->json([
            'message' => 'User created successfully',
            'data' => new UserResource($user)
        ], 201);
    }

    /**
     * Get user by ID.
     * @param string $id
     * @return UserResource
     */
    public function show(string $id)
    {
        $user = $this->userRepository->find($id);
        return new UserResource($user);
    }

    /**
     * Update user by ID.
     * @param UpdateUserRequest $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $data = $request->validated();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = $this->userRepository->update($id, $data);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Delete user by ID.
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $this->userRepository->delete($id);

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Get users by role ID.
     * @param int $id_Role
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersByRole($id_Role)
    {
        $users = $this->userRepository->getListRole($id_Role);
        return $this->success(new UserCollection($users), "success");
    }

    /**
     * Login user and return token.
     * @param Request $request (gmail, password)
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $result = $request->validated();

        $user = $this->userRepository->findByEmail($result['gmail']);

        if (!$user || !Hash::check($result['password'], $user->password)) {
            return $this->error('Đăng nhập thất bại', 401, null);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => new UserResource($user)
        ], 'Đăng nhập thành công', 200);
    }

    /**
     * Get list of doctors (id, name).
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDoctor()
    {
        $doctors = User::where('id_role', 2)
            ->select('id', 'name')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $doctors
        ]);
    }
}
