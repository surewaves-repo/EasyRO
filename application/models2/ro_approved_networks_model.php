<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoApprovedNetworksModel extends Eloquent
{
    protected $table = 'ro_approved_networks';
    protected $primaryKey = 'id';
    public $timestamps =false;
}