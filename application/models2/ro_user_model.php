<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoUserModel extends Eloquent
{
    protected $table = 'ro_user';
    protected $primaryKey = 'user_id';
    public $timestamps = false;
}