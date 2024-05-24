<?php

class Br_Cron_job extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ro_model');
        $this->load->model('mg_model');
        $this->load->model('user_model');
        $this->load->model('am_model');
        $this->load->model('api_model');
        $this->load->helper('url');
        $this->load->helper("generic");
        $this->load->helper("common");
        $this->load->library("parse_ro_schedule");
        if (ENABLE_PROFILER == '1') {
            $this->output->enable_profiler("true");
        }
    }

    public function index()
    {
    }

    public function map_MG_RO_to_Br()
    {
        $this->load->library('Cron_Lock_Manager');
        $isLock = $this->lock('map_MG_RO_to_BR');
        if ($isLock) {
            $resBrUserCustomerInfo = $this->api_model->manageCustomerIdUserId();
            echo "customer=";
            print_r($resBrUserCustomerInfo);
            if (count($resBrUserCustomerInfo) > 0) {
                $br_customer_id = $resBrUserCustomerInfo['br_customer_id'];
                $br_user_id = $resBrUserCustomerInfo['br_user_id'];

                $this->load->helper("hash_api");
                $internal_ros = $this->mg_model->getInternalRoNumber();// get the pending ro_id;
                $languageArr = $this->api_model->getLangaugeIds($br_customer_id, $br_user_id);
                foreach ($internal_ros as $eachInternalRo) {
                    echo "eachInternalRo---" . $eachInternalRo['internal_ro'] . "<br />";
                    $successPushedArr = array();
                    $internal_ro_number = $eachInternalRo['internal_ro'];
                    $networks = $this->mg_model->get_networks_for_ro_br($internal_ro_number);    // get all network id which are mapped with br_channels
                    echo "<br /> networks <pre>";
                    print_r($networks);

                    // update status = processing
                    if (count($networks) > 0) {
                        $failedContent = false;
                        $this->parse_ro_schedule->setRoStatus($eachInternalRo['ro_id'], 'processing');//-- to processing;
                        foreach ($networks as $net) {
                            $order_id = rtrim(base64_encode($net['internal_ro_number']), '=');
                            $network_id = $net['customer_id'];
                            $report = $this->ro_pdf_report($order_id, $network_id);//mani sir array only with br channel details
                            echo "<br /> report <pre>";
                            print_r($report);
                            if (count($report) > 0) {
                                $responseArr = $this->parse_ro_schedule->parseAndMakeRoScheduleAsPerBR($report);// it will make ro schedule and content array
                            }
                            if ($responseArr['status'] == 'error') {
                                email_send(BR_MAPPING_MAIL, '', 'br_update_content', '', array('ERROR' => json_encode($responseArr['data'])));
                                $this->parse_ro_schedule->setRoStatus($eachInternalRo['ro_id'], 'error');
                                exit;
                            }
                        }
                        $changeCaptionNameArr = array();
                        $contentArr = $this->parse_ro_schedule->getContents();
                        echo "<br />contentArr=";
                        print_r($contentArr);
                        foreach ($contentArr as $contentId => $contentDetails) {//parsing each content
                            $content_url = $contentDetails['content_url'];
                            $fileName = substr($content_url, (strrpos($content_url, '/') + 1), strlen($content_url));
                            $fileExt = substr($content_url, (strrpos($content_url, '.') + 1), strlen($content_url));
                            $localServer_path = DOCUMENT_ROOT . 'surewaves_easy_ro/ro_content_files/' . $fileName;
                            $ret = $this->downloadContent($fileName, $localServer_path);
                            if ($ret) {// convert it using ffmpeg
                                $outputFile = DOCUMENT_ROOT . 'surewaves_easy_ro/ro_content_files/ffmpeg_dir/' . $fileName;
                                $conversionFFMPEGStat = $this->convertFileThroughFFMPEG($localServer_path, $outputFile);
                                if ($conversionFFMPEGStat) {
                                    $content_duration = $this->getContentDuration($outputFile);
                                    $status = $this->convertToZip($outputFile);
                                    echo "zip results = <pre>";
                                    print_r($status);
                                    if ($status['set']) {
                                        $outputZipFilePath = $status['url'];
                                        $zip_md5 = md5_file($outputZipFilePath);
                                        $uploadStat = $this->sendContentToS3($outputZipFilePath);// send the converted file to s3.
                                        echo "s3 upload status = <pre>";
                                        print_r($uploadStat);
                                        if ($uploadStat['status']) {

                                            $file_md5 = md5_file($outputFile);
                                            $file_size = filesize($outputFile);
                                            $contentDetails['data']['content_path'] = $uploadStat['url'];
                                            $contentDetails['data']['content_md5'] = $file_md5;
                                            $contentDetails['data']['content_file_size'] = $file_size;
                                            $contentDetails['data']['content_file_ext'] = $fileExt;
                                            $contentDetails['data']['mg_content_id'] = $contentId;//
                                            $contentDetails['data']['zip_md5'] = $zip_md5;
                                            $contentDetails['data']['sub_brand'] = '';
                                            $contentDetails['data']['content_duration'] = $content_duration;
                                            $contentDetails['data']['language_id'] = $this->getlanguageID($contentDetails['data']['language'], $languageArr);
                                            $contentDetails['data']['tape_id'] = str_replace(" ", "_", $contentDetails['data']['content_caption']);

                                            $timeStamp = date("d-m-Y H:i:s");
                                            $contentDataArr['user_id'] = $br_user_id;// this will be 	decided 	after-wards
                                            $contentDataArr['customer_id'] = $br_customer_id;
                                            $contentDataArr['timestamp'] = $timeStamp;
                                            $contentDataArr['appkey'] = APPKEY;
                                            $contentExistDataArr = $contentDataArr;
                                            $contentExistDataArr['authkey'] = apiAuth(array("timestamp" => $timeStamp, "appkey" => APPKEY), "ContentMd5CaptionCheck");
                                            $contentDataArr['authkey'] = apiAuth(array("timestamp" => $timeStamp, "appkey" => APPKEY), "UpdateContentDetails");
                                            $contentCheckingParam = array();
                                            $contentCheckingParam['content_md5'] = $file_md5;
                                            $contentCheckingParam['content_caption'] = $contentDetails['data']['content_caption'];
                                            $contentCheckingParam['advertiser_id'] = $contentDetails['data']['advertiser'];

                                            echo "<pre>";
                                            print_r($contentDetails);;
                                            $contentExistArr = $this->checkContentExistInBR($contentExistDataArr, $contentCheckingParam, $isfromEasyRo = 1);
                                            $isUploadContentRequired = true;

                                            if ($contentExistArr['status'] == 'error') {
                                                //
                                                if ($contentExistArr['is_caption_exists'] == 1 && $contentExistArr['is_md5_exists'] == 0) {// caption exist not md5

                                                    $contentDetails['data']['content_caption'] = $contentExistArr['content_caption'] . strtotime(date("Y-m-d H:i:s.u"));
                                                    $isUploadContentRequired = true;
                                                    $changeCaptionNameArr[$contentId] = $contentDetails['data']['content_caption'];
                                                }
                                                if ($contentExistArr['is_caption_exists'] == 0 && $contentExistArr['is_md5_exists'] == 1) {// md5 exist not caption
                                                    $isUploadContentRequired = false;
                                                    $contentDetails['data']['content_caption'] = $contentExistArr['content_caption'];
                                                    $changeCaptionNameArr[$contentId] = $contentDetails['data']['content_caption'];
                                                }
                                                if ($contentExistArr['is_caption_exists'] == 1 && $contentExistArr['is_md5_exists'] == 1) {
                                                    echo "<br>----inside-- </br>";
                                                    $isUploadContentRequired = false;
                                                }
                                                if (array_key_exists('content_id', $contentExistArr) && array_key_exists('content_path', $contentExistArr)) {
                                                    if (!array_key_exists($contentId, $successPushedArr)) {
                                                        $successPushedArr['content'][$contentId]['br_content_id'] = $contentExistArr['content_id'];
                                                        $successPushedArr['content'][$contentId]['channel'] = $contentDetails['channel'];
                                                    }
                                                }
                                            }
                                            if ($isUploadContentRequired) {// whether to upload the content or not

                                                $contentDataArr['data'][0] = $contentDetails['data'];
                                                echo "content api arr =" . json_encode($contentDataArr);

                                                $url = INGESTXPRESS_API . "/UpdateContentDetails";
                                                $_ch = curl_init();
                                                curl_setopt($_ch, CURLOPT_URL, $url);
                                                curl_setopt($_ch, CURLOPT_POST, 1);
                                                curl_setopt($_ch, CURLOPT_POSTFIELDS, http_build_query(array("request" => json_encode($contentDataArr))));
                                                curl_setopt($_ch, CURLOPT_RETURNTRANSFER, true);
                                                $result = curl_exec($_ch);
                                                curl_close($_ch);
                                                $br_contentArr = json_decode($result, true);// expected array be array('generated unique_id' => 'br_content_id')
                                                echo "content api arr res=";
                                                print_r($br_contentArr);
                                                if ($br_contentArr['status'] == "success") {
                                                    if (!array_key_exists($contentId, $successPushedArr)) {
                                                        $successPushedArr['content'][$contentId]['br_content_id'] = $br_contentArr['contents']['content_id'];
                                                        $successPushedArr['content'][$contentId]['channel'] = $contentDetails['channel'];
                                                    }
                                                } else {
                                                    $error = array();
                                                    $error = $br_contentArr;
                                                    $error['internal_ro'] = $eachInternalRo['internal_ro'];
                                                    email_send(BR_MAPPING_MAIL, '', 'br_update_content', '', array('ERROR' => json_encode($error)));
                                                    $this->parse_ro_schedule->setRoStatus($eachInternalRo['ro_id'], 'error');
                                                    $failedContent = true;
                                                    break;
                                                }
                                            }    // end of content upload
                                        }// end of upload to s3
                                    } else {
                                        echo "failed to zip the content = $outputFile.<br />";
                                        $error = array();
                                        $error['status'] = 'error';
                                        $error['data'] = "failed to zip the content = $outputFile.";
                                        email_send(BR_MAPPING_MAIL, '', 'br_update_content', '', array('ERROR' => json_encode($error)));
                                        $this->parse_ro_schedule->setRoStatus($eachInternalRo['ro_id'], 'error');
                                        $failedContent = true;
                                        break;
                                    }// end of zip conversion
                                } else {
                                    echo "failed to convert ffmpeg the contents = $localServer_path , $outputFile <br />";
                                    $error = array();
                                    $error['status'] = 'error';
                                    $error['data'] = "failed to convert ffmpeg the contents = $localServer_path , $outputFile.";
                                    email_send(BR_MAPPING_MAIL, '', 'br_update_content', '', array('ERROR' => json_encode($error)));
                                    $this->parse_ro_schedule->setRoStatus($eachInternalRo['ro_id'], 'error');
                                    $failedContent = true;
                                    break;
                                }//end of ffmpeg conversion
                            } else {
                                $error = array();
                                $error['status'] = 'error';
                                $error['data'] = "failed to download the content $content_url ";
                                $error['internal_ro'] = $eachInternalRo['internal_ro'];
                                email_send(BR_MAPPING_MAIL, '', 'br_update_content', '', array('ERROR' => json_encode($error)));
                                $this->parse_ro_schedule->setRoStatus($eachInternalRo['ro_id'], 'error');
                                $failedContent = true;
                                break;
                                //exit;
                            }// end of content download
                        }/// end of content arr parsing
                        //echo "<pre>";print_r($successPushedArr);
                        if (!$failedContent) {
                            $parsedroScheduleArr = $this->parse_ro_schedule->getRoScheduleAsPerBR();
                            //echo "before parse ---<pre>";print_r($parsedroScheduleArr);
                            //echo "<br/> ---- <pre>";print_r($changeCaptionNameArr);
                            if (count($changeCaptionNameArr) > 0) {
                                $parsedroScheduleArr = $this->parse_ro_schedule->parseROScheduleAndChangeCaptionName($changeCaptionNameArr, $parsedroScheduleArr);
                            }
                            echo "after parse<pre>";
                            print_r($parsedroScheduleArr);//exit;
                            $timeStamp = date("d-m-Y H:i:s");
                            $roScheduleArr['user_id'] = $br_user_id;// this will be decided after-wards
                            $roScheduleArr['customer_id'] = $br_customer_id;
                            $roScheduleArr['timestamp'] = $timeStamp;
                            $roScheduleArr['appkey'] = APPKEY;
                            $roScheduleArr['authkey'] = apiAuth(array("timestamp" => $timeStamp, "appkey" => APPKEY), "RoSchedule");

                            $roScheduleArr['data'][0] = $parsedroScheduleArr;
                            echo "ro schedule arr =";
                            print_r($roScheduleArr);
                            $url = INGESTXPRESS_API . "/RoSchedule";
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("request" => json_encode($roScheduleArr))));
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $result = curl_exec($ch);
                            curl_close($ch);
                            $ro_Schedule = json_decode($result, true);//
                            echo "ro schedule arr res=";
                            print_r($ro_Schedule);
                            if ($ro_Schedule['status'] == 'success') {
                                $successPushedArr['ro']['mg_ro_id'] = $eachInternalRo['ro_id'];
                                $successPushedArr['ro']['br_ro_id'] = $ro_Schedule['ro_id'];
                                echo "<pre>";
                                print_r($successPushedArr);
                                $this->manageBROperationOntoDB($successPushedArr);
                                $this->parse_ro_schedule->setRoStatus($eachInternalRo['ro_id'], 'completed');
                            } else {
                                $error = array();
                                $error = $ro_Schedule;
                                $error['internal_ro'] = $eachInternalRo['internal_ro'];
                                email_send(BR_MAPPING_MAIL, '', 'br_ro_schdule', '', array('ERROR' => json_encode($error)));
                                $this->parse_ro_schedule->setRoStatus($eachInternalRo['ro_id'], 'error');
                                //exit;
                                continue;
                            }// end of ro uploading to BR
                        }// end of choosing whether to upload ro on content basis.
                    }// decides on available networks
                }// end of queing of ros.
            }// end of customer data fetched from br customer api.
            $this->cron_lock_manager->unlock('map_MG_RO_to_BR');
        }
    }

    public function lock($functionName)
    {
        $this->load->library('Cron_Lock_Manager');
        if ($this->cron_lock_manager->lock($functionName) !== FALSE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function ro_pdf_report($order_id, $network_id)
    {
        $order = $this->ro_model->get_ro_details(base64_decode($order_id));
        $internal_ro_number = base64_decode($order_id);

        $campaigns_v0 = $this->mg_model->get_network_campaigns_new(base64_decode($order_id), $network_id);
        $campaigns_v1 = $this->mg_model->get_network_campaigns_v1_new(base64_decode($order_id), $network_id);

        $campaigns = array_merge($campaigns_v0, $campaigns_v1);
        if (count($campaigns) == 0) {
            if (count($campaigns_v0) > 0) {
                $campaigns = $campaigns_v0;
            } else {
                $campaigns = $campaigns_v1;
            }
        }

        $camp_ids = array();
        $regions = array();
        $network_ro_report_details = array();
        $network_ro_report_details['customer_ro_number'] = $order[0]['customer_ro_number'];
        $network_ro_report_details['internal_ro_number'] = $internal_ro_number;
        $network_ro_report_details['client_name'] = $order[0]['client_name'];
        $network_ro_report_details['agency_name'] = $order[0]['agency_name'];
        foreach ($campaigns as $c) {
            if ($c['enterprise_id'] == $network_id) {
                array_push($regions, $c['screen_region_id']);
            }
        }
        $region_ids = array_unique($regions);
        $region_ids = array_values($region_ids);
        $key = 0;
        $i = 0;
        foreach ($region_ids as $id) {
            foreach ($campaigns as $c) {
                if ($c['screen_region_id'] == $id && $c['enterprise_id'] == $network_id) {
                    $camp_ids[$key][$i] = $c['campaign_id'];
                    $i++;
                }
            }
            $key++;
            $i = 0;
        }
        $model = array();
        $count = count($region_ids);
        if ($count == 2) {
            $noChannelSchedule = false;

            foreach ($region_ids as $key => $id) {

                $channels_summary_v1 = $this->mg_model->get_channel_schedule_summary_v1_br(implode(',', $camp_ids[$key]), $id);
                $channels_summary_v0 = $this->mg_model->get_channel_schedule_summary_br(implode(',', $camp_ids[$key]), $id);
                if (count($channels_summary_v1) <= 0 && count($channels_summary_v0) <= 0) {
                    $noChannelSchedule = true;
                    continue;
                }
                $channels_summary[$key] = array_merge($channels_summary_v0, $channels_summary_v1);

                $channels[$key] = array();
                foreach ($channels_summary[$key] as $chanl) {
                    array_push($channels[$key], $chanl['channel_name']);
                }
                $channel_ids[$key] = array();
                foreach ($channels_summary[$key] as $chanl) {
                    array_push($channel_ids[$key], $chanl['channel_id']);
                }


                $ro_details_v1 = $this->mg_model->get_ro_report_details_v1(implode(',', $camp_ids[$key]), $network_id);
                $ro_details_v0 = $this->mg_model->get_ro_report_details(implode(',', $camp_ids[$key]), $network_id);
                $ro_details[$key] = array_merge($ro_details_v0, $ro_details_v1);

                $nw_channels[$key] = $this->mg_model->get_network_channels(implode(',', $channel_ids[$key]), $network_id);

                $ro_channels[$key] = array();
                $i = 0;
                foreach ($nw_channels[$key] as $key1 => $chnl) {
                    $ro_channels[$key][$i] = $chnl['channel_name'];
                    $i++;
                }
                $nw_ro_channels[$key] = array_unique($ro_channels[$key]);
                $captions_v1 = $this->mg_model->get_captions_under_network_v1(implode(',', $camp_ids[$key]), $network_id);
                $kaptions_v1 = $this->mg_model->get_captions_per_channel_v1(implode(',', $camp_ids[$key]));

                $captions_v0 = $this->mg_model->get_captions_under_network(implode(',', $camp_ids[$key]), $network_id);
                $kaptions_v0 = $this->mg_model->get_captions_per_channel(implode(',', $camp_ids[$key]));

                $captions[$key] = array_merge($captions_v0, $captions_v1);
                $kaptions[$key] = array_merge($kaptions_v0, $kaptions_v1);
                if (count($kaptions[$key]) == 0) {
                    if (count($kaptions_v0) > 0) {
                        $kaptions[$key] = $kaptions_v0;
                    } else {
                        $kaptions[$key] = $kaptions_v1;
                    }
                }

                $caption[$key] = array();
                foreach ($captions[$key] as $key1 => $c) {
                    array_push($caption[$key], $c['caption_name']);
                }

                $timeband_value = $this->ro_model->get_timeband_from_config($network_id);
                $timeband_val = explode(",", $timeband_value['timeband']);
                $data = array();
                for ($i = 0; $i < count($timeband_val); $i++) {
                    $time_val = explode("#", $timeband_val[$i]);

                    $tmp = array();
                    $tmp['start_time'] = $time_val[0];
                    $tmp['end_time'] = $time_val[1];

                    $data[$i] = $tmp;
                }

                $timeband[$key] = $data;

                /*$timeband[$key] = array( "0" => array("start_time"=>'06:00',"end_time"=>'10:00'),
                        "1" => array("start_time"=>'10:01',"end_time"=>'13:00'),
                        "2" => array("start_time"=>'13:01',"end_time"=>'18:00'),
                        "3" => array("start_time"=>'18:01',"end_time"=>'21:00'),
                        "4" => array("start_time"=>'21:01',"end_time"=>'23:59'),
                    ); */

                $dates[$key] = $this->mg_model->get_network_dates_v1(implode(',', $camp_ids[$key]), $network_id);
                $complete_cancellation = false;
                $cancel_start_date = '';
                if (!isset($dates[$key][0]['start_date']) || !isset($dates[$key][0]['end_date'])) {
                    $complete_cancellation = true;
                    $dates[$key] = $this->mg_model->get_network_cancel_dates_v1(implode(',', $camp_ids[0]), $network_id);
                    $cancel_start_date = $dates[0]['start_date'];
                }
            }
            if (!$noChannelSchedule) {
                foreach ($kaptions as $key => $kap) {
                    foreach ($kap as $key1 => $k) {
                        $kaption[$key][$k['channel_name']][] = $k['caption_name'];
                    }
                }
                $temp_start_date = "";
                $temp_end_date = "";
                $i = 0;
                foreach ($dates as $key => $dat) {
                    foreach ($dat as $key1 => $da) {
                        if (empty($da['start_date']) || empty($da['end_date'])) continue;
                        if ($i == 0) {
                            $temp_start_date = $da['start_date'];
                            $temp_end_date = $da['end_date'];
                            $i++;
                        } else {
                            if ($da['start_date'] <= $temp_start_date) {
                                $temp_start_date = $da['start_date'];
                            }
                            if ($da['end_date'] >= $temp_end_date) {
                                $temp_end_date = $da['end_date'];
                            }
                            $i++;
                        }
                    }
                }
                $nw_dates_mail = array("start_date" => $temp_start_date, "end_date" => $temp_end_date);
                $nw_dates = array("start_date" => $temp_start_date, "end_date" => $temp_end_date);
                $month1 = date('m', strtotime($nw_dates['start_date']));
                $month2 = date('m', strtotime($nw_dates['end_date']));
                $year_1 = date('Y', strtotime($nw_dates['start_date']));
                $year_2 = date('Y', strtotime($nw_dates['end_date']));

                if ($year_2 > $year_1) {
                    $year_gap = $year_2 - $year_1;
                    $month2 = $year_gap * 12 + $month2;
                }

                $no_of_months = $month2 - $month1 + 1;
                $start_date = date('Y-m-d', strtotime($nw_dates['start_date']));
                $end_date = date('Y-m-d', strtotime($nw_dates['end_date']));
                $day = 86400; // Day in seconds
                $format = 'Y-m-d'; // Output format (see PHP date funciton)
                $sTime = strtotime($start_date); // Start as time
                $eTime = strtotime($end_date); // End as time
                $numDays = round(($eTime - $sTime) / $day) + 1;
                $days = array();
                for ($d = 0; $d < $numDays; $d++) {
                    $days[] = date($format, ($sTime + ($d * $day)));
                }
                $sTime1 = strtotime($start_date); // Start as time
                $eTime1 = strtotime($end_date); // End as time
                $tmp = date('mY', $eTime1);
                $months[] = date('F', $sTime1);

                while ($sTime1 < $eTime1) {
                    $sTime1 = strtotime(date('Y-m-d', $sTime1) . ' +1 month');
                    if (date('mY', $sTime1) != $tmp && ($sTime1 < $eTime1)) {
                        $months[] = date('F', $sTime1);
                    }
                }
                $months[] = date('F', $eTime1);
                $months_unique = array_unique($months);
                $activity_months = implode(',', $months_unique);
                for ($i = 0; $i < count($region_ids); $i++) {
                    $program_id_channel = array();
                    foreach ($timeband[$i] as $key => $tb) {
                        foreach ($nw_channels[$i] as $key1 => $channel) {
                            $tmp_program_id_channel = array();
                            $channel_name = $channel['channel_name'];
                            foreach ($kaptions[$i] as $key2 => $cap) {
                                $start_time = $tb['start_time'];
                                $end_time = $tb['end_time'];
                                $time = $start_time . '-' . $end_time;

                                if ($cap['channel_name'] == $channel_name) {
                                    $caption_name = $cap['caption_name'];

                                    $summary_v1 = $this->mg_model->get_report_v1(implode(',', $camp_ids[$i]), $network_id, $channel_name, $start_time, $end_time, $caption_name, $program_id_channel[$channel_name]);
                                    $caption_details_v1 = $this->mg_model->get_caption_details_v1(implode(',', $camp_ids[$i]), $network_id, $caption_name);

                                    $summary_v0 = $this->mg_model->get_report(implode(',', $camp_ids[$i]), $network_id, $channel_name, $start_time, $end_time, $caption_name, $program_id_channel[$channel_name]);
                                    $caption_details_v0 = $this->mg_model->get_caption_details(implode(',', $camp_ids[$i]), $network_id, $caption_name);

                                    $summary = array_merge($summary_v0, $summary_v1);
                                    $caption_details = array_merge($caption_details_v0, $caption_details_v1);

                                    $as = array();
                                    foreach ($summary as $su) {
                                        $temp = date('Y-m-d', strtotime($su['date']));
                                        if (array_key_exists($temp, $as)) {
                                            continue;
                                        }
                                        $as[$temp] = $su['impressions'];
                                        if (!isset($tmp_program_id_channel[$channel_name])) {
                                            $tmp_program_id_channel[$channel_name] = array();
                                        }
                                        array_push($tmp_program_id_channel[$channel_name], $su['pu_id']);
                                    }
                                    if ($region_ids[$i] == 1) {
                                        $ad_type = 'Spot Ad';
                                    } elseif ($region_ids[$i] == 3) {
                                        $ad_type = 'Banner Ad';
                                    }
                                    $report[$ad_type][$channel_name][$caption_name][$time]['brand_new'] = $caption_details[0]['brand_new'];
                                    $report[$ad_type][$channel_name][$caption_name][$time]['language'] = $caption_details[0]['Language'];
                                    $report[$ad_type][$channel_name][$caption_name][$time]['ro_duration'] = $caption_details[0]['ro_duration'];
                                    foreach ($days as $day) {
                                        if (isset($as[$day])) {
                                            $report[$ad_type][$channel_name][$caption_name][$time]['date'][$day] = $as[$day];
                                        } else {
                                            $report[$ad_type][$channel_name][$caption_name][$time]['date'][$day] = 0;
                                        }
                                    }
                                } else {

                                }
                            }

                            $program_id_channel[$channel_name] = array_merge($program_id_channel[$channel_name], $tmp_program_id_channel[$channel_name]);
                            $program_id_channel[$channel_name] = array_unique($program_id_channel[$channel_name]);
                        }
                    }
                }
                $reports = array();
                foreach ($report as $ad => $adtype) {
                    foreach ($adtype as $chnl => $channel) {
                        foreach ($channel as $cap => $caption) {
                            foreach ($caption as $tb => $timeband) {

                                $reports[] = array_merge(array("ad_type" => $ad, "channel_name" => $chnl, "caption_name" => $cap, "timeband" => $tb), $timeband);
                            }
                        }
                    }
                }

                $network_details = $this->mg_model->get_network_details($network_id);
                $network_amount_details = $this->mg_model->get_network_amount_details($internal_ro_number, $network_id);
                $network_name = $network_details[0]['customer_name'];
                foreach ($nw_ro_channels as $key => $chnl) {
                    $chnls[] = implode(',', $chnl);
                }
                $users = $this->user_model->get_bhs();
                foreach ($users as $key => $user) {
                    $cc_emails[] = $user['user_email'];
                }
                $cc_emails_list = implode(',', $cc_emails);
                $channels = array_unique($chnls);


                $content_download_link = $this->ro_model->get_download_link($internal_ro_number, $network_id);
                //filter channel which is being cancelled
                //$cancel_channel = $this->mg_model->get_cancel_channel_for_internal_ro($internal_ro_number) ;
                //$content_download_link = $this->mg_model->filter_schedule_channel($content_download_link,$cancel_channel) ;

                $show_link = 0;
                if (count($content_download_link) > 0) {
                    $show_link = 1;
                } else {
                    $show_link = 0;
                }
                $data_array = array('internal_ro_number' => $internal_ro_number, 'customer_name' => $ro_details[0][0]['customer_name']);
                $nw_ro_report = $this->mg_model->get_nw_ro_number_from_nw_ro_report($data_array);
                $nw_ro_number = $nw_ro_report[0]['network_ro_number'];

                $market_cluster_names = $this->mg_model->getScheduledMarketForChannel($channels, $internal_ro_number);
                $market_cluster_name = array();
                foreach ($market_cluster_names as $mcn) {
                    if (!in_array($mcn['market_name'], $market_cluster_name)) {
                        array_push($market_cluster_name, $mcn['market_name']);
                    }

                }

                $unique_channel = $this->make_unique_channel($channels);
                $model = array();
                $model['internal_ro_number'] = $internal_ro_number;
                $model['reports'] = $reports;
                $model['cc_emails_list'] = $cc_emails_list;
                $model['ro_details'] = $ro_details[0];
                $model['channels'] = $unique_channel;
                $model['market_cluster_name'] = implode(",", $market_cluster_name);
                $model['dates'] = $nw_dates_mail;
                $model['days'] = $days;
                $model['no_of_months'] = $no_of_months;
                $model['network_details'] = $network_details[0];
                $model['network_amount_details'] = $network_amount_details[0];
                $model['content_download_link'] = $content_download_link;
                $model['show_link'] = $show_link;
                $model['nw_ro_number'] = $nw_ro_number;
                $model['complete_cancellation'] = $complete_cancellation;
                $model['cancel_start_date'] = $cancel_start_date;
                $model['cancel_ro'] = $this->mg_model->check_for_ro_cancellation($internal_ro_number);
                $model['nw_ro_to_email_list'] = get_nw_ro_to_email_list($internal_ro_number, $network_details[0]);
                $model['nw_ro_email'] = get_nw_ro_email($internal_ro_number);
            }
            //echo print_r($model);exit;
        } elseif ($count == 1 && $region_ids[0] == 1) {

            $noChannelSchedule = false;
            $id = $region_ids[0];
            $channels_summary_v1 = $this->mg_model->get_channel_schedule_summary_v1_br(implode(',', $camp_ids[0]), $id);
            $channels_summary_v0 = $this->mg_model->get_channel_schedule_summary_br(implode(',', $camp_ids[0]), $id);
            if (count($channels_summary_v1) <= 0 && count($channels_summary_v0) <= 0) {
                $noChannelSchedule = true;
                continue;
            }
            if (!$noChannelSchedule) {
                $channels_summary = array_merge($channels_summary_v0, $channels_summary_v1);

                $channels = array();
                foreach ($channels_summary as $key => $chnl) {
                    array_push($channels, $chnl['channel_name']);
                }
                $channel_ids = array();
                foreach ($channels_summary as $key => $chanl) {
                    array_push($channel_ids, $chanl['channel_id']);
                }

                $ro_details_v1 = $this->mg_model->get_ro_report_details_v1(implode(',', $camp_ids[0]), $network_id);
                $ro_details_v0 = $this->mg_model->get_ro_report_details(implode(',', $camp_ids[0]), $network_id);
                $ro_details = array_merge($ro_details_v0, $ro_details_v1);
                $nw_channels = $this->mg_model->get_network_channels(implode(',', $channel_ids), $network_id);
                $ro_channels = array();
                $i = 0;
                foreach ($nw_channels as $key1 => $chnl) {
                    $ro_channels[$i] = $chnl['channel_name'];
                    $i++;
                }
                $nw_ro_channels = array_unique($ro_channels);
                $captions_v1 = $this->mg_model->get_captions_under_network_v1(implode(',', $camp_ids[0]), $network_id);
                $kaptions_v1 = $this->mg_model->get_captions_per_channel_v1(implode(',', $camp_ids[0]));
                $captions_v0 = $this->mg_model->get_captions_under_network(implode(',', $camp_ids[0]), $network_id);
                $kaptions_v0 = $this->mg_model->get_captions_per_channel(implode(',', $camp_ids[0]));

                $captions = array_merge($captions_v0, $captions_v1);
                $kaptions = array_merge($kaptions_v0, $kaptions_v1);
                if (count($kaptions) == 0) {
                    if (count($kaptions_v0) > 0) {
                        $kaptions = $kaptions_v0;
                    } else {
                        $kaptions = $kaptions_v1;
                    }
                }
                $caption = array();
                foreach ($captions as $key1 => $c) {
                    array_push($caption, $c['caption_name']);
                }

                $timeband_value = $this->ro_model->get_timeband_from_config($network_id);
                $timeband_val = explode(",", $timeband_value['timeband']);
                $data = array();
                for ($i = 0; $i < count($timeband_val); $i++) {
                    $time_val = explode("#", $timeband_val[$i]);

                    $tmp = array();
                    $tmp['start_time'] = $time_val[0];
                    $tmp['end_time'] = $time_val[1];

                    $data[$i] = $tmp;
                }
                $timeband = $data;

                /*$timeband = array( "0" => array("start_time"=>'06:00',"end_time"=>'10:00'),
                        "1" => array("start_time"=>'10:01',"end_time"=>'13:00'),
                        "2" => array("start_time"=>'13:01',"end_time"=>'18:00'),
                        "3" => array("start_time"=>'18:01',"end_time"=>'21:00'),
                        "4" => array("start_time"=>'21:01',"end_time"=>'23:59'),
                    ); */

                //$dates = $this->mg_model->get_network_dates(implode(',',$camp_ids[0]),$network_id);
                $dates = $this->mg_model->get_network_dates_v1(implode(',', $camp_ids[0]), $network_id);
                $complete_cancellation = false;
                $cancel_start_date = '';
                if (!isset($dates[0]['start_date']) || !isset($dates[0]['end_date'])) {
                    $complete_cancellation = TRUE;
                    $dates = $this->mg_model->get_network_cancel_dates_v1(implode(',', $camp_ids[0]), $network_id);
                    $cancel_start_date = $dates[0]['start_date'];
                }
                $nw_dates_mail = array("start_date" => $dates[0]['start_date'], "end_date" => $dates[0]['end_date']);
                $nw_dates = array("start_date" => $dates[0]['start_date'], "end_date" => $dates[0]['end_date']);
                $month1 = date('m', strtotime($nw_dates['start_date']));
                $month2 = date('m', strtotime($nw_dates['end_date']));
                $year_1 = date('Y', strtotime($nw_dates['start_date']));
                $year_2 = date('Y', strtotime($nw_dates['end_date']));

                if ($year_2 > $year_1) {
                    $year_gap = $year_2 - $year_1;
                    $month2 = $year_gap * 12 + $month2;
                }

                $no_of_months = $month2 - $month1 + 1;
                $start_date = date('Y-m-d', strtotime($nw_dates['start_date']));
                $end_date = date('Y-m-d', strtotime($nw_dates['end_date']));
                $day = 86400; // Day in seconds
                $format = 'Y-m-d'; // Output format (see PHP date funciton)
                $sTime = strtotime($start_date); // Start as time
                $eTime = strtotime($end_date); // End as time
                $sTime1 = strtotime($start_date); // Start as time
                $eTime1 = strtotime($end_date); // End as time
                $tmp = date('mY', $eTime1);
                $months[] = date('F', $sTime1);

                while ($sTime1 < $eTime1) {
                    $sTime1 = strtotime(date('Y-m-d', $sTime1) . ' +1 month');
                    if (date('mY', $sTime1) != $tmp && ($sTime1 < $eTime1)) {
                        $months[] = date('F', $sTime1);
                    }
                }
                $months[] = date('F', $eTime1);
                $months_unique = array_unique($months);
                $activity_months = implode(',', $months_unique);
                $numDays = round(($eTime - $sTime) / $day) + 1;
                $days = array();
                for ($d = 0; $d < $numDays; $d++) {
                    $days[] = date($format, ($sTime + ($d * $day)));
                }

                $program_id_channel = array();
                foreach ($timeband as $key => $tb) {
                    foreach ($nw_channels as $key1 => $channel) {
                        $tmp_program_id_channel = array();
                        $channel_name = $channel['channel_name'];
                        foreach ($kaptions as $key2 => $cap) {
                            $start_time = $tb['start_time'];
                            $end_time = $tb['end_time'];
                            $time = $start_time . '-' . $end_time;
                            if ($cap['channel_name'] == $channel_name) {
                                $caption_name = $cap['caption_name'];
                                $summary_v1 = $this->mg_model->get_report_v1(implode(',', $camp_ids[0]), $network_id, $channel_name, $start_time, $end_time, $caption_name, $program_id_channel[$channel_name]);
                                $caption_details_v1 = $this->mg_model->get_caption_details_v1(implode(',', $camp_ids[0]), $network_id, $caption_name);

                                $summary_v0 = $this->mg_model->get_report(implode(',', $camp_ids[0]), $network_id, $channel_name, $start_time, $end_time, $caption_name, $program_id_channel[$channel_name]);
                                $caption_details_v0 = $this->mg_model->get_caption_details(implode(',', $camp_ids[0]), $network_id, $caption_name);


                                $summary = array_merge($summary_v0, $summary_v1);
                                $caption_details = array_merge($caption_details_v0, $caption_details_v1);

                                $as = array();
                                foreach ($summary as $su) {
                                    $temp = date('Y-m-d', strtotime($su['date']));
                                    if (array_key_exists($temp, $as)) {
                                        continue;
                                    }
                                    $as[$temp] = $su['impressions'];
                                    if (!isset($tmp_program_id_channel[$channel_name])) {
                                        $tmp_program_id_channel[$channel_name] = array();
                                    }
                                    array_push($tmp_program_id_channel[$channel_name], $su['pu_id']);
                                }
                                $program_id_channel[$channel_name] = array_unique($program_id_channel[$channel_name]);
                                $ad_type = 'Spot Ad';
                                $report[$ad_type][$channel_name][$caption_name][$time]['brand_new'] = $caption_details[0]['brand_new'];
                                $report[$ad_type][$channel_name][$caption_name][$time]['language'] = $caption_details[0]['Language'];
                                $report[$ad_type][$channel_name][$caption_name][$time]['ro_duration'] = $caption_details[0]['ro_duration'];
                                foreach ($days as $day) {
                                    if (isset($as[$day])) {
                                        $report[$ad_type][$channel_name][$caption_name][$time]['date'][$day] = $as[$day];
                                    } else {
                                        $report[$ad_type][$channel_name][$caption_name][$time]['date'][$day] = 0;
                                    }
                                }
                            } else {
                            }
                        }
                        $program_id_channel[$channel_name] = array_merge($program_id_channel[$channel_name], $tmp_program_id_channel[$channel_name]);
                        $program_id_channel[$channel_name] = array_unique($program_id_channel[$channel_name]);
                    }
                }
                $reports = array();
                foreach ($report as $ad => $adtype) {
                    foreach ($adtype as $chnl => $channel) {
                        foreach ($channel as $cap => $caption) {
                            foreach ($caption as $tb => $timeband) {
                                $reports[] = array_merge(array("ad_type" => $ad, "channel_name" => $chnl, "caption_name" => $cap, "timeband" => $tb), $timeband);
                            }
                        }
                    }
                }

                $network_details = $this->mg_model->get_network_details($network_id);
                $network_amount_details = $this->mg_model->get_network_amount_details($internal_ro_number, $network_id);
                $network_name = $network_details[0]['customer_name'];
                $users = $this->user_model->get_bhs();
                foreach ($users as $key => $user) {
                    $cc_emails[] = $user['user_email'];
                }
                $cc_emails_list = implode(',', $cc_emails);

                $content_download_link = $this->ro_model->get_download_link($internal_ro_number, $network_id);

                $show_link = 0;
                if (count($content_download_link) > 0) {
                    $show_link = 1;
                } else {
                    $show_link = 0;
                }
                $data_array = array('internal_ro_number' => $internal_ro_number, 'customer_name' => $ro_details[0]['customer_name']);
                $nw_ro_report = $this->mg_model->get_nw_ro_number_from_nw_ro_report($data_array);
                $nw_ro_number = $nw_ro_report[0]['network_ro_number'];

                $market_cluster_names = $this->mg_model->getScheduledMarketForChannel($channels, $internal_ro_number);
                $market_cluster_name = array();
                foreach ($market_cluster_names as $mcn) {
                    if (!in_array($mcn['market_name'], $market_cluster_name)) {
                        array_push($market_cluster_name, $mcn['market_name']);
                    }
                }
                $unique_channel = $this->make_unique_channel($nw_ro_channels);
                $model = array();
                $model['internal_ro_number'] = $internal_ro_number;
                $model['reports'] = $reports;
                $model['ro_details'] = $ro_details;
                $model['cc_emails_list'] = $cc_emails_list;
                $model['channels'] = $unique_channel;
                $model['market_cluster_name'] = implode(",", $market_cluster_name);
                $model['dates'] = $nw_dates_mail;
                $model['days'] = $days;
                $model['no_of_months'] = $no_of_months;
                $model['network_details'] = $network_details[0];
                $model['network_amount_details'] = $network_amount_details[0];
                $model['content_download_link'] = $content_download_link;
                $model['show_link'] = $show_link;
                $model['nw_ro_number'] = $nw_ro_number;
                $model['complete_cancellation'] = $complete_cancellation;
                $model['cancel_start_date'] = $cancel_start_date;
                $model['cancel_ro'] = $this->mg_model->check_for_ro_cancellation($internal_ro_number);
                $model['nw_ro_to_email_list'] = get_nw_ro_to_email_list($internal_ro_number, $network_details[0]);
                $model['nw_ro_email'] = get_nw_ro_email($internal_ro_number);
                //echo print_r($model,true);exit;
            }

        } elseif ($count == 1 && $region_ids[0] == 3) {
            $noChannelSchedule = false;
            $id = $region_ids[0];
            $channels_summary_v1 = $this->mg_model->get_channel_schedule_summary_v1_br(implode(',', $camp_ids[0]), $id);
            $channels_summary_v0 = $this->mg_model->get_channel_schedule_summary_br(implode(',', $camp_ids[0]), $id);
            if (count($channels_summary_v1) <= 0 && count($channels_summary_v0) <= 0) {
                $noChannelSchedule = true;
                continue;
            }
            if (!$noChannelSchedule) {
                $channels_summary = array_merge($channels_summary_v0, $channels_summary_v1);

                $channels = array();
                foreach ($channels_summary as $key => $chnl) {
                    array_push($channels, $chnl['channel_name']);
                }
                $channel_ids = array();
                foreach ($channels_summary as $key => $chanl) {
                    array_push($channel_ids, $chanl['channel_id']);
                }

                $ro_details_v1 = $this->mg_model->get_ro_report_details_v1(implode(',', $camp_ids[0]), $network_id);
                $ro_details_v0 = $this->mg_model->get_ro_report_details(implode(',', $camp_ids[0]), $network_id);
                $ro_details = array_merge($ro_details_v0, $ro_details_v1);
                $nw_channels = $this->mg_model->get_network_channels(implode(',', $channel_ids), $network_id);
                $ro_channels = array();
                $i = 0;
                foreach ($nw_channels as $key1 => $chnl) {
                    $ro_channels[$i] = $chnl['channel_name'];
                    $i++;
                }
                $nw_ro_channels = array_unique($ro_channels);

                $captions_v1 = $this->mg_model->get_captions_under_network_v1(implode(',', $camp_ids[0]), $network_id);
                $kaptions_v1 = $this->mg_model->get_captions_per_channel_v1(implode(',', $camp_ids[0]));
                $captions_v0 = $this->mg_model->get_captions_under_network(implode(',', $camp_ids[0]), $network_id);
                $kaptions_v0 = $this->mg_model->get_captions_per_channel(implode(',', $camp_ids[0]));

                if (count($kaptions) == 0) {
                    if (count($kaptions_v0) > 0) {
                        $kaptions = $kaptions_v0;
                    } else {
                        $kaptions = $kaptions_v1;
                    }
                }
                $captions = array_merge($captions_v0, $captions_v1);
                $kaptions = array_merge($kaptions_v0, $kaptions_v1);
                $caption = array();
                foreach ($captions as $key1 => $c) {
                    array_push($caption, $c['caption_name']);
                }

                $timeband_value = $this->ro_model->get_timeband_from_config($network_id);
                $timeband_val = explode(",", $timeband_value['timeband']);
                $data = array();
                for ($i = 0; $i < count($timeband_val); $i++) {
                    $time_val = explode("#", $timeband_val[$i]);

                    $tmp = array();
                    $tmp['start_time'] = $time_val[0];
                    $tmp['end_time'] = $time_val[1];

                    $data[$i] = $tmp;
                }


                $timeband = $data;

                /*$timeband[$key] = array( "0" => array("start_time"=>'06:00',"end_time"=>'10:00'),
                        "1" => array("start_time"=>'10:01',"end_time"=>'13:00'),
                        "2" => array("start_time"=>'13:01',"end_time"=>'18:00'),
                        "3" => array("start_time"=>'18:01',"end_time"=>'21:00'),
                        "4" => array("start_time"=>'21:01',"end_time"=>'23:59'),
                    ); */


                //$dates = $this->mg_model->get_network_dates(implode(',',$camp_ids[0]),$network_id);
                $dates = $this->mg_model->get_network_dates_v1(implode(',', $camp_ids[0]), $network_id);
                $complete_cancellation = false;
                $cancel_start_date = '';
                if (!isset($dates[0]['start_date']) || !isset($dates[0]['end_date'])) {
                    $complete_cancellation = TRUE;
                    $dates = $this->mg_model->get_network_cancel_dates_v1(implode(',', $camp_ids[0]), $network_id);
                    $cancel_start_date = $dates[0]['start_date'];
                }
                $nw_dates_mail = array("start_date" => $dates[0]['start_date'], "end_date" => $dates[0]['end_date']);
                $nw_dates = array("start_date" => $dates[0]['start_date'], "end_date" => $dates[0]['end_date']);
                $month1 = date('m', strtotime($nw_dates['start_date']));
                $month2 = date('m', strtotime($nw_dates['end_date']));

                $year_1 = date('Y', strtotime($nw_dates['start_date']));
                $year_2 = date('Y', strtotime($nw_dates['end_date']));

                if ($year_2 > $year_1) {
                    $year_gap = $year_2 - $year_1;
                    $month2 = $year_gap * 12 + $month2;
                }
                $no_of_months = $month2 - $month1 + 1;
                $start_date = date('Y-m-d', strtotime($nw_dates['start_date']));
                $end_date = date('Y-m-d', strtotime($nw_dates['end_date']));
                $day = 86400; // Day in seconds
                $format = 'Y-m-d'; // Output format (see PHP date funciton)
                $sTime = strtotime($start_date); // Start as time
                $eTime = strtotime($end_date); // End as time
                $sTime1 = strtotime($start_date); // Start as time
                $eTime1 = strtotime($end_date); // End as time
                $tmp = date('mY', $eTime1);
                $months[] = date('F', $sTime1);

                while ($sTime1 < $eTime1) {
                    $sTime1 = strtotime(date('Y-m-d', $sTime1) . ' +1 month');
                    if (date('mY', $sTime1) != $tmp && ($sTime1 < $eTime1)) {
                        $months[] = date('F', $sTime1);
                    }
                }
                $months[] = date('F', $eTime1);
                $months_unique = array_unique($months);
                $activity_months = implode(',', $months_unique);
                $numDays = round(($eTime - $sTime) / $day) + 1;
                $days = array();
                for ($d = 0; $d < $numDays; $d++) {
                    $days[] = date($format, ($sTime + ($d * $day)));
                }
                $program_id_channel = array();
                foreach ($timeband as $key => $tb) {
                    foreach ($nw_channels as $key1 => $channel) {
                        $tmp_program_id_channel = array();
                        $channel_name = $channel['channel_name'];
                        foreach ($kaptions as $key2 => $cap) {
                            $start_time = $tb['start_time'];
                            $end_time = $tb['end_time'];
                            $time = $start_time . '-' . $end_time;
                            if ($cap['channel_name'] == $channel_name) {
                                $caption_name = $cap['caption_name'];
                                $summary_v1 = $this->mg_model->get_report_v1(implode(',', $camp_ids[0]), $network_id, $channel_name, $start_time, $end_time, $caption_name, $program_id_channel[$channel_name]);
                                $caption_details_v1 = $this->mg_model->get_caption_details_v1(implode(',', $camp_ids[0]), $network_id, $caption_name);

                                $summary_v0 = $this->mg_model->get_report(implode(',', $camp_ids[0]), $network_id, $channel_name, $start_time, $end_time, $caption_name, $program_id_channel[$channel_name]);
                                $caption_details_v0 = $this->mg_model->get_caption_details(implode(',', $camp_ids[0]), $network_id, $caption_name);

                                $summary = array_merge($summary_v0, $summary_v1);
                                $caption_details = array_merge($caption_details_v0, $caption_details_v1);
                                $as = array();
                                foreach ($summary as $su) {
                                    $temp = date('Y-m-d', strtotime($su['date']));
                                    if (array_key_exists($temp, $as)) {
                                        continue;
                                    }
                                    $as[$temp] = $su['impressions'];
                                    if (!isset($tmp_program_id_channel[$channel_name])) {
                                        $tmp_program_id_channel[$channel_name] = array();
                                    }
                                    array_push($tmp_program_id_channel[$channel_name], $su['pu_id']);
                                }
                                $ad_type = 'Banner Ad';
                                $report[$ad_type][$channel_name][$caption_name][$time]['brand_new'] = $caption_details[0]['brand_new'];
                                $report[$ad_type][$channel_name][$caption_name][$time]['language'] = $caption_details[0]['Language'];
                                $report[$ad_type][$channel_name][$caption_name][$time]['ro_duration'] = $caption_details[0]['ro_duration'];
                                foreach ($days as $day) {
                                    if (isset($as[$day])) {
                                        $report[$ad_type][$channel_name][$caption_name][$time]['date'][$day] = $as[$day];
                                    } else {
                                        $report[$ad_type][$channel_name][$caption_name][$time]['date'][$day] = 0;
                                    }
                                }
                            } else {
                            }
                        }
                        $program_id_channel[$channel_name] = array_merge($program_id_channel[$channel_name], $tmp_program_id_channel[$channel_name]);
                        $program_id_channel[$channel_name] = array_unique($program_id_channel[$channel_name]);
                    }
                }
                $reports = array();
                foreach ($report as $ad => $adtype) {
                    foreach ($adtype as $chnl => $channel) {
                        foreach ($channel as $cap => $caption) {
                            foreach ($caption as $tb => $timeband) {
                                $reports[] = array_merge(array("ad_type" => $ad, "channel_name" => $chnl, "caption_name" => $cap, "timeband" => $tb), $timeband);
                            }
                        }
                    }
                }

                $network_details = $this->mg_model->get_network_details($network_id);
                $network_amount_details = $this->mg_model->get_network_amount_details($internal_ro_number, $network_id);
                $network_name = $network_details[0]['customer_name'];
                //approval mail alert
                $users = $this->user_model->get_users();
                $users = $this->user_model->get_bhs();
                foreach ($users as $key => $user) {
                    $cc_emails[] = $user['user_email'];
                }
                $cc_emails_list = implode(',', $cc_emails);

                $content_download_link = $this->ro_model->get_download_link($internal_ro_number, $network_id);
                $show_link = 0;
                if (count($content_download_link) > 0) {
                    $show_link = 1;
                } else {
                    $show_link = 0;
                }
                $data_array = array('internal_ro_number' => $internal_ro_number, 'customer_name' => $ro_details[0]['customer_name']);
                $nw_ro_report = $this->mg_model->get_nw_ro_number_from_nw_ro_report($data_array);
                $nw_ro_number = $nw_ro_report[0]['network_ro_number'];

                $market_cluster_names = $this->mg_model->getScheduledMarketForChannel($channels, $internal_ro_number);
                $market_cluster_name = array();
                foreach ($market_cluster_names as $mcn) {
                    if (!in_array($mcn['market_name'], $market_cluster_name)) {
                        array_push($market_cluster_name, $mcn['market_name']);
                    }
                }
                $unique_channel = $this->make_unique_channel($nw_ro_channels);

                $model = array();
                $model['internal_ro_number'] = $internal_ro_number;
                $model['reports'] = $reports;
                $model['cc_emails'] = $cc_emails_list;
                $model['ro_details'] = $ro_details;
                $model['channels'] = $unique_channel;
                $model['market_cluster_name'] = implode(",", $market_cluster_name);
                $model['dates'] = $nw_dates_mail;
                $model['days'] = $days;
                $model['no_of_months'] = $no_of_months;
                $model['network_details'] = $network_details[0];
                $model['network_amount_details'] = $network_amount_details[0];
                $model['content_download_link'] = $content_download_link;
                $model['show_link'] = $show_link;
                $model['nw_ro_number'] = $nw_ro_number;
                $model['cancel_ro'] = $this->mg_model->check_for_ro_cancellation($internal_ro_number);
                $model['complete_cancellation'] = $complete_cancellation;
                $model['cancel_start_date'] = $cancel_start_date;
                $model['nw_ro_to_email_list'] = get_nw_ro_to_email_list($internal_ro_number, $network_details[0]);
                $model['nw_ro_email'] = get_nw_ro_email($internal_ro_number);
            }
            //echo print_r($model,true);exit;
            //$this->load->view('ro_manager/Network_Big_Pdf',$model);
        }
        return $model;
    }

    public function make_unique_channel($channels)
    {
        $data = array();
        foreach ($channels as $chnl) {
            $chnl_value = explode(",", $chnl);
            foreach ($chnl_value as $val) {
                if (!in_array($val, $data)) {
                    array_push($data, $val);
                } else {
                    continue;
                }
            }
        }
        return $data;
    }

    public function downloadContent($content_uri, $localServer_path)
    {

        $fileName = $content_uri;
        $this->load->library('s3');
        $this->s3->setAuth(AMAZON_S3_KEY, AWS_SECRET_KEY);
        $result = $this->s3->getObject('sv2-test-advertiser-content', $fileName, false);
        $file = fopen($localServer_path, "w");
        $fwrite = fwrite($file, $result->body);
        fclose($file);
        if ($fwrite === false) {
            return false;

        } else {
            return true;
        }
    }

    public function convertFileThroughFFMPEG($localServer_path, $outputFile)
    {
        $cmd = 'ffmpeg' . " -i $localServer_path -vcodec mpeg1video -b:v 12000k -y " . $outputFile;
        shell_exec($cmd);
        return true;
    }

    public function getContentDuration($filePath)
    {
        $cmd = 'ffmpeg' . " -i $filePath 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//";
        $duration = shell_exec($cmd);
        $duration = explode(":", $duration);
        $duration_in_sec = $duration[0] * 3600 + $duration[1] * 60 + $duration[2];
        return $duration_in_sec;
    }

    public function convertToZip($filepath)
    {

        $fileName = substr($filepath, (strrpos($filepath, '/') + 1), strlen($filepath));
        $zipfileName = substr($fileName, 0, strrpos($fileName, '.')) . ".zip";
        $zip_BasePath = substr($filepath, 0, strrpos($filepath, '/'));
        $zip_CompletePath = $zip_BasePath . "/" . $zipfileName;
        $zip = new ZipArchive;

        if (file_exists($zip_CompletePath)) {
            unlink($zip_CompletePath);
        }

        if ($zip->open($zip_CompletePath, ZipArchive::CREATE) !== TRUE) {
            return array('set' => false, 'url' => '');
        }

        $ret = chmod($filepath, 0777);
        $zip->addFile($filepath, $fileName);
        $zip->close();

        return array('set' => true, 'url' => $zip_CompletePath);
    }

    public function sendContentToS3($filePath)
    {
        // return array('status' => 'true/false','url' => 's3 url');
        // upload to S3
        //require_once("S3.php");
        $this->load->library('s3');
        $fileName = substr($filePath, (strrpos($filePath, '/') + 1), strlen($filePath));
        $s3 = new S3(AMAZON_KEY, AMAZON_VALUE);
        S3::putObject(S3::inputFile("$filePath"), S3_BUCKET, "$fileName", S3::ACL_PUBLIC_READ);
        // end uploading
        $s3_url = BUCKET_URL . $fileName;
        return array('status' => true, 'url' => $s3_url);
    }

    public function getlanguageID($languageStr, $languageBrArr)
    {
        $retLanguageID = '';
        foreach ($languageBrArr as $language) {
            if (strtolower(trim($language['language'])) == strtolower(trim($languageStr))) {
                $retLanguageID = $language['language_id'];
                break;
            }
        }
        if ($retLanguageID == '') {
            return $languageBrArr[0]['language_id'];
        } else {
            return $retLanguageID;
        }
    }

    public function checkContentExistInBR($apiArr, $contentData, $isfromEasyRo = '')
    {
        $apiArr['is_from_easy_ro'] = $isfromEasyRo;
        $apiArr['data'] = $contentData;
        $url = INGESTXPRESS_API . "/ContentMd5CaptionCheck";
        $_ch = curl_init();
        curl_setopt($_ch, CURLOPT_URL, $url);
        curl_setopt($_ch, CURLOPT_POST, 1);
        curl_setopt($_ch, CURLOPT_POSTFIELDS, http_build_query(array("request" => json_encode($apiArr))));
        curl_setopt($_ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($_ch);
        curl_close($_ch);
        $br_contentArr = json_decode($result, true);
        return $br_contentArr;
    }

    public function manageBROperationOntoDB($mapArray)
    {
        foreach ($mapArray['content'] as $contentId => $contentDetails) {
            /*--- This part is commented because right now no content edit is there.

                $contentStat = $this->parse_ro_schedule->checkContentAlreadyPresent($contentId);
             if(count($contentStat) > 0){// already present
                if($contentStat['br_content_id'] != $contentDetails['br_content_id']){// update both br and ro_id
                    $this->parse_ro_schedule->updateMapMGBR($contentId,$contentDetails['br_content_id'],$mapArray['ro']['mg_ro_id'],$mapArray['ro']['br_ro_id']);
                    $this->parse_ro_schedule->updateContentInMGAsPerBr($contentId,$contentDetails['br_content_id']);
                }else if($contentStat['mg_ro_id']  != $mapArray['ro']['mg_ro_id'] || $contentStat['br_ro_id']  != $mapArray['ro']['br_ro_id']){ //update only ro_id
                    $this->parse_ro_schedule->updateMapMGBR($contentId,'',$mapArray['ro']['mg_ro_id'],$mapArray['ro']['br_ro_id']);
                }
            }else{//insert
            ------- End
            */
            foreach ($contentDetails['channel'] as $mgChannelId => $mgRelatedBrChannel) {
                $this->parse_ro_schedule->insertContentInMGAsPerBr($contentId, $mgChannelId, $mgRelatedBrChannel['br_channel_id'], $mapArray['ro']['mg_ro_id'], $mapArray['ro']['br_ro_id'], $contentDetails['br_content_id']);
            }
            $this->parse_ro_schedule->updateContentInMGAsPerBr($contentId, $contentDetails['br_content_id']);
            //}
        }
    }


}

?>
