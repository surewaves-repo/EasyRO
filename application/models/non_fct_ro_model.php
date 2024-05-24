<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Non_fct_ro_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function createNonFctRo($userData)
    {
        $nonFctRoId = $this->insert_helper('ro_non_fct_ro', $userData);
        return $nonFctRoId;
    }

    public function insert_helper($table_name, $data_array)
    {
        $this->db->insert($table_name, $data_array);
        return $this->db->insert_id();
    }

    public function getNonFctRoForRoId($whereData)
    {
        $result = $this->db->get_where('ro_non_fct_ro', $whereData);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function get_non_fct_ro_details($id = null)
    {//echo $id;exit;
        $query = "select * from ro_non_fct_ro where id=$id";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            return $result;
        }
    }

    public function getNonFctRoForInternalRo($internalRoNumber)
    {
        $query = "select rnfr.id as non_fct_ro_id,rnfr.customer_ro_number,rnfr.internal_ro_number,rnfr.agency,rnfr.agency_contact,rnfr.region_id, "
            . " rnfr.client,rnfr.client_contact,rnfr.account_manager_name,rnfr.financial_year, "
            . " rnfr.gross_ro_amount,rnfr.agency_commision,rnfr.description, "
            . " rnfmp.month,rnfmp.price,rnf.approved_by"
            . " from ro_non_fct_ro rnfr"
            . " inner join ro_non_fct_monthly_price rnfmp on rnfr.id = rnfmp.non_fct_ro_id "
            . " inner join ro_non_fct_request rnf on rnfr.id = rnf.non_fct_ro_id "
            . " where rnfr.internal_ro_number='$internalRoNumber' and rnf.request_type='ro_approval' ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_non_fct_ro_approval_status($whereData)
    {
        $result = $this->db->get_where('ro_non_fct_request', $whereData);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function getNonFctFinalArray($dataArray)
    {
        $data = array();

        $data['customer_ro_number'] = $dataArray[0]['customer_ro_number'];
        $data['internal_ro_number'] = $dataArray[0]['internal_ro_number'];
        $data['id'] = $dataArray[0]['non_fct_ro_id'];
        $data['agency'] = $dataArray[0]['agency'];
        $data['agency_contact'] = $dataArray[0]['agency_contact'];
        $data['client'] = $dataArray[0]['client'];
        $data['client_contact'] = $dataArray[0]['client_contact'];
        $data['account_manager_name'] = $dataArray[0]['account_manager_name'];
        $data['financial_year'] = $dataArray[0]['financial_year'];
        $data['gross_ro_amount'] = $dataArray[0]['gross_ro_amount'];
        $data['agency_commision'] = $dataArray[0]['agency_commision'];
        $data['description'] = $dataArray[0]['description'];
        $data['approved_by'] = $dataArray[0]['approved_by'];

        $data['month'] = array();

        foreach ($dataArray as $val) {
            $tmp = array();
            $tmp['month'] = $val['month'];
            $tmp['price'] = $val['price'];
            array_push($data['month'], $tmp);
        }
        return $data;
    }

    public function getNonFctRoForCustomerRoAndPrfeix($customerRo, $ro_prefix)
    {
        $query = "Select * from  ro_non_fct_ro where customer_ro_number='$customerRo' and internal_ro_number like '%$ro_prefix%' ";
        $res = $this->db->query($query);
        $res_ary = $res->result("array");

        if ($res->num_rows() != 0) {
            return $res_ary[0]['internal_ro_number'];
        } else {
            return 0;
        }
    }

    public function getNonFctRoForGeneratingInternalRo($ro_prefix_db, $ro_prefix)
    {
        $query = "Select  DISTINCT(internal_ro_number) from ro_non_fct_ro where internal_ro_number like '%$ro_prefix_db%' ORDER BY internal_ro_number DESC";
        $res = $this->db->query($query);
        $result = $res->result("array");

        $ro_number = $result[0]['internal_ro_number'];

        if (isset($ro_number) && !empty($ro_number)) {
            $len = strlen($ro_prefix);
            $snum = substr($ro_number, $len);
            $order_number = (int)$snum + 1;
        }
        if ($order_number == 0) {
            $order_number = 1;
        }
        $order_number = sprintf('%03d', $order_number);
        $internal_ro_number = $ro_prefix . $order_number;
        return $internal_ro_number;
    }


    public function get_monthly_amounts_for_ro($id)
    {

        $query = "select * from ro_non_fct_monthly_price where non_fct_ro_id = " . $id . " ";
        $res = $this->db->query($query);
        $result = $res->result("array");

        $data = array();
        foreach ($result as $key => $val) {
            $data[$val['month']] = $val['price'];
        }
        return $data;
    }

    public function getRegionData($whereData)
    {
        $result = $this->db->get_where('ro_master_geo_regions', $whereData);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function getNumberOfTimesExpenseDivision($non_fct_ro_id)
    {
        $monthlyAmount = $this->getMonthlyAmount(array('non_fct_ro_id' => $non_fct_ro_id));
        $count = 0;
        foreach ($monthlyAmount as $val) {
            if ($val['price'] == 0) {
                continue;
            } else {
                $count = $count + 1;
            }
        }
        if ($count == 0) {
            $count = 1;
        }
        return $count;
    }

    public function getMonthlyAmount($whereData)
    {
        $result = $this->db->get_where('ro_non_fct_monthly_price', $whereData);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function updateNonFctRo($whereData, $userData)
    {
        $this->db->update('ro_non_fct_ro', $userData, $whereData);
    }

    public function updateNonFctAmounts($whereData, $userData)
    {
        $this->db->update('ro_non_fct_monthly_price', $userData, $whereData);
    }

    public function createNonFctMonthlyPrice($userData)
    {
        $this->insert_helper('ro_non_fct_monthly_price', $userData);
    }

    public function getNonFctMonthlyPrice($id)
    {

    }

    public function updateNonFctMonthlyPrice($id)
    {

    }

    public function approvalRequestNonFctRo($userData)
    {
        $this->insert_helper('ro_non_fct_request', $userData);
    }

    public function getApprovalRequestNonFctRo($id)
    {

    }

    public function updateApprovalRequestNonFctRo($userData, $whereData)
    {
        $this->db->update('ro_non_fct_request', $userData, $whereData);
    }

    public function editNonFctRo()
    {
    }

    public function check_for_non_fct_ro_existence($cust_ro)
    {
        $chk_cust_ro_exist_qry = "select * from ro_non_fct_ro where customer_ro_number='$cust_ro'";
        $res_chk_cust_ro_exist_qry = $this->db->query($chk_cust_ro_exist_qry);
        if ($res_chk_cust_ro_exist_qry->num_rows() > 0) {
            echo 1;
        }
    }

    public function getFinancialRunningNumber($financialYear)
    {
        $where = " financial_year='$financialYear'  ";
        $query = "select max(financial_year_running_no) as financial_year_running_no from ro_non_fct_ro where  " . $where;
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            return $result[0]['financial_year_running_no'];
        } else {
            return 1;
        }
    }

    public function monthlyNonFctReport($givenDate)
    {
        $month = date("m", strtotime($givenDate));
        $year = date("Y", strtotime($givenDate));

        if ($month <= 3) {
            $financialYear = $year - 1;
        } else {
            $financialYear = $year;
        }

        $monthName = date("F", strtotime($givenDate));

        $query = "select rnfr.id,rnfr.internal_ro_number,rnfr.client,rnfr.agency_commision from ro_non_fct_ro rnfr "
            . " inner join ro_non_fct_request rnf on rnfr.id=rnf.non_fct_ro_id"
            . " inner join ro_non_fct_monthly_price rnfmp on rnfr.id=rnfmp.non_fct_ro_id"
            . " where rnfr.financial_year='$financialYear' and rnf.approved_by= 1 and rnfmp.month='$monthName' and rnfmp.price !=0 order by rnfr.client";
        $query_res = $this->db->query($query);
        $data = array();

        if ($query_res->num_rows() != 0) {
            $result = $query_res->result("array");
            foreach ($result as $val) {
                $client_name = $val['client'];
                $non_fct_id = $val['id'];
                $internal_ro = $val['internal_ro_number'];
                $expense = $val['agency_commision'];

                $monthlyCalculation = $this->nonFctMonthlyCalculation($non_fct_id, $monthName, $expense);

                $tmp = array();
                $tmp['client'] = $client_name;
                $tmp['internal_ro'] = $internal_ro;
                $tmp['revenue'] = floor($monthlyCalculation['revenue']);
                $tmp['network_payout'] = floor($monthlyCalculation['network_payout']);
                $tmp['sw_net_contribute'] = floor($monthlyCalculation['net_contribution']);
                $tmp['net_contribuition_percent'] = round(($monthlyCalculation['net_contribution'] / $monthlyCalculation['revenue']) * 100);

                array_push($data, $tmp);


            }
            //echo $data;

            $data = $this->mg_model->convert_data_into_structure_bkp($data);
            return $this->mg_model->getMonthlyNonFctMisReport_bkp($data);

        } else {
            $structure = "<table border='1' style='text-align:left;width:100%' ><tr>"
                . "<th><b>Client Name</b></th>"
                . "<th><b>No. Ro's</b></th>"
                . "<th><b>Revenue(INR)</b></th>"
                . "<th><b>Expenses(INR)</b></th>"
                . "<th><b>Net Contribution(INR)</b></th>"
                . "<th><b>Net Contribution %</b></th>"
                . "</tr>";

            $structure = $structure . "<tr>";
            $structure = $structure . "<td>#</td>";
            $structure = $structure . "<td> 0 </td>";
            $structure = $structure . "<td> 0 </td>";
            $structure = $structure . "<td> 0 </td>";
            $structure = $structure . "<td> 0 </td>";
            $structure = $structure . "<td> 0 </td>";
            $structure = $structure . "</tr>";

            $structure = $structure . "</table>";
            return $structure;
        }

    }

    public function nonFctMonthlyCalculation($non_fct_id, $monthName, $expense, $monthlyRoAmount)
    {
        //$numberOfTimesExpenseDivision = $this->getNumberOfTimesExpenseDivision($non_fct_id) ;
        //$monthlyAmount = $this->getMonthlyAmount(array('non_fct_ro_id'=>$non_fct_id,'month'=>$monthName)) ;
        $totalAmount = $this->getTotalNonFctRoAmount(array('non_fct_ro_id' => $non_fct_id));
        if ($totalAmount == 0) {
            $totalAmount = 1;
        }
        $monthlyFraction = $monthlyRoAmount / $totalAmount;
        /* if($numberOfTimesExpenseDivision == 0) {
            $numberOfTimesExpenseDivision = 1 ;
        }

        $monthlyExpense = $expense/$numberOfTimesExpenseDivision ; */
        $monthlyExpense = $expense * $monthlyFraction;
        $data = array();
        $data['revenue'] = $monthlyRoAmount;
        $data['network_payout'] = $monthlyExpense;
        $data['net_contribution'] = $monthlyRoAmount - $monthlyExpense;

        return $data;
    }

    public function getTotalNonFctRoAmount($whereData)
    {
        $this->db->select_sum('price');
        $result = $this->db->get_where('ro_non_fct_monthly_price', $whereData);
        if ($result->num_rows() > 0) {
            $amount = $result->result("array");
            return $amount[0]['price'];
        }
        return 0;
    }


    /*public function financialYearNonFctReport($givenDate) {
        $month = date("m",strtotime($givenDate)) ;
        $year = date("Y",strtotime($givenDate));
        if($month <= 3){
                $to_year = $year;
                $from_year = $year - 1;
        }
        else{
                $to_year = $year + 1;
                $from_year = $year;
        }
        $current_financial_month_start_date = date("$from_year-04-01") ;
        $current_financial_month_end_date = date("$to_year-03-31") ;

        $structure = "<table border='1' style='text-align:left;width:100%'><tr>"
                    ."<th><b>Month Name</b></th>"
                    ."<th><b>No. Ro's</b></th>"
                    ."<th><b>Target</b></th>"
                    ."<th><b>Revenue(INR)</b></th>"
                    ."<th><b>% Achieved</b></th>"
                    ."<th><b>Expenses(INR)</b></th>"
                    ."<th><b>Net Contribution(INR)</b></th>"
                    ."<th><b>Net Contribution %</b></th><tr>";

        $total_yr_ro = 0 ;
        $total_yr_revenue = 0 ;
        $total_yr_network_payout_val = 0 ;
        $total_yr_sw_net_contribution = 0 ;
        $total_yr_target = 0;
        $total_yr_target_achieved = 0;

        for($i=strtotime($current_financial_month_start_date); $i<= strtotime($current_financial_month_end_date); $i = strtotime("+1 month", $i) ){
            $monthName = date("F",$i) ;

            $start_date = date("Y-m-d",$i) ;
            if(date("m",$i)== date("m")) {
                $end_date = date("Y-m-t") ;
            }else {
                $end_date = date("Y-m-t",strtotime("+1 month -1 days", $i)) ;
            }
                 
            $query = "select rnfr.id,rnfr.internal_ro_number,rnfr.client,rnfr.agency_commision,rnfr.region_id from ro_non_fct_ro rnfr"
                . " inner join ro_non_fct_request rnf on rnfr.id=rnf.non_fct_ro_id"
                . " inner join ro_non_fct_monthly_price rnfmp on rnfr.id=rnfmp.non_fct_ro_id"
                . " where rnfr.financial_year='$from_year' and rnf.approved_by= 1 and rnfmp.month='$monthName' and rnfmp.price !=0 order by rnfr.client" ;
            $query_res = $this->db->query($query);		
            
            if($query_res->num_rows()!=0 ){
                    $no_of_ro = $query_res->num_rows() ;

                    $total_revenue = 0 ;
                    $total_network_payout_val= 0 ;
                    $total_sw_net_contribution= 0 ;
                    $monthly_target = 0;

                    //$net_contribution = 0 ;
                    $result = $query_res->result("array");	
                    foreach($result as $val){
                        $non_fct_id = $val['id'];
                        $region_id = $val['region_id'];
                        $expense = $val['agency_commision'];

                        if($region_id != '' && $region_id != NULL ){
                            $calculate_target = $this->mg_model->get_targets_for_month($region_id,$start_date,$end_date);
                        }else{
                            $calculate_target = 0;
                        }

                        $monthly_target = $monthly_target + $calculate_target[0]['target'];

                        $monthlyCalculation = $this->nonFctMonthlyCalculation($non_fct_id,$monthName,$expense) ;				

                            
                        $total_revenue = $total_revenue + $monthlyCalculation['revenue'] ;                                                
                        $total_network_payout_val = $total_network_payout_val + $monthlyCalculation['network_payout'] ;
                        $total_sw_net_contribution = $total_sw_net_contribution + $monthlyCalculation['net_contribution'] ;
                    }
                      $net_contribuition_percent =   round(($total_sw_net_contribution/$total_revenue)*100) ;
                      $target_achieved = round(($total_revenue/$monthly_target) *100);

                      $structure = $structure."<tr>";
                      $structure = $structure."<td>".date("M-Y",$i)."</td>"; 
                      $structure = $structure."<td>".$no_of_ro."</td>";
                      $structure = $structure."<td>".floor($monthly_target)."</td>";
                      $structure = $structure."<td>".floor($total_revenue)."</td>";
                      $structure = $structure."<td>".floor($target_achieved)."</td>";
                      $structure = $structure."<td>".floor($total_network_payout_val)."</td>"; 
                      $structure = $structure."<td>".floor($total_sw_net_contribution)."</td>";
                      $structure = $structure."<td>".$net_contribuition_percent." %</td>";
                      $structure = $structure."</tr>" ;
                      
                      $userData = array(
                                            'financial_year' => $from_year,
                                            'month_name' => date("F",$i) ,
                                            'no_of_ro' => $no_of_ro ,
                                            'target' => floor($monthly_target),
                                            'revenue' => floor($total_revenue),
                                            'target_achieved' =>floor($target_achieved),
                                            'network_payout' => floor($total_network_payout_val),
                                            'net_contribuition' => floor($total_sw_net_contribution),
                                            'is_fct' => 0
                                        );
                      $whereData = array(
                                            'financial_year' => $from_year,
                                            'month_name' => date("F",$i) ,
                                            'is_fct' => 0
                                         );

                      $this->mg_model->insertIntoMonthWiseRoReport($userData,$whereData) ;

                      $total_yr_ro = $total_yr_ro + $no_of_ro ;
                      $total_yr_target = $total_yr_target + $monthly_target ;
                      $total_yr_revenue = $total_yr_revenue+floor($total_revenue) ;
                      $total_yr_network_payout_val = $total_yr_network_payout_val + floor($total_network_payout_val)  ;
                      $total_yr_sw_net_contribution = $total_yr_sw_net_contribution +floor($total_sw_net_contribution) ;
            }                   
        }
        $net_yr_contribuition_percent =   round(($total_yr_sw_net_contribution/$total_yr_revenue)*100) ;
        $total_yr_target_achieved = round(($total_yr_revenue/$total_yr_target)*100);

        $structure = $structure."<tr>";
        $structure = $structure."<td>#</td>"; 
        $structure = $structure."<td>".$total_yr_ro."</td>";
        $structure = $structure."<td>".$total_yr_target."</td>";
        $structure = $structure."<td>".$total_yr_revenue."</td>";
        $structure = $structure."<td>".$total_yr_target_achieved."</td>";
        $structure = $structure."<td>".$total_yr_network_payout_val."</td>"; 
        $structure = $structure."<td>".$total_yr_sw_net_contribution."</td>";
        $structure = $structure."<td>".$net_yr_contribuition_percent." %</td>";
        $structure = $structure."</tr>" ;


        $structure = $structure."</table>" ;
        return $structure ;
    } */

    public function financialYearNonFctReport($givenDate)
    {
        $month = date("m", strtotime($givenDate));
        $year = date("Y", strtotime($givenDate));
        if ($month <= 3) {
            $to_year = $year;
            $from_year = $year - 1;
        } else {
            $to_year = $year + 1;
            $from_year = $year;
        }
        $current_financial_month_start_date = date("$from_year-04-01");
        $current_financial_month_end_date = date("$to_year-03-31");

        $structure = "<table border='1' style='text-align:left;width:100%'><tr>"
            . "<th><b>Month Name</b></th>"
            . "<th><b>No. Ro's</b></th>"
            . "<th><b>Revenue(INR)</b></th>"
            . "<th><b>Expenses(INR)</b></th>"
            . "<th><b>Net Contribution(INR)</b></th>"
            . "<th><b>Net Contribution %</b></th><tr>";

        $total_yr_ro = 0;
        $total_yr_revenue = 0;
        $total_yr_network_payout_val = 0;
        $total_yr_sw_net_contribution = 0;
        for ($i = strtotime($current_financial_month_start_date); $i <= strtotime($current_financial_month_end_date); $i = strtotime("+1 month", $i)) {
            $monthName = date("F", $i);

            $query = "select rnfr.id,rnfr.internal_ro_number,rnfr.client,rnfr.agency_commision from ro_non_fct_ro rnfr"
                . " inner join ro_non_fct_request rnf on rnfr.id=rnf.non_fct_ro_id"
                . " inner join ro_non_fct_monthly_price rnfmp on rnfr.id=rnfmp.non_fct_ro_id"
                . " where rnfr.financial_year='$from_year' and rnf.approved_by= 1 and rnfmp.month='$monthName' and rnfmp.price !=0 order by rnfr.client";
            $query_res = $this->db->query($query);

            if ($query_res->num_rows() != 0) {
                $no_of_ro = $query_res->num_rows();

                $total_revenue = 0;
                $total_network_payout_val = 0;
                $total_sw_net_contribution = 0;
                //$net_contribution = 0 ;
                $result = $query_res->result("array");
                foreach ($result as $val) {
                    $non_fct_id = $val['id'];
                    $expense = $val['agency_commision'];

                    $monthlyCalculation = $this->nonFctMonthlyCalculation($non_fct_id, $monthName, $expense);


                    $total_revenue = $total_revenue + $monthlyCalculation['revenue'];
                    $total_network_payout_val = $total_network_payout_val + $monthlyCalculation['network_payout'];
                    $total_sw_net_contribution = $total_sw_net_contribution + $monthlyCalculation['net_contribution'];
                }
                $net_contribuition_percent = round(($total_sw_net_contribution / $total_revenue) * 100);
                $structure = $structure . "<tr>";
                $structure = $structure . "<td>" . date("M-Y", $i) . "</td>";
                $structure = $structure . "<td>" . $no_of_ro . "</td>";
                $structure = $structure . "<td>" . floor($total_revenue) . "</td>";
                $structure = $structure . "<td>" . floor($total_network_payout_val) . "</td>";
                $structure = $structure . "<td>" . floor($total_sw_net_contribution) . "</td>";
                $structure = $structure . "<td>" . $net_contribuition_percent . " %</td>";
                $structure = $structure . "</tr>";

                $userData = array(
                    'financial_year' => $from_year,
                    'month_name' => date("F", $i),
                    'no_of_ro' => $no_of_ro,
                    'revenue' => floor($total_revenue),
                    'network_payout' => floor($total_network_payout_val),
                    'net_contribuition' => floor($total_sw_net_contribution),
                    'is_fct' => 0
                );
                $whereData = array(
                    'financial_year' => $from_year,
                    'month_name' => date("F", $i),
                    'is_fct' => 0
                );

                $this->mg_model->insertIntoMonthWiseRoReport($userData, $whereData);

                $total_yr_ro = $total_yr_ro + $no_of_ro;
                $total_yr_revenue = $total_yr_revenue + floor($total_revenue);
                $total_yr_network_payout_val = $total_yr_network_payout_val + floor($total_network_payout_val);
                $total_yr_sw_net_contribution = $total_yr_sw_net_contribution + floor($total_sw_net_contribution);
            }
        }
        $net_yr_contribuition_percent = round(($total_yr_sw_net_contribution / $total_yr_revenue) * 100);

        $structure = $structure . "<tr>";
        $structure = $structure . "<td>#</td>";
        $structure = $structure . "<td>" . $total_yr_ro . "</td>";
        $structure = $structure . "<td>" . $total_yr_revenue . "</td>";
        $structure = $structure . "<td>" . $total_yr_network_payout_val . "</td>";
        $structure = $structure . "<td>" . $total_yr_sw_net_contribution . "</td>";
        $structure = $structure . "<td>" . $net_yr_contribuition_percent . " %</td>";
        $structure = $structure . "</tr>";


        $structure = $structure . "</table>";
        return $structure;
    }

    public function get_non_fct_ros($profile_id, $user_id, $is_test_user)
    {

        $logged_in = $this->session->userdata("logged_in_user");
        $users_list = $this->get_all_reporting_users($profile_id, $user_id);
        $regionArray = $this->am_model->getLoginUserRegions($logged_in[0]['user_id']);
        //$region_id = $regionArray[0]['region_id'];
        $region_ids = '';
        foreach ($regionArray as $region) {
            if ($region_ids == '') {
                $region_ids = $region['region_id'];
            } else {
                $region_ids = $region_ids . "," . $region['region_id'];
            }
        }

        $query = "SELECT nfct.* ,req.approved_by,req.approval_level  FROM `ro_non_fct_ro` AS nfct
                        INNER JOIN `ro_non_fct_request` AS req ON nfct.id = req.non_fct_ro_id
                        WHERE req.approved_by= 0 AND nfct.user_id IN ($users_list) and nfct.region_id IN ($region_ids)  ORDER BY nfct.id DESC";

        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");

            return $result;
        }
        return array();
    }

    public function get_all_reporting_users($profile_id, $user_id)
    {

        $rep_users = array();

        switch ($profile_id) {

            //Account Manager
            case '6':
                array_push($rep_users, $user_id);
                break;

            //Group Manager
            case '12':
                $query = "SELECT DISTINCT account_manager,group_manager FROM ro_external_ro_user_map
                            where group_manager = $user_id AND is_fct=0";
                $res = $this->db->query($query);
                if ($res->num_rows() > 0) {
                    $result_users = $res->result("array");
                    foreach ($result_users as $user) {
                        array_push($rep_users, $user['account_manager']);
                        array_push($rep_users, $user['group_manager']);
                    }
                }
                break;

            //Regional Director
            case '11':
                $query = "SELECT DISTINCT account_manager,group_manager,regional_director FROM ro_external_ro_user_map
                            where regional_director = $user_id AND is_fct=0";
                $res = $this->db->query($query);
                if ($res->num_rows() > 0) {
                    $result_users = $res->result("array");
                    foreach ($result_users as $user) {
                        array_push($rep_users, $user['account_manager']);
                        array_push($rep_users, $user['group_manager']);
                        array_push($rep_users, $user['regional_director']);
                    }
                }
                break;

            //Other users
            default:
                $query = "SELECT DISTINCT user_id FROM ro_non_fct_ro";
                $res = $this->db->query($query);
                if ($res->num_rows() > 0) {
                    $result_users = $res->result("array");
                    foreach ($result_users as $user) {
                        array_push($rep_users, $user['user_id']);
                    }
                }
                break;
        }

        if (count($rep_users) > 0) {
            $result_users = array_unique($rep_users);
        } else {
            /*$query = "SELECT DISTINCT user_id FROM ro_non_fct_ro";
            $res = $this->db->query($query);
            if($res->num_rows() > 0) {
                $result_users = $res->result("array");
                foreach($result_users as $user){
                    array_push($rep_users,$user['user_id']);
                }
            }

            $result_users = array_unique($rep_users);*/
            $result_users = array();
        }


        $user_ids = $this->am_model->get_reporting_user_list($result_users);
        return $user_ids;
    }

    public function get_non_fct_approved_ros($profile_id, $user_id, $is_test_user)
    {

        $logged_in = $this->session->userdata("logged_in_user");
        $users_list = $this->get_all_reporting_users($profile_id, $user_id);
        $regionArray = $this->am_model->getLoginUserRegions($logged_in[0]['user_id']);
        //$region_id = $regionArray[0]['region_id'];
        $region_ids = '';
        foreach ($regionArray as $region) {
            if ($region_ids == '') {
                $region_ids = $region['region_id'];
            } else {
                $region_ids = $region_ids . "," . $region['region_id'];
            }
        }

        $query = "SELECT nfct.* ,req.approved_by,req.approval_level  FROM `ro_non_fct_ro` AS nfct
                        INNER JOIN `ro_non_fct_request` AS req ON nfct.id = req.non_fct_ro_id
                        WHERE req.approved_by= 1 AND nfct.user_id IN ($users_list) AND nfct.region_id IN ($region_ids) ORDER BY nfct.id DESC";

        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");

            return $result;
        }
        return array();
    }

}

?>
