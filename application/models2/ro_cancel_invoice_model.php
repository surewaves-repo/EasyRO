<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoCancelInvoiceModel extends Eloquent
{
    protected $table = 'ro_cancel_invoice';
    protected $primaryKey = 'id';
    public $timestamps = false;
}