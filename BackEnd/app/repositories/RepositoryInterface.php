<?php

namespace App\Repositories;

interface RepositoryInterface
{
    /**
     * Get all
     * @return mixed
     */
    public function getAll();

    /**
     * Get one
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * Create
     * @param $object
     * @return mixed
     */
    public function create($object);

    /**
     * Update
     * @param $id
     * @param $object
     * @return mixed
     */
    public function update($id, $object);

    /**
     * Delete
     * @param $id
     * @return mixed
     */
    public function delete($id);
    
    /**
     * Paginate records.
     *
     * @param  int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 10,$search = null, $role = null);
}