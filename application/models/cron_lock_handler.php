<?php
/**
 * Created by PhpStorm.
 * User: Shahrukh
 * Date: 6/18/2015
 * Time: 7:20 PM
 */

class Cron_Lock_Handler extends CI_Model
{

    public function __construct()
    {

        parent::__construct();

    }

    public function getFunctionLockStatusDateTime($functionName)
    {

        $query = " SELECT
                      status, lock_date_time
                    FROM
                      ro_cron_lock
                    WHERE
                      function_name_hash	 = '" . $functionName . "' ";

        $resultObj = $this->db->query($query);
        return $resultObj->result_array();

    }

    public function getLock($functionNameHash, $functionName)
    {

        $dateTime = date('Y-m-d H:i:s');

        $query = " INSERT INTO ro_cron_lock ( function_name, status, function_name_hash, lock_date_time )
                    VALUES ( '" . $functionName . "', 'locked', '" . $functionNameHash . "', '" . $dateTime . "' ) ";

        $this->db->query($query);

    }

    public function releaseAcquiredLock($functionNameHash)
    {

        $query = " UPDATE ro_cron_lock SET status = 'free' WHERE function_name_hash = '" . $functionNameHash . "' ";

        $this->db->query($query);

    }

    public function updateLockStatusDateTime($functionNameHash, $dateTime)
    {

        $query = " UPDATE ro_cron_lock SET status= 'locked', lock_date_time = '" . $dateTime . "'
                    WHERE function_name_hash = '" . $functionNameHash . "' ";

        $this->db->query($query);
    }

}

?>