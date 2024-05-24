<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class SvNewBrandModel extends Eloquent
{
    protected $table = 'sv_new_brand';
    protected $primaryKey = 'id';
    public $timestamps = false;
}