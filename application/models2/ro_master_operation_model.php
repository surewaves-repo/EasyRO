<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoMasterOperationModel extends Eloquent
{
    protected $table = 'ro_master_operation';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    public function RoUserProfile()
    {
        return $this->belongsToMany('application\models2\RoUserProfileModel', 'ro_profile_operation', 'Operation_Fk_Id', 'Profile_Fk_Id');
    }

    public function RoTask()
    {
        return $this->belongsToMany('application\models2\RoTaskModel', 'ro_operation_task', 'Operation_Fk_Id', 'Task_Fk_Id');
    }
}