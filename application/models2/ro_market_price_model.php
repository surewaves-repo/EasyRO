<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoMarketPriceModel extends Eloquent
{
    protected $table = 'ro_market_price';
    protected $primaryKey = 'id';
    public $timestamps = false;
}