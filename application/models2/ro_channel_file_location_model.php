<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoChannelFileLocationModel extends Eloquent
{
    protected $table = 'ro_channel_file_location';
    protected $primaryKey = 'id';
    public $timestamps = false;
}