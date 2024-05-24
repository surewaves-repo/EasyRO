<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoOperationTaskModel extends Eloquent
{
    protected $table = 'ro_operation_task';
    protected $primaryKey = 'Id';
    public $timestamps = false;
}