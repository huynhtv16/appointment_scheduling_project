<?php

namespace App\Repositories\User;

use App\Repositories\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * Get list of users by role ID.
     *
     * @param int $id_Role
     * @return array
     */
    public function getListRole(int $id_Role);

    /**
     * Find user by email.
     *
     * @param string $email
     * @return \App\Models\User|null
     */
    public function findByEmail(string $email);
}
