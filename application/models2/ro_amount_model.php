<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoAmountModel extends Eloquent
{
    protected $table = 'ro_amount';
    protected $primaryKey = 'id';
    public $timestamps = False;
}