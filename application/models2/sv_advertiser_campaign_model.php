<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class SvAdvertiserCampaignModel extends Eloquent
{
    protected $table = 'sv_advertiser_campaign';
    protected $primaryKey = 'campaign_id';
    public $timestamps = false;
}