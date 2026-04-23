<?php

namespace App\Repositories;

use App\Repositories\RepositoryInterface;

abstract class BaseRepository implements RepositoryInterface
{
    protected $model;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setModel();
    }

    /**
     * Abstract method to get the model class.
     */
    abstract public function getModel();

    /**
     * Set the model instance.
     */
    public function setModel()
    {
        $this->model = app()->make($this->getModel());
    }

    /**
     * Get all records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Find a record by ID.
     *
     * @param  int $id
     * @return object|null
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new record.
     *
     * @param  array $object
     * @return object
     */
    public function create($object)
    {
        return $this->model->create($object);
    }

    /**
     * Update a record by Id.
     *
     * @param  int $id
     * @param  array $object
     * @return object|false
     */
    public function update($id,  $object)
    {
        $result = $this->find($id);
        if ($result) {
            $result->update($object);
            return $result;
        }
        return false;
    }

    /**
     * Delete a record by Id.
     *
     * @param  int $id
     * @return bool
     */
    public function delete($id)
    {
        $result = $this->find($id);
        if ($result) {
            $result->delete();
            return true;
        }
        return false;
    }

    /**
     * Paginate records.
     *
     * @param  int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 10,$search = null, $role = null)
    {
        return $this->model->when($search, function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('gmail', 'LIKE', "%{$search}%");
        })
        ->when($role, function ($q) use ($role) {
            $q->where('id_role', $role);
        })
        ->orderBy('id', 'desc')
        ->paginate($perPage);
    }
    
}
