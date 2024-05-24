<?php

class Channel_aggregate_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function call_channelwiseAggregate()
    {
        $this->db->query("call channelwise_aggregate()");
    }
}

?>