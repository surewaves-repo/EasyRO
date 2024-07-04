<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoNetworkRoReportDetailsModel extends Eloquent
{
    protected $table = 'ro_network_ro_report_details';
    protected $primaryKey = 'network_ro_number';
    public $timestamps = false;
}