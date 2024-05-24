<?php

namespace application\models_repo;

use application\repo\Repository;

include_once APPPATH . 'repo/repository.php';

class RoUserRegionRepo extends Repository
{
    public function model()
    {
        return 'application\models2\RoUserRegionModel';
    }
}