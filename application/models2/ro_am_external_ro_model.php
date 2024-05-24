<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoAmExternalRoModel extends Eloquent
{
    protected $table = 'ro_am_external_ro';
    protected $primaryKey = 'id';
    public $timestamps = false;
}