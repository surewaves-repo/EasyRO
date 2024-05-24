<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class SvTvChannelModel extends Eloquent
{
    protected $table = 'sv_tv_channel';
    protected $primaryKey = 'tv_channel_id';
    public $timestamps = false;
}