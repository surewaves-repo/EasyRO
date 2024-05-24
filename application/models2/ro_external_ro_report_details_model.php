<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoExternalRoReportDetailsModel extends Eloquent
{
    protected $table = 'ro_external_ro_report_details';
    protected $primaryKey = 'customer_ro_number';
    public $timestamps = false;
}