<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Audit_Trail_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function insertAuditMessages()
    {
        //Check Audit Type Existing From ro_audit_type
        //If Yes,insert into ro_audit_messages
    }
}        

