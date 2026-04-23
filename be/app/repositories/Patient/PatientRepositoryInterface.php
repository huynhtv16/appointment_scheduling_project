<?php

namespace App\Repositories\Patient;

use App\Repositories\RepositoryInterface;

interface PatientRepositoryInterface extends RepositoryInterface
{
    /**
     * Get Patient by Email
     * @return mixed
     */
    public function getByEmail(string $email);
}