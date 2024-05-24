<?php

class Mso_payment_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function getBillingName($start_date, $end_date)
    {
        $query = "select distinct sc.customer_id,customer_name,billing_name from sv_customer sc "
            . "Inner Join ro_mso_invoice_upload_details rmiud on sc.customer_id = rmiud.network_id "
            . "Inner Join ro_mso_invoice_upload_data rmiuda on  rmiud.invoice_number = rmiuda.invoice_number "
            . "where rmiuda.invoice_date between '$start_date' and '$end_date' ";

        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function getAllInvoiceData($start_date, $end_date, $network_id)
    {

        $query = "select group_concat(rnrrd.network_ro_number SEPARATOR '</br>') AS network_ro_number,rmiude.network_id, rmiude.ro_id ,rmiud.invoice_date,rmiud.invoice_number,rmiud.Ro_Amount,rmiud.Actual_Ro_Amount,rmiud.Ro_Amount_Payable,rmiud.Actual_Ro_Amount_Payable,internal_ro, rmiud.file,sc.billing_name
                    from ro_mso_invoice_upload_data rmiud 
                    inner join ro_mso_invoice_upload_details rmiude on rmiud.invoice_number = rmiude.invoice_number 
                    inner join ro_am_external_ro raer on rmiude.ro_id = raer.id
                    inner join sv_customer sc on sc.customer_id = rmiude.network_id
                    inner join ro_network_ro_report_details rnrrd on rnrrd.customer_name = sc.customer_name  AND raer.internal_ro = rnrrd.internal_ro_number
                    where rmiud.invoice_date between '$start_date' and '$end_date'";
        if (isset($network_id) || !empty($network_id)) {
            $query .= " AND rmiude.network_id=$network_id ";
        }
        $query .= " group by rmiud.invoice_number ";

        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function getUpdatedInvoiceData($start_date, $end_date, $network_id)
    {

        $query = "Select X.invoice_date,X.invoice_number,group_concat(distinct sc.billing_name SEPARATOR '</br>') as billing_name,
                    X.file,X.ro_amount,X.actual_ro_amount,X.ro_amount_payable,X.actual_ro_amount_payable,X.new_payment
                    from
                    (SELECT rmiud.invoice_date,
                           rmiud.invoice_number,
                           rmiud.file,
                           rmiud.ro_amount,
                           rmiud.actual_ro_amount,
                           rmiud.ro_amount_payable,
                           rmiud.actual_ro_amount_payable,
                           sum(Round(IF(rmp.new_payment IS NULL,0.0000,rmp.new_payment), 2) ) AS new_payment
                    FROM   ro_mso_invoice_upload_data rmiud    
                           LEFT JOIN ro_mso_payment rmp ON rmiud.invoice_number = rmp.invoice_number       
                    WHERE  rmiud.invoice_date BETWEEN '$start_date' AND '$end_date' group by rmiud.invoice_number) X 
                    inner Join ro_mso_invoice_upload_details rmiud on X.invoice_number = rmiud.invoice_number 
                    inner join sv_customer sc on rmiud.network_id = sc.customer_id";
        if (isset($network_id) || !empty($network_id)) {
            $query .= " AND rmiude.network_id=$network_id ";
        }
        $query .= " group by rmiud.invoice_number ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function downloadInvoicePdf($InvoiceNumber)
    {

        $query = "SELECT file FROM ro_mso_invoice_upload_data WHERE invoice_number = '$InvoiceNumber' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();

    }

    public function getPayment($invoiceNumber)
    {

        $query = "select rmp.amount_paid,rmp.new_payment, rmiud.invoice_number, rmiud.Ro_Amount, rmiud.Ro_Amount_Payable, rmiud.Actual_Ro_Amount,  rmiud.Actual_Ro_Amount_Payable"
            . " from ro_mso_invoice_upload_data rmiud left join ro_mso_payment rmp on rmiud.invoice_number = rmp.invoice_number"
            . " where rmiud.invoice_number = '$invoiceNumber'";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function update_payment_record_details($data)
    {

        $this->db->trans_begin();
        $this->db->insert('ro_mso_payment', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }

    public function getDetails($invoiceNumber)
    {

        $query = "SELECT payment_date,
                   Round(new_payment,2),
                   Round(basic_amount,2),
                   Round(service_tax,2),
                   Round(tds,2),
                   mode_of_payment,
                   bank_name,
                   transaction_number,
                   transaction_date,
                   remarks
            FROM   ro_mso_payment
            WHERE  invoice_number = '$invoiceNumber' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();

    }

    public function getNetwork()
    {

        $query = "select customer_id, customer_name from sv_customer";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function getRoData($startDate, $endDate, $customer_id)
    {
        $query = "  SELECT *
                        FROM 
                            (SELECT DISTINCT raer.id,raer.internal_ro,rnrd.net_amount_payable,rmp.mail_status
                            FROM ro_am_external_ro raer
                            INNER JOIN ro_approved_networks ran ON raer.internal_ro = ran.internal_ro_number
                            INNER JOIN ro_network_ro_report_details rnrd on ran.internal_ro_number = rnrd.internal_ro_number
                            LEFT JOIN ro_mail_performance rmp on raer.id = rmp.ro_id
                            WHERE raer.test_user_creation =0
                                    AND ran.customer_id = $customer_id AND ran.customer_name = rnrd.customer_name
                                    AND ((raer.camp_start_date >= '$startDate'
                                    AND raer.camp_end_date <= '$endDate')
                                    OR (raer.camp_start_date <= '$startDate'
                                    AND raer.camp_end_date
                                BETWEEN '$startDate'
                                    AND '$endDate')
                                    OR (raer.camp_start_date
                                BETWEEN '$startDate'
                                    AND '$endDate'
                                    AND raer.camp_end_date >= '$endDate')
                                    OR (raer.camp_start_date <= '$startDate'
                                    AND raer.camp_end_date >= '$endDate')) ) T ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function performanceData($internalroNumber, $customer_id)
    {


        $query = "SELECT   Sum(spotschedulesec)            AS spotScheduled, 
                                      Sum(spotplayedsec)              AS spotPlayed, 
                                      Sum(bannerschedulesec)          AS banScheduled, 
                                      Sum(bannerplayedsec)            AS banPlayed, 
                                      Sum(makegood_spotschedulesec)   AS goodSpotScheduled, 
                                      Sum(makegood_spotplayedsec)     AS goodSpotPlayed, 
                                      Sum(makegood_bannerschedulesec) AS goodBanScheduled, 
                                      Sum(makegood_bannerplayedsec)   AS goodBanPlayed       
                            FROM   sv_mis_channel_fct_aggregate 
                            WHERE  customer_id = $customer_id
                                   AND internal_ro_number = '$internalroNumber'";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();


    }

    public function sendMail($roId, $networkId)
    {

        $query = " INSERT into ro_mail_performance(ro_id,network_id,mail_data,mail_status) VALUES($roId,$networkId,'',1)";
        $this->db->query($query);

    }

    public function downloadExcel($start_date, $end_date, $network_id)
    {

        $query = "SELECT DISTINCT rmiud.invoice_date,
                   rmiud.invoice_number,
                   rmiud.file,
                   rmiud.ro_amount,
                   rmiud.actual_ro_amount,
                   rmiud.ro_amount_payable,
                   rmiud.actual_ro_amount_payable,
                   IF(rmp.remaining_amount IS NULL,0.0000,rmp.remaining_amount) as remaining_amount,
                   IF(rmp.amount_paid IS NULL,0.0000,rmp.amount_paid) as amount_paid,
                   IF(rmp.new_payment IS NULL,0.0000,rmp.new_payment) as new_payment,
                   IF(rmp.payment_date IS NULL,'0000-00-00',rmp.payment_date) as payment_date,
                   IF(rmp.basic_amount IS NULL,0.0000,rmp.basic_amount) as basic_amount,
                   IF(rmp.tds IS NULL,0.0000,rmp.tds) as tds,
                   IF(rmp.service_tax IS NULL,0.0000,rmp.service_tax) as service_tax,
                   IF(rmp.transaction_number IS NULL,0,rmp.transaction_number) as transaction_number,
                   IF(rmp.transaction_date IS NULL,'0000-00-00',rmp.transaction_date) as transaction_date,
                   rmp.mode_of_payment, rmp.bank_name, rmp.remarks,sc.billing_name                   
            FROM   ro_mso_invoice_upload_data rmiud
                   LEFT JOIN ro_mso_payment rmp
                          ON rmiud.invoice_number = rmp.invoice_number
                   INNER JOIN ro_mso_invoice_upload_details rmiude ON rmiud.invoice_number = rmiude.invoice_number
                   INNER JOIN sv_customer sc on rmiude.network_id = sc.customer_id
            WHERE  rmiud.invoice_date BETWEEN '$start_date' AND '$end_date' ";
        if (isset($network_id) || !empty($network_id)) {
            $query .= " AND rmiude.network_id=$network_id ";
        }
        $query .= " order by payment_date";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();

    }

    public function insertExcelUpload($content)
    {

        $this->db->trans_begin();
        $this->db->insert('ro_mso_payment', $content);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }
}

?> 