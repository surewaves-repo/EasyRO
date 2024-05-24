<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class SvAdvertiserDisplayModel extends Eloquent
{
    protected $table = 'sv_advertiser_display';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function SvNewAdvertiser()
    {
        return $this->belongsTo('application\models2\SvNewAdvertiserModel','advertiser_id','id');
    }
}