<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoClientContactModel extends Eloquent
{
    protected $table = 'ro_client_contact';
    protected $primaryKey = 'id';
    public $timestamps = false;
}