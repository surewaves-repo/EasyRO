<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class SvNewAdvertiserModel extends Eloquent
{
    protected $table = 'sv_new_advertiser';
    protected $primaryKey = 'id';
    public $timestamps = false;
}