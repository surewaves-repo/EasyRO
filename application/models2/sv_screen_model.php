<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class SvScreenModel extends Eloquent
{
    protected $table = 'sv_screen';
    protected $primaryKey = 'screen_id';
    public $timestamps = false;
}