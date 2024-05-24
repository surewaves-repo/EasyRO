<?php

class AM_model extends CI_Model
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

    public function get_all_agency()
    {
        $query = "select * from sv_agency_display
		            inner join sv_new_agency on sv_new_agency.id = sv_agency_display.agency_id
		            where sv_new_agency.agency_name != 'donot use delete' order by sv_new_agency.agency_name ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_all_adv()
    {
        $query = "select distinct advertiser_display_name from sv_advertiser_display
		            inner join sv_new_advertiser on sv_new_advertiser.id = sv_advertiser_display.advertiser_id
		            where sv_new_advertiser.active=1 order by sv_advertiser_display.advertiser_display_name";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_all_markets($is_cluster = 0)
    {
        $query = "select * from sv_sw_market where is_cluster = '$is_cluster' order by sw_market_name";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_market_details($market_id)
    {
        $query = "select * from sv_sw_market where id = $market_id";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function insert_agency_info_against_ro($cust_ro)
    {
        $agency_name = $this->input->post('txt_agency_name');
        $agency_address = $this->input->post('txt_agency_address');
        $agency_billing_name = $this->input->post('txt_agency_billing_name');
        //$agency_category = $this->input->post('txt_agency_category');

        $query = "select * from ro_agency_against_ro where agency_name='$agency_name'";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return 'exists';
        } else {
            /*$insert_qry = "INSERT INTO `ro_agency_against_ro` (`id`, `customer_ro`, `agency_name`, `billing_info`, `billing_address`, `pay_cylce`, `agency_contact_name`, `agency_contact_no`, `agency_location`, `agency_address`, `agency_category`) VALUES (NULL, '$cust_ro', '$agency_name', '$billing_info', '$billing_address', '$pay_cylce', '$agency_contact_name', '$agency_contact_no', '$agency_location', '$agency_address', '$agency_category')";
			$res_insert_qry = $this->db->query($insert_qry); */

            $user_data = array('agency_name' => $agency_name,
                'agency_address' => $agency_address,
                'agency_billing_name' => $agency_billing_name
            );
            $this->db->insert('ro_agency_against_ro', $user_data);

            $chk_new_agency_query = "select * from sv_new_agency where agency_name='$agency_name'";
            $res_chk_new_agency_query = $this->db->query($chk_new_agency_query);

            if ($res_chk_new_agency_query->num_rows() == 0) {
                $inser_new_agency_qry = "INSERT INTO sv_new_agency(id,agency_name) values (NULL,'$agency_name')";
                $res_inser_new_agency_qry = $this->db->query($inser_new_agency_qry);
            }
            return 'success';
        }

    }

    public function post_add_agency_contact($operation)
    {
        $agency_dis_name = $this->input->post('txt_agency_name');
        $billing_info = $this->input->post('txt_billing_info');
        $billing_address = $this->input->post('txt_billing_address');
        //$pay_cylce = $this->input->post('txt_pay_cylce');
        $agency_contact_name = $this->input->post('txt_agency_contact_name');
        //$agency_designation = $this->input->post('txt_agency_designation');
        $agency_contact_no = $this->input->post('txt_agency_contact_no');
        //$agency_location = $this->input->post('txt_agency_location');
        $agency_address = $this->input->post('txt_agency_address');
        $agency_email = $this->input->post('txt_agency_email');
        $agency_state = $this->input->post('agency_state');

        //new implementation Dec-15
        $agency_details = $this->get_new_agency_details($agency_dis_name);
        $agency_name = $agency_details[0]['agency_name'];
//        $billing_address = $agency_details[0]['billing_address'];
//        $billing_info = $agency_details[0]['billing_info'];
        $billing_cylce = $agency_details[0]['billing_cycle'];
//        $agency_address = $agency_details[0]['agency_address'];

        if ($operation == 'add') {
            $qry = "INSERT INTO `ro_agency_contact` (`id`,`agency_display_name`, `agency_name`, `billing_info`, `billing_address`, `billing_cylce`, `agency_contact_name`, `agency_contact_no`, `agency_address`, `agency_email`,`agency_state`) VALUES (NULL,'$agency_dis_name','$agency_name', '$billing_info', '$billing_address', '$billing_cylce', '$agency_contact_name','$agency_contact_no', '$agency_address', '$agency_email','$agency_state')";
        } else {
            $qry = "UPDATE `ro_agency_contact` SET `billing_info` = '$billing_info',`billing_address` = '$billing_address',`billing_cylce` = '$billing_cylce',`agency_contact_name` = '$agency_contact_name',`agency_contact_no` = '$agency_contact_no',`agency_address` = '$agency_address',`agency_email` = '$agency_email',`agency_state` = '$agency_state' WHERE `id` ='$operation';";
        }
        $res_qry = $this->db->query($qry);
        return 'success';
    }

    function get_new_agency_details($agencyDisplay)
    {
        $query = "select * from  sv_agency_display
                    inner join sv_new_agency on sv_new_agency.id = sv_agency_display.agency_id
                    where sv_agency_display.agency_display_name='$agencyDisplay'";
        $res = $this->db->query($query);
        return $res->result("array");
    }

    public function post_add_client_contact($operation)
    {
        $client_display_name = $this->input->post('txt_client_display_name');
        //$billing_info = $this->input->post('txt_billing_info');
        //$billing_address = $this->input->post('txt_billing_address');
        //$pay_cylce = $this->input->post('txt_pay_cylce');
        $client_contact_name = $this->input->post('txt_client_contact_name');
        $client_designation = $this->input->post('txt_client_designation');
        $client_contact_no = $this->input->post('txt_client_contact_no');
        $client_location = $this->input->post('txt_client_location');
        $client_address = $this->input->post('txt_client_address');
        $client_email = $this->input->post('txt_client_email');
        $client_state = $this->input->post('client_state');

        //$direct_client = $this->input->post('rd_direct_client');

        //new implementation Dec-15
        $client_details = $this->get_advertiser_details($client_display_name);
        $client_name = $client_details[0]['advertiser'];
        $billing_address = $client_details[0]['billing_address'];
        $billing_info = $client_details[0]['billing_info'];
        $billing_cylce = $client_details[0]['billing_cycle'];
//        $client_address = $client_details[0]['client_address'];
        $direct_client = $client_details[0]['direct_client'];

        if ($operation == 'add') {
            $qry = "INSERT INTO `ro_client_contact` (`id`,`client_name`, `client_display_name`, `billing_info`, `billing_address`, `bill_cylce`, `client_contact_name`,`client_designation`, `client_contact_number`, `client_location`, `client_address`, `client_email`,`direct_client`,`client_state`) VALUES (NULL, '$client_name','$client_display_name', '$billing_info', '$billing_address', '$billing_cylce', '$client_contact_name','$client_designation', '$client_contact_no', '$client_location', '$client_address', '$client_email','$direct_client','$client_state')";
        } else {
            $qry = "UPDATE `ro_client_contact` SET `billing_info` = '$billing_info',`billing_address` = '$billing_address',`bill_cylce` = '$billing_cylce',`client_contact_name` = '$client_contact_name',`client_designation` = '$client_designation',`client_contact_number` = '$client_contact_no',`client_location` = '$client_location',`client_address` = '$client_address',`client_email` = '$client_email', `direct_client` = '$direct_client',`client_state` = '$client_state' WHERE `id` ='$operation';";
        }
        $res_qry = $this->db->query($qry);
        return 'success';
    }

    function get_advertiser_details($client_display_name)
    {
        $query = "select * from sv_advertiser_display
                    inner join sv_new_advertiser on sv_new_advertiser.id=sv_advertiser_display.advertiser_id
                    where sv_advertiser_display.advertiser_display_name='$client_display_name'
					group by sv_advertiser_display.advertiser_display_name";
        $res = $this->db->query($query);
        return $res->result("array");
    }

    public function insert_client_info_against_ro($cust_ro)
    {
        $client_name = $this->input->post('txt_client_name');
        $client_contact_name = $this->input->post('txt_client_contact_name');
        $client_contact_number = $this->input->post('txt_client_contact_number');
        $client_location = $this->input->post('txt_client_location');
        $client_address = $this->input->post('txt_client_address');
        $client_category = $this->input->post('txt_client_category');
        $direct_client = $this->input->post('rd_direct_client');
        if ($direct_client == 'Y') {
            $billing_info = $this->input->post('txt_client_billing_info');
            $billing_address = $this->input->post('txt_client_billing_address');
            $pay_cylce = $this->input->post('txt_client_pay_cycle');
        } else {
            $billing_info = "";
            $billing_address = "";
            $pay_cylce = "";
        }
        $query = "select * from ro_client_against_ro where customer_ro='$cust_ro' and client_name='$client_name'";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return 'exists';
        } else {
            $insert_qry = "INSERT INTO `ro_client_against_ro` (`id`, `customer_ro`, `client_name`, `client_contact_name`, `client_contact_number`, `client_location`, `client_address`, `client_category`,`direct_client`,`billing_info`, `billing_address`, `pay_cylce`) VALUES (NULL, '$cust_ro', '$client_name', '$client_contact_name', '$client_contact_number', '$client_location', '$client_address', '$client_category', '$direct_client', '$billing_info', '$billing_address', '$pay_cylce')";
            $res_insert_qry = $this->db->query($insert_qry);

            $chk_new_adv_query = "select * from sv_new_advertiser where advertiser='$client_name'";
            $res_chk_new_adv_query = $this->db->query($chk_new_adv_query);

            if ($res_chk_new_adv_query->num_rows() == 0) {
                $inser_new_adv_qry = "INSERT INTO sv_new_advertiser(id,advertiser,product_group_id,active) values (NULL,'$client_name',331,1)"; // adding new advertiser in tam under Miscellaneous product group
                $res_inser_new_adv_qry = $this->db->query($inser_new_adv_qry);
            }
            return 'success';
        }

    }

    //changing - ravishankar singh
    public function get_adv_brand($adv)
    {
        $query = "select * from sv_new_brand where new_advertiser_id in (select advertiser_id from sv_advertiser_display where advertiser_display_name ='$adv') order by brand";
        $res = $this->db->query($query);
        $brands_arrs = $res->result("array");
        return $brands_arrs;

        /*$option = "";
        foreach ($brands_arrs as $brands_arr) {
            $option .= '<option value="' . $brands_arr['id'] . '">' . $brands_arr['brand'] . '</option>';
        }
        return $option;*/
    }

    public function insert_brand_for_client($brand_id)
    {
        $brand_name = $this->input->post('txt_brand_name');
        $query = "select * from sv_new_brand where brand='$brand_name' and new_advertiser_id=(select new_advertiser_id from sv_new_brand where id=$brand_id)";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return 'exists';
        } else {
            $new_adv_qry = "select new_advertiser_id from sv_new_brand where id=$brand_id";
            $res_new_adv_qry = $this->db->query($new_adv_qry);
            $single_adv_arrs = $res_new_adv_qry->result("array");
            $new_adv_id = $single_adv_arrs[0]['new_advertiser_id'];
            $insert_qry = "INSERT INTO `sv_new_brand` (`id`, `brand`, `new_advertiser_id`) VALUES (NULL, '$brand_name', '$new_adv_id')";
            $res_insert_qry = $this->db->query($insert_qry);
            return 'success';
        }

    }

    //         This function is no longer needed as 'file_location' column in ro_am_external_ro_files table is redundant with
    //        'file_path' in ro_am_external_ro table.
    //         Also multiple files are not attached for RO , they are zipped and then uploaded as a single file
    /*
     public function addFilesForAmRo($ro_id, $file_location)
     {
        $userData = array('ro_id' => $ro_id, 'file_location' => $file_location);
        $this->db->insert('ro_am_external_ro_files', $userData);
        log_message('info', 'ro_creation query STEP_20 - ' . $this->db->last_query());
     }
     */

    //this function is no longer needed in cron_job - ravishankar singh
    //This function is no longer needed as 'file_location' column in ro_am_external_ro_files table is redundant with
    // 'file_path' in ro_am_external_ro table.
    /*public function getFilesForAmRo($where_data)
    {
        $result = $this->db->get_where('ro_am_external_ro_files', $where_data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }*/

    /*public function getFileLocation($ro_id)
    {
        $query = "select group_concat(file_location) as file_location from ro_am_external_ro_files where ro_id=$ro_id";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }*/

    public function create_external_ro()
    {
        $cust_ro = trim($this->input->post('txt_ext_ro')); //done 1
        $agency_display_name = $this->input->post('sel_agency'); //done 3
        $agency_details = $this->get_new_agency_details($agency_display_name); //done 3
        log_message('info', 'ro_creation printing agency_details STEP_2 --' . print_r($agency_details, TRUE));
        $agency = $agency_details[0]['agency_name']; //done 3
        $client_display_name = $this->input->post('sel_client'); //done 4
        $client_details = $this->get_advertiser_details($client_display_name); //done 4
        log_message('info', 'ro_creation printing client_details STEP_3 --' . print_r($client_details, TRUE));
        $client = $client_details[0]['advertiser']; //done 4
        $brand = $this->input->post('hid_brand'); //done 5
        //$industry 		= $this->input->post('txt_industry');
        $industry = '';
        $user_id = $this->input->post('hid_user_id'); //done 6
        $is_test_user = $this->input->post('user_type'); //done 7
        //$vertical 		= $this->input->post('txt_vertical');
        //$region 			= $this->input->post('txt_region');
        $vertical = '';
        $region = $this->input->post('regionSelectBox');; //done 8
        //$market 			= $this->input->post('hid_market');
        // taking markets excluding 0 amount
        $market = '';    //done 9
        foreach ($_POST['markets'] as $market_name => $market_ro_amount) {
            if ($market_ro_amount['spot'] == 0 && $market_ro_amount['banner'] == 0) {
                continue;
            }
            if ($market == '') {
                $market = $market_name;
                $market = str_replace("_", " ", $market);
            } else {
                $market .= ',' . $market_name;
                $market = str_replace("_", " ", $market);
            }
        }
        // end taking markets
        $ro_date = $this->input->post('txt_ro_date'); //done 10
        $camp_start_date = $this->input->post('txt_camp_start_date'); //done 11
        $camp_end_date = $this->input->post('txt_camp_end_date'); //done 12
        //$booking 			= $this->input->post('txt_booking');
        //$category 		= $this->input->post('txt_category');
        $booking = '';
        $category = '';
        $gross = $this->input->post('txt_gross'); //done 13
        $agency_com = $this->input->post('txt_agency_com'); //done 14
        $net_agency_com = $this->input->post('txt_net_agency_com'); //done 15
        $spcl_inst = $this->input->post('txt_spcl_inst'); //done 16
        $file_pdf = $_FILES['file_pdf']['name'];
        $client_approval_email = $this->uploadClientApprovedEmail($user_id); //done 17
        log_message('info', 'ro_creation printing client_approval_email STEP_4 --' . $client_approval_email);
        // running number added
        $running_no = 0;
        //$running_no = $this->get_financial_running_number($ro_date);
        $running_no = $this->get_financial_running_number_v1($ro_date, $is_test_user); // done 18

        $running_no = $running_no + 1; //done 18
        $db_running_no = sprintf('%04d', $running_no); //done 18
        $financial_year = $this->get_financial_year_for_ro_date($ro_date); //done 18
        $db_financial_year = $db_financial_year = $this->get_financial_year_for_ro_date_v1($ro_date); //done 18
        $running_no = $financial_year . '-' . $db_running_no; //done 18
        // end

        $internal_ro_no = $this->generate_internal_ro_number($client, $agency, $cust_ro, $camp_start_date, $running_no); //done 19
        log_message('info', 'ro_creation printing internal_ro_no STEP_5 --' . $internal_ro_no);
        $make_good_type = $this->input->post('rd_make_good'); //done 20
        $agency_contact_id = $this->input->post('sel_agency_contact'); //done 21
        $client_contact_id = $this->input->post('sel_client_contact'); //done 22
        if ($client_contact_id == 'new') {
            $client_contact_id = 0;
        }

        $chk_cust_ro_exist_qry = "select * from ro_am_external_ro where cust_ro='$cust_ro'"; //done 2
        $res_chk_cust_ro_exist_qry = $this->db->query($chk_cust_ro_exist_qry); //done 2
        if ($res_chk_cust_ro_exist_qry->num_rows() > 0) {
            echo '<span style="color:#F00;">The external ro already exists.</span>';
        } else {
            /* @@@@@@@@@@@@@@@@@@@@@@@@@@@ Inserting mail list ------------------*/
            $mailList = implode(",", $this->input->post('Order_history_recipient_ids'));
            $logged_in = $this->session->userdata("logged_in_user");
            $mailList = $logged_in[0]['user_email'] . "," . $mailList;

            $insert_qry = "INSERT INTO `ro_am_external_ro` (`id`, `cust_ro`, `internal_ro`, `agency`, `client`, `brand`, `industry`, `user_id`,`vertical`,
                            `region_id`, `market`, `ro_date`, `camp_start_date`, `camp_end_date`, `booking`, `category`, `gross`, `agency_com`, `net_agency_com`,
                             `spcl_inst`, `file_path`, `make_good_type`,`agency_contact_id`,`client_contact_id`,`previous_ro_amount`,`financial_year_running_no`,
                             `financial_year`,`test_user_creation`,`order_history_mail_list`,`client_approval_mail`) VALUES 
                             (NULL, '$cust_ro', '$internal_ro_no', '$agency', '$client', '$brand', '$industry', '$user_id', '$vertical', '$region',
                              '$market', '$ro_date', '$camp_start_date', '$camp_end_date', '$booking', '$category', '$gross', '$agency_com', '$net_agency_com',
                               '$spcl_inst', '$file_pdf_s3_url','$make_good_type','$agency_contact_id','$client_contact_id','$gross','$db_running_no',
                               '$db_financial_year','$is_test_user','$mailList','$client_approval_email')";
            $res_insert_qry = $this->db->query($insert_qry); //done 23
            $last_inserted_id = $this->db->insert_id();
            log_message('info', 'USER_INFO--' . $insert_qry);
            log_message('info', 'ro_creation STEP_6');

            //update ro_status
            $this->update_ro_status($last_inserted_id, 'RO_Created'); //done 24
            // activate advertiser if non active
            $update_new_adv_qry = "update sv_new_advertiser set active=1 where advertiser like '$client'";
            $res_update_new_adv_qry = $this->db->query($update_new_adv_qry);        //done 25
            log_message('info', 'ro_creation query is -- ' . $update_new_adv_qry . ' STEP_9');
            //change by mani : updating ro amount in ro_amount
            $ro_amount_data = array(
                'customer_ro_number' => $cust_ro,
                'internal_ro_number' => $internal_ro_no,
                'ro_amount' => $gross,
                'agency_commission_amount' => $agency_com,
                'timestamp' => date("Y-m-d H:i:s"),
                'approval_timestamp' => date("Y-m-d H:i:s"),
                'approval_user_id' => $user_id
            );
            log_message('info', 'ro_creation printing ro_amount_data STEP_10-' . print_r($update_new_adv_qry, TRUE));
            $this->add_ro_amount($ro_amount_data); //done 26
            // add individual market amount
            log_message('info', 'ro_creation entering add_ro_amount_for_each_market with param ' . $last_inserted_id . ' STEP_11');
            $this->add_ro_amount_for_each_market($last_inserted_id); //done 27
            // end

            //Adding in Approval Flow
            $logged_in = $this->session->userdata("logged_in_user");
            $user_id = $this->input->post('hid_user_id');
            $reporting_manager_details = $this->user_model->getUserDetailOfUserReportingManager($user_id); //done28

            if ($reporting_manager_details[0]['profile_id'] == 12) {
                $reporting_manager_details = $this->user_model->getUserDetailOfUserReportingManager($reporting_manager_details[0]['user_id']); //done28
            }
            if ($reporting_manager_details[0]['profile_id'] == 10) {
                $approvalLevel = 2;
            } else {
                $approvalLevel = $this->user_model->getApprovalLevel($logged_in[0]['profile_id']); //done28
            }

            $approvalData = array(
                'ext_ro_id' => $last_inserted_id,
                'cancel_type' => 'submit_ro_approval',
                'user_id' => $user_id,
                'date_of_submission' => date('Y-m-d H:i:s'),
                'date_of_cancel' => date('Y-m-d'),
                'reason' => 'None',
                'invoice_instruction' => 'Submitted By AM/GM',
                'ro_amount' => 0,
                'cancel_ro_by_admin' => 0,
                'bh_reason' => '',
                'approval_level' => $approvalLevel

            );
            log_message('info', 'ro_creation printing approvalData STEP_12-' . print_r($approvalData, TRUE));
            $whereApprovalData = array('ext_ro_id' => $last_inserted_id, 'cancel_type' => 'submit_ro_approval');
            $this->ro_model->insert_for_pending_status($whereApprovalData, $approvalData); //done 29
            exec("nohup /opt/lampp/bin/php /opt/lampp/htdocs/surewaves_easy_ro/cron.php /cron_job/calculateNetContribuitionAndMarketRate/$last_inserted_id > /dev/null &");
            return 1;
        }
    }

    public function uploadClientApprovedEmail($user_id)
    {
        if (!is_dir('easy_ro_temp_pdf')) {
            mkdir('easy_ro_temp_pdf');
        }
        $fileName = str_replace(" ", "_", $_FILES['client_aproval_mail']['name']);
        $fileNewName = $user_id . "_" . date('Y_m_d_H_i_s') . "_" . $fileName;
        $_FILES['client_aproval_mail']['name'] = $fileNewName;

        $config['upload_path'] = 'easy_ro_temp_pdf/';
        $config['allowed_types'] = '*';
        //$config['file_name'] = $fileNewName;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('client_aproval_mail') == False) {
            $error = array('error' => $this->upload->display_errors());
            $this->load->view('account_manager/create_ext_ro', $error);
        } else {
            $this->upload->data();
            return $this->uploadOntoS3($fileNewName);
        }
    }

    public function uploadOntoS3($file_name)
    {
        $file_location = $_SERVER['DOCUMENT_ROOT'] . "surewaves_easy_ro/" . 'easy_ro_temp_pdf/' . $file_name;

        require_once("S3.php");
        $s3 = new S3(AMAZON_KEY, AMAZON_VALUE);
        S3::putObject(S3::inputFile("$file_location"), "sw_easy_ro_am_pdf", "$file_name", S3::ACL_PUBLIC_READ);

        $file_pdf_s3_url = "http://s3.amazonaws.com/sw_easy_ro_am_pdf/" . $file_name;
        return $file_pdf_s3_url;
    }

    public function get_financial_running_number_v1($ro_date, $is_test_user)
    {
        $month = date("m", strtotime($ro_date));

        $financial_year = '';
        if ($month <= 3) {
            $financial_year = date("Y", strtotime($ro_date)) - 1;
        } else {
            $financial_year = date("Y", strtotime($ro_date));
        }
        /*$financial_end_year = $financial_year+1;
				$current_financial_start_date = date("$financial_year-04-01") ;
				$current_financial_end_date = date("$financial_end_year-03-31") ;

				$where = "( ( '$current_financial_start_date' <= 'ro_date') and ( 'ro_date' <= '$current_financial_end_date') )" ; */
        $where = " financial_year='$financial_year' and test_user_creation='$is_test_user' ";
        $query = "select max(financial_year_running_no) as financial_year_running_no from ro_am_external_ro where  " . $where;
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            return $result[0]['financial_year_running_no'];
        } else {
            return 0;
        }
    }

    public function get_financial_year_for_ro_date($ro_date)
    {
        $month = date("m", strtotime($ro_date));

        $financial_year = '';
        if ($month <= 3) {
            $financial_year = date("y", strtotime($ro_date)) - 1;
        } else {
            $financial_year = date("y", strtotime($ro_date));
        }

        return $financial_year;

    }

    public function get_financial_year_for_ro_date_v1($ro_date)
    {
        $month = date("m", strtotime($ro_date));

        $financial_year = '';
        if ($month <= 3) {
            $financial_year = date("Y", strtotime($ro_date)) - 1;
        } else {
            $financial_year = date("Y", strtotime($ro_date));
        }

        return $financial_year;
    }

    public function generate_internal_ro_number($client_name, $agency_name, $cro_number, $campaign_startdate, $running_no)
    {

        $ro_startdate = date('M-Y', strtotime($campaign_startdate));
        // Internal RO number nomenclature SW/<ClientName>/<agency Name>/<month>-<year yyyy>-<ro-number>
        $ro_prefix = 'SW' . '/' . $running_no . '/' . $client_name . '/' . $agency_name . '/' . $ro_startdate . '-';
        $ro_prefix_db = $client_name . '/' . $agency_name . '/' . $ro_startdate . '-';
        $order_number = 1;

        //$query="Select * from sv_advertiser_campaign where customer_ro_number='$cro_number' and internal_ro_number like 'SW/%' and visible_in_easyro !=0 ORDER BY start_date DESC";
        $query = "Select * from  ro_am_external_ro where cust_ro='$cro_number' and internal_ro like '%$ro_prefix%' ORDER BY camp_start_date DESC";
        $res = $this->db->query($query);
        $res_ary = $res->result("array");
        if ($res->num_rows() != 0) {
            return $res_ary[0]['internal_ro']; // If we have already generated the Internal RO number for this RO, return that.
        }

        //$query_n = "Select  DISTINCT(internal_ro_number) from sv_advertiser_campaign where internal_ro_number like '".$ro_prefix.'%'."' ORDER BY internal_ro_number DESC";
        $query_n = "Select  DISTINCT(internal_ro) from ro_am_external_ro where internal_ro like '%$ro_prefix_db%' ORDER BY internal_ro DESC";
        $res_n = $this->db->query($query_n);
        $res_n_ary = $res_n->result("array");
        $ro_number = $res_n_ary[0]['internal_ro']; // get the first internal ro_number of sorted list
        if (isset($ro_number) && !empty($ro_number)) {
            $len = strlen($ro_prefix);
            $snum = substr($ro_number, $len);
            $order_number = (int)$snum + 1;
        }
        $order_number = sprintf('%03d', $order_number);
        $internal_ro_number = $ro_prefix . $order_number;
        return $internal_ro_number;
    }

    public function update_ro_status($am_external_ro_id, $status)
    {
        log_message('info', 'ro_creation ENTERED update_ro_status FUNCTION with param ' . $am_external_ro_id . '--' . $status . ' STEP 7 ');
        $data_avail_in_db = $this->data_for_ro_id_in_ro_status($am_external_ro_id);
        log_message('info', 'ro_creation printing data_avail_in_db STEP_8-' . print_r($data_avail_in_db, TRUE));
        if (count($data_avail_in_db) > 0) {
            $previous_status = $data_avail_in_db[0]['ro_status'];
            $user_data = array('ro_status' => $status, 'previous_status' => $previous_status);
            $this->db->where('am_external_ro_id', $am_external_ro_id);
            $this->db->update('ro_status', $user_data);
        } else {
            $user_data = array('am_external_ro_id' => $am_external_ro_id, 'ro_status' => $status);
            $this->db->insert('ro_status', $user_data);
        }
        log_message('info', 'ro_creation printing query STEP_9-' . $this->db->last_query());
    }

    public function data_for_ro_id_in_ro_status($am_external_ro_id)
    {
        $query = "select * from ro_status where am_external_ro_id='$am_external_ro_id' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function add_ro_amount($data)
    {
        $this->db->insert('ro_amount', $data);
        log_message('info', 'ro_creation printing QUERY STEP_11 - ' . $this->db->last_query());
    }

    public function add_ro_amount_for_each_market($ro_id)
    {//echo '<pre>';print_r($_POST['markets']);

        $marketArray['price'] = $_POST['markets'];
        $marketArray['fct'] = $_POST['FCT'];
        $marketArray['rate'] = $_POST['rate'];

        foreach ($marketArray['price'] as $marketName => $market_ro_amount) {
            $market = $marketName;
            $marketName = str_replace("_", " ", $marketName);
            if ($market_ro_amount['spot'] == 0 && $market_ro_amount['banner'] == 0) {
                continue;
            }

            $data = array(
                'ro_id' => $ro_id,
                'market' => $marketName,
                'price' => ($market_ro_amount['spot'] + $market_ro_amount['banner']),

                'spot_price' => $market_ro_amount['spot'],
                'spot_fct' => $marketArray['fct'][$market]['spot_FCT'],
                'spot_rate' => $marketArray['rate'][$market]['spot_rate'],

                'banner_price' => $market_ro_amount['banner'],
                'banner_fct' => $marketArray['fct'][$market]['banner_FCT'],
                'banner_rate' => $marketArray['rate'][$market]['banner_rate'],

                'fixed_spot_price' => $market_ro_amount['spot'],
                'fixed_spot_fct' => $marketArray['fct'][$market]['spot_FCT'],

                'fixed_banner_price' => $market_ro_amount['banner'],
                'fixed_banner_fct' => $marketArray['fct'][$market]['banner_FCT']
            );
            $this->db->insert('ro_market_price', $data);

            log_message('info', 'ro_creation printing query STEP_12 - ' . $this->db->last_query());
        }
    }

    public function get_mailto_list()
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        $query = "select * from ro_user where profile_id IN(1,2,3) and is_test_user='$is_test_user'";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function generate_internal_ro_number_advance_ro($client_name, $agency_name, $cro_number, $campaign_startdate, $running_no)
    {

        $ro_startdate = date('M-Y', strtotime($campaign_startdate));
        // Internal RO number nomenclature SW/<ClientName>/<agency Name>/<month>-<year yyyy>-<ro-number>
        $ro_prefix = 'SW' . '/' . $running_no . '/' . $client_name . '/' . $agency_name . '/' . $ro_startdate . '/-/';
        $ro_prefix_db = $client_name . '/' . $agency_name . '/' . $ro_startdate . '/-/';
        $order_number = 1;

        //$query="Select * from sv_advertiser_campaign where customer_ro_number='$cro_number' and internal_ro_number like 'SW/%' and visible_in_easyro !=0 ORDER BY start_date DESC";
        $query = "Select * from  ro_am_external_ro where cust_ro='$cro_number' and internal_ro like '%$ro_prefix%' ORDER BY camp_start_date DESC";
        $res = $this->db->query($query);
        $res_ary = $res->result("array");
        if ($res->num_rows() != 0) {
            return $res_ary[0]['internal_ro']; // If we have already generated the Internal RO number for this RO, return that.
        }
        //$query_n = "Select  DISTINCT(internal_ro_number) from sv_advertiser_campaign where internal_ro_number like '".$ro_prefix.'%'."' ORDER BY internal_ro_number DESC";
        $query_n = "Select  DISTINCT(internal_ro) from ro_am_external_ro where internal_ro like '%$ro_prefix_db%' ORDER BY internal_ro DESC";
        $res_n = $this->db->query($query_n);
        $res_n_ary = $res_n->result("array");
        $ro_number = $res_n_ary[0]['internal_ro']; // get the first internal ro_number of sorted list
        if (isset($ro_number) && !empty($ro_number)) {
            $len = strlen($ro_prefix);
            $snum = substr($ro_number, $len);
            $order_number = (int)$snum + 1;
        }
        $order_number = sprintf('%03d', $order_number);
        $internal_ro_number = $ro_prefix . $order_number;
        //$internal_ro_number;
        return $internal_ro_number;
    }

    public function generate_internal_ro_number_v1($client_name, $agency_name, $cro_number, $campaign_startdate, $running_no)
    {

        $ro_startdate = date('M-Y', strtotime($campaign_startdate));
        // Internal RO number nomenclature SW/<ClientName>/<agency Name>/<month>-<year yyyy>-<ro-number>
        $ro_prefix = 'SW' . '/' . $running_no . '/' . $client_name . '/' . $agency_name . '/' . $ro_startdate . '-';
        $order_number = 1;

        //$query="Select * from sv_advertiser_campaign where customer_ro_number='$cro_number' and internal_ro_number like 'SW/%' and visible_in_easyro !=0 ORDER BY start_date DESC";
        $query = "Select * from  ro_am_external_ro where cust_ro='$cro_number' and internal_ro like '%$ro_prefix%' ORDER BY camp_start_date DESC";
        $res = $this->db->query($query);
        $res_ary = $res->result("array");
        if ($res->num_rows() != 0) {
            return $res_ary[0]['internal_ro']; // If we have already generated the Internal RO number for this RO, return that.
        }
        //$query_n = "Select  DISTINCT(internal_ro_number) from sv_advertiser_campaign where internal_ro_number like '".$ro_prefix.'%'."' ORDER BY internal_ro_number DESC";
        $query_n = "Select  DISTINCT(internal_ro) from ro_am_external_ro where internal_ro like '%$ro_prefix%' ORDER BY internal_ro DESC";
        $res_n = $this->db->query($query_n);
        $res_n_ary = $res_n->result("array");
        $ro_number = $res_n_ary[0]['internal_ro']; // get the first internal ro_number of sorted list
        if (isset($ro_number) && !empty($ro_number)) {
            $len = strlen($ro_prefix);
            $snum = substr($ro_number, $len);
            $order_number = (int)$snum;
        }
        $order_number = sprintf('%03d', $order_number);
        $internal_ro_number = $ro_prefix . $order_number;
        $internal_ro_number;
        return $internal_ro_number;
    }

    public function get_ro_for_am($user_id)
    {
        $query = "select * from ro_am_external_ro where user_id=$user_id";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            $ro_list = array();
            $i = 0;
            foreach ($result as $val) {
                array_push($ro_list, $val);
                $brand_name = $this->get_brand_names($val['brand']);
                //array_push($ro_list,$brand_name);
                $ro_list[$i]['brand_name'] = $brand_name;
                $i++;
            }
            return $ro_list;
        }
        return array();
    }

    public function get_brand_names($brand)
    {
        $brand_name = '';
        if ($brand != '') {
            $query = "select * from sv_new_brand where id in ($brand)";
            $res = $this->db->query($query);

            if ($res->num_rows() > 0) {
                $result = $res->result("array");
                foreach ($result as $brands) {
                    if ($brand_name == '') {
                        $brand_name .= $brands['brand'];
                    } else {
                        $brand_name .= ', ' . $brands['brand'];
                    }
                }
                return $brand_name;//exit;
            }
        }
        return $brand_name;
    }

    public function get_campaigns($profile_id, $user_id, $is_test_user)
    {

        //Code changed for getting ROs : Nitish 30/06/2015
        $users_list = $this->get_all_reporting_users($profile_id, $user_id);
        $logged_in = $this->session->userdata("logged_in_user");
        $regionArray = $this->getLoginUserRegions($logged_in[0]['user_id']);
        $region_id = $regionArray[0]['region_id'];

        if ($is_test_user != 2) {
            $query = "select am.*,ru.reporting_manager_id,ru.user_name,0 as ro_approval_status
                                        from ro_am_external_ro as am
                                        inner join ro_user as ru on ru.user_id = am.user_id
                                        where am.test_user_creation=$is_test_user
                                        and am.user_id IN ($users_list)
                                        and am.internal_ro not in(select internal_ro_number from ro_amount AS r
                                        WHERE r.ro_approval_status =1) ";
        } else {
            $query = "select am.*,ru.reporting_manager_id,ru.user_name,0 as ro_approval_status
                                        from ro_am_external_ro as am
                                        inner join ro_user as ru on ru.user_id = am.user_id
                                        where am.test_user_creation !=1
                                        and am.user_id IN ($users_list)
                                        and am.internal_ro not in(select internal_ro_number from ro_amount AS r
                                        WHERE r.ro_approval_status =1) and am.id not in (select ro_id from ro_linked_advance_ro)";
        }

        if ($profile_id == 11 || $profile_id == 12) {
            $query .= "and region_id = $region_id order by am.camp_start_date DESC";
        } else {
            $query .= "order by am.camp_start_date DESC";
        }


        /*if($is_test_user != 2) {
                    if($profile_id == 10 || $profile_id == 1|| $profile_id == 2 || $profile_id == 3){
                        //showing all ROs for NH BH COO and SCH
                        $query = "SELECT am .* , ru.reporting_manager_id, ru.user_name, 0 AS ro_approval_status
                                        FROM ro_am_external_ro AS am
                                        INNER JOIN ro_user AS ru ON ru.user_id = am.user_id
                                        WHERE am.test_user_creation =$is_test_user
                                        AND am.internal_ro NOT
                                        IN (SELECT internal_ro_number FROM ro_amount AS r
                                        WHERE r.ro_approval_status =1)
                                        ORDER BY am.camp_start_date DESC";
                    }
                    else{
                        $query = "select am.*,ru.reporting_manager_id,ru.user_name,0 as ro_approval_status
                                        from ro_am_external_ro as am
                                        inner join ro_user as ru on ru.user_id = am.user_id
                                        where am.test_user_creation=$is_test_user
                                        and ( ru.reporting_manager_id = $user_id or am.user_id = $user_id )
                                        and am.internal_ro not in(select internal_ro_number from ro_amount AS r
                                        WHERE r.ro_approval_status =1) order by am.camp_start_date DESC";
                    }
                }else{
                    if($profile_id == 10 || $profile_id == 1|| $profile_id == 2 || $profile_id == 3){
                        //showing all ROs for NH BH COO and SCH
                        $query = "SELECT am . * , ru.reporting_manager_id, ru.user_name, 0 AS ro_approval_status
                                        FROM ro_am_external_ro AS am
                                        INNER JOIN ro_user AS ru ON ru.user_id = am.user_id
                                        WHERE am.test_user_creation !=1
                                        AND am.internal_ro NOT
                                        IN (SELECT internal_ro_number FROM ro_amount AS r WHERE r.ro_approval_status =1)
                                        AND am.id NOT
                                        IN (SELECT ro_id FROM ro_linked_advance_ro)
                                        ORDER BY am.camp_start_date DESC ";

                    }
                    else{
                        $query = "select am.*,ru.reporting_manager_id,ru.user_name,0 as ro_approval_status
                                        from ro_am_external_ro as am
                                        inner join ro_user as ru on ru.user_id = am.user_id
                                        where am.test_user_creation !=1
                                        and ( ru.reporting_manager_id = $user_id or am.user_id = $user_id )
                                        and am.internal_ro not in(select internal_ro_number from ro_amount AS r
                                        WHERE r.ro_approval_status =1) and am.id not in (select ro_id from ro_linked_advance_ro)
                                        order by am.camp_start_date DESC";
                    }
                    
                }*/


        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            $ro_list = array();
            $i = 0;
            foreach ($result as $val) {
                $ro_status_detail = $this->data_for_ro_id_in_ro_status($val['id']);
                $validRo = TRUE;
                if ($val['test_user_creation'] != 2) {
                    $validRo = $this->isValidRo($val['internal_ro'], $val['camp_end_date'], $ro_status_detail[0]['ro_status']);
                }
                if (!$validRo) {
                    continue;
                }
                array_push($ro_list, $val);
                $brand_name = $this->get_brand_names($val['brand']);
                //$is_cancelled = $this->is_cancelled($val['id']);
                //@mani:get status detail from newly created table
                //$ro_status_detail = $this->data_for_ro_id_in_ro_status($val['id']) ;
                $ro_status = $this->convert_status_into_format($ro_status_detail[0]['ro_status']);
                $ro_list[$i]['ro_status_entry'] = $ro_status;
                /*if(count($is_cancelled) > 0 ) {
					if($is_cancelled[0]['cancel_ro_by_admin'] == 1) {
                                                if(strtotime($is_cancelled[0]['date_of_cancel']) > strtotime(date('Y-m-d'))) {
                                                    $ro_list[$i]['is_cancelled']= '1';
                                                }else {
                                                    $ro_list[$i]['is_cancelled']= '3';
                                                }
						
					}else{
						$ro_list[$i]['is_cancelled']= '0';
					}
				}else {
					$ro_list[$i]['is_cancelled']= '2';
				} */
                //array_push($ro_list,$brand_name);
                $where_data = array(
                    'ext_ro_id' => $val['id'],
                    'cancel_type' => 'ro_approval'
                );

                $schedule_approval = $this->is_cancel_request_sent_by_am($where_data);

                //$submitted_by = $this->get_user_name($val['user_id']);
                if(isset($schedule_approval) && !empty($schedule_approval))
                    $approved_by = $this->get_user_name($schedule_approval[0]['user_id']);

                $ro_list[$i]['submitted_by'] = $val['user_name'];
                if(isset($approved_by) || !empty($approved_by))
                    $ro_list[$i]['approved_by'] = $approved_by[0]['user_name'];
                else
                    $ro_list[$i]['approved_by'] = '';
                $ro_list[$i]['brand_name'] = $brand_name;
                $i++;
            }//echo '<pre>';print_r($ro_list);exit;
            return $ro_list;
        }
        return array();
    }

    public function get_all_reporting_users($profile_id, $user_id)
    {

        $rep_users = array();

        switch ($profile_id) {

            //Account Manager
            case '6':
                /*$query = "SELECT DISTINCT user_id FROM ro_am_external_ro where user_id = $user_id";
                $res = $this->db->query($query);
                if($res->num_rows() > 0) {
                    $result_users = $res->result("array");
                    foreach($result_users as $user){
                        array_push($rep_users,$user['account_manager']);
                    }
                }*/
                array_push($rep_users, $user_id);
                break;

            //Group Manager
            case '12':
                $query = "SELECT DISTINCT account_manager,group_manager FROM ro_external_ro_user_map
                            where group_manager = $user_id AND is_fct=1";
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
                            where regional_director = $user_id AND is_fct=1";
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
                $query = "SELECT DISTINCT user_id FROM ro_am_external_ro";
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
            /*$query = "SELECT DISTINCT user_id FROM ro_am_external_ro";
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


        $user_ids = $this->get_reporting_user_list($result_users);
        return $user_ids;
    }

    public function get_reporting_user_list($result_users)
    {
        if (count($result_users) > 0) {
            $user_list = '';
            foreach ($result_users as $user) {
                if ($user != NULL) {
                    if ($user_list == '') {
                        $user_list = $user;
                    } else {
                        $user_list .= "," . $user;
                    }
                }
            }
            return $user_list;
        } else {
            return 0;
        }

    }

    function getLoginUserRegions($userId)
    {

        $userId = trim($userId);

        $query = " SELECT mgr.id as region_id,
                               mgr.region_name
                        FROM   ro_master_geo_regions mgr
                               INNER JOIN ro_user_region ur
                                       ON mgr.id = ur.region_id
                        WHERE  ur.user_id = " . $userId;

        $resultObject = $this->db->query($query);
        $resultArray = $resultObject->result("array");

        return $resultArray;
    }

    public function isValidRo($internal_ro, $campaign_end_date, $ro_status)
    {
        if (empty($internal_ro) || !isset($internal_ro)) {
            return FALSE;
        } else {
            $validRo = TRUE;
            $todays_date = date('Y-m-d');
            $time_difference = strtotime($todays_date) - strtotime($campaign_end_date);
            $returnValue = True;
            if ($time_difference > 30 * 24 * 60 * 60) {
                $returnValue = FALSE;
            }

            switch ($ro_status) {
                case 'RO_Created':
                    $validRo = $returnValue;
                    break;
                case 'RO_Rejected':
                    $validRo = $returnValue;
                    break;
                case 'scheduling_in_progress':
                    $validRo = $returnValue;
                    break;
                case 'approval_requested':
                    $validRo = $returnValue;
                    break;
                case 'cancel_requested':
                    $validRo = $returnValue;
                    break;
                default:
                    $validRo = $returnValue;
                    break;
            }
            return $validRo;
        }
    }

    public function convert_status_into_format($format)
    {
        $actual_value = '';
        switch ($format) {
            case 'RO_Created':
                $actual_value = 'RO Created';
                break;
            case 'RO_Rejected':
                $actual_value = 'RO Rejected';
                break;
            case 'scheduling_in_progress':
                $actual_value = 'Scheduling In Progress';
                break;
            case 'approval_requested':
                $actual_value = 'Approval Requested';
                break;
            case 'approved':
                $actual_value = 'Approved';
                break;
            case 'cancel_requested':
                $actual_value = 'Cancel Requested';
                break;
            case 'cancel_approved':
                $actual_value = 'Cancel Approved';
                break;
            case 'cancelled':
                $actual_value = 'Cancelled';
                break;
            default:
                $actual_value = 'Scheduling In Progress';
                break;
        }
        return $actual_value;
    }

    public function is_cancel_request_sent_by_am($where_data)
    {
        $result = $this->db->get_where('ro_cancel_external_ro', $where_data);
        //$query = "select * from ro_cancel_external_ro where ext_ro_id='$am_ro_id' " ;
        //$result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function get_user_name($user_id)
    {
//        $query = "select user_name from ro_user where user_id ='$user_id'";
//        $res = $this->db->query($query);
//        $result = $res->result("array");

        $whereData = array('user_id' => $user_id);
        $result = $this->db->select('user_name')->get_where('ro_user',$whereData);

        return $result->result("array");
    }

    public function get_campaigns_approved($profile_id, $user_id, $is_test_user)
    {

        //Code changed for getting ROs : Nitish 30/06/2015
        $users_list = $this->get_all_reporting_users($profile_id, $user_id);
        $logged_in = $this->session->userdata("logged_in_user");
        $regionArray = $this->getLoginUserRegions($logged_in[0]['user_id']);
        $region_id = $regionArray[0]['region_id'];


        if ($is_test_user != 2) {
            $query = "SELECT am.* , r.ro_approval_status, ru.reporting_manager_id, ru.user_name
                                FROM ro_am_external_ro AS am
                                INNER JOIN ro_amount AS r ON am.internal_ro = r.internal_ro_number
                                INNER JOIN ro_user AS ru ON ru.user_id = am.user_id
                                WHERE r.ro_approval_status =1
                                AND test_user_creation =$is_test_user
                                and am.user_id IN ($users_list)";
        } else {
            $query = "SELECT am.*, r.ro_approval_status,ru.reporting_manager_id, ru.user_name
                                FROM ro_am_external_ro AS am
                                INNER JOIN ro_amount AS r ON am.internal_ro = r.internal_ro_number
                                INNER JOIN ro_user AS ru ON ru.user_id = am.user_id
                                WHERE r.ro_approval_status = 1 and test_user_creation !=1
                                and am.user_id IN ($users_list)
                                and am.id not in (select ro_id from ro_linked_advance_ro)";
        }

        if ($profile_id == 11 || $profile_id == 12) {
            $query .= "and region_id = $region_id ORDER BY r.approval_timestamp DESC";
        } else {
            $query .= "ORDER BY r.approval_timestamp DESC";
        }

        /*if($is_test_user != 2) {
                if($profile_id == 10 || $profile_id == 1|| $profile_id == 2){
                    //showing all ROs for NH BH and COO
                    $query = "SELECT am. * , r.ro_approval_status,ru.reporting_manager_id, ru.user_name
                                FROM ro_am_external_ro AS am
                                INNER JOIN ro_amount AS r ON am.internal_ro = r.internal_ro_number
                                INNER JOIN ro_user AS ru ON ru.user_id = am.user_id
                                WHERE r.ro_approval_status =1
                                AND test_user_creation =$is_test_user
                                ORDER BY r.approval_timestamp DESC ";

		        }
                else{
                    $query = "SELECT am.* , r.ro_approval_status, ru.reporting_manager_id, ru.user_name
                                FROM ro_am_external_ro AS am
                                INNER JOIN ro_amount AS r ON am.internal_ro = r.internal_ro_number
                                INNER JOIN ro_user AS ru ON ru.user_id = am.user_id
                                WHERE r.ro_approval_status =1
                                AND test_user_creation =$is_test_user
                                AND (ru.reporting_manager_id =$user_id OR am.user_id =$user_id)
                                ORDER BY r.approval_timestamp DESC";
                }
            }else {
                if($profile_id == 10 || $profile_id == 1|| $profile_id == 2){
                    //showing all ROs for NH BH and COO
                    $query = "SELECT am.*, r.ro_approval_status, ru.reporting_manager_id, ru.user_name
                                FROM ro_am_external_ro AS am
                                INNER JOIN ro_amount AS r ON am.internal_ro = r.internal_ro_number
                                INNER JOIN ro_user AS ru ON ru.user_id = am.user_id
                                WHERE r.ro_approval_status = 1 and test_user_creation !=1
                                and am.id not in (select ro_id from ro_linked_advance_ro)
                                ORDER BY r.approval_timestamp DESC";

                }
                else{
                    $query = "SELECT am.*, r.ro_approval_status,ru.reporting_manager_id, ru.user_name
                                FROM ro_am_external_ro AS am
                                INNER JOIN ro_amount AS r ON am.internal_ro = r.internal_ro_number
                                INNER JOIN ro_user AS ru ON ru.user_id = am.user_id
                                WHERE r.ro_approval_status = 1 and test_user_creation !=1
                                AND (ru.reporting_manager_id =$user_id OR am.user_id =$user_id)
                                and am.id not in (select ro_id from ro_linked_advance_ro)
                                ORDER BY r.approval_timestamp DESC";
                }
            }*/


        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            $ro_list = array();
            $i = 0;
            foreach ($result as $val) {
                array_push($ro_list, $val);
                $brand_name = $this->get_brand_names($val['brand']);

                //$is_cancelled = $this->is_cancelled($val['id']);
                //@mani:get status detail from newly created table
                $ro_status_detail = $this->data_for_ro_id_in_ro_status($val['id']);
                $ro_status = $this->convert_status_into_format($ro_status_detail[0]['ro_status']);
                $ro_list[$i]['ro_status_entry'] = $ro_status;
                /*if(count($is_cancelled) > 0 ) {
					if($is_cancelled[0]['cancel_ro_by_admin'] == 1) {
						if(strtotime($is_cancelled[0]['date_of_cancel']) > strtotime(date('Y-m-d'))) {
                                                    $ro_list[$i]['is_cancelled']= '1';
                                                }else {
                                                    $ro_list[$i]['is_cancelled']= '3';
                                                }
					}else{
						$ro_list[$i]['is_cancelled']= '0';
					}
				}else {
					$ro_list[$i]['is_cancelled']= '2';
				} */
                //array_push($ro_list,$brand_name);
                $where_data = array(
                    'ext_ro_id' => $val['id'],
                    'cancel_type' => 'ro_approval'
                );

                $schedule_approval = $this->is_cancel_request_sent_by_am($where_data);

                //$submitted_by = $this->get_user_name($val['user_id']);
                $approved_by = $this->get_user_name($schedule_approval[0]['user_id']);

                $ro_list[$i]['submitted_by'] = $val['user_name'];
                $ro_list[$i]['approved_by'] = $approved_by[0]['user_name'];

                $ro_list[$i]['brand_name'] = $brand_name;
                $i++;
            }
            return $ro_list;
        }
        return array();
    }

    public function get_ro_file_path($id)
    {
//        $query = "select group_concat(file_location) as file_path from ro_am_external_ro_files where ro_id=$id ";

        $query = "select file_path from ro_am_external_ro where id=$id ";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            $file_path = $result[0]['file_path'];
            return $file_path;
        }
    }

    public function get_ro_details($id = null, $edit = null)
    {//echo $id;exit;
        $query = "select * from ro_am_external_ro where id=$id";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            $ro_list = array();
            $i = 0;
            foreach ($result as $val) {
                array_push($ro_list, $val);
                $brand_name = $this->get_brand_names($val['brand']);
                $region_name = $this->get_region_name($val['region_id']);
                //array_push($ro_list,$brand_name);
                $ro_list[$i]['brand_name'] = $brand_name;
                $ro_list[$i]['region_name'] = $region_name[0]['region_name'];
                $agency_contact_details = $this->get_agency_contact($val['agency_contact_id']);
                $client_contact_details = $this->get_client_contact($val['client_contact_id']);
                $ro_list[$i]['agency_contact_name'] = $agency_contact_details[0]['agency_contact_name'];
                $ro_list[$i]['client_contact_name'] = $client_contact_details[0]['client_contact_name'];

                $ro_list[$i]['mailList'] = $val['order_history_mail_list'];

                $i++;
            }//echo '<pre>';print_r($ro_list);exit;
            return $ro_list;
        }
        return array();
    }

    public function get_region_name($region_id)
    {
        $query = "select * from ro_master_geo_regions where id ='$region_id'";
        $res = $this->db->query($query);
        $result = $res->result("array");
        return $result;
    }

    public function get_agency_contact($contact_id)
    {
        $query = "select * from ro_agency_contact where id ='$contact_id'";
        $res = $this->db->query($query);
        $result = $res->result("array");
        return $result;
    }

    public function get_client_contact($contact_id)
    {
        $query = "select * from ro_client_contact where id ='$contact_id'";
        $res = $this->db->query($query);
        $result = $res->result("array");
        return $result;
    }

    public function get_all_contents_for_ro($internal_ro)
    {
        $query = "select distinct caption_name from sv_advertiser_campaign where internal_ro_number= '$internal_ro' ";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            return $result;
        }
    }

    public function get_all_brands_for_ro($internal_ro)
    {
        $query = "select distinct brand_id,brand_new from sv_advertiser_campaign where internal_ro_number= '$internal_ro' ";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            return $result;
        }
    }

    public function get_markets_for_content($content, $internal_ro)
    {
        $query = "select distinct sac.market_id,rmp.market,rmp.spot_price,rmp.banner_price,rmp.spot_fct,rmp.banner_fct,amex.camp_start_date,amex.camp_end_date from sv_advertiser_campaign as sac
                    inner join ro_am_external_ro as amex on sac.internal_ro_number = amex.internal_ro
                    inner join sv_sw_market as mkt on sac.market_id = mkt.id
                    inner join ro_market_price as rmp on mkt.sw_market_name = rmp.market
                    where sac.internal_ro_number= '$internal_ro'
                    and sac.caption_name = '$content' and rmp.ro_id = amex.id ";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            return $result;
        }
    }

    public function get_markets_for_brand($brand, $internal_ro)
    {
        $query = "select distinct sac.market_id,rmp.market,rmp.spot_price,rmp.banner_price,rmp.spot_fct,rmp.banner_fct,amex.camp_start_date,amex.camp_end_date from sv_advertiser_campaign as sac
                    inner join ro_am_external_ro as amex on sac.internal_ro_number = amex.internal_ro
                    inner join sv_sw_market as mkt on sac.market_id = mkt.id
                    inner join ro_market_price as rmp on mkt.sw_market_name = rmp.market
                    where sac.internal_ro_number= '$internal_ro'
                    and sac.brand_id = '$brand' and rmp.ro_id = amex.id ";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            return $result;
        }
    }

    public function get_non_fct_ro_details($id = null, $edit = null)
    {//echo $id;exit;
        $query = "select * from ro_non_fct_ro where id=$id";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            return $result;
        }
    }

    public function get_all_am()
    {
        $query = "select * from ro_user where profile_id=6 order by user_name";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }


    // report generation

    public function edit_external_ro()
    {
        $id = $this->input->post('hid_id');
        $industry = $this->input->post('txt_industry');
        $user_id = $this->input->post('sel_am');
        $vertical = $this->input->post('txt_vertical');
        $region = $this->input->post('txt_region');
        $category = $this->input->post('txt_category');
        $gross = $this->input->post('txt_gross');
        $agency_com = $this->input->post('txt_agency_com');
        $net_agency_com = $this->input->post('txt_net_agency_com');
        $spcl_inst = $this->input->post('txt_spcl_inst');

        $query = "update ro_am_external_ro set industry='$industry', user_id='$user_id', vertical='$vertical', region='$region', category='$category', gross='$gross',  agency_com='$agency_com', net_agency_com='$net_agency_com', spcl_inst='$spcl_inst' where id=$id";
        $res = $this->db->query($query);
        log_message('info', 'USER_INFO --- in edit_external_ro function and query is --' . $query);
        //update ro amount relative to external ros
        return;
    }

    public function am_edit_external_ro()
    {
        $id = $this->input->post('hid_id');
        /*$industry 		= $this->input->post('txt_industry');
		$user_id 			= $this->input->post('sel_am');
		$vertical 			= $this->input->post('txt_vertical');
		$region 			= $this->input->post('txt_region');
		$category 			= $this->input->post('txt_category');*/
        $prev_cust_ro = $this->input->post('hid_prev_cust_ro');
        $cust_ro = trim($this->input->post('txt_ext_ro'));
        $ro_date = $this->input->post('txt_ro_date');
        $user_id = $this->input->post('hid_user_id');
        $account_manager_id = $this->input->post('sel_am');
        $agency = $this->input->post('txt_agency');
        $agency_contact = $this->input->post('sel_agency_contact_name');
        $client = $this->input->post('txt_client');
        $client_contact = $this->input->post('sel_client_contact_name');
        if ($client_contact == 'new') {
            $client_contact = 0;
        }
        $camp_start_date = $this->input->post('txt_camp_start_date');
        $camp_end_date = $this->input->post('txt_camp_end_date');
        //$market 			= $this->input->post('hid_market');
        // taking markets excluding 0 amount
        $market = '';
        foreach ($_POST['markets'] as $market_name => $market_ro_amount) {
            if ($market_ro_amount['spot'] == 0 && $market_ro_amount['banner'] == 0) {
                continue;
            }
            if ($market == '') {
                $market = $market_name;
                $market = str_replace("_", " ", $market);
            } else {
                $market .= ',' . $market_name;
                $market = str_replace("_", " ", $market);
            }
        }
        // end taking markets
        $gross = $this->input->post('txt_gross');
        $agency_com = $this->input->post('txt_agency_com');
        $net_agency_com = $this->input->post('txt_net_agency_com');
        $spcl_inst = $this->input->post('txt_spcl_inst');
        $make_good_type = $this->input->post('rd_make_good');

        // running number added
        //$running_no = $this->get_running_no_of_ext_ro($id,$ro_date);
        $ro_details_value = $this->ro_detail_for_ro_id($id);
        $running_no = $ro_details_value[0]['financial_year_running_no'];
        $db_running_no = sprintf('%04d', $running_no);

        //$financial_year = $this->get_financial_year_for_ro_date($ro_date);
        //$db_financial_year = $this->get_financial_year_for_ro_date_v1($ro_date);
        $db_financial_year = $ro_details_value[0]['financial_year'];
        $financial_year = substr(trim($ro_details_value[0]['financial_year']), -2);
        $running_no = $financial_year . '-' . $db_running_no;
        // end

        //$internal_ro_no		= $this->generate_internal_ro_number_v1($client, $agency, $cust_ro,$camp_start_date,$running_no);
        $internal_ro_no = $ro_details_value[0]['internal_ro'];

        $campaign_created = $this->input->post('hid_campaign_created');
        $chk_cust_ro_exist_qry = "select * from ro_am_external_ro where cust_ro='$cust_ro'";
        $res_chk_cust_ro_exist_qry = $this->db->query($chk_cust_ro_exist_qry);


        /* @@@@@@@@@@@@@@@@@@@@@@@@@@@ Inserting mail list ------------------*/

        $mailList = implode(",", $this->input->post('Order_history_recipient_ids'));
        $regionId = $this->input->post("regionSelectBox");

        if ($res_chk_cust_ro_exist_qry->num_rows() > 0 && $prev_cust_ro != $cust_ro) {
            return 'exists';
        } else {
            if ($campaign_created == 1) {
                $query = "update ro_am_external_ro set agency_contact_id='$agency_contact',client_contact_id='$client_contact',user_id='$account_manager_id', order_history_mail_list='$mailList', region_id ='$regionId' where id=$id";
            } else {
                $query = "update ro_am_external_ro set cust_ro = '$cust_ro', internal_ro='$internal_ro_no', user_id='$account_manager_id', ro_date='$ro_date', camp_start_date='$camp_start_date',camp_end_date='$camp_end_date', market='$market', gross='$gross', agency_com='$agency_com', net_agency_com='$net_agency_com',spcl_inst='$spcl_inst', make_good_type='$make_good_type', agency_contact_id='$agency_contact',client_contact_id='$client_contact',financial_year_running_no='$db_running_no',financial_year='$db_financial_year', order_history_mail_list='$mailList', region_id ='$regionId' where id=$id";
            }
            $res = $this->db->query($query);
            log_message('info', 'USER_INFO -- am_edit_external_ro function and query is -' . $query);
            //change by mani : updating ro amount in ro_amount
            $ro_amount_data = array(
                'customer_ro_number' => $cust_ro,
                'internal_ro_number' => $internal_ro_no,
                'ro_amount' => $gross,
                'agency_commission_amount' => $agency_com,
                'timestamp' => date("Y-m-d H:i:s"),
                //'approval_timestamp'	=>	date("Y-m-d H:i:s"),
                'approval_user_id' => $user_id
            );
            $this->update_ro_amount($ro_amount_data, $internal_ro_no);
            //$this->update_ro_amount_v1($ro_amount_data,$internal_ro_no);
            // add individual market amount
            $this->update_ro_amount_for_each_market($id);
            // end

            //update ro_status
            $this->update_ro_status($id, 'RO_Created');
            return 1;
        }

    }

    public function ro_detail_for_ro_id($ro_id)
    {
        $query = "select * from ro_am_external_ro where id='$ro_id' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function update_ro_amount($ro_amount_data, $internal_ro_no)
    {
        $this->db->where('internal_ro_number', $internal_ro_no);
        $this->db->update('ro_amount', $ro_amount_data);
    }

    public function update_ro_amount_for_each_market($ro_id)
    {

        $this->db->delete('ro_market_price', array('ro_id' => $ro_id));

        /*foreach($_POST['markets'] as $market=>$market_ro_amount){
			$market = str_replace("_"," ",$market);
			if($market_ro_amount['spot'] == 0 && $market_ro_amount['banner'] == 0){continue;}

            $data = array(
                'ro_id'=>$ro_id,
                'market'=>$market,
                'price'=> ($market_ro_amount['spot'] + $market_ro_amount['banner']),
                'spot_price'=> $market_ro_amount['spot'],
                'spot_fct'  => $market_ro_amount['spot_FCT'],
                'banner_price'=>$market_ro_amount['banner'],
                'banner_fct'  => $market_ro_amount['banner_FCT']
            ) ;
            $this->db->insert('ro_market_price', $data );
		}*/

        $marketArray['price'] = $_POST['markets'];
        $marketArray['fct'] = $_POST['FCT'];
        $marketArray['rate'] = $_POST['rate'];

        foreach ($marketArray['price'] as $marketName => $market_ro_amount) {
            $market = $marketName;
            $marketName = str_replace("_", " ", $marketName);
            if ($market_ro_amount['spot'] == 0 && $market_ro_amount['banner'] == 0) {
                continue;
            }

            $data = array(
                'ro_id' => $ro_id,
                'market' => $marketName,
                'price' => ($market_ro_amount['price']['spot'] + $market_ro_amount['price']['banner']),

                'spot_price' => $market_ro_amount['spot'],
                'spot_fct' => $marketArray['fct'][$market]['spot_FCT'],
                'spot_rate' => $marketArray['rate'][$market]['spot_rate'],

                'banner_price' => $market_ro_amount['banner'],
                'banner_fct' => $marketArray['fct'][$market]['banner_FCT'],
                'banner_rate' => $marketArray['rate'][$market]['banner_rate'],

                'fixed_spot_price' => $market_ro_amount['spot'],
                'fixed_spot_fct' => $marketArray['fct'][$market]['spot_FCT'],

                'fixed_banner_price' => $market_ro_amount['banner'],
                'fixed_banner_fct' => $marketArray['fct'][$market]['banner_FCT']

            );
            $this->db->insert('ro_market_price', $data);
        }

    }

    public function update_ro_amount_v1($ro_amount_data, $internal_ro_no)
    {
        $res = $this->db->get_where('ro_amount', array('internal_ro_number' => $internal_ro_no));
        //$res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $this->db->where('internal_ro_number', $internal_ro_no);
            $this->db->update('ro_amount', $ro_amount_data);
        } else {
            /*$user_data = array(
								'customer_ro_number' => $ro_amount_data['customer_ro_number'],
								'internal_ro_number' => $ro_amount_data['internal_ro_number'],
								'ro_amount' => $ro_amount_data['ro_amount'],
								'agency_commission_amount' => $ro_amount_data['agency_commission_amount'],
								'agency_rebate_on' => ,
								'timestamp' => $ro_amount_data['timestamp'],
								'approval_timestamp' => $ro_amount_data['approval_timestamp'],
								'approval_user_id' => $ro_amount_data['approval_user_id']
						  ); */
            $ro_amount_data['approval_timestamp'] = date("Y-m-d H:i:s");
            $this->db->insert('ro_amount', $ro_amount_data);
        }


    }

    public function cancel_ro()
    {
        $ro_id = $this->input->post('hid_id');
        $user_id = $this->input->post('hid_user_id');
        $date_of_submission = date('Y-m-d');
        $date_of_cancel = $this->input->post('txt_cancel_date');
        $reason = $this->input->post('txt_reason');
        $invoice_inst = $this->input->post('txt_inv_inst');
        $invoice_amount = $this->input->post('txt_inv_amnt');

        $user_data = array(
            'ext_ro_id' => $ro_id,
            'cancel_type' => 'cancel_ro',
            'user_id' => $user_id,
            'date_of_submission' => $date_of_submission,
            'date_of_cancel' => $date_of_cancel,
            'reason' => $reason,
            'invoice_instruction' => $invoice_inst,
            'ro_amount' => $invoice_amount,
            'cancel_ro_by_admin' => 0
        );

        //$query = "INSERT INTO `ro_cancel_external_ro` (`id`, `ext_ro_id`, `user_id`, `date_of_submission`, `date_of_cancel`, `reason`, `invoice_instruction`) VALUES (NULL, '$ro_id', '$user_id', '$date_of_submission', '$date_of_cancel', '$reason', '$invoice_inst')";
        //$res = $this->db->query($query);
        $where_data = array(
            'ext_ro_id' => $ro_id,
            'cancel_type' => 'cancel_ro',
        );
        $cancelled_data = $this->am_model->get_cancelled_data($where_data);
        if (count($cancelled_data) > 0) {
            $this->db->where($where_data);
            $this->db->update('ro_cancel_external_ro', $user_data);

            return $cancelled_data[0]['id'];
        } else {
            $this->db->insert('ro_cancel_external_ro', $user_data);

            return $this->db->insert_id();
        }
    }

    public function cancel_ro_mailto_list()
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        $query = "select * from ro_user where profile_id IN(1,2,3,7) and is_test_user='$is_test_user' ";
        //$query = "select * from ro_user where user_id IN(15)";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function is_cancelled($ro_id)
    {
        $query = "select * from ro_cancel_external_ro where ext_ro_id = '$ro_id'";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_all_invoice_for_ro($user_id, $ext_ro)
    {
        $query = "select * from  ro_am_invoice_collection where ext_ro = '$ext_ro'";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function invoice_collection()
    {
        $user_id = $this->input->post('hid_user_id');
        $ext_ro = $this->input->post('hid_ext_id');
        $invoice_no = $this->input->post('txt_inv_no');
        $chk_complete = $this->input->post('chk_complete');
        if ($chk_complete != 1) {
            $chk_complete = 0;
        }
        $collection_date = $this->input->post('txt_collection_date');
        $cheque_no = $this->input->post('txt_cheque_no');
        $cheque_date = $this->input->post('txt_cheque_date');
        $amnt_collected = $this->input->post('txt_amnt_collected');
        $tds = $this->input->post('txt_tds');
        $comment = $this->input->post('txt_comment');

        $query = "INSERT INTO `ro_am_invoice_collection` (`id`, `ext_ro`, `user_id`, `invoice_no`, `chk_complete`, `collection_date`, `cheque_no`, `cheque_date`, `amnt_collected`, `tds`, `comment`) VALUES (NULL, '$ext_ro ', '$user_id', '$invoice_no', '$chk_complete', '$collection_date', '$cheque_no', '$cheque_date ', '$amnt_collected', '$tds', '$comment');";
        $res = $this->db->query($query);
        return 1;
    }

    public function chk_to_add_invoice($user_id, $ext_ro)
    {
        $query = "select * from  ro_am_invoice_collection where ext_ro = '$ext_ro' and chk_complete='1'";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return '0';
        }
        return '1';
    }

    function get_invoice_reports($user_id = NULL, $is_test_user)
    {
        $page = $this->input->post('page');
        $per_page = $this->input->post('rp');

        if ($page > 1) {
            $offset = ($page - 1) * $per_page;
        } else {
            $offset = 0;
        }

        $record = $this->_get_invoice_records($page, $offset, $per_page, $user_id, $is_test_user);//print_r($record);exit;
        //$total_row = count($record);//echo $total_row;exit;
        $total_row = $this->_count_get_invoice_records($user_id, $is_test_user);
        return $this->build_ro_record_json($record, $total_row, $page, $is_test_user);
    }

    function _get_invoice_records($page = 1, $offset = NULL, $limit = NULL, $user_id = NULL, $is_test_user)
    {
        $start_date = date('Y-m-d', strtotime($this->input->post('from')));
        $end_date = date('Y-m-d', strtotime($this->input->post('to')));
        $report_type = $this->input->post('sel_view_type'); // 1 for colledtion date and 2 for ro date
        //$query = "select ext_ro,user_id,chk_complete,sum(amnt_collected) as total_amnt_collected,sum(tds) as total_tds from ro_am_invoice_collection where collection_date >= '$start_date' and collection_date <= '$end_date' group by `ext_ro`";
        if ($is_test_user != 2) {
            if ($user_id != NULL) {
                if ($report_type == 1) {
                    $query = "select * from ro_am_invoice_collection where ro_id in (select id from ro_am_external_ro where test_user_creation ='$is_test_user' and (( camp_start_date >= '$start_date' and camp_end_date <= '$end_date' )
or
( camp_start_date <= '$start_date' and camp_end_date between '$start_date' and '$end_date')
or
( camp_start_date between '$start_date' and '$end_date' and camp_end_date >= '$end_date' )
or
( camp_start_date <= '$start_date' and camp_end_date >= '$end_date' )) ) and user_id=$user_id";
                }
                if ($report_type == 2) {
                    $query = "select aic.* from ro_am_invoice_collection as aic,ro_am_external_ro as aer where aic.ro_id=aer.id and aic.collection_date >= '$start_date' and aic.collection_date <= '$end_date' and aic.user_id=$user_id and aer.test_user_creation='$is_test_user'";
                }
            } else {
                if ($report_type == 1) {
                    $query = "select * from ro_am_invoice_collection where ro_id in (select id from ro_am_external_ro where test_user_creation ='$is_test_user' and (( camp_start_date >= '$start_date' and camp_end_date <= '$end_date' )
    or
    ( camp_start_date <= '$start_date' and camp_end_date between '$start_date' and '$end_date')
    or
    ( camp_start_date between '$start_date' and '$end_date' and camp_end_date >= '$end_date' )
    or
    ( camp_start_date <= '$start_date' and camp_end_date >= '$end_date' )) )";
                }
                if ($report_type == 2) {
                    //$query = "select * from ro_am_invoice_collection where collection_date >= '$start_date' and collection_date <= '$end_date'";
                    $query = "select aic.* from ro_am_invoice_collection as aic,ro_am_external_ro as aer where aic.ro_id=aer.id and aic.collection_date >= '$start_date' and aic.collection_date <= '$end_date' and aer.test_user_creation='$is_test_user'";
                }
            }
        } else {
            if ($user_id != NULL) {
                if ($report_type == 1) {
                    $query = "select * from ro_am_invoice_collection where ro_id in (select id from ro_am_external_ro where id not in (select ro_id from ro_linked_advance_ro) and (( camp_start_date >= '$start_date' and camp_end_date <= '$end_date' )
or
( camp_start_date <= '$start_date' and camp_end_date between '$start_date' and '$end_date')
or
( camp_start_date between '$start_date' and '$end_date' and camp_end_date >= '$end_date' )
or
( camp_start_date <= '$start_date' and camp_end_date >= '$end_date' )) ) and user_id=$user_id";
                }
                if ($report_type == 2) {
                    $query = "select aic.* from ro_am_invoice_collection as aic,ro_am_external_ro as aer where aic.ro_id=aer.id and aic.collection_date >= '$start_date' and aic.collection_date <= '$end_date' and aic.user_id=$user_id and aer.id not in (select ro_id from ro_linked_advance_ro)";
                }
            } else {
                if ($report_type == 1) {
                    $query = "select * from ro_am_invoice_collection where ro_id in (select id from ro_am_external_ro where id not in (select ro_id from ro_linked_advance_ro) and (( camp_start_date >= '$start_date' and camp_end_date <= '$end_date' )
    or
    ( camp_start_date <= '$start_date' and camp_end_date between '$start_date' and '$end_date')
    or
    ( camp_start_date between '$start_date' and '$end_date' and camp_end_date >= '$end_date' )
    or
    ( camp_start_date <= '$start_date' and camp_end_date >= '$end_date' )) )";
                }
                if ($report_type == 2) {
                    //$query = "select * from ro_am_invoice_collection where collection_date >= '$start_date' and collection_date <= '$end_date'";
                    $query = "select aic.* from ro_am_invoice_collection as aic,ro_am_external_ro as aer where aic.ro_id=aer.id and aic.collection_date >= '$start_date' and aic.collection_date <= '$end_date' and aer.id not in (select ro_id from ro_linked_advance_ro)";
                }
            }
        }

        if (isset($limit) && isset($offset)) {
            $query = $query . " limit " . $offset . "," . $limit;
        }
        $res = $this->db->query($query);
        return $res->result("array");

    }

    function _count_get_invoice_records($user_id = NULL, $is_test_user)
    {
        $start_date = date('Y-m-d', strtotime($this->input->post('from')));
        $end_date = date('Y-m-d', strtotime($this->input->post('to')));
        $report_type = $this->input->post('sel_view_type'); // 1 for colledtion date and 2 for ro date
        //$query = "select ext_ro,user_id,chk_complete,sum(amnt_collected) as total_amnt_collected,sum(tds) as total_tds from ro_am_invoice_collection where collection_date >= '$start_date' and collection_date <= '$end_date' group by `ext_ro`";
        if ($is_test_user != 2) {
            if ($user_id != NULL) {
                if ($report_type == 1) {
                    $query = "select * from ro_am_invoice_collection where ro_id in (select id from ro_am_external_ro where test_user_creation ='$is_test_user' and (( camp_start_date >= '$start_date' and camp_end_date <= '$end_date' )
or
( camp_start_date <= '$start_date' and camp_end_date between '$start_date' and '$end_date')
or
( camp_start_date between '$start_date' and '$end_date' and camp_end_date >= '$end_date' )
or
( camp_start_date <= '$start_date' and camp_end_date >= '$end_date' )) ) and user_id=$user_id";
                }
                if ($report_type == 2) {
                    //$query = "select * from ro_am_invoice_collection where collection_date >= '$start_date' and collection_date <= '$end_date' and user_id=$user_id";
                    $query = "select aic.* from ro_am_invoice_collection as aic,ro_am_external_ro as aer where aic.ro_id=aer.id and aic.collection_date >= '$start_date' and aic.collection_date <= '$end_date' and aic.user_id=$user_id and aer.test_user_creation='$is_test_user'";
                }
            } else {
                if ($report_type == 1) {
                    $query = "select * from ro_am_invoice_collection where ro_id in (select id from ro_am_external_ro where test_user_creation ='$is_test_user' and (( camp_start_date >= '$start_date' and camp_end_date <= '$end_date' )
    or
    ( camp_start_date <= '$start_date' and camp_end_date between '$start_date' and '$end_date')
    or
    ( camp_start_date between '$start_date' and '$end_date' and camp_end_date >= '$end_date' )
    or
    ( camp_start_date <= '$start_date' and camp_end_date >= '$end_date' )) )";
                }
                if ($report_type == 2) {
                    //$query = "select * from ro_am_invoice_collection where collection_date >= '$start_date' and collection_date <= '$end_date'";
                    $query = "select aic.* from ro_am_invoice_collection as aic,ro_am_external_ro as aer where aic.ro_id=aer.id and aic.collection_date >= '$start_date' and aic.collection_date <= '$end_date' and aer.test_user_creation='$is_test_user'";
                }
            }
        } else {
            if ($user_id != NULL) {
                if ($report_type == 1) {
                    $query = "select * from ro_am_invoice_collection where ro_id in (select id from ro_am_external_ro where id not in (select ro_id from ro_linked_advance_ro) and (( camp_start_date >= '$start_date' and camp_end_date <= '$end_date' )
or
( camp_start_date <= '$start_date' and camp_end_date between '$start_date' and '$end_date')
or
( camp_start_date between '$start_date' and '$end_date' and camp_end_date >= '$end_date' )
or
( camp_start_date <= '$start_date' and camp_end_date >= '$end_date' )) ) and user_id=$user_id";
                }
                if ($report_type == 2) {
                    $query = "select aic.* from ro_am_invoice_collection as aic,ro_am_external_ro as aer where aic.ro_id=aer.id and aic.collection_date >= '$start_date' and aic.collection_date <= '$end_date' and aic.user_id=$user_id and aer.id not in (select ro_id from ro_linked_advance_ro)";
                }
            } else {
                if ($report_type == 1) {
                    $query = "select * from ro_am_invoice_collection where ro_id in (select id from ro_am_external_ro where id not in (select ro_id from ro_linked_advance_ro) and (( camp_start_date >= '$start_date' and camp_end_date <= '$end_date' )
    or
    ( camp_start_date <= '$start_date' and camp_end_date between '$start_date' and '$end_date')
    or
    ( camp_start_date between '$start_date' and '$end_date' and camp_end_date >= '$end_date' )
    or
    ( camp_start_date <= '$start_date' and camp_end_date >= '$end_date' )) )";
                }
                if ($report_type == 2) {
                    //$query = "select * from ro_am_invoice_collection where collection_date >= '$start_date' and collection_date <= '$end_date'";
                    $query = "select aic.* from ro_am_invoice_collection as aic,ro_am_external_ro as aer where aic.ro_id=aer.id and aic.collection_date >= '$start_date' and aic.collection_date <= '$end_date' and aer.id not in (select ro_id from ro_linked_advance_ro)";
                }
            }
        }

        $res = $this->db->query($query);
        $result = $res->result("array");
        return (count($result));

    }

    function build_ro_record_json($records, $total_row, $page, $is_test_user)
    {
        $json = Array();
        $items = Array();
        foreach ($records as $record) {
            // get ro details
            $where = '';
            if ($is_test_user != 2) {
                $where = $where . " and test_user_creation='$is_test_user' ";
            } else {
                $where = $where . " and id not in (select ro_id from ro_linked_advance_ro) ";
            }
            $ext_ro_details_qry = "select * from ro_am_external_ro where cust_ro='" . $record['ext_ro'] . "' ";
            $ext_ro_details_qry = $ext_ro_details_qry . $where;

            $res_ext_ro_details_qry = $this->db->query($ext_ro_details_qry);
            $res_ext_ro_details = $res_ext_ro_details_qry->result("array");
            // end

            // get user name
            $user_id_qry = "select user_name from ro_user where user_id='" . $record['user_id'] . "' and is_test_user='$is_test_user' ";
            $res_user_id_qry = $this->db->query($user_id_qry);
            $res_user_id = $res_user_id_qry->result("array");
            // end

            //get approved by user
            $where_data = array(
                'ext_ro_id' => $res_ext_ro_details[0]['id'],
                'cancel_type' => 'ro_approval'
            );

            $schedule_approval = $this->is_cancel_request_sent_by_am($where_data);
            $approved_by = $this->get_user_name($schedule_approval[0]['user_id']);
            //end

            // check full payment done or not
            $chk_full_payment_qry = "select * from ro_am_invoice_collection where ro_id='" . $record['ro_id'] . "' and chk_complete=1";
            $res_chk_full_payment_qry = $this->db->query($chk_full_payment_qry);
            if (count($res_chk_full_payment_qry > 0)) {
                $complete_payment = 'Yes';
            } else {
                $complete_payment = 'No';
            }
            $record['chk_complete'] = $complete_payment;
            // end

            $items[] = array(
                'id' => $record['ext_ro'],
                'cell' => array(
                    $res_user_id[0]['user_name'],
                    $approved_by,
                    $record['invoice_no'],
                    $record['ext_ro'],
                    $res_ext_ro_details[0]['internal_ro'],
                    date('d-M-Y', strtotime($res_ext_ro_details[0]['camp_start_date'])),
                    date('d-M-Y', strtotime($res_ext_ro_details[0]['camp_end_date'])),
                    $res_ext_ro_details[0]['client'],
                    $res_ext_ro_details[0]['agency'],
                    /*$res_ext_ro_details[0]['gross'],*/
                    $record['amnt_collected'],
                    $record['tds'],
                    $record['cheque_no'],
                    $record['cheque_date'],
                    $record['collection_date']
                    /*,
					$record['total_amnt_collected'],
					$record['total_tds'],
					$record['chk_complete']*/
                )
            );
        }
        //echo '<pre>';print_r($items);exit;
        $json['page'] = $page;
        $json['total'] = $total_row;
        $json['rows'] = $items;
        //print_r($json);exit;
        return json_encode($json);
    }

    public function download_invoice_report_csv($start_date, $end_date, $user_id = NULL, $report_type = NULL, $is_test_user)
    {
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));
        //$query = "select ext_ro,user_id,sum(amnt_collected) as total_amnt_collected,sum(tds) as total_tds from ro_am_invoice_collection where collection_date >= '$start_date' and collection_date <= '$end_date' group by `ext_ro`";
        if ($is_test_user != 2) {
            if ($user_id != NULL) {
                if ($report_type == 1) {
                    $query = "select * from ro_am_invoice_collection where ro_id in (select id from ro_am_external_ro where test_user_creation ='$is_test_user' and (( camp_start_date >= '$start_date' and camp_end_date <= '$end_date' )
or
( camp_start_date <= '$start_date' and camp_end_date between '$start_date' and '$end_date')
or
( camp_start_date between '$start_date' and '$end_date' and camp_end_date >= '$end_date' )
or
( camp_start_date <= '$start_date' and camp_end_date >= '$end_date' )) ) and user_id=$user_id";
                }
                if ($report_type == 2) {
                    //$query = "select * from ro_am_invoice_collection where collection_date >= '$start_date' and collection_date <= '$end_date' and user_id=$user_id";
                    $query = "select aic.* from ro_am_invoice_collection as aic,ro_am_external_ro as aer where aic.am_id=aer.id and aic.collection_date >= '$start_date' and aic.collection_date <= '$end_date' and aic.user_id=$user_id and aer.test_user_creation='$is_test_user'";
                }
            } else {
                if ($report_type == 1) {
                    $query = "select * from ro_am_invoice_collection where ro_id in (select id from ro_am_external_ro where test_user_creation ='$is_test_user' and  (( camp_start_date >= '$start_date' and camp_end_date <= '$end_date' )
    or
    ( camp_start_date <= '$start_date' and camp_end_date between '$start_date' and '$end_date')
    or
    ( camp_start_date between '$start_date' and '$end_date' and camp_end_date >= '$end_date' )
    or
    ( camp_start_date <= '$start_date' and camp_end_date >= '$end_date' )) )";
                }
                if ($report_type == 2) {
                    //$query = "select * from ro_am_invoice_collection where collection_date >= '$start_date' and collection_date <= '$end_date'";
                    $query = "select aic.* from ro_am_invoice_collection as aic,ro_am_external_ro as aer where aic.am_id=aer.id and aic.collection_date >= '$start_date' and aic.collection_date <= '$end_date' and aer.test_user_creation='$is_test_user'";
                }
            }
        } else {
            if ($user_id != NULL) {
                if ($report_type == 1) {
                    $query = "select * from ro_am_invoice_collection where ro_id in (select id from ro_am_external_ro where id not in (select ro_id from ro_linked_advance_ro) and (( camp_start_date >= '$start_date' and camp_end_date <= '$end_date' )
or
( camp_start_date <= '$start_date' and camp_end_date between '$start_date' and '$end_date')
or
( camp_start_date between '$start_date' and '$end_date' and camp_end_date >= '$end_date' )
or
( camp_start_date <= '$start_date' and camp_end_date >= '$end_date' )) ) and user_id=$user_id";
                }
                if ($report_type == 2) {
                    $query = "select aic.* from ro_am_invoice_collection as aic,ro_am_external_ro as aer where aic.am_id=aer.id and aic.collection_date >= '$start_date' and aic.collection_date <= '$end_date' and aic.user_id=$user_id and aer.id not in (select ro_id from ro_linked_advance_ro)";
                }
            } else {
                if ($report_type == 1) {
                    $query = "select * from ro_am_invoice_collection where ro_id in (select id from ro_am_external_ro where id not in (select ro_id from ro_linked_advance_ro) and (( camp_start_date >= '$start_date' and camp_end_date <= '$end_date' )
    or
    ( camp_start_date <= '$start_date' and camp_end_date between '$start_date' and '$end_date')
    or
    ( camp_start_date between '$start_date' and '$end_date' and camp_end_date >= '$end_date' )
    or
    ( camp_start_date <= '$start_date' and camp_end_date >= '$end_date' )) )";
                }
                if ($report_type == 2) {
                    //$query = "select * from ro_am_invoice_collection where collection_date >= '$start_date' and collection_date <= '$end_date'";
                    $query = "select aic.* from ro_am_invoice_collection as aic,ro_am_external_ro as aer where aic.am_id=aer.id and aic.collection_date >= '$start_date' and aic.collection_date <= '$end_date' and aer.id not in (select ro_id from ro_linked_advance_ro)";
                }
            }
        }

        $res = $this->db->query($query);
        $record = $res->result("array");
        return $this->build_ro_record_json($record, $total_row, $page, $is_test_user);
    }

    function get_agency_info($agency)
    {
        $query = "select * from  ro_agency_contact where agency_display_name='$agency' order by agency_contact_name";
        $res = $this->db->query($query);
        $data = $res->result("array");
        $opt = '<option value="new">New</option>';
        foreach ($data as $values) {
            $opt .= '<option value="' . $values['id'] . '">' . $values['agency_contact_name'] . '</option>';
        }
        return $opt;
    }

    function get_client_info($client_display_name)
    {
        $query = "select * from  ro_client_contact where client_display_name='$client_display_name' order by client_contact_name";
        $res = $this->db->query($query);
        $data = $res->result("array");
        $opt = '<option value="new">New</option>';
        foreach ($data as $values) {
            $opt .= '<option value="' . $values['id'] . '">' . $values['client_contact_name'] . '</option>';
        }
        return $opt;
    }

    function get_agency_contact_info($agency_contact_id)
    {
        $query = "select * from  ro_agency_contact where id='$agency_contact_id'";
        $res = $this->db->query($query);
        return $res->result("array");

    }

    function get_client_contact_info($client_contact_id)
    {
        $query = "select * from  ro_client_contact where id='$client_contact_id' order by client_contact_name";
        $res = $this->db->query($query);
        return $res->result("array");


    }

    function get_all_advertiser()
    {
        $query = "select * from sv_new_advertiser where active='0' order by advertiser";
        $res = $this->db->query($query);
        return $res->result("array");
    }

    function post_activate_client($id)
    {
        $query = "update sv_new_advertiser set active=1 where id='$id'";
        $res = $this->db->query($query);
        return;
    }

    function get_all_client_contact($client)
    {
        $query = "select * from ro_client_contact where client_name = '$client' order by client_contact_name";
        $res = $this->db->query($query);
        return $res->result("array");
    }

    function get_all_agency_contact($agency)
    {
        $query = "select * from ro_agency_contact where agency_name = '$agency' order by agency_contact_name";
        $res = $this->db->query($query);
        return $res->result("array");
    }

    public function verify_campaign_created_for_internal_ro($internal_ro)
    {
        $query = "select * from sv_advertiser_campaign where internal_ro_number='$internal_ro' and (campaign_status !='cancelled' or  campaign_status !='pending_approval')";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }

        return array();
    }

    public function is_cancel_request_sent($am_ro_id)
    {
        $query = "select * from ro_cancel_external_ro where ext_ro_id='$am_ro_id' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }

        return array();
    }

    public function ro_detail_for_external_ro($external_ro)
    {
        $query = "select * from ro_am_external_ro where cust_ro='$external_ro' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function ro_detail_for_internal_ro($internal_ro)
    {
        $whereData = array('internal_ro' => $internal_ro);
        $result = $this->db->get_where('ro_am_external_ro',$whereData);
        if ($result->num_rows() > 0) {
            $result = $result->result("array");
            $ro_list = array();
            $i = 0;
            foreach ($result as $val) {
                array_push($ro_list, $val);

                $where_data = array(
                    'ext_ro_id' => $val['id'],
                    'cancel_type' => 'ro_approval'
                );

                $schedule_approval = $this->is_cancel_request_sent_by_am($where_data);

                $submitted_by = $this->get_user_name($val['user_id']);
                $approved_by = $this->get_user_name($schedule_approval[0]['user_id']);

                $brand_name = $this->get_brand_names($val['brand']);

                $ro_list[$i]['submitted_by'] = $submitted_by[0]['user_name'];;
                $ro_list[$i]['approved_by'] = $approved_by[0]['user_name'];

                $ro_list[$i]['brand_name'] = $brand_name;
                $i++;
            }//echo '<pre>';print_r($ro_list);exit;
            return $ro_list;
        }
        return array();
    }

    public function get_ro_amount_data($internal_ro_number)
    {
        $data = array('internal_ro_number' => $internal_ro_number);
        $result = $this->db->get_where('ro_amount', $data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function update_ro_data_in_ro_ext($ro_id, $data)
    {
        $this->db->where('id', $ro_id);
        $this->db->update('ro_am_external_ro', $data);
        log_message('info', 'USER_INFO -- in function update_ro_data_in_ro_ext and query is - ' . $this->db->last_query());
    }

    public function update_external_ro_report_details($ro_amnt_data, $internal_ro_number)
    {
        $this->db->where('internal_ro_number', $internal_ro_number);
        $this->db->update('ro_external_ro_report_details', $ro_amnt_data);
    }

    public function insert_into_job_queue($user_data)
    {
        $this->db->insert('sv_job_queue', $user_data);
    }

    public function update_cancel_for_admin($ro_id, $user_data)
    {
        $data_avail_in_db = $this->is_cancel_request_sent_cancel_ro($ro_id);

        if (count($data_avail_in_db) > 0) {
            // override all the data
            //$data = array('cancel_ro_by_admin' => 1) ;
            $this->db->where(array('ext_ro_id' => $ro_id, 'cancel_type' => 'cancel_ro'));
            $this->db->update('ro_cancel_external_ro', $user_data);
            return $data_avail_in_db[0]['id'];
        } else {
            $this->db->insert('ro_cancel_external_ro', $user_data);
            return $this->db->insert_id();
        }
    }

    public function is_cancel_request_sent_cancel_ro($am_ro_id)
    {
        $query = "select * from ro_cancel_external_ro where ext_ro_id='$am_ro_id' and cancel_type='cancel_ro' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }

        return array();
    }

    public function get_ro_dates($ro_id)
    {
        $query = "select camp_start_date,camp_end_date from ro_am_external_ro where id='$ro_id'";
        $result = $this->db->query($query);
        return $result->result("array");
    }

    public function get_cancelled_markets_for_ro($ro_id)
    {
        $query = "select * from `ro_market_price_tmp` where am_ro_id = '$ro_id' and approved_type = 1 and is_cancelled = 1 ";
        $result = $this->db->query($query);
        return $result->result("array");
    }

    public function get_cancelled_markets_for_ro_v1($ro_id)
    {
        $query = "select * from `ro_market_price_tmp` rmpt "
            . "inner join ro_cancel_external_ro rcer on rcer.ext_ro_id = rmpt.am_ro_id and rcer.id = rmpt.cancel_id "
            . "where rmpt.am_ro_id = '$ro_id' and rmpt.approved_type = 1 and rmpt.is_cancelled = 1 and rcer.cancel_type = 'cancel_market' ";
        $result = $this->db->query($query);
        return $result->result("array");
    }

    public function get_cancel_requested_markets_for_ro($ro_id)
    {
        $query = "select * from `ro_market_price_tmp` where am_ro_id = '$ro_id' and approved_type = 0 and is_cancelled = 1 ";
        $result = $this->db->query($query);
        return $result->result("array");
    }

    public function get_cancel_requested_markets_for_ro_v1($ro_id)
    {
        $query = "select rmpt.* from `ro_market_price_tmp` rmpt "
            . "inner join ro_cancel_external_ro rcer on rcer.ext_ro_id = rmpt.am_ro_id "
            . "where rmpt.am_ro_id = '$ro_id' and rmpt.approved_type = 0 and rmpt.is_cancelled = 1 and rcer.cancel_type = 'cancel_market' ";
        $result = $this->db->query($query);
        return $result->result("array");
    }

    public function get_cancelled_markets($where_data)
    {
        $result = $this->db->get_where('ro_market_price_tmp', $where_data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function get_revised_ro_amount($where_data)
    {
        $result = $this->db->get_where('ro_cancel_external_ro', $where_data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function change_ro_status($am_ro_id)
    {
        $data_avail_in_db = $this->data_for_ro_id_in_ro_status($am_ro_id);
        $previous_status = $data_avail_in_db[0]['previous_status'];
        $user_data = array('ro_status' => $previous_status);
        $this->db->where('am_external_ro_id', $am_ro_id);
        $this->db->update('ro_status', $user_data);
    }

    public function get_ro_details_for_external_ro($external_ro)
    {
        $query = "select * from ro_am_external_ro where cust_ro='$external_ro' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function get_ro_details_for_internal_ro($internal_ro)
    {
        $query = "select * from ro_am_external_ro where internal_ro='$internal_ro' ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function get_am_email_for_created_ro($ro_id)
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $is_test_user = $logged_in[0]['is_test_user'];

        $query = "select u.user_email from ro_user as u,ro_am_external_ro as amr where amr.user_id=u.user_id and amr.id='$ro_id' and is_test_user='$is_test_user'  ";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function get_all_am_bh_coo()
    {
        $query = "select * from ro_user where profile_id in (1,2,6)";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function verify_ro_status($am_external_ro_id)
    {
        $query = "select * from ro_status where am_external_ro_id='$am_external_ro_id' and ro_status='approved'";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }

        return array();
    }

    public function ro_assigned_user_name($user_id)
    {
        $query = "select user_name from ro_user where user_id='$user_id'";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            $rs = $result->result("array");
            return $rs[0]['user_name'];
        }

        return array();
    }

    public function get_campaigns_from_adv_campaign($data)
    {
        $this->db->select('campaign_id');
        $result = $this->db->get_where('sv_advertiser_campaign', $data);
        return $result->result("array");
    }

    public function update_advertiser_screens_dates($campaign_ids = 0, $date_of_cancel = '')
    {
        if (!isset($campaign_ids) || empty($campaign_ids)) {
            $campaign_ids = 0;
        }
        $query = "update sv_advertiser_campaign_screens_dates set status='cancelled' where campaign_id in ($campaign_ids) and date >='$date_of_cancel'";
        $this->db->query($query);

        // $query = "update sv_advertiser_campaign set do_make_good='0' where campaign_id in ($campaign_ids) ";
        //$this->db->query($query);
    }

    public function update_all_advertiser_screens_dates($campaign_ids = 0)
    {
        if (!isset($campaign_ids) || empty($campaign_ids)) {
            $campaign_ids = 0;
        }
        $query = "update sv_advertiser_campaign_screens_dates set status='cancelled' where campaign_id in ($campaign_ids) ";
        $this->db->query($query);
    }

    public function update_campaign_status($campaign_ids = 0)
    {
        if (!isset($campaign_ids) || empty($campaign_ids)) {
            $campaign_ids = 0;
        }
        $query = "update sv_advertiser_campaign set campaign_status='cancelled' where campaign_id in ($campaign_ids) ";
        $this->db->query($query);
    }

    public function get_all_channels_scheduled($internal_ro_number)
    {
        $query = "select ac.campaign_id from sv_advertiser_campaign as ac where ac.internal_ro_number = '$internal_ro_number' and `is_make_good` = 0 and selected_region_id in (1,3) and visible_in_easyro = 1";
        $res = $this->db->query($query);
        $result = $res->result("array");
        $campaign_id = array();
        foreach ($result as $val) {
            array_push($campaign_id, $val['campaign_id']);
        }
        $campaign_ids = implode(",", $campaign_id);

        $channel_qry_spot = "SELECT (ac.ro_duration*SUM( impressions )) as total_spot_ad_seconds, sc.channel_id, c.channel_name, e.revenue_sharing FROM sv_advertiser_campaign_screens_dates AS sd, sv_screen AS sc, sv_tv_channel AS c, sv_advertiser_campaign as ac,sv_customer as e WHERE c.enterprise_id=e.customer_id and sd.campaign_id IN ($campaign_ids) and sd.screen_region_id = 1 AND  sd.screen_id = sc.screen_id AND ac.campaign_id=sd.campaign_id and c.tv_channel_id = sc.channel_id GROUP BY c.channel_name,ac.ro_duration,sd.screen_region_id";
        $channel_qry_spot_res = $this->db->query($channel_qry_spot);
        $spot_res = $channel_qry_spot_res->result("array");

        $channel_qry_bnr = "SELECT (ac.ro_duration*SUM( impressions )) as total_banner_ad_seconds, sc.channel_id, c.channel_name, e.revenue_sharing FROM sv_advertiser_campaign_screens_dates AS sd, sv_screen AS sc, sv_tv_channel AS c, sv_advertiser_campaign as ac,sv_customer as e WHERE c.enterprise_id=e.customer_id and sd.campaign_id IN ($campaign_ids) and sd.screen_region_id = 3 AND sd.screen_id = sc.screen_id AND ac.campaign_id=sd.campaign_id and c.tv_channel_id = sc.channel_id GROUP BY c.channel_name,ac.ro_duration,sd.screen_region_id";
        $channel_qry_bnr_res = $this->db->query($channel_qry_bnr);
        $bnr_res = $channel_qry_bnr_res->result("array");

        $channel_array = array();
        $i = 0;
        foreach ($spot_res as $spot_res_val) {
            $channel_array[$i]['channel_id'] = $spot_res_val['channel_id'];
            $channel_array[$i]['channel_name'] = $spot_res_val['channel_name'];
            $channel_array[$i]['revenue_sharing'] = $spot_res_val['revenue_sharing'];
            $channel_array[$i]['total_spot_ad_seconds'] = $spot_res_val['total_spot_ad_seconds'];
            foreach ($bnr_res as $bnr_res_val) {
                if ($bnr_res_val['channel_id'] == $spot_res_val['channel_id']) {
                    $channel_array[$i]['total_banner_ad_seconds'] = $bnr_res_val['total_banner_ad_seconds'];
                }
            }
            $i++;
        }
        if (count($spot_res) == 0) {
            foreach ($bnr_res as $bnr_res_val) {
                $channel_array[$i]['channel_id'] = $bnr_res_val['channel_id'];
                $channel_array[$i]['channel_name'] = $bnr_res_val['channel_name'];
                $channel_array[$i]['revenue_sharing'] = $bnr_res_val['revenue_sharing'];
                $channel_array[$i]['total_banner_ad_seconds'] = $bnr_res_val['total_banner_ad_seconds'];
                $i++;
            }
        }
        return $channel_array;

        /*if($channel_qry_res->num_rows() > 0) {
                    return $channel_qry_res->result("array");
             } */

    }

    public function get_all_channels_scheduled_v1($internal_ro_number)
    {
        $query = "select ac.campaign_id from sv_advertiser_campaign as ac where ac.internal_ro_number = '$internal_ro_number' and `is_make_good` = 0 and selected_region_id in (1,3) and visible_in_easyro = 1";
        $res = $this->db->query($query);
        $result = $res->result("array");
        $campaign_id = array();
        foreach ($result as $val) {
            array_push($campaign_id, $val['campaign_id']);
        }
        $campaign_ids = implode(",", $campaign_id);

        $channel_qry_spot = "SELECT ac.ro_duration,sc.channel_id, c.channel_name, e.revenue_sharing,sd.status,sd.impressions,sd.screen_region_id FROM sv_advertiser_campaign_screens_dates AS sd, sv_screen AS sc, sv_tv_channel AS c, sv_advertiser_campaign as ac,sv_customer as e WHERE c.enterprise_id=e.customer_id and sd.campaign_id IN ($campaign_ids) and sd.screen_id = sc.screen_id AND ac.campaign_id=sd.campaign_id and c.tv_channel_id = sc.channel_id";
        $channel_qry_spot_res = $this->db->query($channel_qry_spot);
        $spot_res = $channel_qry_spot_res->result("array");


        $channel_array = array();
        foreach ($spot_res as $spot_res_val) {
            $channel_id = $spot_res_val['channel_id'];
            $value = $this->get_total_ad_seconds_for_status($spot_res_val['status'], $spot_res_val['impressions'], $spot_res_val['ro_duration']);

            if (!array_key_exists($channel_id, $channel_array)) {
                $channel_array[$channel_id] = array();

                $channel_array[$channel_id]['channel_id'] = $spot_res_val['channel_id'];
                $channel_array[$channel_id]['channel_name'] = $spot_res_val['channel_name'];
                $channel_array[$channel_id]['revenue_sharing'] = $spot_res_val['revenue_sharing'];
                $channel_array[$channel_id]['total_spot_ad_seconds'] = 0;
                $channel_array[$channel_id]['total_banner_ad_seconds'] = 0;

                if ($spot_res_val['screen_region_id'] == 1) {
                    $channel_array[$channel_id]['total_spot_ad_seconds'] = $value;
                } elseif ($spot_res_val['screen_region_id'] == 3) {
                    $channel_array[$channel_id]['total_banner_ad_seconds'] = $value;
                }

            } else {
                if ($spot_res_val['screen_region_id'] == 1) {
                    $channel_array[$channel_id]['total_spot_ad_seconds'] = $channel_array[$channel_id]['total_spot_ad_seconds'] + $value;
                } elseif ($spot_res_val['screen_region_id'] == 3) {
                    $channel_array[$channel_id]['total_banner_ad_seconds'] = $channel_array[$channel_id]['total_banner_ad_seconds'] + $value;
                }
            }
        }
        return $channel_array;

    }

    public function get_total_ad_seconds_for_status($status, $impression, $duration)
    {
        $value = 0;
        if ($status == 'cancelled') {
            return $value;
        } elseif ($status == 'saved' || $status == 'scheduled') {
            $value = $duration * $impression;
            return $value;
        } else {
            return $value;
        }
    }

    public function get_channel_scheduled_value($internal_ro_number, $channel_id)
    {
        $channel_qry_spot = "SELECT ac.ro_duration,sd.status,sd.impressions,sd.screen_region_id FROM sv_advertiser_campaign_screens_dates AS sd, sv_advertiser_campaign as ac WHERE  ac.campaign_id=sd.campaign_id and ac.channel_id = $channel_id and ac.internal_ro_number='$internal_ro_number' and sd.status='scheduled' and `is_make_good` = 0 and selected_region_id in (1,3) and visible_in_easyro = 1";
        $channel_qry_spot_res = $this->db->query($channel_qry_spot);
        $spot_res = $channel_qry_spot_res->result("array");

        $channel_array = array();
        $channel_array['total_spot_ad_seconds'] = 0;
        $channel_array['total_banner_ad_seconds'] = 0;
        foreach ($spot_res as $spot_res_val) {
            $channel_id = $spot_res_val['channel_id'];
            $value = $this->get_total_ad_seconds_for_status($spot_res_val['status'], $spot_res_val['impressions'], $spot_res_val['ro_duration']);

            if ($spot_res_val['screen_region_id'] == 1) {
                $channel_array['total_spot_ad_seconds'] += $value;
            } elseif ($spot_res_val['screen_region_id'] == 3) {
                $channel_array['total_banner_ad_seconds'] += $value;
            }
        }
        return $channel_array;

    }

    public function get_all_cancelled_channels_scheduled($internal_ro_number)
    {
        $query = "select ac.campaign_id from sv_advertiser_campaign as ac where ac.internal_ro_number = '$internal_ro_number' and `is_make_good` = 0 and selected_region_id in (1,3) and visible_in_easyro = 1";
        $res = $this->db->query($query);
        $result = $res->result("array");
        $campaign_id = array();
        foreach ($result as $val) {
            array_push($campaign_id, $val['campaign_id']);
        }
        $campaign_ids = implode(",", $campaign_id);

        $channel_qry_spot = "SELECT (ac.ro_duration*SUM( impressions )) as total_spot_ad_seconds, sc.channel_id, c.channel_name, e.revenue_sharing FROM sv_advertiser_campaign_screens_dates AS sd, sv_screen AS sc, sv_tv_channel AS c, sv_advertiser_campaign as ac,sv_customer as e WHERE c.enterprise_id=e.customer_id and sd.campaign_id IN ($campaign_ids) and sd.screen_region_id = 1 AND sd.status ='cancelled' AND  sd.screen_id = sc.screen_id AND ac.campaign_id=sd.campaign_id and c.tv_channel_id = sc.channel_id GROUP BY c.channel_name,ac.ro_duration,sd.screen_region_id";
        $channel_qry_spot_res = $this->db->query($channel_qry_spot);
        $spot_res = $channel_qry_spot_res->result("array");

        $channel_qry_bnr = "SELECT (ac.ro_duration*SUM( impressions )) as total_banner_ad_seconds, sc.channel_id, c.channel_name, e.revenue_sharing FROM sv_advertiser_campaign_screens_dates AS sd, sv_screen AS sc, sv_tv_channel AS c, sv_advertiser_campaign as ac,sv_customer as e WHERE c.enterprise_id=e.customer_id and sd.campaign_id IN ($campaign_ids) and sd.screen_region_id = 3 AND sd.status ='cancelled' AND  sd.screen_id = sc.screen_id AND ac.campaign_id=sd.campaign_id and c.tv_channel_id = sc.channel_id GROUP BY c.channel_name,ac.ro_duration,sd.screen_region_id";
        $channel_qry_bnr_res = $this->db->query($channel_qry_bnr);
        $bnr_res = $channel_qry_bnr_res->result("array");

        $channel_array = array();
        $i = 0;
        foreach ($spot_res as $spot_res_val) {
            $channel_array[$i]['channel_id'] = $spot_res_val['channel_id'];
            $channel_array[$i]['channel_name'] = $spot_res_val['channel_name'];
            $channel_array[$i]['revenue_sharing'] = $spot_res_val['revenue_sharing'];
            $channel_array[$i]['total_spot_ad_seconds'] = 0;
            foreach ($bnr_res as $bnr_res_val) {
                if ($bnr_res_val['channel_id'] == $spot_res_val['channel_id']) {
                    $channel_array[$i]['total_banner_ad_seconds'] = 0;
                }
            }
            $i++;
        }
        if (count($spot_res) == 0) {
            foreach ($bnr_res as $bnr_res_val) {
                $channel_array[$i]['channel_id'] = $bnr_res_val['channel_id'];
                $channel_array[$i]['channel_name'] = $bnr_res_val['channel_name'];
                $channel_array[$i]['revenue_sharing'] = $bnr_res_val['revenue_sharing'];
                $channel_array[$i]['total_banner_ad_seconds'] = 0;
                $i++;
            }
        }
        return $channel_array;
    }

    public function get_running_no_of_ext_ro($id, $ro_date)
    {
        $data = array('id' => $id);
        $res = $this->db->get_where('ro_am_external_ro', $data);
        $result = $res->result("array");

        $old_ro_financial_year = $this->get_financial_year_for_ro_date($result[0]['ro_date']);
        $new_ro_financial_year = $this->get_financial_year_for_ro_date($ro_date);

        if ($old_ro_financial_year != $new_ro_financial_year) {
            $running_number = $this->get_financial_running_number($ro_date);
            return $running_number + 1;
        } else {
            return $result[0]['financial_year_running_no'];
        }

    }

    public function get_financial_running_number($ro_date)
    {
        $month = date("m", strtotime($ro_date));

        $financial_year = '';
        if ($month <= 3) {
            $financial_year = date("Y", strtotime($ro_date)) - 1;
        } else {
            $financial_year = date("Y", strtotime($ro_date));
        }
        /*$financial_end_year = $financial_year+1;
				$current_financial_start_date = date("$financial_year-04-01") ;
				$current_financial_end_date = date("$financial_end_year-03-31") ;
				
				$where = "( ( '$current_financial_start_date' <= 'ro_date') and ( 'ro_date' <= '$current_financial_end_date') )" ; */
        $where = " financial_year='$financial_year' ";
        $query = "select max(financial_year_running_no) as financial_year_running_no from ro_am_external_ro where  " . $where;
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            $result = $res->result("array");
            return $result[0]['financial_year_running_no'];
        } else {
            return 0;
        }
    }

    public function get_markets_against_ro($ro_id)
    {
        $result = $this->db->get_where('ro_market_price', array('ro_id' => $ro_id));
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function get_actual_campaign_end_date_for_ro($internal_ro)
    {
        $query = "select max(date) as end_date from sv_advertiser_campaign_screens_dates where campaign_id in (select campaign_id from sv_advertiser_campaign where internal_ro_number = '$internal_ro') and status='scheduled'";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            $res = $result->result("array");
            if (isset($res[0]['end_date'])) {
                return date('Y-m-d', strtotime($res[0]['end_date']));
            } else {
                return '';
            }
        }
        return '';
    }

    public function get_cancelled_data($where_data)
    {
        $result = $this->db->get_where('ro_cancel_external_ro', $where_data);
        //$result = $this->db->query($query) ;
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function update_cancelled_data($user_data, $where_data)
    {
        $this->db->where($where_data);
        $this->db->update('ro_cancel_external_ro', $user_data);
    }

    public function insert_into_cancel_market($user_data)
    {
        $this->db->insert('ro_cancel_external_ro', $user_data);
        return $this->db->insert_id();
    }

    public function get_value_tmp_market($where_data)
    {
        $result = $this->db->get_where('ro_market_price_tmp', $where_data);
        //$result = $this->db->query($query) ;
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function get_maxm_level_from_price_tmp($where_data)
    {
        $this->db->select_max('requested_number');
        $result = $this->db->get_where('ro_market_price_tmp', $where_data);

        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function update_tmp_market($user_data, $where_data)
    {
        $this->db->where($where_data);
        $this->db->update('ro_market_price_tmp', $user_data);
    }

    public function insert_into_tmp_table($user_data)
    {
        $this->db->insert('ro_market_price_tmp', $user_data);
    }

    public function check_for_ro_existence($cust_ro)
    {
        $chk_cust_ro_exist_qry = "select * from ro_am_external_ro  where cust_ro='$cust_ro'";
        $res_chk_cust_ro_exist_qry = $this->db->query($chk_cust_ro_exist_qry);
        if ($res_chk_cust_ro_exist_qry->num_rows() > 0) {
            echo 1;
        }
    }

    public function check_for_ro_existence_for_new($cust_ro)
    {
        $chk_cust_ro_exist_qry = "select * from ro_am_external_ro where cust_ro='$cust_ro'";
        $res_chk_cust_ro_exist_qry = $this->db->query($chk_cust_ro_exist_qry);
        if ($res_chk_cust_ro_exist_qry->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function get_data_from_cancel_ro($whereData)
    {
        $result = $this->db->get_where('ro_cancel_external_ro', $whereData);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function get_all_clusters()
    {
        $query = "select * from sv_sw_clusters order by cluster_name";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_markets_for_cluster()
    {
        $query = "SELECT ssc.cluster_name, scm.market_id, ssm.sw_market_name FROM `sv_sw_clusters` AS ssc
                            JOIN `sv_cluster_market` AS scm ON ssc.id = scm.cluster_id
                            JOIN `sv_sw_market` AS ssm ON scm.market_id = ssm.id";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function getAgencyContactMailIdsUsingIds($contactIds)
    {

        $temp = explode(",", $contactIds);
        $contactIds = implode("','", $temp);

        $query = "select DISTINCT agency_email from ro_agency_contact where id IN ('" . $contactIds . "' ) ";
        $res = $this->db->query($query);
        $data = $res->result_array();

        return $data;

    }

    /*public function getContactMailIdsUsingIds( $contactIds ){

                    $temp = explode(",", $contactIds) ;
                    $contactIds = implode("','", $temp) ;

                    $query = "select DISTINCT agency_email from ro_agency_contact where id IN ('".$contactIds."' ) ";
                    $res   = $this->db->query($query) ;
                    $data  = $res->result_array() ;

                    return$data ;

                }*/

    /*function used for inserting data for new RO*/

    public function getClientContactMailIdsUsingIds($contactIds)
    {

        $temp = explode(",", $contactIds);
        $contactIds = implode("','", $temp);

        $query = "select DISTINCT client_email from ro_client_contact where id IN ('" . $contactIds . "' ) ";
        $res = $this->db->query($query);
        $data = $res->result_array();

        return $data;

    }

    /*function used to update client related data*/

    public function insert_into_am_ext_ro($create_ro_data)
    {
        $this->db->insert('ro_am_external_ro', $create_ro_data);
        log_message('info', 'USER_INFO -- in function insert_into_am_ext_ro and query is - ' . $this->db->last_query());
    }

    /*function used to get last inserted id*/

    public function update_new_advertiser($data, $where)
    {
        $this->db->update('sv_new_advertiser', $data, $where);
    }

    /*function used to insert data into content table for an ro*/

    public function get_last_insert_id()
    {
        $last_inserted_id = $this->db->insert_id();
        return $last_inserted_id;
    }

    /*function used to get already linked advanced ros by ajax*/

    public function insert_content_data_for_ro($content_data)
    {
        $this->db->insert('ro_content', $content_data);
    }

    /*function used to get all external ros*/

    public function get_linked_advanced_ros($cust_ro_id)
    {
        $query = "select rla.*,amer.cust_ro from ro_linked_advance_ro as rla
                              inner join ro_am_external_ro as amer on amer.id = rla.advance_ro_id
                              where ro_id = " . $cust_ro_id . "
                              order by ro_id desc";
        $res = $this->db->query($query);
        $res_ary = $res->result("array");
        if ($res->num_rows() != 0) {
            return $res_ary;
        }
    }

    /*function used to get all advanced ros*/

    public function get_all_cust_ros()
    {
        $query = "Select * from  ro_am_external_ro where test_user_creation = 0 ORDER BY camp_start_date DESC";
        $res = $this->db->query($query);
        $res_ary = $res->result("array");
        if ($res->num_rows() != 0) {
            return $res_ary;
        }
    }

    /*function used to delete records for that ro*/

    public function get_all_advanced_ros()
    {
        $query = "Select * from  ro_am_external_ro where test_user_creation = 2 ORDER BY camp_start_date DESC";
        $res = $this->db->query($query);
        $res_ary = $res->result("array");
        if ($res->num_rows() != 0) {
            return $res_ary;
        }
    }

    /*function used to link an ro to advanced ro*/

    public function remove_link_to_advanced_ros($link_data)
    {
        $this->db->delete('ro_linked_advance_ro', $link_data);
    }

    public function link_ro_to_advanced_ro($link_data)
    {
        $this->db->insert('ro_linked_advance_ro', $link_data);
    }

    //Get user regions

    function get_channel_priorities_for_ro($internal_ro_number)
    {
        log_message('DEBUG','In am_model@get_channel_priorities_for_ro | Inside function get_channel_priorities_for_ro');
        $query = "select distinct sac.market_id,ssm.sw_market_name,stc.priority,stc.tv_channel_id,stc.channel_name from sv_advertiser_campaign as sac
                        inner join sv_tv_channel as stc on sac.channel_id = stc.tv_channel_id
                        inner join sv_sw_market as ssm on sac.market_id = ssm.id
                        inner join sv_advertiser_campaign_screens_dates acsd on acsd.campaign_id = sac.campaign_id
                        where sac.internal_ro_number = '$internal_ro_number'
                        and acsd.status = 'scheduled' ORDER BY stc.priority ASC ";
        log_message('DEBUG','In am_model@get_channel_priorities_for_ro | Fetching result for INTERNAL RO - '.print_r($internal_ro_number, true));
        $resultObject = $this->db->query($query);
        $resultArray = $resultObject->result("array");
        log_message('DEBUG','In am_model@get_channel_priorities_for_ro | Fetched result -'.print_r($resultArray, true));

        $marketPriorityArray = array();
        $priorityArray = array();

        foreach ($resultArray as $res) {

            if (array_key_exists($res['market_id'], $marketPriorityArray)) {
                $marketPriorityArray[$res['market_id']]['channel_priority'][$res['priority']][$res['tv_channel_id']]['channel_id'] = $res['tv_channel_id'];
                $marketPriorityArray[$res['market_id']]['channel_priority'][$res['priority']][$res['tv_channel_id']]['channel_name'] = $res['channel_name'];
                $marketPriorityArray[$res['market_id']]['channel_priority'][$res['priority']][$res['tv_channel_id']]['is_priority_cancel'] = 'no';
                array_push($priorityArray, $res['priority']);
            } else {
                $marketPriorityArray[$res['market_id']]['market_id'] = $res['market_id'];
                $marketPriorityArray[$res['market_id']]['market_name'] = $res['sw_market_name'];
                $marketPriorityArray[$res['market_id']]['channel_priority'][$res['priority']][$res['tv_channel_id']]['channel_id'] = $res['tv_channel_id'];
                $marketPriorityArray[$res['market_id']]['channel_priority'][$res['priority']][$res['tv_channel_id']]['channel_name'] = $res['channel_name'];
                $marketPriorityArray[$res['market_id']]['channel_priority'][$res['priority']][$res['tv_channel_id']]['is_priority_cancel'] = 'no';
                array_push($priorityArray, $res['priority']);
            }
        }

        $finalPriorityArray = array();
        $finalPriorityArray['priority'] = array_unique($priorityArray);
        $finalPriorityArray['marketPriority'] = $marketPriorityArray;

        return $finalPriorityArray;
    }

    public function getRoDetailsForAccountManagers($user_id, $start_date, $end_date)
    {
        $query = "select count(id) as ro_count,sum(gross) as total_ro_amount from ro_am_external_ro where user_id = '$user_id'  ";
        $query = $query . " and (( camp_start_date >= '$start_date' and camp_end_date <= '$end_date' )
                                                or
                ( camp_start_date <= '$start_date' and camp_end_date between '$start_date' and '$end_date')
                                                or
                ( camp_start_date between '$start_date' and '$end_date' and camp_end_date >= '$end_date' )
                                                or
                ( camp_start_date <= '$start_date' and camp_end_date >= '$end_date' ))";

        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            return $result->result("array");
        }

    }

    public function getRoDetailsForAccountManagersForRoDate($user_id, $start_date, $end_date)
    {
        $query = "select count(id) as ro_count,sum(gross)/100000 as total_ro_amount from ro_am_external_ro where user_id = '$user_id'  ";
        $query = $query . " and ro_date between '$start_date' and '$end_date' ";

        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            return $result->result("array");
        }

    }

    public function getMonthlyTargetForUser($userId, $financialYear, $monthName)
    {

        $query = "select sum(target)/100000 as target from ro_user_targets where user_id in ($userId) and financial_year='$financialYear'  and month in ('$monthName') ";
        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            return $result->result("array");
        }
    }

    public function getRoDetailsForAccountManagersForRoDateQuaterly($user_id, $start_date, $end_date)
    {
        $query = "select count(id) as ro_count,sum(gross) as total_ro_amount from ro_am_external_ro where user_id in ($user_id)  ";
        $query = $query . " and ro_date between '$start_date' and '$end_date' ";

        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            return $result->result("array");
        }

    }

    public function getQuarterlyTargetForUserAndMonths($userId, $financialYear, $monthNames)
    {
        $monthName = explode(",", $monthNames);
        $target = 0;
        foreach ($monthName as $value) {
            $month = $value;
            $targetValue = $this->getQuarterlyTargetForUser($userId, $financialYear, $month);
            $target += $targetValue[0]['target'];
        }
        return $target;
    }

    public function getQuarterlyTargetForUser($userId, $financialYear, $monthName)
    {
        $query = "select sum(target) as target from ro_user_targets where user_id in ($userId) and financial_year='$financialYear'  and month in ('$monthName') ";
        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            return $result->result("array");
        }
    }

    public function getQuaterlyTargetForUser($userId, $financialYear, $monthName)
    {
        $query = "select target/100000 as target from ro_user_targets where user_id = '$userId' and financial_year='$financialYear'  and month='$monthName' ";
        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            return $result->result("array");
        }
    }

    public function getChildProfileId($where_data)
    {
        $result = $this->db->get_where('ro_profile_hierarchy', $where_data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function getChildUserId($user_id)
    {
        $query = "select user_id from ro_user where reporting_manager_id in ($user_id) ";
        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            return $result->result("array");
        }
    }

    public function getApprovedRo($userId, $start_date, $end_date)
    {
        $query = "select am.id,camp_start_date,camp_end_date from ro_am_external_ro as am
                    inner join ro_external_ro_report_details as nr on am.internal_ro = nr.internal_ro_number where user_id in ($userId)";
        $query = $query . " and (( camp_start_date >= '$start_date' and camp_end_date <= '$end_date' )
                                                or
                ( camp_start_date <= '$start_date' and camp_end_date between '$start_date' and '$end_date')
                                                or
                ( camp_start_date between '$start_date' and '$end_date' and camp_end_date >= '$end_date' )
                                                or
                ( camp_start_date <= '$start_date' and camp_end_date >= '$end_date' ))";
        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            return $result->result("array");
        }
        return array();
    }

    public function getRoValuesFromMis($userData)
    {
        $result = $this->db->get_where('ro_mis_report_revenue', $userData);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }

        return array();
    }

    public function getTotalScheduledFCT($internalRo, $channel_id, $regionId)
    {
        $campaignIds = $this->getScheduledCampaignIdsForRosAndChannel($internalRo, $channel_id);

        $query = "SELECT (ac.ro_duration*SUM(acsd.impressions )) as total_ad_seconds,acsd.screen_region_id "
            . "FROM sv_advertiser_campaign_screens_dates AS acsd "
            . "Inner Join sv_advertiser_campaign as ac on ac.campaign_id=acsd.campaign_id "
            . "WHERE ac.campaign_id IN ($campaignIds) and acsd.screen_region_id in($regionId) and acsd.status ='scheduled' ";

        $query .= " GROUP BY ac.ro_duration,acsd.screen_region_id";
        $results = $this->db->query($query);
        $fcts = $results->result("array");

        $data = array();
        $data['ad_seconds'] = 0;

        foreach ($fcts as $value) {
            $data['ad_seconds'] += $value['total_ad_seconds'];
        }

        return $data;
    }

    public function getScheduledCampaignIdsForRosAndChannel($internalRo, $channel_id)
    {
        $query = "select group_concat(campaign_id) as campaign_id from sv_advertiser_campaign
                    where campaign_status NOT IN ( 'pending_approval', 'cancelled' )
                    AND internal_ro_number = '$internalRo' AND is_make_good = 0 And channel_id='$channel_id' ";
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

    public function getAchievedROAmount($roIds, $monthName, $startDate, $endDate)
    {
        if (!isset($monthName)) {
            $monthName = date("F");
        }
        $achievedAmount = 0;
        foreach ($roIds as $val) {
            $ro_id = $val['id'];
            //$ro_Values = $this->getRoValuesFromMis(array('ro_id'=>$ro_id,'month_name'=>$monthName)) ;
            $ro_Values = $this->getRoValuesForMaxmFct(array('ro_id' => $ro_id, 'month_name' => $monthName, 'start_date' => $startDate, 'end_date' => $endDate));
            $achievedAmount += $ro_Values;
        }
        return $achievedAmount;
    }

    public function getRoValuesForMaxmFct($userData)
    {
        $ro_id = $userData['ro_id'];
        $monthName = $userData['month_name'];
        $start_date = $userData['start_date'];
        $end_date = $userData['end_date'];

        $roDetails = $this->ro_detail_for_ro_id($ro_id);
        $internalRo = $roDetails[0]['internal_ro'];

        $roMarketPrice = $this->get_market_for_ro($ro_id);
        $marketAmount = 0;
        foreach ($roMarketPrice as $value) {
            $market = $value['market'];
            $marketDetails = $this->getMarketDetails(array('sw_market_name' => $market));

            $spotMarketAmount = $value['spot_price'];
            $bannerMarketAmount = $value['banner_price'];

            $maxmChannelFct = $this->getMaximumChannelsFctByRegion($internalRo, $marketDetails[0]['id']);
            if (!empty($maxmChannelFct['spotChannel'])) {
                //1 is region Id
                $scheduledSpot = $this->getScheduledFCT($internalRo, $maxmChannelFct['spotChannel'], $start_date, $end_date, 1);
                //$totalSpot =  $this->getTotalScheduledFCT($internalRo,$maxmChannelFct['spotChannel'],1) ;
                $totalSpot = $maxmChannelFct['totalSpotFct'];

                $fraction = $scheduledSpot['ad_seconds'] / $totalSpot['ad_seconds'];
                $marketAmount += $spotMarketAmount * $fraction;
            }

            if (!empty($maxmChannelFct['bannerChannel'])) {
                //3 is region id
                $scheduledSpot = $this->getScheduledFCT($internalRo, $maxmChannelFct['bannerChannel'], $start_date, $end_date, 3);
                //$totalSpot =  $this->getTotalScheduledFCT($internalRo,$maxmChannelFct['bannerChannel'],3) ;
                $totalSpot = $maxmChannelFct['totalBannerFct'];

                $fraction = $scheduledSpot['ad_seconds'] / $totalSpot['ad_seconds'];
                $marketAmount += $bannerMarketAmount * $fraction;
            }
        }
        return $marketAmount;
    }

    public function get_market_for_ro($ro_id)
    {
        $query = "select * from `ro_market_price` where ro_id = '$ro_id'";
        $result = $this->db->query($query);
        return $result->result("array");
    }

    public function getMarketDetails($where_data)
    {
        $result = $this->db->get_where('sv_sw_market', $where_data);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }

        return array();
    }

    public function getMaximumChannelsFctByRegion($internalRo, $marketId)
    {
        $campaignIds = $this->getScheduledCampaignIdsForRos($internalRo, $marketId);
        $data = array();
        $query = "Select max(X.total_ad_seconds) as total_fct,X.screen_region_id,X.channel_id
                        from
            (SELECT ro_duration*SUM(acsd.impressions) as total_ad_seconds,acsd.screen_region_id,ac.channel_id,ac.caption_name
                FROM sv_advertiser_campaign_screens_dates acsd 
                INNER JOIN sv_advertiser_campaign ac on ac.campaign_id=acsd.campaign_id 
                WHERE  ac.campaign_id IN ($campaignIds)  and acsd.screen_region_id in(1,3) and acsd.status ='scheduled' 
                GROUP BY acsd.screen_region_id,ac.channel_id ) X
                GROUP BY X.screen_region_id ";
        $result = $this->db->query($query);
        if ($result->num_rows() != 0) {
            $res = $result->result("array");
            foreach ($res as $val) {
                if ($val['screen_region_id'] == 1) {
                    $data['spotChannel'] = $val['channel_id'];
                    $data['totalSpotFct'] = $val['total_fct'];
                }

                if ($val['screen_region_id'] == 3) {
                    $data['bannerChannel'] = $val['channel_id'];
                    $data['totalBannerFct'] = $val['total_fct'];
                }
            }
            return $data;
        }
        $data['spotChannel'] = 0;
        $data['bannerChannel'] = 0;
        return $data;
    }

    public function getScheduledCampaignIdsForRos($internal_ro, $marketId)
    {
        $query = "select group_concat(campaign_id) as campaign_id from sv_advertiser_campaign
                    where campaign_status NOT IN ( 'pending_approval', 'cancelled' )
                    AND internal_ro_number = '$internal_ro' AND is_make_good = 0 And market_id='$marketId' ";
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

    public function getScheduledFCT($internalRo, $channel_id, $start_date, $end_date, $regionId)
    {
        $campaignIds = $this->getScheduledCampaignIdsForRosAndChannel($internalRo, $channel_id);

        $query = "SELECT (ac.ro_duration*SUM(acsd.impressions )) as total_ad_seconds,acsd.screen_region_id "
            . "FROM sv_advertiser_campaign_screens_dates AS acsd "
            . "Inner Join sv_advertiser_campaign as ac on ac.campaign_id=acsd.campaign_id "
            . "WHERE ac.campaign_id IN ($campaignIds) and acsd.screen_region_id in($regionId) and acsd.status ='scheduled' and "
            . "(acsd.date >= '$start_date' and acsd.date <='$end_date' ) ";

        $query .= " GROUP BY ac.ro_duration,acsd.screen_region_id";
        $results = $this->db->query($query);
        $fcts = $results->result("array");

        $data = array();
        $data['ad_seconds'] = 0;

        foreach ($fcts as $value) {
            $data['ad_seconds'] += $value['total_ad_seconds'];
        }

        return $data;
    }

    public function getAchievedAmountForUserAndDate($userId, $startDate, $endDate)
    {
        $ros = $this->am_model->getApprovedRo($userId, $startDate, $endDate);
        $achievedAmount = 0;
        foreach ($ros as $val) {
            $ro_id = $val['id'];
            $ro_start_date = $val['camp_start_date'];
            $ro_end_date = $val['camp_end_date'];

            if (strtotime($ro_start_date) < strtotime($startDate)) {
                $ro_start_date = $startDate;
            }

            if (strtotime($ro_end_date) > strtotime($endDate)) {
                $ro_end_date = $endDate;
            }

            //$monthNames = $this->getMonthNameForGivenPeriod($ro_start_date,$ro_end_date) ;
            $ro_values = $this->getRoAmountForGivenPeriods($ro_id, $ro_start_date, $ro_end_date);
            $achievedAmount += $ro_values;
        }
        return $achievedAmount;
    }

    public function getRoAmountForGivenPeriods($ro_id, $ro_start_date, $ro_end_date)
    {
        $modified_startDate = date("Y-m-01", strtotime($ro_start_date));
        for ($i = strtotime($modified_startDate); $i <= strtotime($ro_end_date); $i = strtotime("+1 month", $i)) {
            $startDate = date("Y-m-d", $i);
            $endDate = date("Y-m-t", $i);
            $monthName = date("F", $i);
            return $this->getRoValuesForMaxmFct(array('ro_id' => $ro_id, 'month_name' => $monthName, 'start_date' => $startDate, 'end_date' => $endDate));
        }
    }

    public function getRoAmountForGivenPeriod($ro_id, $monthNames)
    {
        $monthValue = explode(",", $monthNames);
        $monthValue = "'" . implode("','", $monthValue) . "'";
        $query = "select sum(revenue) as revenue from ro_mis_report_revenue where ro_id=$ro_id and month_name in ($monthValue) ";
        $result = $this->db->query($query);

        if ($result->num_rows() != 0) {
            $res = $result->result("array");
            return $res[0]['revenue'];
        }
        return array();
    }

    public function getMonthNameForGivenPeriod($startDate, $endDate)
    {
        $monthNames = '';
        $modified_startDate = date("Y-m-01", strtotime($startDate));
        for ($i = strtotime($modified_startDate); $i <= strtotime($endDate); $i = strtotime("+1 month", $i)) {
            $month_name = date("F", $i);
            if (empty($monthNames)) {
                $monthNames = $month_name;
            } else {
                $monthNames = $monthNames . "," . $month_name;
            }
        }
        return $monthNames;
    }

    /*public function getAchievedROAmount($roIds){
            $monthName = date("F") ;
            $achievedAmount = 0 ;
            foreach($roIds as $val){
                $ro_id = $val['id'] ;
                $ro_Values = $this->getRoValuesFromMis(array('ro_id'=>$ro_id,'month_name'=>$monthName)) ;
                $achievedAmount += $ro_Values[0]['revenue'] ; 
            }
            return $achievedAmount ;
        } */
    public function getMonthlyTarget($userId, $monthName)
    {
        $this->load->model('common_model');
        $yearValues = $this->common_model->getYears();

        if (!isset($monthName)) {
            $monthName = date("F");
        }

        $userMonthlyTarget = $this->am_model->getMonthlyTargetForUser($userId, $yearValues['financial_year'], $monthName);
        return $userMonthlyTarget[0]['target'];
    }

    public function getMonthlyTargetSumForDownload($user_id, $months, $year)
    {

        $userMonthlyTarget = $this->am_model->getMonthlyTargetForUser($user_id, $year, $months);
        return $userMonthlyTarget[0]['target'];
    }

    public function getAllDistinctUsersOfRo($tableName)
    {
        $query = "select distinct user_id from $tableName";
        $result = $this->db->query($query);
        if ($result->num_rows() > 0) {
            //add association=1 or 0
            return $result->result("array");
        }
    }

    public function updateRegionForUser($regionId, $userId, $tableName)
    {
        //$this->db->update($tableName,$userData,$whereData);
        $query = "update $tableName set region_id=$regionId where user_id=$userId";
        $this->db->query($query);
    }

    public function updateGSTForAgency($gstValue, $name)
    {
        $whereArray['agency_display_name'] = $name;
        $data['gst'] = $gstValue;
        $this->db->where($whereArray);
        $this->db->update('ro_agency_contact', $data);
        $affected_rows = $this->db->affected_rows();
        return $affected_rows;
    }

    public function updateGSTForAdvertiser($gstValue, $name)
    {
        $whereArray['client_display_name'] = $name;
        $data['gst'] = $gstValue;
        $this->db->where($whereArray);
        $this->db->update('ro_client_contact', $data);
        $affected_rows = $this->db->affected_rows();
        return $affected_rows;
    }

    public function getGSTForAgency($name)
    {
        $this->db->select('*');
        $this->db->from("ro_agency_contact");
        $this->db->where('agency_display_name', $name);
        $query = $this->db->get();
        $records = $query->result_array();
        if (count($records) > 0) {
            return $records[0];
        } else {
            return array();
        }
    }

    public function getGSTForAdvertiser($name)
    {
        $this->db->select('*');
        $this->db->from("ro_client_contact");
        $this->db->where('client_display_name', $name);
        $query = $this->db->get();
        $records = $query->result_array();
        if (count($records) > 0) {
            return $records[0];
        } else {
            return array();
        }
    }

    public function getAllStates()
    {
        $this->db->select('*');
        $this->db->from("sv_sw_states");
        $this->db->order_by("state_name", "asc");
        $query = $this->db->get();
        $records = $query->result_array();
        return $records;
    }
}


?>
