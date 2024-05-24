<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoAgencyContactModel extends Eloquent
{
    protected $table = 'ro_agency_contact';
    protected $primaryKey = 'id';
    public $timestamps = false;
}