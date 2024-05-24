<?php


namespace application\models2;

use Illuminate\Database\Eloquent\Model as Eloquent;

class SvJobQueueModel extends Eloquent
{
    protected $table = 'sv_job_queue';
    protected $primaryKey = 'job_id';
    public $timestamps = false;
}