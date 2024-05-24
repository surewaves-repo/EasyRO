<?php if (!defined('BASEPATH')) exit("NO Direct Script Access Allowed");

class Utility extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ro_model');
        $this->load->model('am_model');
        $this->load->model('utility_model');
        $this->load->model("menu_model");
        $this->load->library('session');
        $this->load->config('form_validation');
        $this->load->library('form_validation');
        $this->load->helper("url");
        $this->load->helper("hash_api");
        $this->customerArr = array();
        $this->advBrandArr = array();
    }

    //function to redirect based on the user logged in status
    public function index()
    {
        $logged_in = $this->session->userdata("logged_in_user");
        if (isset($logged_in[0])) {
            $logged_in = $this->session->userdata("logged_in_user");
            $model = array();
            $model['logged_in_user'] = $logged_in[0];
            $model['profile_id'] = $logged_in[0]['profile_id'];
            $model['menu'] = $this->menu_model->get_header_menu();
            $this->load->view('utility/utilityList', $model);
        } else {
            $this->load->view('ro_manager/login');
        }
    }

    public function utilityGrid()
    {
        $records = $this->utility_model->getBrands();
        $this->output->set_header('Content-Type: application/json');
        $this->output->set_output($records);
    }

    public function manager($activeTab = 0)
    {
        $logged_in = $this->session->userdata("logged_in_user");
        if (isset($logged_in[0])) {
            $logged_in = $this->session->userdata("logged_in_user");
            $model = array();
            $model['logged_in_user'] = $logged_in[0];
            $model['profile_id'] = $logged_in[0]['profile_id'];
            $model['menu'] = $this->menu_model->get_header_menu();
            $model['activeTab'] = $activeTab;
            $model['validation_errors'] = $this->session->flashdata('validation');
            $model['success_msg'] = $this->session->flashdata('success');

            $model['CategoryList'] = $this->utility_model->getCategory();
            $model['allAgency'] = $this->utility_model->getAgency();

            $this->customerArr = $this->getCustomerDetails();
            $model['br_customer'] = $this->customerArr;
            $model['activateAdvertiser'] = $this->load->view('utility/activateAdvertiser', $model, true);
            $model['addCategory'] = $this->load->view('utility/addCategory', $model, true);
            $model['addProduct'] = $this->load->view('utility/addProduct', $model, true);
            $model['addAdvertiser'] = $this->load->view('utility/addAdvertiser', $model, true);
            $model['addBrands'] = $this->load->view('utility/addBrand', $model, true);
            $model['addAgency'] = $this->load->view('utility/addAgency', $model, true);
            $model['mapBrands'] = $this->load->view('utility/mapBrBrands', $model, true);
            $this->load->view('utility/manageBrands', $model);
        } else {
            $this->load->view('ro_manager/login');
        }
    }

    public function getCustomerDetails()
    {
        $timeStamp = date("d-m-Y H:i:s");
        $api['appkey'] = APPKEY;
        $api['authkey'] = apiAuth(array("timestamp" => $timeStamp, "appkey" => APPKEY), "getEasyRoCustomer");
        $api['timestamp'] = $timeStamp;

        $url = INGESTXPRESS_API . "/getEasyRoCustomer";
        $_ch = curl_init();
        curl_setopt($_ch, CURLOPT_URL, $url);
        curl_setopt($_ch, CURLOPT_POST, 1);
        curl_setopt($_ch, CURLOPT_POSTFIELDS, http_build_query(array("request" => json_encode($api))));
        curl_setopt($_ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($_ch);
        curl_close($_ch);
        $br_contentArr = json_decode($result, true);
        $customer = array();
        if ($br_contentArr['status'] == 'success') {
            $br_customer_id = $br_contentArr['data']['customer_id'];
            $customer[$br_customer_id]['name'] = $br_contentArr['data']['customer_name'];
            $customer[$br_customer_id]['user']['id'] = $br_contentArr['data']['user_id'];
            $customer[$br_customer_id]['user']['name'] = $br_contentArr['data']['full_name'];

        }
        return $customer;
    }

    public function post_activate_advertiser()
    {
        $logged_in = $this->session->userdata("logged_in_user");
        if (isset($logged_in[0])) {
            if ($this->form_validation->run('activate_advertiser_form') == FALSE)
                $this->session->set_flashdata('validation', validation_errors());
            else {
                $advertiserID = $this->input->post("act_advertiser");
                $advertiserName = $this->utility_model->getAdvertiserName($advertiserID);
                //$res = $this->utility_model->saveAdvertiser($advertiserID);
                $res = $this->utility_model->activateAdvertiser($advertiserName);
                $this->session->set_flashdata('success', "Advertiser activate successfully");
            }
            redirect("/utility/manager");
        } else {
            $this->load->view('ro_manager/login');
        }

    }

    public function post_add_category()
    {
        $logged_in = $this->session->userdata("logged_in_user");
        if (isset($logged_in[0])) {
            if ($this->form_validation->run('add_category_form') == FALSE)
                $this->session->set_flashdata('validation', validation_errors());
            else {
                $category_id = $this->input->post("CategoryID");
                $category_name = trim($this->input->post("addNewCategory"));
                $check = $this->utility_model->checkCategory($category_id, $category_name);
                if (count($check) != 0)
                    $this->session->set_flashdata('validation', "Category is already exist !!");
                else {
                    $res = $this->utility_model->addCategory($category_name, $category_id);
                    $this->session->set_flashdata('success', $res);
                }
            }
            redirect("/utility/manager/1");
        } else {
            $this->load->view('ro_manager/login');
        }
    }

    public function post_add_product()
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $product_group = trim($this->input->post("addNewProduct"));
        if (isset($logged_in[0])) {
            /*if ($this->form_validation->run('add_product_form') == FALSE)
            $this->session->set_flashdata('validation', validation_errors());*/
            $error_in_validation = $this->form_validation->set_rules('addNewProduct', 'New Product', 'trim|required| xss_clean');
            $is_alpha_numeric = $this->alphanumeric_verification($product_group);
            if ($error_in_validation) {
                if ($is_alpha_numeric == 0) {
                    $this->session->set_flashdata('validation', "Product Name is not valid !!");
                } else {
                    $cat_id = $this->input->post("product_category");
                    $product_group = trim($this->input->post("addNewProduct"));
                    $ProductID = $this->input->post("ProductID");
                    $check = $this->utility_model->checkProducts($cat_id, $ProductID, $product_group);
                    if (count($check) != 0)
                        $this->session->set_flashdata('validation', "Product is already exist !!");
                    else {
                        $res = $this->utility_model->addProduct($cat_id, $product_group, $ProductID);
                        $this->session->set_flashdata('success', $res);
                    }
                }
            }
            redirect("/utility/manager/2");
        } else {
            $this->load->view('ro_manager/login');
        }
    }

    public function alphanumeric_verification($str)
    {
        //$alphanumericPattern = "/^[a-zA-Z0-9 ]$/" ;
        $alphanumericPattern = "/^[a-zA-Z0-9\-\s]+$/";
        $value = preg_match($alphanumericPattern, $str);
        return $value;
        /*if($value == 0) {
            //does not match
            //$this->form_validation->set_message('alphanumeric_verification', 'brand name is not valid');
            return TRUE;
        }else{
            return FALSE;
        }*/
    }

    public function post_add_advertiser()
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $advertiser = trim($this->input->post("addNewAdvertiser"));
        $advertiser_display = trim($this->input->post("addNewDisplayAdvertiser"));
        $edit_advertiser_id = trim($this->input->post("advertiser_advertiser"));
        $selected_advertiser_name = trim($this->input->post("hid_advertiser_name"));
        $edit_advertiser_display = trim($this->input->post("advertiser_display_advertiser"));

        $direct_client = trim($this->input->post("rd_direct_client"));
        if ($direct_client == 1) {
            $postal_address = trim($this->input->post("postalClientAddress"));
            $billing_info = trim($this->input->post("billingClientInfo"));
            $billing_address = trim($this->input->post("billingClientAddress"));
            $bill_cycle = trim($this->input->post("billClientCycle"));
        }

        if (isset($logged_in[0])) {
            /*if ($this->form_validation->run('add_advertiser_form') == FALSE)
                $this->session->set_flashdata('validation', validation_errors());*/
            $error_in_validation = $this->form_validation->set_rules('addNewAdvertiser', 'New Advertiser', 'trim|required| xss_clean');
            //$is_alpha_numeric = $this->alphanumeric_verification($advertiser);
            if ($error_in_validation) {
                $product_group_id = $this->input->post("advertiser_product");
                $advertiser = trim($this->input->post("addNewAdvertiser"));
                $AdvertiserID = $this->input->post("AdvertiserID");
                //$check = $this->utility_model->checkAdvertiser($product_group_id, $AdvertiserID, $advertiser_display);
                //if(count($check)!=0 && $edit_advertiser_display == '' )
                // $this->session->set_flashdata('validation', "Advertiser Display Name is already exist !!");
                //else
                //{
                $res = $this->utility_model->addAdvertiser($product_group_id, $advertiser, $advertiser_display, $AdvertiserID, $direct_client, $billing_info, $billing_address, $bill_cycle, $postal_address, $edit_advertiser_id, $edit_advertiser_display, $selected_advertiser_name);
                $this->session->set_flashdata('success', $res);
                // }
            }

            redirect("/utility/manager/3");
        } else {
            $this->load->view('ro_manager/login');
        }
    }

    public function post_add_brand()
    {
        $logged_in = $this->session->userdata("logged_in_user");
        $brand = trim($this->input->post("addNewBrand"));
        if (isset($logged_in[0])) {
            /*if ($this->form_validation->run('add_brand_form') == FALSE)
            $this->session->set_flashdata('validation', validation_errors());*/
            $error_in_validation = $this->form_validation->set_rules('addNewBrand', 'New Brand', 'trim|required|xss_clean');
            $is_alpha_numeric = $this->alphanumeric_verification($brand);

            if ($error_in_validation) {
                if ($is_alpha_numeric == 0) {
                    $this->session->set_flashdata('validation', "Brand Name is not valid !!");
                } else {
                    $new_advertiser_id = $this->input->post("brand_advertiser");
                    $brand = trim($this->input->post("addNewBrand"));
                    $BrandID = $this->input->post("BrandID");
                    $check = $this->utility_model->checkBrand($new_advertiser_id, $BrandID, $brand);
                    if (count($check) != 0)
                        $this->session->set_flashdata('validation', "Brand is already exist !!");
                    else {
                        $res = $this->utility_model->addBrand($new_advertiser_id, $brand, $BrandID);
                        $this->session->set_flashdata('success', $res);
                    }
                }
            }
            redirect("/utility/manager/4");
        } else {
            $this->load->view('ro_manager/login');
        }
    }

    public function post_add_agency()
    {
        $logged_in = $this->session->userdata("logged_in_user");

        $agency_name = trim($this->input->post("agencyName"));
        $edit_agency_id = trim($this->input->post("editAgency"));
        $agency_dis_name = trim($this->input->post("agencyDisName"));
        $edit_agency_dis_name = trim($this->input->post("editDisAgency"));
        $postal_address = trim($this->input->post("postalAgencyAddress"));
        $billing_info = trim($this->input->post("billingAgencyInfo"));
        $billing_address = trim($this->input->post("billingAgencyAddress"));
        $bill_cycle = trim($this->input->post("billAgencyCycle"));

        if (isset($logged_in[0])) {
            /*if ($this->form_validation->run('add_product_form') == FALSE)
            $this->session->set_flashdata('validation', validation_errors());*/
            $error_in_validation = $this->form_validation->set_rules('agencyName', 'New Agency', 'trim|required| xss_clean');
            //$is_alpha_numeric = $this->alphanumeric_verification($postal_address);
            if ($error_in_validation) {
                $check_display_name = $this->utility_model->checkAgencyDisplay($agency_dis_name);
                $check_agency_name = $this->utility_model->checkAgency($agency_name);
                if (count($check_display_name) != 0 && $edit_agency_dis_name == '') {
                    $this->session->set_flashdata('validation', "Agency Display Name already exist !!");
                } else
                    if (count($check_agency_name) != 0 && $edit_agency_id == '') {
                        $this->session->set_flashdata('validation', "Agency Name already exist !!");
                    } else {
                        $res = $this->utility_model->addAgency($agency_name, $agency_dis_name, $postal_address, $billing_info, $billing_address, $bill_cycle, $edit_agency_id, $edit_agency_dis_name);
                        $this->session->set_flashdata('success', $res);
                    }
            }
            redirect("/utility/manager/5");
        } else {
            $this->load->view('ro_manager/login');
        }
    }

    function getProducts()
    {
        $CategoryID = $this->input->post('CategoryID');
        $productList = $this->utility_model->getProducts($CategoryID);
        echo "<option value=''></option>";
        foreach ($productList as $key => $val)
            echo "<option value='" . $val['id'] . "'>" . $val['product_group'] . "</option>";
    }

    function getInActiveAdvertiser()
    {
        $ProductID = $this->input->post('ProductID');
        $productList = $this->utility_model->getAdvertiser($ProductID, 0);
        echo "<option value=''></option>";
        foreach ($productList as $key => $val)
            echo "<option value='" . $val['id'] . "'>" . $val['advertiser'] . "</option>";
    }

    function getAllAdvertiser()
    {
        $ProductID = $this->input->post('ProductID');
        $productList = $this->utility_model->getAllAdvertiser($ProductID);
        echo "<option value=''></option>";
        foreach ($productList as $key => $val)
            echo "<option value='" . $val['id'] . "'>" . $val['advertiser'] . "</option>";
    }

    function getActiveAdvertiser()
    {
        $ProductID = $this->input->post('ProductID');
        $productList = $this->utility_model->getAdvertiser($ProductID, 1);
        echo "<option value=''></option>";
        foreach ($productList as $key => $val)
            echo "<option value='" . $val['id'] . "'>" . $val['advertiser'] . "</option>";
    }

    function getBrand()
    {
        $AdvertiserID = $this->input->post('AdvertiserID');
        $brandList = $this->utility_model->getBrand($AdvertiserID);
        echo "<option value=''></option>";
        foreach ($brandList as $key => $val)
            echo "<option value='" . $val['id'] . "'>" . $val['brand'] . "</option>";
    }

    function getAgencyDisplays()
    {
        $agency_id = $this->input->post('agency_id');
        $agencyDisplayList = $this->utility_model->getAgencyDisplays($agency_id);
        echo "<option value=''>New</option>";
        foreach ($agencyDisplayList as $key => $val)
            echo "<option value='" . $val['agency_display_name'] . "'>" . $val['agency_display_name'] . "</option>";
    }

    function getAgencyDetails()
    {
        $agencyDisplay = $this->input->post('agency_dis_name');
        $agencyDetails = $this->am_model->get_new_agency_details($agencyDisplay);
        echo json_encode($agencyDetails);
    }

    function getAdvertiserDetails()
    {
        $advertiser_display_name = $this->input->post('advertiser_display_name');
        $advertiserDetails = $this->utility_model->get_advertiser_details($advertiser_display_name);
        echo json_encode($advertiserDetails);
    }

    //function to check whether user is already logged in or not

    function getAdvertiserDisplays()
    {
        $advertiser_id = $this->input->post('advertiser_id');
        $advertiserDisplayList = $this->utility_model->getAdvertiserDisplays($advertiser_id);
        echo "<option value=''>New</option>";
        foreach ($advertiserDisplayList as $key => $val)
            echo "<option value='" . $val['advertiser_display_name'] . "'>" . $val['advertiser_display_name'] . "</option>";
    }

    function is_logged_in($javascript_redirect = 0)
    {
        $logged_in_user = $this->session->userdata("logged_in_user");
        $logged_in_user = $logged_in_user[0];
        if (!isset($logged_in_user) || empty($logged_in_user)) {

            if ($javascript_redirect == '1') {
                echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/login";</script>';
            } else {
                redirect("/");
            }
        } else {
            $this->is_menu_hit();
        }
    }

    public function is_menu_hit()
    {
        $is_hit = $this->ro_model->is_menu_hit();
        if ($is_hit) {
            $this->find_data_for_cache();
        }
    }

    public function find_data_for_cache()
    {
        $user_id = $this->get_user_id();

        $confirmation_data = $this->ro_model->get_data_for_confirmation_customer($user_id);
        foreach ($confirmation_data as $val) {
            $order_id_ser = $val['order_id'];
            $confirmation_id = $val['confirmation_id'];
            $order_id = unserialize($order_id_ser);

            $key_channel = 'add_cancel_channel_' . $user_id . "_" . $order_id;;
            $key_posted = "loadSave_" . $user_id . "_" . $order_id;

            $this->delete_from_cache($key_channel);
            $this->delete_from_cache($key_posted);

            $this->ro_model->delete_from_confirmation($order_id_ser);
            $this->ro_model->delete_from_confirmation_customer($confirmation_id, $user_id);
        }
    }

    public function getBRAdvertisers()
    {

        $timeStamp = date("d-m-Y H:i:s");
        $api['appkey'] = APPKEY;
        $api['authkey'] = apiAuth(array("timestamp" => $timeStamp, "appkey" => APPKEY), "AdAgencyList");
        $api['timestamp'] = $timeStamp;
        $api['user_id'] = 158;
        $api['customer_id'] = $this->input->post('customerId');
        $url = INGESTXPRESS_API . "/AdAgencyList";
        $_ch = curl_init();
        curl_setopt($_ch, CURLOPT_URL, $url);
        curl_setopt($_ch, CURLOPT_POST, 1);
        curl_setopt($_ch, CURLOPT_POSTFIELDS, http_build_query(array("request" => json_encode($api))));
        curl_setopt($_ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($_ch);
        curl_close($_ch);
        /* $result = '{
    "status": "success",
    "data": [{
        "advertiser_id": "112",
        "advertiser_name": "10paisa.com",
        "brand": [{
            "brand_id": "272",
            "brand_name": "10paisa.com",
            "sub_brand": []
        },{
                        "brand_id": "274",
                        "brand_name": "10paisa1.com",
                        "sub_brand": []
                }]
    }, {
        "advertiser_id": "114",
        "advertiser_name": "123 Health Studio",
        "brand": [{
            "brand_id": "274",
            "brand_name": "123 Health Studio",
            "sub_brand": []
        }]
    }]
}'; */
        $this->advBrandArr = json_decode($result, true);

        if ($this->advBrandArr['status'] == 'success') {
            echo json_encode($this->advBrandArr['data']);
        } else {
            echo json_encode(array());
        }
    }

    public function post_add_br_brand()
    {
        $mg_advId = $this->input->post('mg_brand_advertiser');
        $br_advId = $this->input->post('br_advertiser');
        $mg_BrandId = $this->input->post('mg_brand_brand');
        $br_BrandId = $this->input->post('br_brand');
        $oldMappedbr_BrandId = $this->input->post('hidMappedBROLDAdvID');
        if ($oldMappedbr_BrandId != '') {
            $this->utility_model->unMapMgAdvertiser($mg_advId);
            $this->utility_model->unMapMgAdvertiserBrand($mg_advId);
        }
        $mappedMGAdvInfo = $this->utility_model->getMGMapInfo($br_advId);
        if (count($mappedMGAdvInfo) > 0) {
            $mappedMGAdvId = $mappedMGAdvInfo[0]['id'];
            if ($mappedMGAdvId != $mg_advId) {
                $this->utility_model->unMapMgAdvertiser($mappedMGAdvId);
                $this->utility_model->unMapMgAdvertiserBrand($mappedMGAdvId);
            }
        }
        $this->utility_model->updateMappedBrAdv($mg_advId, $br_advId);
        $this->utility_model->updateMappedBrBrand($mg_advId, $mg_BrandId, $br_BrandId);
        redirect("/utility/manager/6");
    }

    public function getBrand_Br()
    {
        $AdvertiserID = $this->input->post('AdvertiserID');
        $retArr['brandList'] = $this->utility_model->getBrandWithoutBr($AdvertiserID);
        $retArr['brbrandList'] = $this->utility_model->getBrandWithBr($AdvertiserID);
        $retArr['mappedBrAdvId'] = $this->utility_model->getmappedBrAdvId($AdvertiserID);

        echo json_encode($retArr);
    }

    public function unMapMGAdvertiser()
    {
        $mg_advId = $this->input->post('unMapMgAdvId');
        $this->utility_model->unMapMgAdvertiser($mg_advId);
        $this->utility_model->unMapMgAdvertiserBrand($mg_advId);
    }

    public function getAllBrand()
    {
        $AdvertiserID = $this->input->post('AdvertiserID');
        $brandList = $this->utility_model->getBrand($AdvertiserID);
        echo "<option value='-1'>-- select --</option>";
        foreach ($brandList as $key => $val)
            echo "<option value='" . $val['id'] . "'>" . $val['brand'] . "</option>";
    }

    public function getOnlyAdvertisers()
    {
        $retArr = array();
        $retArr['status'] = false;
        $advertiserName = $this->input->get('advertiser_name');
        $advertiserFetched = $this->utility_model->getOnlyAdvertisersBYName($advertiserName);
        if (count($advertiserFetched) > 0) {
            $retArr['status'] = false;
        } else {
            $retArr['status'] = true;

        }
        echo json_encode($retArr);
    }

    public function getAllAdvertisersList()
    {

        $ProductID = $this->input->get('productId');
        $advertiserListFromProduct = $this->utility_model->getAllAdvertiser($ProductID);
        $allAdvertiserList = $this->utility_model->getAllAdvertiserList();
        echo json_encode(array('advertiserListFromProduct' => $advertiserListFromProduct, 'allAdvertiserList' => $allAdvertiserList));
    }

    public function getAllAdvertiserDisplayNames()
    {
        $advertiser_name = $this->input->get('advertiser_name');
        //echo $advertiser_name;exit;
        $allAdvertiserDisplayList = $this->utility_model->getAllAdvertiserDisplayNames($advertiser_name);
        echo json_encode($allAdvertiserDisplayList);
    }

    public function checkBrandNameExist()
    {
        $brand_name = $this->input->get('brand_name');
        $brandFetched = $this->utility_model->getOnlyBrandsBYName($brand_name);
        if (count($brandFetched) > 0) {
            $retArr['status'] = true;
        } else {
            $retArr['status'] = false;

        }
        echo json_encode($retArr);
    }


}

?>
