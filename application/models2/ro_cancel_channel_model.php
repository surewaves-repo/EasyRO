<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoCancelChannelModel extends Eloquent
{
    protected $table = 'ro_cancel_channel';
    protected $primaryKey = 'id';
    public $timestamps = false;
}