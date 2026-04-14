<?php

namespace App\Repositories\Patient;

use App\Repositories\Patient\PatientRepositoryInterface;
use App\Repositories\BaseRepository;

class PatientRepository extends BaseRepository implements PatientRepositoryInterface
{
    /**
     * Get the model
     *
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Patient::class;
    }

    /**
     * Get Patient by Email
     * @return mixed
     */
    public function getByEmail(string $gmail){
        return $this->model::where('gmail', $gmail)->first();
    }

    /**
     * Paginate patients with optional search and role filter.
     *
     * @param int $perPage
     * @param string|null $search
     * @param int|null $role
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 10, $search = null, $role = null)
    {
        return $this->model
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('gmail', 'LIKE', "%{$search}%")
                        ->orWhere('cccd', 'LIKE', "%{$search}%")
                        ->orWhere('bhyt', 'LIKE', "%{$search}%")
                        ->orWhere('gender', 'LIKE', "%{$search}%")
                        ->orWhere('prehistoric', 'LIKE', "%{$search}%")
                        ->orWhere('address', 'LIKE', "%{$search}%");
                });
            })
            ->paginate($perPage);
    }
}