<?php
/**
 * Created by PhpStorm.
 * Author: Yash
 * Date: October, 2019
 */

namespace application\services\common_services;

use application\feature_dal\postApprovalRoFeature;


class UpdateApprovalStatusAndCampaignStatusService
{
    private $CI;
    private $postApprovalFeatureObj;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('session');
        $this->postApprovalFeatureObj = new postApprovalRoFeature();
    }

    public function updateApprovalStatusAndCampaignStatus($internalRoNo)
    {
        log_message('info','In UpdateApprovalAndCampaignStatus @ updateApproveCampaignStatus | Entered internalRoNo => '.print_r($internalRoNo,True));
        $result = $this->postApprovalFeatureObj->getPlaylistId($internalRoNo);
        log_message('info','In UpdateApprovalAndCampaignStatus @ updateApproveCampaignStatus | Data From Query Is => '.print_r($result,True));
        if(!isset($result) || empty($result)){
            return;
        }
        $playlistIdForDummyContent = array();
        $playlistIdForOriginalContent = array();
        foreach ($result as $r){
            if(!isset($r['content_url']) || empty($r['content_url'])){
                array_push($playlistIdForDummyContent,$r['playlist_id']);
            }
            else{
                array_push($playlistIdForOriginalContent,$r['playlist_id']);
            }
        }
        log_message('info','In UpdateApprovalAndCampaignStatus @ updateApproveCampaignStatus | PlaylistId Dummy => '.print_r($playlistIdForDummyContent,True));
        log_message('info','In UpdateApprovalAndCampaignStatus @ updateApproveCampaignStatus | PlaylistId Original => '.print_r($playlistIdForOriginalContent,True));

        foreach($playlistIdForDummyContent as $playListID){
            $cond = array('internal_ro_number' => $internalRoNo, 'playlist_id' => $playListID);
            $data = array('approved_status' => 'approved', 'campaign_status' => 'pending_content');
            $this->postApprovalFeatureObj->updateInSvAdvertiserCampaign($cond,$data);
        }

        foreach($playlistIdForOriginalContent as $playListID){
            $cond = array('internal_ro_number' => $internalRoNo, 'playlist_id' => $playListID);
            $data = array('approved_status' => 'approved', 'campaign_status' => 'saved');
            $this->postApprovalFeatureObj->updateInSvAdvertiserCampaign($cond,$data);
        }
        log_message('info','In UpdateApprovalAndCampaignStatus @ updateApproveCampaignStatus | Returning ');
    }
}