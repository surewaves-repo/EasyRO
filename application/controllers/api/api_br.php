<?php
require APPPATH . '/libraries/REST_Controller.php';

class API_br extends REST_Controller
{
    private $_return;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper("hash_api");
        $this->load->model('api_model');
    }

    public function getAllMgChannels_post()
    {
        $input_values = json_decode($this->post("request"), true);
        if (checkInputs($input_values)) {
            $auth = apiAuth($input_values, "getAllMgChannels");
            if ($auth == $input_values['authkey']) {
                $all_channels = $this->api_model->fetchMgChannels();
                if (count($all_channels) > 0) {
                    echo json_encode(array("status" => "success", "data" => $all_channels));
                } else {
                    echo json_encode(array("status" => "failed", "data" => $all_channels));
                }
            } else {
                $this->_return = array("status" => "error", "data" => "Authentication Failed !!!");
            }
        } else {
            $this->_return = array("status" => "error", "data" => " Input values missing!!");
        }
        $this->response($this->_return);
    }

    public function updateBrChannelBasedInfo_post()
    {
        $input_values = json_decode($this->post("request"), true);
        log_message('info', 'check arr=>' . $this->post("request"));
        if (checkInputs($input_values)) {
            $auth = apiAuth($input_values, "updateBrChannelBasedInfo");
            if ($auth == $input_values['authkey']) {
                $ret = $this->api_model->updateBrChannelInfo($input_values['data']);
                if ($ret) {
                    $this->_return = array("status" => "success", "data" => 'Data Saved Successfully');
                } else {
                    $this->_return = array("status" => "failed", "data" => 'Failed to save Data');
                }
            } else {
                $this->_return = array("status" => "error", "data" => "Authentication Failed !!!");
            }
        } else {
            $this->_return = array("status" => "error", "data" => " Input values missing!!");
        }
        $this->response($this->_return);
    }

    /**
     * FUnction to updload the AdLogs
     * @params
     * authkey
     * customer_id
     * JSON data
     * Broadcast Reporter
     */
    public function updatePOPFromAdLog_post()
    {
        $input_values = json_decode($this->post("request"), true);
        if (checkInputs($input_values)) {
            $auth = apiAuth($input_values, "updatePOPFromAdLog");
            if ($auth == $input_values['authkey']) {
                $ret = $this->api_model->updatePOPFromAdLog($input_values['data']);
                if ($ret) {
                    $this->_return = array("status" => "success", "data" => 'Data Saved Successfully');
                } else {
                    $this->_return = array("status" => "failed", "data" => 'Failed to save Data');
                }
            } else {
                $this->_return = array("status" => "error", "data" => "Authentication Failed !!!");
            }
        } else {
            $this->_return = array("status" => "error", "data" => " Input values missing!!");
        }
        $this->response($this->_return);
    }
}

?>
