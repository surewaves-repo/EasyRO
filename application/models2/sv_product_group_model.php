<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class SvProductGroupModel extends Eloquent
{
    protected $table = 'sv_product_group';
    protected $primaryKey = 'id';
    public $timestamps = false;
}