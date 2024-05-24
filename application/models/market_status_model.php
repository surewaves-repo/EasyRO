<?php

class Market_status_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function getMarketStatus($startDate, $endDate)
    {

        $resultObject = $this->db->query("CALL GetMarketStatus('$startDate', '$endDate')");

        return $resultObject;
    }

}

?>