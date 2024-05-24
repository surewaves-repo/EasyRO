<?php

class Cron_Lock_Manager
{

    private $CIObject;

    function __construct()
    {

        $this->CIObject =& get_instance();

        $this->CIObject->load->model('cron_lock_handler');

    }

    public function lock($functionName)
    {

        $returnVale = FALSE;

        $resultStatusLockDateTime = $this->CIObject->cron_lock_handler->getFunctionLockStatusDateTime(md5($functionName));

        if (!empty($resultStatusLockDateTime)) {

            $status = $resultStatusLockDateTime[0]["status"];
            $lockedDateTime = $resultStatusLockDateTime[0]["lock_date_time"];

            if ($this->checkStatus($status) && $this->checkLockDuration($lockedDateTime)) {

                $this->reAcquireLockStatusDateTime($functionName);
                $returnVale = TRUE;

            } else if (!$this->checkStatus($status)) {

                $this->reAcquireLockStatusDateTime($functionName);
                $returnVale = TRUE;

            } else if ($this->checkStatus($status)) {

                $returnVale = FALSE;

            }

        } else {

            $this->acquireLock($functionName);
            $returnVale = TRUE;
        }

        return $returnVale;
    }

    private function checkStatus($status)
    {

        $returnVale = FALSE;

        if (trim($status) == "locked") {

            $returnVale = TRUE;

        }

        return $returnVale;
    }

    private function checkLockDuration($lockDateTime)
    {

        $returnVale = FALSE;

        $time = time();

        if ($time - strtotime($lockDateTime) > (3600)) {

            $returnVale = TRUE;

        }

        return $returnVale;
    }

    private function reAcquireLockStatusDateTime($functionName)
    {

        $dateTime = date('Y-m-d H:i:s');

        $this->CIObject->cron_lock_handler->updateLockStatusDateTime(md5($functionName), $dateTime);

    }

    private function acquireLock($functionName)
    {

        $this->CIObject->cron_lock_handler->getLock(md5($functionName), $functionName);

    }

    public function unlock($functionName)
    {

        $this->CIObject->cron_lock_handler->releaseAcquiredLock(md5($functionName));

    }

}

?>