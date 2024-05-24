<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoTaskModel extends Eloquent
{
    protected $table = 'ro_task';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    public function RoMasterOperation()
    {
        return $this->belongsToMany('application\models2\RoMasterOperationModel', 'ro_operation_task', 'Task_Fk_Id', 'Operation_Fk_Id');
    }
}
