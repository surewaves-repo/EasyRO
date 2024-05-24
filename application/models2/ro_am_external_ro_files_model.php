<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoAmExternalRoFilesModel extends Eloquent
{
    protected $table = 'ro_am_external_ro_files';
    protected $primaryKey = 'id';
    public $timestamps = false;
}