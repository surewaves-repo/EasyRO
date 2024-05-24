<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoProgressionMailStatusModel extends Eloquent
{
    protected $table = 'ro_progression_mail_status';
    protected $primaryKey = 'id';
    public $timestamps = False;
}