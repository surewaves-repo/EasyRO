<?php
/**
 * Created by PhpStorm.
 * User: Nitish
 * Date: 7/21/15
 * Time: 12:56 PM
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Invoice_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function insert_helper($table_name, $data_array)
    {
        $this->db->insert($table_name, $data_array);
        return $this->db->insert_id();
    }

    public function get_ros_for_invoice($start_date, $end_date)
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];
        $today = date('Y-m-d');

        $query = "SELECT DISTINCT am.id,am.cust_ro,am.internal_ro,am.agency,am.client,am.brand,am.gross,am.agency_com,am.camp_start_date,am.camp_end_date
                    FROM  `ro_approved_networks` as app
                    INNER JOIN `ro_am_external_ro` as am ON app.internal_ro_number = am.internal_ro ";
        $query = $query . " and (( am.camp_start_date >= '$start_date' and am.camp_end_date <= '$end_date' )
                                            or
            ( am.camp_start_date <= '$start_date' and am.camp_end_date between '$start_date' and '$end_date')
                                            or
            ( am.camp_start_date between '$start_date' and '$end_date' and am.camp_end_date >= '$end_date' )
                                            or
            ( am.camp_start_date <= '$start_date' and am.camp_end_date >= '$end_date' ))";
        //WHERE am.camp_end_date BETWEEN '$start_d' AND '$end_d' ";

        $query = $query . "WHERE am.test_user_creation = '$is_test_user' ";
        $query = $query . " order by am.ro_date desc";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function get_invoices_for_download($start_date, $end_date)
    {
        $query = "SELECT rir.*,am.agency,am.client,(rir.no_of_impression*rir.duration*rir.rate) as invoice_amount FROM  `ro_invoice_report` as rir
                    INNER JOIN `ro_am_external_ro` as am ON rir.ro_id = am.id ";
        $query = $query . " and (( am.camp_start_date >= '$start_date' and am.camp_end_date <= '$end_date' )
                                            or
            ( am.camp_start_date <= '$start_date' and am.camp_end_date between '$start_date' and '$end_date')
                                            or
            ( am.camp_start_date between '$start_date' and '$end_date' and am.camp_end_date >= '$end_date' )
                                            or
            ( am.camp_start_date <= '$start_date' and am.camp_end_date >= '$end_date' ))";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function update_invoice_details($data, $where)
    {
        $this->db->update('ro_invoice_report', $data, $where);
    }


    public function get_unsplitted_invoice_details($ro_id, $start_date, $end_date)
    {
        //$result = $this->db->get_where('ro_invoice_report',$where_data) ;
        $query = "SELECT agency.agency_contact_name,agency.agency_contact_no,agency.agency_address,am.agency as agency_name,
                         agency.gst as agency_gst,agency.agency_state,client.client_state,client.gst as client_gst,client.client_contact_name,client.client_contact_number,client.client_address,am.client as client_name,
                         ru.user_name as supplier,spg.product_group,ssm.sw_market_name as market_name,am.agency_com as agency_commission,am.ro_date,rir.*
                    FROM ro_invoice_report as rir
                    INNER JOIN sv_sw_market as ssm ON rir.market_id = ssm.id
                    INNER JOIN ro_am_external_ro as am ON rir.ro_id = am.id
                    INNER JOIN ro_user as ru ON am.user_id = ru.user_id
                    INNER JOIN sv_new_brand as snb ON rir.brand_id = snb.id
                    INNER JOIN sv_new_advertiser as sna ON snb.new_advertiser_id = sna.id
                    INNER JOIN sv_product_group as spg ON sna.product_group_id = spg.id
                    LEFT JOIN ro_agency_contact as agency ON am.agency_contact_id =agency.id
                    LEFT JOIN ro_client_contact as client ON am.client_contact_id =client.id
                    WHERE rir.ro_id='$ro_id' ";
        $query = $query . " AND (( rir.start_date >= '$start_date' and rir.end_date <= '$end_date' )
                                            or
            ( rir.start_date <= '$start_date' and rir.end_date between '$start_date' and '$end_date')
                                            or
            ( rir.start_date between '$start_date' and '$end_date' and rir.end_date >= '$end_date' )
                                            or
            ( rir.start_date <= '$start_date' and rir.end_date >= '$end_date' ))";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function get_splitted_invoice_details($invoice_id, $start_date, $end_date)
    {
        //$result = $this->db->get_where('ro_invoice_report',$where_data) ;
        $query = "SELECT agency.agency_contact_name,agency.agency_contact_no,agency.billing_address as agency_address,agency.agency_name as agency_name,
                         agency.gst as agency_gst,agency.agency_state,client.client_state,client.gst as client_gst,client.client_contact_name,client.client_contact_number,client.client_address,am.client as client_name,
                         ru.user_name as supplier,spg.product_group,ssm.sw_market_name as market_name,(am.agency_com / am.gross) as agency_commission_percent,am.ro_date,rir.*
                    FROM ro_invoice_report as rir
                    INNER JOIN sv_sw_market as ssm ON rir.market_id = ssm.id
                    INNER JOIN ro_am_external_ro as am ON rir.ro_id = am.id
                    INNER JOIN ro_user as ru ON am.user_id = ru.user_id
                    INNER JOIN sv_new_brand as snb ON rir.brand_id = snb.id
                    INNER JOIN sv_new_advertiser as sna ON snb.new_advertiser_id = sna.id
                    INNER JOIN sv_product_group as spg ON sna.product_group_id = spg.id
                    LEFT JOIN ro_agency_contact as agency ON am.agency_contact_id =agency.id
                    LEFT JOIN ro_client_contact as client ON am.client_contact_id =client.id
                    WHERE rir.id IN ($invoice_id) ";
        $query = $query . " AND (( rir.start_date >= '$start_date' and rir.end_date <= '$end_date' )
                                            or
            ( rir.start_date <= '$start_date' and rir.end_date between '$start_date' and '$end_date')
                                            or
            ( rir.start_date between '$start_date' and '$end_date' and rir.end_date >= '$end_date' )
                                            or
            ( rir.start_date <= '$start_date' and rir.end_date >= '$end_date' ))";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function getStartEndDateForRo($internal_ro_number)
    {
        $query_campaign = "select group_concat(campaign_id) as campaign_id from sv_advertiser_campaign where internal_ro_number= '$internal_ro_number' ";
        $res_campaign = $this->db->query($query_campaign);

        $result_campaign = $res_campaign->result("array");
        $campaignId = $result_campaign[0]['campaign_id'];


        $query = "select min(sacsd.date) as start_date,max(sacsd.date) as end_date"
            . " from sv_advertiser_campaign_screens_dates as sacsd"
            . " where sacsd.campaign_id in (" . $campaignId . ") and sacsd.status != 'cancelled' ";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            $data['start_date'] = $result[0]['start_date'];
            $data['end_date'] = $result[0]['end_date'];
            return $data;
        }
        /* $query = "select min(sacsd.date) as start_date,max(sacsd.date) as end_date"
                 . " from sv_advertiser_campaign_screens_dates as sacsd"
                 . " inner join sv_advertiser_campaign as sac on sacsd.campaign_id=sac.campaign_id"
                 . " where sac.internal_ro_number= '$internal_ro_number' and sacsd.status != 'cancelled' " ;
         $res = $this->db->query($query);
         if($res->num_rows() > 0) {
             $result =  $res->result("array");
             $data['start_date'] = $result[0]['start_date'] ;
             $data['end_date'] = $result[0]['end_date'] ;
             return $data ;
         }*/
    }

    public function getStartAndEndDateForInvoiceGeneration($internal_ro_number, $split_by, $monthYear)
    {
        $query = "select min(sacsd.date) as start_date,max(sacsd.date) as end_date"
            . " from sv_advertiser_campaign_screens_dates as sacsd"
            . " inner join sv_advertiser_campaign as sac on sacsd.campaign_id=sac.campaign_id"
            . " where sac.internal_ro_number= '$internal_ro_number' and sacsd.status != 'cancelled' ";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            $campaign_start_date = $result[0]['start_date'];
            $campaign_end_date = $result[0]['end_date'];

            $billingCycle = $this->getBillingCyclePayment($internal_ro_number);
            $consolidatedBill = FALSE;

            if ($billingCycle == 'Consolidated') {
                $consolidatedBill = TRUE;

                $this->getValuesForInvoiceGeneration($campaign_start_date, $campaign_end_date, $internal_ro_number, $split_by, $monthYear, $consolidatedBill);
            } else {
                $this->divideDateForInvoiceGeneration($campaign_start_date, $campaign_end_date, $internal_ro_number, $split_by, $monthYear);
            }
        }
    }

    public function getBillingCyclePayment($internal_ro_number)
    {
        $query = "select raer.agency,raer.client,rna.internal_agency,rac.billing_cylce as agency_billing_cycle,rcc.billing_info as client_billing_cycle from ro_am_external_ro raer "
            . "Inner join sv_new_agency rna on rna.agency_name = raer.agency "
            . "Inner Join ro_agency_contact rac on rac.id = raer.agency_contact_id "
            . "left Join ro_client_contact rcc on rcc.id = raer.client_contact_id "
            . "where raer.internal_ro = '$internal_ro_number' ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            if ($result[0]['internal_agency'] == 1) {
                $billingInfo = $result[0]['client_billing_cycle'];
            } else {
                $billingInfo = $result[0]['agency_billing_cycle'];
            }
            return $billingInfo;
        }
        return FALSE;
    }

    public function getValuesForInvoiceGeneration($start_date, $end_date, $internal_ro_number, $split_by, $monthYear, $consolidatedBill)
    {
        //Get Ro Detail
        $roDetails = $this->am_model->get_ro_details_for_internal_ro($internal_ro_number);
        $campaignIds = $this->getScheduledCampaignIdsForRos($internal_ro_number);
        //$valueAddedCampaign = $this->getScheduledValueAddedCampaignIdsForRos($internal_ro_number) ;
        // if(isset($valueAddedCampaign) || !empty($valueAddedCampaign)) {
        //    $this->procesValueAddedCampaign($valueAddedCampaign,$start_date,$end_date,$internal_ro_number,$split_by,$monthYear,$consolidatedBill) ;
        // }

        $query = "Select max(X.total_ad_seconds) as total_fct,X.screen_region_id,X.ro_duration,X.impressions,X.brand_id,X.brand_name,X.market_id,
                    X.channel_id,X.caption_name
                        from
            (SELECT ro_duration*SUM(acsd.impressions) as total_ad_seconds,acsd.screen_region_id,ac.ro_duration,SUM(acsd.impressions) as impressions,
                ac.brand_id,ac.brand_new as brand_name,ac.market_id,ac.channel_id,ac.caption_name
                FROM sv_advertiser_campaign_screens_dates acsd 
                INNER JOIN sv_advertiser_campaign ac on ac.campaign_id=acsd.campaign_id 
                WHERE  ac.campaign_id IN ($campaignIds)  and acsd.screen_region_id in(1,3) and (acsd.date >= '$start_date' and acsd.date <='$end_date') 
                and  acsd.status ='scheduled' 
                GROUP BY acsd.screen_region_id,ac.ro_duration,ac.brand_id,ac.market_id,ac.channel_id ) X
                GROUP BY X.screen_region_id,X.ro_duration,X.brand_id,X.market_id";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            $this->processGenerateInvoice($result, $roDetails, $start_date, $end_date, $split_by, $monthYear, $consolidatedBill);
            //$this->processValueAddedInvoiceGeneration($result,$roDetails,$start_date,$end_date,$split_by,$monthYear) ;
        }
        //Update Rate And Release Order Date Later
    }

    public function getScheduledCampaignIdsForRos($internal_ro)
    {
        $query = "select group_concat(campaign_id) as campaign_id from sv_advertiser_campaign
                where campaign_status NOT IN ( 'pending_approval', 'cancelled' )
                AND internal_ro_number = '$internal_ro' AND is_make_good = 0 AND is_value_added = 0 ";
        $results = $this->db->query($query);

        if ($results->num_rows() != 0) {
            $campaignId = $results->result("array");
            if ($campaignId[0]['campaign_id'] != NULL) {
                return $campaignId[0]['campaign_id'];
            } else {
                return 0;
            }
        }
        return 0;
    }

    public function processGenerateInvoice($resultValues, $roDetails, $start_date, $end_date, $split_by, $monthYear, $consolidatedBill)
    {
        if (!$consolidatedBill) {
            $monthYearValues = explode(" ", $monthYear);
            $month = $monthYearValues[0];
            $year = $monthYearValues[1];
        }


        //Rate & Release order should be proper;Now just updated
        foreach ($resultValues as $values) {
            $marketRate = $this->getMarketRate($roDetails[0]['id'], $values['screen_region_id'], $values['market_id']);
            $dataForInsertion = array(
                'ro_id' => $roDetails[0]['id'],
                'customer_ro' => $roDetails[0]['cust_ro'],
                'internal_ro' => $roDetails[0]['internal_ro'],
                'month_name' => date("F", strtotime($start_date)),
                'start_date' => $start_date,
                'end_date' => $end_date,
                'region_id' => $values['screen_region_id'],
                'market_id' => $values['market_id'],
                'channel_id' => $values['channel_id'],
                'brand_id' => $values['brand_id'],
                'brand_name' => $values['brand_name'],
                'content_name' => $values['caption_name'],
                'invoice_date' => date('Y-m-d'),
                'no_of_impression' => $values['impressions'],
                'duration' => $values['ro_duration'],
                'rate' => $marketRate,
                'release_order_date' => date('Y-m-d'),
                'split_by_market' => $split_by['split_by_market'],
                'split_by_brand' => $split_by['split_by_brand'],
                'split_by_content' => $split_by['split_by_content'],
                'is_generated' => 2,
                'is_value_added' => 0
            );

            $whereData = array(
                'ro_id' => $roDetails[0]['id'],
                'start_date' => $start_date,
                'end_date' => $end_date,
                'region_id' => $values['screen_region_id'],
                'market_id' => $values['market_id'],
                'channel_id' => $values['channel_id'],
                'brand_id' => $values['brand_id'],
                'content_name' => $values['caption_name'],
                'is_value_added' => 0
            );

            $updateData = array(
                'invoice_date' => date('Y-m-d'),
                'no_of_impression' => $values['impressions'],
                'duration' => $values['ro_duration'],
                'rate' => $marketRate,
                'split_by_market' => $split_by['split_by_market'],
                'split_by_brand' => $split_by['split_by_brand'],
                'split_by_content' => $split_by['split_by_content'],
                'release_order_date' => date('Y-m-d')
            );

            $invoiceData = $this->getRoInvoiceData($whereData);

            if (!$consolidatedBill) {
                $dbMonthName = date("F", strtotime($start_date));
                $dbYear = date("Y", strtotime($start_date));
                if (count($invoiceData) > 0) {
                    if (($month == $dbMonthName) && ($year == $dbYear)) {
                        $this->updateRoInvoiceData($updateData, $whereData);
                    }
                } else {
                    if (($month == $dbMonthName) && ($year == $dbYear)) {
                        $this->insertIntoRoInvoice($dataForInsertion);
                    }
                }

            } else {
                if (count($invoiceData) > 0) {
                    $this->updateRoInvoiceData($updateData, $whereData);
                } else {
                    $this->insertIntoRoInvoice($dataForInsertion);
                }
            }
            /*
            if(count($invoiceData) > 0 ){
                $this->updateRoInvoiceData($updateData,$whereData) ;
            }else{
                $this->insertIntoRoInvoice($dataForInsertion) ;
            }
                */
        }

    }

    public function getMarketRate($ro_id, $region_id, $marketId)
    {
        if ($region_id == 1) {
            $key_amount = "spot_price";
            $key_fct = "spot_fct";
        } else if ($region_id == 3) {
            $key_amount = "banner_price";
            $key_fct = "banner_fct";
        }
        $query = "select rmp.$key_amount as amount,rmp.$key_fct as FCT from ro_market_price rmp "
            . "Inner Join sv_sw_market ssm on rmp.market = ssm.sw_market_name "
            . "where ssm.id=$marketId and rmp.ro_id = $ro_id ";
        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            $resultValues = $result->result("array");
            $fct = $resultValues[0]['FCT'];
            $amount = $resultValues[0]['amount'];
            if (!empty($fct) && isset($fct)) {
                $rate = round(($amount / $fct) * 10, 2);
                return $rate;
            } else {
                return 0;
            }

        } else {
            return 0;
        }

    }

    public function getRoInvoiceData($data)
    {
        $result = $this->db->get_where('ro_invoice_report', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function updateRoInvoiceData($data, $where)
    {
        $this->db->update('ro_invoice_report', $data, $where);
    }

    public function insertIntoRoInvoice($userData)
    {
        $this->db->insert('ro_invoice_report', $userData);
    }

    public function divideDateForInvoiceGeneration($campaign_start_date, $campaign_end_date, $internal_ro_number, $split_by, $monthYear)
    {
        $month = date("m", strtotime($campaign_start_date));
        $year = date("Y", strtotime($campaign_start_date));
        $month_start_campaign_start_date = date("$year-$month-01");
        $start_date = date("Y-m-d", strtotime($campaign_start_date));

        for ($i = strtotime($month_start_campaign_start_date); $i <= strtotime($campaign_end_date); $i = strtotime("+1 month", $i)) {

            if (date("m", $i) == date("m")) {
                $end_date = date("Y-m-t");
            } else {
                $end_date = date("Y-m-t", strtotime("+1 month -1 days", $i));
            }

            if (strtotime($end_date) > strtotime($campaign_end_date)) {
                $end_date = $campaign_end_date;
            }

            $monthYearValues = explode(" ", $monthYear);
            $monthFromUI = $monthYearValues[0];
            $yearFromUI = $monthYearValues[1];

            $ongoingMonth = date("F", $i);
            $ongoiningYear = date("Y", $i);
            $todaysDate = date('Y-m-d');
            /*if( (strtotime($end_date) > strtotime($todaysDate)) || (strtotime($start_date) > strtotime($todaysDate)) || ( ($monthFromUI != $ongoingMonth) &&  ($yearFromUI != $ongoiningYear) )) {
                continue ;
            }*/
            $billingCycle = $this->getBillingCyclePayment($internal_ro_number);
            $consolidatedBill = FALSE;

            if ($billingCycle == 'Consolidated') {
                $consolidatedBill = TRUE;
            }

            if ($consolidatedBill) {
                if ((strtotime($end_date) > strtotime($todaysDate)) || (strtotime($start_date) > strtotime($todaysDate))) {
                    continue;
                } else {
                    $this->getValuesForInvoiceGeneration($start_date, $end_date, $internal_ro_number, $split_by, $monthYear, $consolidatedBill);
                    $start_date = date("Y-m-d", strtotime("+1 month", $i));
                }
            } else {
                if ((strtotime($end_date) > strtotime($todaysDate)) || (strtotime($start_date) > strtotime($todaysDate)) || (($monthFromUI != $ongoingMonth) && ($yearFromUI != $ongoiningYear))) {
                    continue;
                } else {
                    $this->getValuesForInvoiceGeneration($start_date, $end_date, $internal_ro_number, $split_by, $monthYear, $consolidatedBill);
                    $start_date = date("Y-m-d", strtotime("+1 month", $i));
                }
            }
        }
    }

    public function getScheduledValueAddedCampaignIdsForRos($internal_ro)
    {
        $query = "select group_concat(campaign_id) as campaign_id from sv_advertiser_campaign
                where campaign_status NOT IN ( 'pending_approval', 'cancelled' )
                AND internal_ro_number = '$internal_ro' AND is_value_added = 1 ";
        $results = $this->db->query($query);

        if ($results->num_rows() != 0) {
            $campaignId = $results->result("array");
            if ($campaignId[0]['campaign_id'] != NULL) {
                return $campaignId[0]['campaign_id'];
            } else {
                return 0;
            }
        }
        return 0;
    }

    public function procesValueAddedCampaign($campaignIds, $start_date, $end_date, $internal_ro_number, $split_by, $monthYear, $consolidatedBill)
    {
        $roDetails = $this->am_model->get_ro_details_for_internal_ro($internal_ro_number);
        $query = "Select max(X.total_ad_seconds) as total_fct,X.screen_region_id,X.ro_duration,X.impressions,X.brand_id,X.brand_name,X.market_id,
                    X.channel_id,X.caption_name
                        from
            (SELECT ro_duration*SUM(acsd.impressions) as total_ad_seconds,acsd.screen_region_id,ac.ro_duration,SUM(acsd.impressions) as impressions,
                ac.brand_id,ac.brand_new as brand_name,ac.market_id,ac.channel_id,ac.caption_name
                FROM sv_advertiser_campaign_screens_dates acsd 
                INNER JOIN sv_advertiser_campaign ac on ac.campaign_id=acsd.campaign_id 
                WHERE  ac.campaign_id IN ($campaignIds)  and acsd.screen_region_id in(1,3) and (acsd.date >= '$start_date' and acsd.date <='$end_date') 
                and  acsd.status ='scheduled' 
                GROUP BY acsd.screen_region_id,ac.ro_duration,ac.brand_id,ac.market_id,ac.channel_id ) X
                GROUP BY X.screen_region_id,X.ro_duration,X.brand_id,X.market_id";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            $this->processValueAddedGenerateInvoice($result, $roDetails, $start_date, $end_date, $split_by, $monthYear, $consolidatedBill);
            //$this->processValueAddedInvoiceGeneration($result,$roDetails,$start_date,$end_date,$split_by,$monthYear) ;
        }
    }

    public function processValueAddedGenerateInvoice($resultValues, $roDetails, $start_date, $end_date, $split_by, $monthYear, $consolidatedBill)
    {
        if (!$consolidatedBill) {
            $monthYearValues = explode(" ", $monthYear);
            $month = $monthYearValues[0];
            $year = $monthYearValues[1];
        }


        //Rate & Release order should be proper;Now just updated
        foreach ($resultValues as $values) {
            $marketRate = $this->getMarketRate($roDetails[0]['id'], $values['screen_region_id'], $values['market_id']);
            $dataForInsertion = array(
                'ro_id' => $roDetails[0]['id'],
                'customer_ro' => $roDetails[0]['cust_ro'],
                'internal_ro' => $roDetails[0]['internal_ro'],
                'month_name' => date("F", strtotime($start_date)),
                'start_date' => $start_date,
                'end_date' => $end_date,
                'region_id' => $values['screen_region_id'],
                'market_id' => $values['market_id'],
                'channel_id' => $values['channel_id'],
                'brand_id' => $values['brand_id'],
                'brand_name' => $values['brand_name'],
                'content_name' => $values['caption_name'],
                'invoice_date' => date('Y-m-d'),
                'no_of_impression' => $values['impressions'],
                'duration' => $values['ro_duration'],
                'rate' => $marketRate,
                'release_order_date' => date('Y-m-d'),
                'split_by_market' => $split_by['split_by_market'],
                'split_by_brand' => $split_by['split_by_brand'],
                'split_by_content' => $split_by['split_by_content'],
                'is_generated' => 2,
                'is_value_added' => 1
            );

            $whereData = array(
                'ro_id' => $roDetails[0]['id'],
                'start_date' => $start_date,
                'end_date' => $end_date,
                'region_id' => $values['screen_region_id'],
                'market_id' => $values['market_id'],
                'channel_id' => $values['channel_id'],
                'brand_id' => $values['brand_id'],
                'content_name' => $values['caption_name'],
                'is_value_added' => 1
            );

            $updateData = array(
                'invoice_date' => date('Y-m-d'),
                'no_of_impression' => $values['impressions'],
                'duration' => $values['ro_duration'],
                'rate' => $marketRate,
                'split_by_market' => $split_by['split_by_market'],
                'split_by_brand' => $split_by['split_by_brand'],
                'split_by_content' => $split_by['split_by_content'],
                'release_order_date' => date('Y-m-d')
            );

            $invoiceData = $this->getRoInvoiceData($whereData);

            if (!$consolidatedBill) {
                $dbMonthName = date("F", strtotime($start_date));
                $dbYear = date("Y", strtotime($start_date));
                if (count($invoiceData) > 0) {
                    if (($month == $dbMonthName) && ($year == $dbYear)) {
                        $this->updateRoInvoiceData($updateData, $whereData);
                    }
                } else {
                    if (($month == $dbMonthName) && ($year == $dbYear)) {
                        $this->insertIntoRoInvoice($dataForInsertion);
                    }
                }

            } else {
                if (count($invoiceData) > 0) {
                    $this->updateRoInvoiceData($updateData, $whereData);
                } else {
                    $this->insertIntoRoInvoice($dataForInsertion);
                }
            }
            /*
            if(count($invoiceData) > 0 ){
                $this->updateRoInvoiceData($updateData,$whereData) ;
            }else{
                $this->insertIntoRoInvoice($dataForInsertion) ;
            }
                */
        }

    }

    public function getMarketRate_v1($ro_id, $region_id, $marketId)
    {
        if ($region_id == 1) {
            $key_rate = "spot_rate";
        } else if ($region_id == 3) {
            $key_rate = "banner_rate";
        }

        $query = "select rmp.$key_rate as rate from ro_market_price rmp "
            . "Inner Join sv_sw_market ssm on rmp.market = ssm.sw_market_name "
            . "where ssm.id=$marketId and rmp.ro_id = $ro_id ";
        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            $resultValues = $result->result("array");
            return $resultValues[0]['rate'];
        } else {
            return 0;
        }

    }

    public function deleteInvoiceData($data)
    {
        $this->db->delete('ro_invoice_report', $data);
    }

    public function storeInvoiceHistory($userData)
    {
        $this->db->insert('ro_invoice_report_history', $userData);
    }

    public function updateRoInvoiceDataForInvoiceIds($invoiceIds, $generationStatus)
    {
        $query = "update ro_invoice_report set is_generated=$generationStatus where id in ($invoiceIds) ";
        $this->db->query($query);
    }

    public function getInvoiceValuesForRoAndMonth($monthYear, $roId)
    {
        $monthYearValues = explode(" ", $monthYear);
        $monthFromUI = $monthYearValues[0];
        //$yearFromUI = $monthYearValues[1] ;

        $query = "SELECT ro_id,month_name,start_date, end_date FROM ro_invoice_report";
        $query = $query . " WHERE month_name= '$monthFromUI' ";
        $query = $query . "and ro_id= '$roId' ";
        $query = $query . " GROUP BY ro_id,start_date, end_date ORDER BY ro_id";

        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            $resultData = $result->result("array");

            $data = array();
            $finalData = array();
            foreach ($resultData as $val) {
                $start_date = $val['start_date'];
                $end_date = $val['end_date'];

                $invoiceDataForZeroSplit = $this->getInvoiceDataForZeroSplit($roId, $start_date, $end_date);
                $invoiceDataForZeroSplitByMarket = $this->getInvoiceDataForSplitByMarket($roId, $start_date, $end_date);
                $invoiceDataForSplitByMarketAndBrand = $this->getInvoiceDataForSplitByMarketAndBrand($roId, $start_date, $end_date);
                $invoiceDataForSplitByMarketAndContent = $this->getInvoiceDataForSplitByMarketAndContent($roId, $start_date, $end_date);

                if (count($invoiceDataForZeroSplit) > 0) {
                    array_push($data, $invoiceDataForZeroSplit);
                }

                if (count($invoiceDataForZeroSplitByMarket) > 0) {
                    array_push($data, $invoiceDataForZeroSplitByMarket);
                }

                if (count($invoiceDataForSplitByMarketAndBrand) > 0) {
                    array_push($data, $invoiceDataForSplitByMarketAndBrand);
                }

                if (count($invoiceDataForSplitByMarketAndContent) > 0) {
                    array_push($data, $invoiceDataForSplitByMarketAndContent);
                }

                $dataCount = count($data);
                for ($count = 0; $count < $dataCount; $count++) {
                    //$arrayKeyCount = count($data[$roId][$date_key][$count]) ;
                    foreach ($data[$count] as $dataValue) {
                        array_push($finalData, $dataValue);
                    }
                }
            }
            return $finalData;

        } else {
            return array();
        }
    }

    public function getInvoiceDataForZeroSplit($ro_id, $start_date, $end_date)
    {
        $query = "SELECT group_concat( id ) as id,ro_id,customer_ro, internal_ro, group_concat( region_id ) as region_id,group_concat( market_id ) as market_id, 
                 group_concat( channel_id ) as channel_id,group_concat( brand_id ) as brand_id,group_concat( brand_name ) as brand_name ,
                 group_concat( content_name ) as content_name,group_concat(no_of_impression) as no_of_impression,group_concat(duration) as duration, 
                group_concat(rate) as rate,sum( no_of_impression * duration * rate ) as invoice_amount,split_by_market,split_by_brand,split_by_content,
                is_generated,mail_sent,money_received
                FROM `ro_invoice_report`
                WHERE ro_id =$ro_id And start_date='$start_date' And end_date='$end_date'
                AND split_by_market =0 order by id";
        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            $resultSet = $result->result("array");
            if ($resultSet[0]['id'] == NULL) {
                return array();
            } else {
                $invoiceDataArray = $this->getInvoiceDataInStructure($resultSet, 0);
                return $invoiceDataArray;
            }
        } else {
            return array();
        }
    }

    public function getInvoiceDataInStructure($resultSet, $splitBy)
    {
        $data = array();

        foreach ($resultSet as $val) {
            $ro_details = $this->get_client_buyer_for_ro($val['ro_id']);

            $agency_commission_percent = $this->get_agency_commission_percent($val['ro_id']);
            $agency_commission = $val['invoice_amount'] * $agency_commission_percent[0]['agency_commission_percent'];

            $total_amount_after_commission = $val['invoice_amount'] - $agency_commission;
            $service_tax = $total_amount_after_commission * 14.00 / 100;
            $total_payable = ($total_amount_after_commission + $service_tax) / 10;

            $tmp = array();
            $tmp['id'] = $val['id'];

            //This logic is for more than one invoice id for split by market,content,brand
            if ($splitBy != 0) {
                $splitBy = $this->getSplitByValue($val['id']);
            }
            $tmp['ro_id'] = $val['ro_id'];
            $tmp['customer_ro'] = $val['customer_ro'];
            $tmp['internal_ro'] = $val['internal_ro'];
            $tmp['region_id'] = $val['region_id'];
            $tmp['market_id'] = $val['market_id'];
            $tmp['channel_id'] = $val['channel_id'];
            $tmp['brand_id'] = $val['brand_id'];
            $tmp['brand_name'] = $val['brand_name'];
            $tmp['agency'] = $ro_details[0]['agency'];
            $tmp['client'] = $ro_details[0]['client'];
            $tmp['content_name'] = $val['content_name'];
            $tmp['no_of_impression'] = $val['no_of_impression'];
            $tmp['duration'] = $val['duration'];
            $tmp['rate'] = $val['rate'];
            $tmp['invoice_amount'] = round($total_payable, 2);
            $tmp['split_by'] = $splitBy;
            $tmp['split_by_market'] = $val['split_by_market'];
            $tmp['split_by_brand'] = $val['split_by_brand'];
            $tmp['split_by_content'] = $val['split_by_content'];
            $tmp['is_generated'] = $val['is_generated'];
            $tmp['mail_sent'] = $val['mail_sent'];
            $tmp['money_received'] = $val['money_received'];

            array_push($data, $tmp);
        }
        return $data;
    }

    public function get_client_buyer_for_ro($ro_id)
    {
        $query = "SELECT agency,client FROM ro_am_external_ro WHERE id='$ro_id' ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function get_agency_commission_percent($ro_id)
    {
        $query = "SELECT (agency_com / gross) as agency_commission_percent FROM ro_am_external_ro WHERE id='$ro_id' ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function getSplitByValue($id)
    {
        $idCount = explode(",", $id);
        if (count($idCount) > 0) {
            return 2;
        } else {
            return 1;
        }
    }

    public function getInvoiceDataForSplitByMarket($ro_id, $start_date, $end_date)
    {
        $query = "SELECT group_concat( id ) as id,ro_id,customer_ro, internal_ro, group_concat( region_id ) as region_id,group_concat( market_id ) as market_id, 
                group_concat( channel_id ) as channel_id,group_concat( brand_id ) as brand_id,group_concat( brand_name ) as brand_name ,
                group_concat( content_name ) as content_name,group_concat(no_of_impression) as no_of_impression,group_concat(duration) as duration, 
                group_concat(rate) as rate,sum( no_of_impression * duration * rate ) as invoice_amount,split_by_market,split_by_brand,split_by_content,
                is_generated,mail_sent,money_received
                FROM `ro_invoice_report`
                WHERE ro_id =$ro_id And start_date='$start_date' And end_date='$end_date'
                AND split_by_market =1 and split_by_brand = 0 and split_by_content = 0 group by market_id order by id";
        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            $resultSet = $result->result("array");
            $invoiceDataArray = $this->getInvoiceDataInStructure($resultSet, 1);
            return $invoiceDataArray;
        } else {
            return array();
        }
    }

    public function getInvoiceDataForSplitByMarketAndBrand($ro_id, $start_date, $end_date)
    {
        $query = "SELECT group_concat( id ) as id,ro_id,customer_ro, internal_ro, group_concat( region_id ) as region_id,group_concat( market_id ) as market_id, 
                group_concat( channel_id ) as channel_id,group_concat( brand_id ) as brand_id,group_concat( brand_name ) as brand_name ,
                group_concat( content_name ) as content_name,group_concat(no_of_impression) as no_of_impression,group_concat(duration) as duration, 
                group_concat(rate) as rate,sum( no_of_impression * duration * rate ) as invoice_amount,split_by_market,split_by_brand,split_by_content,
                is_generated,mail_sent,money_received
                FROM `ro_invoice_report`
                WHERE ro_id =$ro_id And start_date='$start_date' And end_date='$end_date'
                AND split_by_market =1 And split_by_brand=1 group by market_id,brand_id order by id";
        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            $resultSet = $result->result("array");
            $invoiceDataArray = $this->getInvoiceDataInStructure($resultSet, 1);
            return $invoiceDataArray;
        } else {
            return array();
        }
    }

    public function getInvoiceDataForSplitByMarketAndContent($ro_id, $start_date, $end_date)
    {
        /*
        $query = "SELECT id,ro_id,customer_ro, internal_ro,region_id,market_id,channel_id ,brand_id ,brand_name ,content_name ,no_of_impression,
                duration, rate, no_of_impression*duration*rate*1/10 as invoice_amount,split_by_market,split_by_brand,split_by_content,is_generated,mail_sent,money_received
                FROM `ro_invoice_report`
                WHERE ro_id =$ro_id And start_date='$start_date' And end_date='$end_date'
                AND split_by_market =1 And split_by_content=1 order by id" ; */
        $query = "SELECT group_concat( id ) as id,ro_id,customer_ro, internal_ro, group_concat( region_id ) as region_id,group_concat( market_id ) as market_id, 
                group_concat( channel_id ) as channel_id,group_concat( brand_id ) as brand_id,group_concat( brand_name ) as brand_name ,
                group_concat( content_name ) as content_name,group_concat(no_of_impression) as no_of_impression,group_concat(duration) as duration, 
                group_concat(rate) as rate,sum( no_of_impression * duration * rate ) as invoice_amount,split_by_market,split_by_brand,split_by_content,
                is_generated,mail_sent,money_received
                FROM `ro_invoice_report`
                WHERE ro_id =$ro_id And start_date='$start_date' And end_date='$end_date'
                AND split_by_market =1 And split_by_content=1 group by market_id,content_name order by id";
        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            $resultSet = $result->result("array");
            $invoiceDataArray = $this->getInvoiceDataInStructure($resultSet, 1);
            return $invoiceDataArray;
        } else {
            return array();
        }
    }

    public function getConsolidatedInvoiceValuesForRo($roId)
    {
        $query = "SELECT ro_id,month_name,start_date, end_date FROM ro_invoice_report ";
        $query = $query . " where ro_id= $roId ";
        $query = $query . " GROUP BY ro_id";
        //,start_date, end_date ORDER BY ro_id";

        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            $resultData = $result->result("array");

            $data = array();
            $finalData = array();

            foreach ($resultData as $val) {

                $invoiceDataForZeroSplit = $this->getConsolidatedInvoiceDataForZeroSplit($roId);
                $invoiceDataForZeroSplitByMarket = $this->getConsolidatedInvoiceDataForSplitByMarket($roId);
                $invoiceDataForSplitByMarketAndBrand = $this->getConsolidatedInvoiceDataForSplitByMarketAndBrand($roId);
                $invoiceDataForSplitByMarketAndContent = $this->getConsolidatedInvoiceDataForSplitByMarketAndContent($roId);

                if (count($invoiceDataForZeroSplit) > 0) {
                    array_push($data, $invoiceDataForZeroSplit);
                }

                if (count($invoiceDataForZeroSplitByMarket) > 0) {
                    array_push($data, $invoiceDataForZeroSplitByMarket);
                }

                if (count($invoiceDataForSplitByMarketAndBrand) > 0) {
                    array_push($data, $invoiceDataForSplitByMarketAndBrand);
                }

                if (count($invoiceDataForSplitByMarketAndContent) > 0) {
                    array_push($data, $invoiceDataForSplitByMarketAndContent);
                }

                $dataCount = count($data);
                for ($count = 0; $count < $dataCount; $count++) {
                    //$arrayKeyCount = count($data[$roId][$date_key][$count]) ;
                    foreach ($data[$count] as $dataValue) {
                        array_push($finalData, $dataValue);
                    }
                }
            }
            return $finalData;

        } else {
            return array();
        }
    }

    public function getConsolidatedInvoiceDataForZeroSplit($ro_id)
    {
        $query = "SELECT group_concat( id ) as id,ro_id,customer_ro, internal_ro, group_concat( region_id ) as region_id,group_concat( market_id ) as market_id, 
                 group_concat( channel_id ) as channel_id,group_concat( brand_id ) as brand_id,group_concat( brand_name ) as brand_name ,
                 group_concat( content_name ) as content_name,group_concat(no_of_impression) as no_of_impression,group_concat(duration) as duration, 
                group_concat(rate) as rate,sum( no_of_impression * duration * rate ) as invoice_amount,split_by_market,split_by_brand,split_by_content,
                is_generated,mail_sent,money_received
                FROM `ro_invoice_report`
                WHERE ro_id =$ro_id AND split_by_market =0 order by id";
        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            $resultSet = $result->result("array");
            if ($resultSet[0]['id'] == NULL) {
                return array();
            } else {
                $invoiceDataArray = $this->getInvoiceDataInStructure($resultSet, 0);
                return $invoiceDataArray;
            }
        } else {
            return array();
        }
    }

    public function getConsolidatedInvoiceDataForSplitByMarket($ro_id)
    {
        $query = "SELECT group_concat( id ) as id,ro_id,customer_ro, internal_ro, group_concat( region_id ) as region_id,group_concat( market_id ) as market_id, 
                group_concat( channel_id ) as channel_id,group_concat( brand_id ) as brand_id,group_concat( brand_name ) as brand_name ,
                group_concat( content_name ) as content_name,group_concat(no_of_impression) as no_of_impression,group_concat(duration) as duration, 
                group_concat(rate) as rate,sum( no_of_impression * duration * rate ) as invoice_amount,split_by_market,split_by_brand,split_by_content,
                is_generated,mail_sent,money_received
                FROM `ro_invoice_report`
                WHERE ro_id =$ro_id AND split_by_market =1 and split_by_brand = 0 and split_by_content = 0 group by market_id order by id";
        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            $resultSet = $result->result("array");
            $invoiceDataArray = $this->getInvoiceDataInStructure($resultSet, 1);
            return $invoiceDataArray;
        } else {
            return array();
        }
    }

    public function getConsolidatedInvoiceDataForSplitByMarketAndBrand($ro_id)
    {
        $query = "SELECT group_concat( id ) as id,ro_id,customer_ro, internal_ro, group_concat( region_id ) as region_id,group_concat( market_id ) as market_id, 
                group_concat( channel_id ) as channel_id,group_concat( brand_id ) as brand_id,group_concat( brand_name ) as brand_name ,
                group_concat( content_name ) as content_name,group_concat(no_of_impression) as no_of_impression,group_concat(duration) as duration, 
                group_concat(rate) as rate,sum( no_of_impression * duration * rate ) as invoice_amount,split_by_market,split_by_brand,split_by_content,
                is_generated,mail_sent,money_received
                FROM `ro_invoice_report`
                WHERE ro_id =$ro_id AND split_by_market =1 And split_by_brand=1 group by market_id,brand_id order by id";
        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            $resultSet = $result->result("array");
            $invoiceDataArray = $this->getInvoiceDataInStructure($resultSet, 1);
            return $invoiceDataArray;
        } else {
            return array();
        }
    }

    public function getConsolidatedInvoiceDataForSplitByMarketAndContent($ro_id)
    {
        $query = "SELECT group_concat( id ) as id,ro_id,customer_ro, internal_ro, group_concat( region_id ) as region_id,group_concat( market_id ) as market_id, 
                group_concat( channel_id ) as channel_id,group_concat( brand_id ) as brand_id,group_concat( brand_name ) as brand_name ,
                group_concat( content_name ) as content_name,group_concat(no_of_impression) as no_of_impression,group_concat(duration) as duration, 
                group_concat(rate) as rate,sum( no_of_impression * duration * rate ) as invoice_amount,split_by_market,split_by_brand,split_by_content,
                is_generated,mail_sent,money_received
                FROM `ro_invoice_report`
                WHERE ro_id =$ro_id AND split_by_market =1 And split_by_content=1 group by market_id,content_name order by id";
        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            $resultSet = $result->result("array");
            $invoiceDataArray = $this->getInvoiceDataInStructure($resultSet, 1);
            return $invoiceDataArray;
        } else {
            return array();
        }
    }

    public function getInvoiceDetail($start_date, $end_date, $ro_id = null)
    {
        if (isset($ro_id)) {
            $resultData = $this->getInvoiceRoForPDF($start_date, $end_date, $ro_id);
        } else {
            $resultData = $this->getInvoiceRo($start_date, $end_date);
        }


        $data = array();
        $finalData = array();
        foreach ($resultData as $val) {
            $roId = $val['ro_id'];
            if (!array_key_exists($roId, $data)) {
                $data[$roId] = array();
                $finalData[$roId] = array();
            }
            $start_date = $val['start_date'];
            $end_date = $val['end_date'];

            $date_key = strtotime($start_date) . "-" . strtotime($end_date);

            if (!array_key_exists($date_key, $data[$roId])) {
                $data[$roId][$date_key] = array();
                $finalData[$roId][$date_key] = array();
            }
            $invoiceDataForZeroSplit = $this->getInvoiceDataForZeroSplit($roId, $start_date, $end_date);
            $invoiceDataForZeroSplitByMarket = $this->getInvoiceDataForSplitByMarket($roId, $start_date, $end_date);
            $invoiceDataForSplitByMarketAndBrand = $this->getInvoiceDataForSplitByMarketAndBrand($roId, $start_date, $end_date);
            $invoiceDataForSplitByMarketAndContent = $this->getInvoiceDataForSplitByMarketAndContent($roId, $start_date, $end_date);

            if (count($invoiceDataForZeroSplit) > 0) {
                array_push($data[$roId][$date_key], $invoiceDataForZeroSplit);
            }

            if (count($invoiceDataForZeroSplitByMarket) > 0) {
                array_push($data[$roId][$date_key], $invoiceDataForZeroSplitByMarket);
            }
            if (count($invoiceDataForSplitByMarketAndBrand) > 0) {
                array_push($data[$roId][$date_key], $invoiceDataForSplitByMarketAndBrand);
            }
            if (count($invoiceDataForSplitByMarketAndContent) > 0) {
                array_push($data[$roId][$date_key], $invoiceDataForSplitByMarketAndContent);
            }
            $dateKeyCount = count($data[$roId][$date_key]);
            for ($count = 0; $count < $dateKeyCount; $count++) {
                //$arrayKeyCount = count($data[$roId][$date_key][$count]) ;
                foreach ($data[$roId][$date_key][$count] as $dataValue) {
                    array_push($finalData[$roId][$date_key], $dataValue);
                }
            }
        }
        return $finalData;


    }

    public function getInvoiceRoForPDF($start_date, $end_date, $ro_id)
    {

        $query = "SELECT ro_id, start_date, end_date FROM ro_invoice_report";
        $query = $query . " WHERE (( start_date >= '$start_date' and end_date <= '$end_date' )
                                            or
            ( start_date <= '$start_date' and end_date between '$start_date' and '$end_date')
                                            or
            ( start_date between '$start_date' and '$end_date' and end_date >= '$end_date' )
                                            or
            ( start_date <= '$start_date' and end_date >= '$end_date' ))";
        if (isset($ro_id)) {
            $query = $query . "and ro_id= '$ro_id' ";
        }
        $query = $query . " GROUP BY ro_id,start_date, end_date ORDER BY ro_id";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            $resultData = $result->result("array");
            return $resultData;
        } else {
            return array();
        }
    }

    public function getInvoiceRo($start_date, $end_date)
    {
        $query = "SELECT ro_id, start_date, end_date FROM ro_invoice_report";
        $query = $query . " WHERE (( start_date >= '$start_date' and end_date <= '$end_date' )
                                            or
            ( start_date <= '$start_date' and end_date between '$start_date' and '$end_date')
                                            or
            ( start_date between '$start_date' and '$end_date' and end_date >= '$end_date' )
                                            or
            ( start_date <= '$start_date' and end_date >= '$end_date' ))";
        $query = $query . " GROUP BY ro_id,start_date, end_date ORDER BY ro_id";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            $resultData = $result->result("array");
            return $resultData;
        } else {
            return array();
        }
    }

    public function getAllInvoiceNumber($month)
    {
        $query = "SELECT invoice_number FROM `ro_invoice_file` WHERE month_name = '$month' and status = 1";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function searchInvoicesForCollectionByInvoiceStr($invoice_str)
    {
        $query = "SELECT ramex.internal_ro, rif.ro_id, rif.invoice_amount,rif.invoice_number,rif.alias_invoice_number,
                    GROUP_CONCAT( collection_date ) AS collection_date,
                    GROUP_CONCAT( cheque_no ) AS cheque_no, GROUP_CONCAT( cheque_date ) AS cheque_date,
                    GROUP_CONCAT( amnt_collected ) AS amnt_collected,
                    GROUP_CONCAT( tds ) AS tds, GROUP_CONCAT( comment ) AS comment
                    FROM `ro_invoice_file` AS rif
                    LEFT JOIN ro_am_invoice_collection AS raic ON rif.alias_invoice_number = raic.invoice_no
                    INNER JOIN ro_am_external_ro AS ramex ON rif.ro_id = ramex.id
                    WHERE rif.alias_invoice_number LIKE '%$invoice_str%'
                    AND rif.status = 1
                    GROUP BY rif.invoice_number
                    ORDER BY rif.invoice_number ASC";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function getInvoicesForCollectionByInvoiceNo($invoice_no)
    {
        $query = "SELECT ramex.internal_ro, rif.ro_id, rif.invoice_amount,rif.invoice_number,rif.alias_invoice_number,
                    GROUP_CONCAT( collection_date ) AS collection_date,
                    GROUP_CONCAT( cheque_no ) AS cheque_no, GROUP_CONCAT( cheque_date ) AS cheque_date,
                    GROUP_CONCAT( amnt_collected ) AS amnt_collected,
                    GROUP_CONCAT( tds ) AS tds, GROUP_CONCAT( comment ) AS comment
                    FROM `ro_invoice_file` AS rif
                    LEFT JOIN ro_am_invoice_collection AS raic ON rif.alias_invoice_number = raic.invoice_no
                    INNER JOIN ro_am_external_ro AS ramex ON rif.ro_id = ramex.id
                    WHERE rif.alias_invoice_number = '$invoice_no'
                    AND rif.status = 1
                    GROUP BY rif.invoice_number
                    ORDER BY rif.invoice_number ASC";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function getInvoicesForCollectionByAgencyClient($agency, $client, $month)
    {
        $query = "SELECT ramex.internal_ro, rif.ro_id, rif.invoice_amount,rif.invoice_number,rif.alias_invoice_number,
                    GROUP_CONCAT( collection_date ) AS collection_date,
                    GROUP_CONCAT( cheque_no ) AS cheque_no, GROUP_CONCAT( cheque_date ) AS cheque_date,
                    GROUP_CONCAT( amnt_collected ) AS amnt_collected,
                    GROUP_CONCAT( tds ) AS tds, GROUP_CONCAT( comment ) AS comment
                    FROM `ro_invoice_file` AS rif
                    LEFT JOIN ro_am_invoice_collection AS raic ON rif.alias_invoice_number = raic.invoice_no
                    INNER JOIN ro_am_external_ro AS ramex ON rif.ro_id = ramex.id
                    WHERE rif.agency_name = '$agency'
                    AND rif.status = 1
                    AND rif.month_name = '$month' ";

        if (isset($client) && $client != null && $client != '') {
            $query .= " AND rif.client_name = '$client' ";
        }
        $query .= " GROUP BY rif.invoice_number ORDER BY rif.invoice_number ASC";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function getAmountCollectedForInvoices($invoice_no)
    {
        $query = "SELECT SUM(amnt_collected) as amount_collected FROM `ro_am_invoice_collection` WHERE invoice_no = '$invoice_no' ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function getInvoiceCollectionCount($roId)
    {
        $query = "SELECT invoice_no FROM `ro_am_invoice_collection` WHERE ro_id = '$roId' ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function getInvoiceFileDetails($invoice_no)
    {
        $query = "SELECT * FROM `ro_invoice_file` WHERE alias_invoice_number = '$invoice_no' ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function updatePaymentsForInvoice($data)
    {
        $this->db->insert('ro_am_invoice_collection', $data);
    }

    public function getAgenciesForInvoice($month)
    {
        $query = "SELECT DISTINCT agency_name FROM `ro_invoice_file` WHERE month_name = '$month' AND status =1 ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function getClientsForInvoice($month, $agency)
    {
        $query = "SELECT DISTINCT client_name FROM `ro_invoice_file` WHERE month_name = '$month' AND agency_name = '$agency' AND status =1 IS NOT NULL";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
    }

    public function storeInvoicePdf($ro_id, $month_name, $split_criteria, $file_location, $clientName, $agencyName, $invoiceAmount, $invoice_number, $alias_invoice_no)
    {
        /*
         * $userData = array('ro_id' => $ro_id,'month_name' => $month_name, 'split_criteria' => $split_criteria,'status' => 1,'file_location' => $file_location,'client_name'=>$clientName,'agency_name'=>$agencyName,'invoice_amount'=>$invoiceAmount) ;

        $whereData = array('ro_id'=>$ro_id,'month_name'=>$month_name,'status'=>1) ;
        $updateData = array('split_criteria'=>$split_criteria,'file_location'=>$file_location,'invoice_amount'=>$invoiceAmount) ;

        $getData = $this->getInvoiceGenerationFile($whereData) ;
        if(count($getData) > 0) {
            $this->updateForInvoiceGenerationFile($updateData,$whereData);
        }else{
            $this->insertForInvoiceGenerationFile($userData);
        } */

        $updateData = array('alias_invoice_number' => $alias_invoice_no, 'status' => 1, 'split_criteria' => $split_criteria, 'file_location' => $file_location, 'client_name' => $clientName, 'agency_name' => $agencyName, 'invoice_amount' => $invoiceAmount);
        $whereData = array('invoice_number' => $invoice_number);
        $this->updateForInvoiceGenerationFile($updateData, $whereData);

        //Update ro_invoice_report for $ro_id and $month_name
        $billingCycle = $this->getBillingCyclePaymentForRoId($ro_id);
        $consolidatedBill = FALSE;

        if ($billingCycle == 'Consolidated') {
            $consolidatedBill = TRUE;
        }

        if ($consolidatedBill) {
            $this->updateRoInvoiceData(array('is_generated' => 1, 'invoice_number' => $invoice_number), array('ro_id' => $ro_id));
        } else {
            $this->updateRoInvoiceData(array('is_generated' => 1, 'invoice_number' => $invoice_number), array('ro_id' => $ro_id, 'month_name' => $month_name));
        }


        //Update The Job Queue
        //$month_year = $month_name." ".date("Y") ;
        //$this->updateForInvoiceGeneration(array('is_generated'=>1),array('ro_id'=>$ro_id,'month_year'=>$month_year)) ;

    }

    //InsertData

    public function updateForInvoiceGenerationFile($data, $where)
    {
        $this->db->update('ro_invoice_file', $data, $where);
    }

    public function getBillingCyclePaymentForRoId($ro_id)
    {
        $query = "select raer.agency,raer.client,rna.internal_agency,rac.billing_cylce as agency_billing_cycle,rcc.billing_info as clinet_billing_cycle from ro_am_external_ro raer "
            . "Inner join sv_new_agency rna on rna.agency_name = raer.agency "
            . "Inner Join ro_agency_contact rac on rac.id = raer.agency_contact_id "
            . "Left Join ro_client_contact rcc on rcc.id = raer.client_contact_id "
            . "where raer.id = '$ro_id'  ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            if ($result[0]['internal_agency'] == 1) {
                $billingInfo = $result[0]['clinet_billing_cycle'];
            } else {
                $billingInfo = $result[0]['agency_billing_cycle'];
            }
            return $billingInfo;
        }
        return FALSE;
    }

    public function generateInvoiceNumber($ro_id, $monthName)
    {
        $month = date("m");
        $todaysDate = date("d");

        if ($todaysDate <= 10) {
            $current_month_name = date("F", strtotime("-1 month"));
        } else {
            $current_month_name = date("F");
        }

        if ($month < 4) {
            $financial_year = date("Y") - 1;
        } else if ($month == 4) {
            if ($todaysDate <= 10) {
                $financial_year = date("Y") - 1;
            } else {
                $financial_year = date("Y");
            }
        } else {
            $financial_year = date("Y");
        }

        $next_financial_year = $financial_year + 1;
        $current_financial_year = substr($financial_year, 2, 2) . "-" . substr($next_financial_year, 2, 2);

        $running_number = $this->getMaximumRunningNumberFromInvoiceFile(array('financial_year' => $financial_year));
        if (count($running_number) > 0) {
            $next_running_number = $running_number[0]['running_number'] + 1;
        } else {
            //$next_running_number = 2247 ;
            $next_running_number = 1; // changed by Biswa .If there is a year change then the invoice count starts from 1;
        }

        $userData = array('ro_id' => $ro_id, 'month_name' => $monthName, 'status' => 0, 'financial_year' => $financial_year, 'running_number' => $next_running_number);
        $this->db->insert('ro_invoice_file', $userData);
        $invoiceNumber = $this->db->insert_id();
        /* -- code changed by Biswa for converting next_running_number to string with leading number of zeros depending on its length */
        $next_running_number = $this->convertNextRunningNumberToStringWithLeadingZeroFormat((string)$next_running_number);
        /* -- end of code by Biswa */

        $data = array();
        $data['invoice_format'] = 'INV|SW|' . $current_financial_year . '|' . $current_month_name . '-' . $next_running_number;
        $data['invoiceNumber'] = $invoiceNumber;
        return $data;
    }

    public function getMaximumRunningNumberFromInvoiceFile($data)
    {
        $this->db->select_max('running_number');
        $result = $this->db->get_where('ro_invoice_file', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function convertNextRunningNumberToStringWithLeadingZeroFormat($str)
    {
        $lengthOfString = strlen($str);
        $newString = '';
        if ($lengthOfString == 1) {
            $newString = '000' . $str;
        } elseif ($lengthOfString == 2) {
            $newString = '00' . $str;
        } elseif ($lengthOfString == 3) {
            $newString = '0' . $str;
        } else {
            $newString = $str;
        }
        return $newString;
    }

    public function getInvoiceGenerationFile($data)
    {
        $result = $this->db->get_where('ro_invoice_file', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function insertForInvoiceGenerationFile($userData)
    {
        $this->db->insert('ro_invoice_file', $userData);
    }

    public function getInvoiceGeneration($data)
    {
        $result = $this->db->get_where('ro_invoice_generation_queue', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function insertForInvoiceGeneration($userData)
    {
        $this->db->insert('ro_invoice_generation_queue', $userData);
    }

    public function updateForInvoiceGeneration($data, $where)
    {
        $this->db->update('ro_invoice_generation_queue', $data, $where);
    }

    public function deleteForInvoiceGeneration($ro_id, $month_year)
    {
        $query = "delete from ro_invoice_generation_queue where ro_id=$ro_id and month_year='$month_year' ";
        $this->db->query($query);
    }

    public function getInvoiceData($monthName, $status)
    {
        $query = "select rif.invoice_number,rif.alias_invoice_number,rif.ro_id,rif.month_name,rif.split_criteria,rif.client_name,rif.agency_name,rif.invoice_amount,"
            . "raer.cust_ro,raer.internal_ro "
            . "from ro_invoice_file rif "
            . "Inner Join ro_am_external_ro raer on rif.ro_id = raer.id "
            . "where rif.month_name='$monthName' and rif.status = $status ";
        $result = $this->db->query($query);
        if ($result->num_rows() != 0) {
            return $result->result("array");
        } else {
            return array();
        }
    }
}
