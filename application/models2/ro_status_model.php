<?php

namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoStatusModel extends Eloquent
{
    protected $table = 'ro_status';
    protected $primaryKey = 'status_id';
    public $timestamps = false;
}