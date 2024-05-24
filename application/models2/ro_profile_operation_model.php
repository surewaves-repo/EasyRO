<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoProfileOperationModel extends Eloquent
{
    protected $table = 'ro_profile_operation';
    protected $primaryKey = 'id';
    public $timestamps = false;
}