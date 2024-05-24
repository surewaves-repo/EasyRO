<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class SvSwMarketModel extends Eloquent
{
    protected $table = 'sv_sw_market';
    protected $primaryKey = 'id';
    public $timestamps = false;
}