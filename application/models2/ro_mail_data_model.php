<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoMailDataModel extends Eloquent
{
    protected $table = 'ro_mail_data';
    protected $primaryKey = 'id';
    public $timestamps = false;
}