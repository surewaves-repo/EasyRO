<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoUserProfileModel extends Eloquent
{
    protected $table = 'ro_user_profile';
    protected $primaryKey = 'profile_id';
    public $timestamps = false;

    public function RoMasterOperation()
    {
        return $this->belongsToMany('application\models2\RoMasterOperationModel', 'ro_profile_operation', 'Profile_Fk_Id', 'Operation_Fk_Id');
    }

    public function RoTask()
    {
        return $this->belongsTo('application\models2\RoTaskModel', 'landing_tab', 'Id');
    }
}