<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoOrdersModel extends Eloquent
{
    protected $table = 'ro_orders';
    protected $primaryKey = 'id';
    public $timestamps = false;
}