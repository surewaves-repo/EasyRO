<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class SvAgencyDisplayModel extends Eloquent
{
    protected $table = 'sv_agency_display';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function SvNewAgency()
    {
        return $this->belongsTo('application\models2\SvNewAgencyModel','agency_id','id');
    }
}