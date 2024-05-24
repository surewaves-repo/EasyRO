<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class SvSwStatesModel extends Eloquent
{
    protected $table = 'sv_sw_states';
    protected $primaryKey = 'state_id';
    public $timestamps = false;
}