<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class SvCustomerModel extends Eloquent
{
    protected $table = 'sv_customer';
    protected $primaryKey = 'customer_id';
    public $timestamps = false;
}