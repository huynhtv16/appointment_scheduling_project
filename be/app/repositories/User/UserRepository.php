<?php

namespace App\Repositories\User;

use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Return the model class used by this repository.
     *
     * @return string User model class
     */
    public function getModel()
    {
        return User::class;
    }

    /**
     * Get all users with a specific role ID.
     *
     * @param int $id_Role Role ID to filter users
     * @return \Illuminate\Support\Collection
     */
    public function getListRole(int $id_Role)
    {
        return $this->model->where('id_role', $id_Role)->get();
    }

    /**
     * Find user by gmail field.
     *
     * @param string $email User's gmail
     * @return User|null Found user or null if not exists
     */
    public function findByEmail(string $email)
    {
        return User::where('gmail', $email)->first();
    }
    
}
