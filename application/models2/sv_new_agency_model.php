<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class SvNewAgencyModel extends Eloquent
{
    protected $table = 'sv_new_agency';
    protected $primaryKey = 'id';
    public $timestamps = false;
}