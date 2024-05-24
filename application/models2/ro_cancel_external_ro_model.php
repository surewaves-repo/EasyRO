<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoCancelExternalRoModel extends Eloquent
{
    protected $table = 'ro_cancel_external_ro';
    protected $primaryKey = 'id';
    public $timestamps = false;
}