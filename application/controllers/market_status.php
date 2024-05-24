<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Market_status extends CI_Controller
{

    private $dataArray = array();
    private $totalColumnCountArray = array();

    public function __construct()
    {

        parent::__construct();
        $this->load->model('market_status_model');
        $this->load->model("menu_model");
        $this->load->library('session');
    }

    public function index()
    {


        $this->setDataArrayAndCountArray();

        $logged_in = $this->session->userdata("logged_in_user");

        $data["dataArray"] = $this->dataArray;
        $data["totalColumnCountArray"] = $this->totalColumnCountArray;
        $data["menu"] = $this->menu_model->get_header_menu();
        $data['logged_in_user'] = $logged_in[0];

        $this->load->view('marketStatus', $data);
    }

    function setDataArrayAndCountArray()
    {

        $today = date("Y-m-d");
        $week = date('Y-m-d', strtotime($today . " -7 day"));
        $month = date('Y-m-d', strtotime($today . " -1 month"));

        $todayStatusObject = $this->getMarketStatus($today . " 00:00:00", $today . " 23:59:59");

        $todayStatusArray = $todayStatusObject->result();
        $this->buildTodayDataArray($todayStatusArray);

        $this->freeObjectResult($todayStatusObject);

        $weekStatusObject = $this->getMarketStatus($week . " 23:59:59", $today . " 00:00:00");

        $weekStatusArray = $weekStatusObject->result();
        $this->buildWeekDataArray($weekStatusArray);

        $this->freeObjectResult($weekStatusObject);

        $monthStatusObject = $this->getMarketStatus($month . " 23:59:59", $today . " 00:00:00");

        $monthStatusArray = $monthStatusObject->result();
        $this->buildMonthDataArray($monthStatusArray);

        $this->freeObjectResult($monthStatusObject);

        $this->buildCountDataArray();

    }

    function getMarketStatus($startDate, $endDate)
    {

        $resultArray = $this->market_status_model->getMarketStatus($startDate, $endDate);

        return $resultArray;
    }

    function buildTodayDataArray($todayArray)
    {

        foreach ($todayArray as $key => $value) {

            $this->dataArray[$value->market_id]['details']['market_name'] = $value->sw_market_name;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['channel_name'] = $value->channel_name;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['tv_channel_id'] = $value->tv_channel_id;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['display_name'] = $this->getDisplayName($value->display_name, $value->locale);
            $this->dataArray[$value->market_id][$value->tv_channel_id]['today_pinging_channel_status'] = $value->pinging_channel_id;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['today_reporting_channel_status'] = $value->reporting_channel_id;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['today_ro_channel_status'] = $value->ro_channel_id;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['today_deployment_status'] = $value->deployment_status;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['today_in_deployment_ro_status'] = $value->in_deployment_and_ro_status;

            $this->dataArray[$value->market_id]['details']['today_ping_count'] += $value->pinging_channel_id;
            $this->dataArray[$value->market_id]['details']['today_report_count'] += $value->reporting_channel_id;
            $this->dataArray[$value->market_id]['details']['today_ro_count'] += $value->ro_channel_id;
            $this->dataArray[$value->market_id]['details']['today_deployed_count'] += $value->deployment_status;
            $this->dataArray[$value->market_id]['details']['today_in_deployed_ro_count'] += $value->in_deployment_and_ro_status;

        }
    }

    function getDisplayName($displayName, $locale)
    {

        $ReturnDisplayName = "";

        if (!empty($displayName) && !empty($locale)) {

            $ReturnDisplayName = $displayName . "-" . $locale;

        } elseif (!empty($displayName)) {

            $ReturnDisplayName = $displayName;

        }

        return $ReturnDisplayName;
    }

    function freeObjectResult($object)
    {

        $object->next_result();
        $object->free_result();

    }

    function buildWeekDataArray($weekArray)
    {

        foreach ($weekArray as $key => $value) {

            $this->dataArray[$value->market_id]['details']['market_name'] = $value->sw_market_name;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['channel_name'] = $value->channel_name;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['tv_channel_id'] = $value->tv_channel_id;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['display_name'] = $this->getDisplayName($value->display_name, $value->locale);
            $this->dataArray[$value->market_id][$value->tv_channel_id]['week_pinging_channel_status'] = $value->pinging_channel_id;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['week_reporting_channel_status'] = $value->reporting_channel_id;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['week_ro_channel_status'] = $value->ro_channel_id;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['week_deployment_status'] = $value->deployment_status;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['week_in_deployment_ro_status'] = $value->in_deployment_and_ro_status;

            $this->dataArray[$value->market_id]['details']['week_ping_count'] += $value->pinging_channel_id;
            $this->dataArray[$value->market_id]['details']['week_report_count'] += $value->reporting_channel_id;
            $this->dataArray[$value->market_id]['details']['week_ro_count'] += $value->ro_channel_id;
            $this->dataArray[$value->market_id]['details']['week_deployed_count'] += $value->deployment_status;
            $this->dataArray[$value->market_id]['details']['week_in_deployed_ro_count'] += $value->in_deployment_and_ro_status;

        }
    }

    function buildMonthDataArray($monthArray)
    {

        foreach ($monthArray as $key => $value) {

            $this->dataArray[$value->market_id]['details']['market_name'] = $value->sw_market_name;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['channel_name'] = $value->channel_name;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['tv_channel_id'] = $value->tv_channel_id;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['display_name'] = $this->getDisplayName($value->display_name, $value->locale);
            $this->dataArray[$value->market_id][$value->tv_channel_id]['month_pinging_channel_status'] = $value->pinging_channel_id;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['month_reporting_channel_status'] = $value->reporting_channel_id;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['month_ro_channel_status'] = $value->ro_channel_id;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['month_deployment_status'] = $value->deployment_status;
            $this->dataArray[$value->market_id][$value->tv_channel_id]['month_in_deployment_ro_status'] = $value->in_deployment_and_ro_status;

            $this->dataArray[$value->market_id]['details']['month_ping_count'] += $value->pinging_channel_id;
            $this->dataArray[$value->market_id]['details']['month_report_count'] += $value->reporting_channel_id;
            $this->dataArray[$value->market_id]['details']['month_ro_count'] += $value->ro_channel_id;
            $this->dataArray[$value->market_id]['details']['month_deployed_count'] += $value->deployment_status;
            $this->dataArray[$value->market_id]['details']['month_in_deployed_ro_count'] += $value->in_deployment_and_ro_status;

        }
    }

    function buildCountDataArray()
    {

        foreach ($this->dataArray as $marketId => $row) {

            $this->totalColumnCountArray['total']['today_ping_count'] += $row['details']['today_ping_count'];
            $this->totalColumnCountArray['total']['today_report_count'] += $row['details']['today_report_count'];
            $this->totalColumnCountArray['total']['today_ro_count'] += $row['details']['today_ro_count'];
            $this->totalColumnCountArray['total']['today_deployed_count'] += $row['details']['today_deployed_count'];
            $this->totalColumnCountArray['total']['today_in_deployed_ro_count'] += $row['details']['today_in_deployed_ro_count'];


            $this->totalColumnCountArray['total']['week_ping_count'] += $row['details']['week_ping_count'];
            $this->totalColumnCountArray['total']['week_report_count'] += $row['details']['week_report_count'];
            $this->totalColumnCountArray['total']['week_ro_count'] += $row['details']['week_ro_count'];
            $this->totalColumnCountArray['total']['week_deployed_count'] += $row['details']['week_deployed_count'];
            $this->totalColumnCountArray['total']['week_in_deployed_ro_count'] += $row['details']['week_in_deployed_ro_count'];

            $this->totalColumnCountArray['total']['month_ping_count'] += $row['details']['month_ping_count'];
            $this->totalColumnCountArray['total']['month_report_count'] += $row['details']['month_report_count'];
            $this->totalColumnCountArray['total']['month_ro_count'] += $row['details']['month_ro_count'];
            $this->totalColumnCountArray['total']['month_deployed_count'] += $row['details']['month_deployed_count'];
            $this->totalColumnCountArray['total']['month_in_deployed_ro_count'] += $row['details']['month_in_deployed_ro_count'];

        }

    }

    function getCsv()
    {

        $this->setDataArrayAndCountArray();

        header('Expires:0');
        header('Cache-control: private');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-disposition: attachment; filename=marketStatus.csv');

        $header = 'Market,Channel details,,, 	Today, , ,,, 	Last Week,  ,,,,	Last Month ';
        $subHeader = '#, Id, Name, Display Name, In Ro, Deployment,In Ro & Deployment,Pinging, Reporting, In Ro, Deployment, In Ro & Deployment,Pinging, Reporting, In Ro,Deployment,In Ro & Deployment, Pinging,Reporting,';
        $csv_content = '';
        ob_clean();
        echo $header;
        echo PHP_EOL . $subHeader;

        foreach ($this->dataArray as $key => $row) {

            $csv_content = PHP_EOL;

            $csv_content .= $row['details']['market_name'];
            $csv_content .= "," . '#';
            $csv_content .= "," . '#';
            $csv_content .= "," . '#';
            $csv_content .= "," . $row['details']['today_ro_count'];
            $csv_content .= "," . $row['details']['today_deployed_count'];
            $csv_content .= "," . $row['details']['today_in_deployed_ro_count'];
            $csv_content .= "," . $row['details']['today_ping_count'];
            $csv_content .= "," . $row['details']['today_report_count'];

            $csv_content .= "," . $row['details']['week_ro_count'];
            $csv_content .= "," . $row['details']['week_deployed_count'];
            $csv_content .= "," . $row['details']['week_in_deployed_ro_count'];
            $csv_content .= "," . $row['details']['week_ping_count'];
            $csv_content .= "," . $row['details']['week_report_count'];

            $csv_content .= "," . $row['details']['month_ro_count'];
            $csv_content .= "," . $row['details']['month_deployed_count'];
            $csv_content .= "," . $row['details']['month_in_deployed_ro_count'];
            $csv_content .= "," . $row['details']['month_ping_count'];
            $csv_content .= "," . $row['details']['month_report_count'];


            foreach ($row as $fKey => $value) {

                if ($fKey != "details") {

                    $csv_content .= PHP_EOL;
                    $csv_content .= "#";
                    $csv_content .= "," . $value["tv_channel_id"];
                    $csv_content .= "," . $value['channel_name'];
                    $csv_content .= "," . $value['display_name'];

                    $csv_content .= "," . $value['today_ro_channel_status'];
                    $csv_content .= "," . $value['today_deployment_status'];
                    $csv_content .= "," . $value['today_in_deployment_ro_status'];
                    $csv_content .= "," . $value['today_pinging_channel_status'];
                    $csv_content .= "," . $value['today_reporting_channel_status'];

                    $csv_content .= "," . $value['week_ro_channel_status'];
                    $csv_content .= "," . $value['week_deployment_status'];
                    $csv_content .= "," . $value['week_in_deployment_ro_status'];
                    $csv_content .= "," . $value['week_pinging_channel_status'];
                    $csv_content .= "," . $value['week_reporting_channel_status'];

                    $csv_content .= "," . $value['month_ro_channel_status'];
                    $csv_content .= "," . $value['month_deployment_status'];
                    $csv_content .= "," . $value['month_in_deployment_ro_status'];
                    $csv_content .= "," . $value['month_pinging_channel_status'];
                    $csv_content .= "," . $value['month_reporting_channel_status'];

                }

            }
            echo $csv_content . PHP_EOL;
        }
        $csv_content = "";
        foreach ($this->totalColumnCountArray as $total) {

            $csv_content .= PHP_EOL;

            $csv_content .= "Total";
            $csv_content .= ",#";
            $csv_content .= ",#";
            $csv_content .= ",#";
            $csv_content .= "," . $total['today_ro_count'];
            $csv_content .= "," . $total['today_deployed_count'];
            $csv_content .= "," . $total['today_in_deployed_ro_count'];
            $csv_content .= "," . $total['today_ping_count'];
            $csv_content .= "," . $total['today_report_count'];

            $csv_content .= "," . $total['week_ro_count'];
            $csv_content .= "," . $total['week_deployed_count'];
            $csv_content .= "," . $total['week_in_deployed_ro_count'];
            $csv_content .= "," . $total['week_ping_count'];
            $csv_content .= "," . $total['week_report_count'];

            $csv_content .= "," . $total['month_ro_count'];
            $csv_content .= "," . $total['month_deployed_count'];
            $csv_content .= "," . $total['month_in_deployed_ro_count'];
            $csv_content .= "," . $total['month_ping_count'];
            $csv_content .= "," . $total['month_report_count'];
        }
        echo $csv_content;
    }
}

?>
