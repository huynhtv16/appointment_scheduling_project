<?php

namespace App\Repositories\ExaminationResult;

use App\Models\ExaminationResult;
use App\Repositories\BaseRepository;

class ExaminationResultRepository extends BaseRepository implements ExaminationResultRepositoryInterface
{
    /**
     * Get the model class
     *
     * @return string
     */
    public function getModel()
    {
        return \App\Models\ExaminationResult::class;
    }
    
}
