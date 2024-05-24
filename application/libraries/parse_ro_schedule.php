<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
require(dirname(__FILE__) . '/' . 'jsonClass.php');

class Parse_ro_schedule
{
    function __construct()
    {
        $this->allRo = new allRo();
        $this->eachRo = new eachRo();
        $this->contents = array();
        $this->customer_id = '';
    }

    public function parseAndMakeRoScheduleAsPerBR($arrayToBeParsed)
    {
        $retArr = array('status' => 'success', "data" => "data were parsed successfully");
        if ($arrayToBeParsed['internal_ro_number'] != '') {
            if ($this->eachRo->ro_number == '') {
                $this->eachRo->ro_number = $this->getCustomerRoNo(trim($arrayToBeParsed['internal_ro_number']));
                $roDate = $this->getROReleaseDate($arrayToBeParsed['internal_ro_number']);
                if ($roDate != '') {
                    $this->eachRo->ro_date = date("d-m-Y", strtotime($roDate));
                    $this->allRo->ro = $this->eachRo;
                } else {
                    $retArr = array('status' => 'error', "data" => "ro date could not be fetched for ro $this->eachRo->ro_number");
                    return $retArr;
                }
            }

            foreach ($arrayToBeParsed['reports'] as $eachRoReport) {

                $findStr = 'Spot';
                $pos = strpos(trim($eachRoReport['ad_type']), $findStr);
                if ($pos === false) {
                    $retArr = array('status' => 'error', "data" => "ro ad_type should be SPOT Ad");
                    break;
                }
                $timeArr = explode("-", $eachRoReport['timeband']);
                $channelDetails = $this->getChannelId($eachRoReport['channel_name']);
                $customerId = $this->getCustomerId($channelDetails['enterprise_id']);
                if ($this->customer_id == '') {
                    $this->customer_id = $customerId;
                }
                $brands = $this->getBrandID($eachRoReport['brand_new']);
                if (count($brands) <= 0) {
                    $retArr = array('status' => 'error', "data" => "BR Brand ID is missing for BRAND - " . $eachRoReport['brand_new']);
                    break;
                }
                $language = $eachRoReport['language'];
                $brand_id = $brands['br_brand_id'];
                $advertiser = $this->getAdvertiser($brands['new_advertiser_id']);
                if (count($advertiser) <= 0) {
                    $retArr = array('status' => 'error', "data" => "BR Advertiser ID is missing for BRAND - " . $eachRoReport['brand_new']);
                    break;
                }
                $advertiser_id = $advertiser['br_advertiser_id'];
                $campaignId = $this->getCampaignID($eachRoReport['caption_name'], $eachRoReport['channel_name'], $arrayToBeParsed['internal_ro_number']);
                //$contentStr						= substr($eachRoReport['caption_name'],0,strrpos($eachRoReport['caption_name'], '.'));
                $contentDetails = $this->getContentId($campaignId);
                if (count($contentDetails) > 0) {
                    $content_id = $contentDetails['content_id'];
                    if (!in_array($contentDetails['content_id'], $this->contents)) {
                        $this->contents[$content_id]['content_name'] = $contentDetails['content_name'];
                        $this->contents[$content_id]['content_url'] = $contentDetails['content_url'];
                        $this->contents[$content_id]['customer_id'] = $customerId;

                        $this->contents[$content_id]['channel'][$channelDetails['tv_channel_id']]['br_channel_id'] = $channelDetails['br_channel_id'];
                        $this->contents[$content_id]['data']['content_caption'] = trim($contentDetails['content_name']);
                        $this->contents[$content_id]['data']['advertiser'] = $advertiser_id;
                        $this->contents[$content_id]['data']['brand'] = $brand_id;
                        $this->contents[$content_id]['data']['language'] = $language;
                    } else {
                        if (!in_array($channelDetails['tv_channel_id'], $this->contents[$content_id]['channel'])) {
                            $this->contents[$content_id]['channel'][$channelDetails['tv_channel_id']]['br_channel_id'] = $channelDetails['br_channel_id'];
                        }
                    }

                    foreach ($eachRoReport['date'] as $date => $impressions) {
                        if ($impressions) {
                            $this->eachRoDetails = new eachRoDetails();

                            $this->eachRoDetails->channel = $channelDetails['br_channel_id'];
                            $this->eachRoDetails->brand = $brand_id;
                            $this->eachRoDetails->advertiser = $advertiser_id;
                            $this->eachRoDetails->content_id = $content_id;
                            $this->eachRoDetails->caption = trim($contentDetails['content_name']);//trim($eachRoReport['caption_name']);
                            $this->eachRoDetails->start_date = date("d-m-Y", strtotime($date));
                            $this->eachRoDetails->end_date = date("d-m-Y", strtotime($date));
                            $this->eachRoDetails->start_time = $timeArr[0] . ':00';
                            $this->eachRoDetails->end_time = $timeArr[1] . ':00';
                            $this->eachRoDetails->impressions = $impressions;
                            $this->allRo->schedule[] = $this->eachRoDetails;
                        }
                    }
                } else {
                    if ($campaignId == '') {
                        $retArr = array('status' => 'error', "data" => "Campaign Id is missing for caption name = " . $eachRoReport['caption_name'] .
                            " , channel name =" . $eachRoReport['channel_name'] . "  and internal ro number = " . $arrayToBeParsed['internal_ro_number']);
                    } else {
                        $retArr = array('status' => 'error', "data" => "Content details  is missing for caption name = " . $eachRoReport['caption_name'] .
                            " , channel name =" . $eachRoReport['channel_name'] . "  and internal ro number = " . $arrayToBeParsed['internal_ro_number']);
                    }
                    break;
                }
            }
        } else {
            $retArr = array('status' => 'error', "data" => "internal_ro_number is missing");
        }
        return $retArr;
    }

    public function getCustomerRoNo($internalRoNo)
    {
        $ci =& get_instance();
        $userData = array('internal_ro' => $internalRoNo);
        $ci->db->select('cust_ro');
        $ci->db->where($userData);
        $res = $ci->db->get('ro_am_external_ro');
        $finalArr = $res->result("array");
        return $finalArr[0]['cust_ro'];

    }

    public function getROReleaseDate($internal_ro_number)
    {
        $ci =& get_instance();
        $userData = array('internal_ro' => $internal_ro_number);
        $ci->db->like($userData);
        $res = $ci->db->get('ro_am_external_ro');
        $finalArr = $res->result("array");
        return $finalArr[0]['ro_date'];
    }

    public function getChannelId($channel_name)
    {
        $ci =& get_instance();
        $userData = array('channel_name' => $channel_name);
        $ci->db->where($userData);
        $res = $ci->db->get('sv_tv_channel');
        $finalArr = $res->result("array");
        return $finalArr[0];
    }

    public function getCustomerId($enterprise_id)
    {
        $ci =& get_instance();
        $userData = array('customer_id' => $enterprise_id);
        $ci->db->where($userData);
        $res = $ci->db->get('sv_customer');
        $finalArr = $res->result("array");
        return $finalArr[0]['br_customer_id'];
    }

    public function getBrandID($brand_name)
    {
        $ci =& get_instance();
        $userData = array('brand' => $brand_name, 'br_brand_id IS NOT NULL' => NULL);
        $ci->db->where($userData);
        $res = $ci->db->get('sv_new_brand');
        $finalArr = $res->result("array");
        return $finalArr[0];
    }

    public function getAdvertiser($advertiser_id)
    {
        $ci =& get_instance();
        $userData = array('id' => $advertiser_id, 'br_advertiser_id IS NOT NULL' => NULL);
        $ci->db->where($userData);
        $res = $ci->db->get('sv_new_advertiser');
        $finalArr = $res->result("array");
        return $finalArr[0];
    }

    public function getCampaignID($caption_name, $channel_name, $internal_ro_number)
    {
        $ci =& get_instance();
        $ci->db->select("sac.campaign_id");
        $ci->db->from("sv_advertiser_campaign as sac");
        $ci->db->join("sv_tv_channel as stc", "sac.channel_id = stc.tv_channel_id");
        $ci->db->where("stc.channel_name", $channel_name);
        $ci->db->where("sac.internal_ro_number", $internal_ro_number);
        $ci->db->where("sac.caption_name", $caption_name);
        $res = $ci->db->get();
        $finalArr = $res->result("array");
        if (count($finalArr) > 0) {
            return $finalArr[0]['campaign_id'];
        } else {
            return '';
        }

    }

    public function getContentId($campaignId)
    {
        $ci =& get_instance();
        /*	// $userData  	= array('content_name' => $content_name);
         $whereArr	= array('content_name' => $content_name,'br_content_id' => NULL);
         $ci->db->where($whereArr);		
         //$ci->db->like($userData);		
         $res 		= $ci->db->get('sv_advertiser_content');
         $finalArr 	= $res->result("array");
         if(count($finalArr) > 0){
             return $finalArr[0];
         }else{
             return array();
         } */
        $ci->db->select("sdco.content_id, sdco.content_name, sdco.actual_content_url as content_url");
        $ci->db->from("sv_advertiser_campaign as sdc");
        $ci->db->join("sv_advertiser_playlist as sap", "sdc.campaign_id = sap.campaign_id");
        $ci->db->join("sv_advertiser_playlist_contents as sdpo", "sdpo.playlist_id = sap.playlist_id");
        $ci->db->join("sv_advertiser_content as sdco", "sdco.content_id = sdpo.content_id");
        $ci->db->join("sv_tv_channel as stc", "stc.tv_channel_id = sdc.channel_id");
        $ci->db->where("sdc.campaign_id =", $campaignId);
        $res = $ci->db->get();
        $finalArr = $res->result("array");
        if (count($finalArr) > 0) {
            return $finalArr[0];
        } else {
            return array();
        }

    }

    public function getRoScheduleAsPerBR()
    {
        return $this->allRo;
    }

    public function getContents()
    {
        return $this->contents;
    }

    public function insertContentInMGAsPerBr($contentId, $mg_channel_id, $br_channel_id, $mg_ro_id, $br_ro_id, $br_content_id)
    {
        $ci =& get_instance();
        $data['mg_content_id'] = $contentId;
        $data['br_channel_id'] = $br_channel_id;
        $data['mg_channel_id'] = $mg_channel_id;
        $data['mg_ro_id'] = $mg_ro_id;
        $data['br_ro_id'] = $br_ro_id;
        $data['br_content_id'] = $br_content_id;
        $ret = $ci->db->insert('sv_mg_br_ro_map', $data);
    }

    public function setRoStatus($ro_id, $status)
    {
        $ci =& get_instance();
        $data = array(
            'status' => $status
        );

        $ci->db->where('ro_id', $ro_id);
        $res = $ci->db->update('sv_br_ro_schedule', $data);
    }

    public function getBrCustomerId()
    {
        return $this->customer_id;
    }

    public function updateMappedRoschedule($br_ro_id, $mg_ro_id)
    {
        $ci =& get_instance();
        $data = array(
            'br_ro_id' => $br_ro_id
        );

        $ci->db->where('mg_ro_id', $mg_ro_id);
        $res = $ci->db->update('sv_mg_br_ro_map', $data);
    }

    public function updateContentInMGAsPerBr($mg_content_id, $br_content_id)
    {
        $ci =& get_instance();
        $data = array(
            'br_content_id' => $br_content_id
        );

        $ci->db->where('content_id', $mg_content_id);
        $res = $ci->db->update('sv_advertiser_content', $data);
    }

    public function updateMapMGBR($mg_content_id, $br_content_id = '', $mg_ro_id = '', $br_ro_id = '')
    {
        $ci =& get_instance();
        $data = array();

        if ($br_content_id != '') {
            $data['br_content_id'] = $br_content_id;
        }
        if ($mg_ro_id != '') {
            $data['mg_ro_id'] = $mg_ro_id;
        }
        if ($br_ro_id != '') {
            $data['br_ro_id'] = $br_ro_id;
        }

        if (count($data) > 0 && $mg_content_id != '') {
            $ci->db->where('mg_content_id', $mg_content_id);
            $res = $ci->db->update('sv_mg_br_ro_map', $data);
        }
    }

    public function checkContentAlreadyPresent($content_id)
    {
        $ci =& get_instance();
        $userData = array('mg_content_id' => $content_id);
        $ci->db->distinct();
        $ci->db->select('mg_content_id,br_content_id,mg_ro_id,br_ro_id');
        $ci->db->where($userData);
        $res = $ci->db->get('sv_mg_br_ro_map');
        $finalArr = $res->result("array");
        if (count($finalArr) > 0) {
            return $finalArr[0];
        } else {
            return array();
        }
    }

    public function parseROScheduleAndChangeCaptionName($changedCaptionArr, $roSchduleArr)
    {
        foreach ($roSchduleArr->schedule as $key => $val) {
            foreach ($changedCaptionArr as $key1 => $val1) {
                if (intval($val->content_id) == $key1) {
                    $roSchduleArr->schedule[$key]->caption = $val1;
                    unset($roSchduleArr->schedule[$key]->content_id);
                } else {
                    unset($roSchduleArr->schedule[$key]->content_id);
                }
            }
        }
        $this->allRo = $roSchduleArr;
        return $roSchduleArr;
    }


}

?>
