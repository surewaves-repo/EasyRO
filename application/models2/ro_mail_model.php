<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoMailModel extends Eloquent
{
    protected $table = 'ro_mail';
    protected $primaryKey = 'id';
    public $timestamps = false;
}