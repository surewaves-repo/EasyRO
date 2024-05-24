<?php

namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RoStaticEmailsModel extends Eloquent
{
    protected $table = 'roStaticEmails';
    protected $primaryKey = 'id';
    public $timestamps = false;
}