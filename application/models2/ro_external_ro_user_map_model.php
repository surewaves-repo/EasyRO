<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoExternalRoUserMapModel extends Eloquent
{
    protected $table = 'ro_external_ro_user_map';
    protected $primaryKey = 'id';
    public $timestamps = false;
}