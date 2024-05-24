<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class SvCategoryModel extends Eloquent
{
    protected $table = 'sv_category';
    protected $primaryKey = 'category_id';
    public $timestamps = false;
}