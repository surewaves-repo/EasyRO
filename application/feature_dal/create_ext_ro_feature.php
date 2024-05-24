<?php

namespace application\feature_dal;

use application\repo\BaseDAL;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

include_once APPPATH . 'repo/base_dal.php';

class CreateExtRoFeature extends BaseDAL
{
    public function __construct()
    {
        parent::__construct();
        log_message('DEBUG', 'In CreateExtRoFeature@constructor | Object Created');
    }

    /**
     * @param $agency
     * @return mixed
     */
    public function getAgencyName($agency)
    {
        log_message('INFO', 'In CreateExtRoFeature@getAgencyName | Fetching Agency Display Name for - ' . print_r($agency, true));
        $agencyDetails = $this->SvAgencyDisplay->getNewAgencyDetails($agency);
        log_message('INFO', 'In CreateExtRoFeature@getAgencyName | Agency Details - ' . print_r($agencyDetails, true));
        return ($agencyDetails['sv_new_agency']['agency_name']);
    }

    /**
     * @param $client
     * @return mixed
     */
    public function getClientName($client)
    {
        log_message('INFO', 'In CreateExtRoFeature@getClientName | Fetching Client Display Name for - ' . print_r($client, true));
        $clientDetails = $this->SvAdvertiserDisplay->getAdvertiserDetails($client);
        log_message('INFO', 'In CreateExtRoFeature@getClientName | Client Details - ' . print_r($clientDetails, true));
        return ($clientDetails['sv_new_advertiser']['advertiser']);
    }

    /**
     * @param $custRo
     * @return array |null
     */
    public function getRoDetails($custRo)
    {
        $condition = array(array('cust_ro', $custRo));
        log_message('DEBUG', 'In CreateExtRoFeature@getRoDetails | Fetching records for - ' . print_r($condition, true));
        $result = $this->RoAmExternalRo->getColumnsWhere($condition, array('*'));
        if ($result->count() > 0) {
            log_message('INFO', 'In CreateExtRoFeature@getRoDetails | RO already exists - ' . print_r($result, true));
            return $result->toArray();
        }
        log_message('INFO', 'In CreateExtRoFeature@getRoDetails | RO does not exist');
        return NULL;
    }

    /**
     * @param $financialYear
     * @param $isTestUser
     * @return int|mixed
     */
    public function getFinancialRunningNumber($financialYear, $isTestUser)
    {
        $condition = array(array('financial_year', $financialYear), array('test_user_creation', $isTestUser));
        log_message('INFO', 'In CreateExtRoFeature@getFinancialRunningNumber | Fetching Details for - ' . print_r($condition, true));
        $result = $this->RoAmExternalRo->getColumnsWhere($condition, array('*'));
        $result = $result->max('financial_year_running_no');
        if (!empty($result)) {
            log_message('INFO', 'In CreateExtRoFeature@getFinancialRunningNumber | Financial Running Year - ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In CreateExtRoFeature@getFinancialRunningNumber | No record found');
        return 0;
    }

    /**
     * @param $croNumber
     * @param $roPrefix
     * @return int
     */
    public function getInternalRoNumber($croNumber, $roPrefix)
    {
        $condition = array(array('cust_ro', $croNumber), array('internal_ro', 'like', '%' . $roPrefix . '%'));
        log_message('INFO', 'In CreateExtRoFeature@getInternalRoNumber | Fetching internal ro number for - ' . print_r($condition, true));
        $result = $this->RoAmExternalRo->getWhereOrder($condition, 'camp_start_date', 'DESC');
        if ($result->count() > 0) {
            $result = $result->toArray();
            log_message('INFO', 'In Repository@getInternalRoNumber | Fetched Record - ' . print_r($result, true));
            return $result[0]['internal_ro'];
        }
        log_message('INFO', 'In Repository@getInternalRoNumber | No record found');
        return 0;
    }

    /**
     * @param $roPrefixDb
     * @return string|null
     */
    public function getDistinctInternalRoNumber($roPrefixDb)
    {
        $condition = array(array('internal_ro', 'like', '%' . $roPrefixDb . '%'));
        log_message('INFO', 'In CreateExtRoFeature@getDistinctInternalRoNumber | Fetching distinct internal ro number - ' . print_r($condition, true));
        $result = $this->RoAmExternalRo->getWhereOrderDistinct($condition, 'internal_ro', 'DESC', 'internal_ro');
        if ($result->count() > 0) {
            $result = $result->toArray();
            log_message('INFO', 'In Repository@getDistinctInternalRoNumber | Fetched Record - ' . print_r($result, true));
            return $result[0]['internal_ro'];
        }
        log_message('INFO', 'In Repository@getDistinctInternalRoNumber | No record found');
        return NULL;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function insertRo($data)
    {
        log_message('INFO', 'In CreateExtRoFeatue@insertRo | Data to insert  - ' . print_r($data, true));
        return $this->RoAmExternalRo->insertGetId($data);
    }


    /**
     * @param $amExternalRoId
     * @param $status
     */
    public function updateDataForRoIdInRoStatus($amExternalRoId, $status)
    {
        $condition = array(array('am_external_ro_id', $amExternalRoId));
        log_message('INFO', 'In CreateExtRoFeature@updateDataForRoIdInRoStatus | Fetching data - ' . print_r($condition, true));
        $result = $this->RoStatus->getColumnsWhere($condition, array('*'));
        if ($result->count() > 0) {
            $previousStatus = $result[0]['ro_status'];
            $userData = array('ro_status' => $status, 'previous_status' => $previousStatus);
            log_message('INFO', 'In CreateExtRoFeature@updateDataForRoIdInRoStatus | Record exists! Updating record - ' . print_r($userData, true));
            $this->RoStatus->updateData($condition, $userData);
            log_message('INFO', 'In CreateExtRoFeature@updateDataForRoIdInRoStatus | Record Updated successfully');
        } else {
            $userData = array('am_external_ro_id' => $amExternalRoId, 'ro_status' => $status);
            log_message('INFO', 'In CreateExtRoFeature@updateDataForRoIdInRoStatus | Record not found! Inserting record - ' . print_r($userData, true));
            $this->RoStatus->insertData($userData);
            log_message('INFO', 'In CreateExtRoFeature@updateDataForRoIdInRoStatus | Record inserted successfully');
        }
    }

    /**
     * @param $client
     */
    public function updateNewAdv($client)
    {
        $condition = array(array('advertiser', $client));
        $data = array('active' => 1);
        log_message('INFO', 'In CreateExtRoFeature@updateNewAdv | Updating where - ' . print_r($condition, true) . ' with ' . print_r($data, true));
        $this->SvNewAdvertiser->updateData($condition, $data);
        log_message('INFO', 'In CreateExtRoFeature@updateNewAdv | Record updates successfully');
    }

    /**
     * @param $data
     */
    public function addRoAmount($data)
    {
        log_message('INFO', 'In CreateExtRoFeature@addRoAmount | Inserting record - ' . print_r($data, true));
        $this->RoAmount->insertData($data);
        log_message('INFO', 'In CreateExtRoFeature@addRoAmount | Record inserted successfully');
    }

    /**
     * @param $data
     */
    public function addMarkets($data)
    {
        log_message('INFO', 'In CreateExtRoFeature@addMarkets | Inserting record of spot and banner - ' . print_r($data, true));
        $this->RoMarketPrice->insertData($data);
        log_message('INFO', 'In CreateExtRoFeature@addMarkets | Record inserted successfully');
    }

    /**
     * @param $userId
     * @param $userType
     * @return array|Collection|mixed
     */
    public function getUserDetailOfUserReportingManager($userId, $userType)
    {
        log_message('INFO', 'In CreateExtRoFeature@getUserDetailOfUserReportingManager | Fetching record for - ' . print_r(array('userId' => $userId, 'userType' => $userType), true) . ' from ro_user');
        $result = DB::table('ro_user AS ru1')
            ->select('ru2.*')
            ->join('ro_user AS ru2', 'ru2.user_id', '=', 'ru1.reporting_manager_id')
            ->where(array(array('ru1.user_id', $userId), array('ru1.is_test_user', $userType)))
            ->get();
        if ($result->count() > 0) {
            $result = $result->toArray();
            $result = json_decode(json_encode($result), true);
            log_message('INFO', 'In CreateExtRoFeature@getUserDetailOfUserReportingManager | Record fetched - ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In CreateExtRoFeature@getUserDetailOfUserReportingManager | No record found');
        return array();
    }

    /**
     * @param $whereApprovalData
     * @param $approvalData
     */
    public function insertForPendingStatus($whereApprovalData, $approvalData)
    {
        log_message('INFO', 'In CreateExtRoFeature@insertForPendingStatus | Fetching record for - ' . print_r($whereApprovalData, true));
        $dataAvailInDb = $this->RoCancelExternalRo->getColumnsWhere($whereApprovalData, array('*'));

        //if data available then update else insert
        if ($dataAvailInDb->count() > 0) {
            log_message('INFO', 'In CreateExtRoFeature@insertForPendingStatus | Record exists! Updating record with - ' . print_r($approvalData, true));
            $this->RoCancelExternalRo->updateData($whereApprovalData, $approvalData);
            log_message('INFO', 'In CreateExtRoFeature@insertForPendingStatus | Record updated successfully');
        } else {
            log_message('INFO', 'In CreateExtRoFeature@insertForPendingStatus | Record not found! Inserting - ' . print_r($approvalData, true));
            $this->RoCancelExternalRo->insertData($approvalData);
            log_message('INFO', 'In CreateExtRoFeature@insertForPendingStatus | Record inserted successfully');
        }
    }

    /**
     * @param $data
     */
    public function insertIntoProgressionMailStatus($data)
    {
        log_message('INFO', 'In CreateExtRoFeature@insertIntoProgressionMailStatus | Inserting record - ' . print_r($data, true));
        $this->RoProgressionMailStatus->insertData($data);
        log_message('INFO', 'In CreateExtRoFeature@insertIntoProgressionMailStatus | Record inserted successfully');
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getUserHierarchy($userId)
    {
        log_message('INFO', 'In CreateExtRoFeature@getUserHierarchy | Fetching user level and reporting manager record for - ' . print_r($userId, true));
        $result = DB::table('ro_user AS t1')
            ->leftJoin('ro_user AS t2', 't2.user_id', '=', 't1.reporting_manager_id')
            ->leftJoin('ro_user AS t3', 't3.user_id', '=', 't2.reporting_manager_id')
            ->leftJoin('ro_user AS t4', 't4.user_id', '=', 't3.reporting_manager_id')
            ->leftJoin('ro_user AS t5', 't5.user_id', '=', 't4.reporting_manager_id')
            ->where(array(array('t1.user_id', $userId)))
            ->get(
                array('t1.profile_id AS lev1profile_id',
                    't1.user_id AS lev1userid',
                    't2.profile_id  AS lev2profile_id',
                    't2.user_id AS lev2userid',
                    't3.profile_id AS lev3profile_id',
                    't3.user_id AS lev3userid',
                    't4.profile_id AS lev4profile_id',
                    't4.user_id AS lev4userid',
                    't5.profile_id AS lev5profile_id',
                    't5.user_id AS lev5userid')
            );
        $result = json_decode(json_encode($result), true);
        return $result;
    }

    /**
     * @param $data
     */
    public function storeHistoryForRoCreation($data)
    {
        log_message('INFO', 'In CreateExtRoFeature@insertRoCreationHistory | Inserting record - ' . print_r($data, true));
        $this->RoExternalRoUserMap->insertData($data);
        log_message('INFO', 'In CreateExtRoFeature@insertRoCreationHistory | Record inserted successfully');
    }

    /**
     * @param $userId
     * @param $userType
     * @return array|mixed
     */
    public function getUserDetailForUserId($userId, $userType)
    {
        log_message('INFO', 'In CreateExtRoFeature@getUserDetailForUserId | Fetching record for - ' . print_r(array('userId' => $userId, 'userType' => $userType), true));
        $result = DB::table('ro_user AS ru')
            ->join('ro_user_profile AS rup', 'ru.profile_id', '=', 'rup.profile_id')
            ->join('ro_user_region AS rur', 'ru.user_id', '=', 'rur.user_id')
            ->join('ro_master_geo_regions AS rmgr', 'rmgr.id', '=', 'rur.region_id')
            ->where(array(array('ru.user_id', $userId), array('ru.active', 1), array('ru.is_test_user', isset($userType) ? $userType : '*')))
            ->get(array('ru.user_id', 'ru.user_name',
                'ru.user_email', 'ru.user_phone',
                'ru.reporting_manager_id', 'ru.profile_id',
                'rup.profile_name as designation',
                'rur.region_id', 'rmgr.region_name', 'ru.profile_image'
            ));
        $result = json_decode(json_encode($result), true);

        if (count($result) > 0) {
            log_message('INFO', 'In CreateExtRoFeature@getUserDetailForUserId | Record fetched successfully' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In CreateExtRoFeature@getUserDetailForUserId | No record found');
        return array();
    }

    /**
     * @param $profileIds
     * @param $regionId
     * @param $userType
     * @return array|mixed
     */
    public function getUserDetailForProfileIdsAndRegion($profileIds, $regionId, $userType)
    {
        log_message('INFO', 'In CreateExtRoFeature@getUserDetailForProfileIdsAndRegion | Fetching user details for ' . print_r(array('profileId' => $profileIds, 'regionId' => $regionId), true));
        $result = DB::table('ro_user AS ru')
            ->join('ro_user_profile AS rup', 'ru.profile_id', '=', 'rup.profile_id')
            ->join('ro_user_region AS rur', 'ru.user_id', '=', 'rur.user_id')
            ->join('ro_master_geo_regions AS rmgr', 'rmgr.id', '=', 'rur.region_id')
            ->whereIn('ru.profile_id', $profileIds)
            ->where(array(array('ru.active', 1), array('rur.region_id', $regionId), array('ru.is_test_user', $userType)))
            ->get(array('ru.user_id', 'ru.user_name', 'ru.user_email',
                'ru.user_phone', 'ru.reporting_manager_id', 'ru.profile_id',
                'rup.profile_name as designation', 'rur.region_id',
                'rmgr.region_name', 'ru.profile_image'
            ));
        $result = json_decode(json_encode($result), True);
        if (count($result) > 0) {
            log_message('INFO', 'In CreateExtRoFeature@getUserDetailForProfileIdsAndRegion | Record fetched successfully ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In CreateExtRoFeature@getUserDetailForProfileIdsAndRegion | No record found');
        return array();
    }

    /**
     * @param $searchKey
     * @return string
     */
    public function getStaticMails($searchKey)
    {
        log_message('INFO', 'In CreateExtRoFeature@getStaticMails | Fetching static emails for - ' . print_r($searchKey, true));
        $typeArr = array($searchKey);
        $result = $this->RoStaticEmails->getStaticMail($typeArr);

        if ($result->count() > 0) {
            $result = $result->toArray();
            log_message('INFO', 'In CreateExtRoFeature@getStaticMails | Record fetched successfully - ' . print_r($result, true));
            return $result[0]['static_emails'];
        } else {
            log_message('INFO', 'In CreateExtRoFeature@getStaticMails | No record found');
            return '';
        }
    }

    /**
     * @param $data
     * @return mixed
     */
    public function insertMailData($data)
    {
        log_message('INFO', 'In CreateExtRoFeature@insertMailData | Inserting email record - ' . print_r($data, true));
        return $this->RoMail->insertGetId($data);
    }

    /**
     * @param $id
     * @param $data
     */
    public function updateMailData($id, $data)
    {
        $condition = array(array('id', $id));
        log_message('INFO', 'In CreateExtRoFeature@updateMailData | Updating mail status for - ' . print_r($condition, true));
        $this->RoMail->updateData($condition, $data);
        log_message('INFO', 'In CreateExtRoFeature@updateMailData | Mail status updated successfully');
    }

    /**
     * @param $brand
     * @return string
     */
    public function getBrandNames($brand)
    {
        $brand = array_map('intval', explode(',', $brand));
        log_message('INFO', 'In CreateExtRoFeature@getBrandNames | Fetching brand name record for - ' . print_r($brand, true));
        $brandName = '';
        if ($brand != '') {
            $result = $this->SvNewBrand->getColumnsWhereIn('id', $brand, array('*'));

            if ($result->count() > 0) {
                $result = $result->toArray();
                foreach ($result as $brands) {
                    if ($brandName == '') {
                        $brandName .= $brands['brand'];
                    } else {
                        $brandName .= ', ' . $brands['brand'];
                    }
                }
                log_message('INFO', 'In CreateExtRoFeature@getBrandNames | Brand names fetched successfully - ' . print_r($brandName, true));
                return $brandName;
            }
        }
        log_message('INFO', 'In CreateExtRoFeature@getBrandNames | No record found');
        return $brandName;
    }

    //=========================================

    /**
     * @param $where_data
     * @return array
     */
    public function getMarketDataForMarketName($where_data)
    {
        $result = $this->SvSwMarket->getWhereOrder($where_data, 'sw_market_name', 'ASC');
        if ($result->count() > 0) {
            log_message('INFO', 'In CreateExtRoFeature@getMarketDataForMarketName | Result Exits - ' . print_r($result, true));
            return $result->toArray();
        }
        log_message('INFO', 'In CreateExtRoFeature@getMarketDataForMarketName | No Result');
        return array();
    }

    /**
     * @param $where_data
     * @param $data
     */
    public function updateMarketPrice($where_data, $data)
    {
        $this->RoMarketPrice->updateData($where_data, $data);
        log_message('INFO', 'In CreateExtRoFeature@updateMarketPrice | Market Price updated successfully');
    }

    /**
     * @param $marketName
     * @return array|mixed
     */
    public function getActiveChannelDetailForMarket($marketName)
    {
        $result = DB::table('sv_tv_channel AS stc')
            ->join('sv_customer AS sc', 'sc.customer_id', '=', 'stc.enterprise_id')
            ->join('sv_market_x_channel AS smc', 'stc.tv_channel_id', '=', 'smc.channel_fk_id')
            ->join('sv_sw_market AS sm', 'sm.id', '=', 'smc.market_fk_id')
            ->where(array(array('sm.sw_market_name', $marketName), array('is_notice', '!=', 1), array('is_blocked', '!=', 1)))
            ->orderBy('tv_channel_id')
            ->distinct()
            ->get(array(
                'stc.tv_channel_id', 'stc.channel_name', 'stc.spot_avg', 'stc.banner_avg', 'sm.id as market_id', 'sc.revenue_sharing'
            ));

        $result = json_decode(json_encode($result), True);
        if (count($result) > 0) {
            log_message('INFO', 'In CreateExtRoFeature@getActiveChannelDetailForMarket | Result Exits - ' . print_r($result, true));
            return $result;
        }
        log_message('INFO', 'In CreateExtRoFeature@getActiveChannelDetailForMarket | No Result');
        return array();
    }

    /**
     * @param $netContribuitionPercent
     * @param $roId
     */
    public function updateNetContributionPercent($netContribuitionPercent, $roId)
    {
        log_message('INFO', 'In CreateExtRoFeature@updateApprovalStatus | Updating RoCancelExernalRo with - ' . print_r(array('netContribuitionPercent' => $netContribuitionPercent, 'RO Id' => $roId), true));
        $data = array('net_contribuition_percent' => $netContribuitionPercent);
        $whereApprovalData = array(array('ext_ro_id', $roId),
            array('cancel_type', 'submit_ro_approval'),
            array('cancel_ro_by_admin', 0));
        $this->RoCancelExternalRo->updateData($whereApprovalData, $data);
        log_message('INFO', 'In CreateExtRoFeature@updateApprovalStatus | Data updated successfully');
    }

    public function getAllStates()
    {
        $r = $this->SvSwStates->getOrder('state_name', 'asc');
        log_message('INFO', 'In CreateExtRoFeature@getAllStates | Data is ' . print_r($r, True));
        return $r->toArray();
    }

    public function getAgencyContactInfo($agencyContactId)
    {
        $condition = array(array('id', $agencyContactId));
        $result = $this->RoAgencyContact->getColumnsWhere($condition, array('*'));
        $result = $result->toArray();
        log_message('INFO', 'In CreateExtRoFeature@getAgencyContactInfo | Data is ' . print_r($result, True));
        return $result[0];
    }

    public function getAgencyDetails($agencyDisplay)
    {
        log_message('INFO', 'In CreateExtRoFeature@getAgencyDetails ');
        return $this->SvAgencyDisplay->getNewAgencyDetails($agencyDisplay);
    }

    public function checkAgencyInRoAgency($agency_name, $agency_contact_name, $agency_email)
    {
        $condition = array(array('agency_name', $agency_name), array('agency_contact_name',$agency_contact_name), array('agency_email',$agency_email));
        $result = $this->RoAgencyContact->getColumnsWhere($condition, array('*'));
        $result = $result->toArray();
        log_message('INFO', 'In CreateExtRoFeature@checkAgencyInRoAgency | Data is ' . print_r($result, True));
        return $result;
    }

    public function insertRoAgencyContact($data)
    {
        $this->RoAgencyContact->insertData($data);
        log_message('INFO', 'In CreateExtRoFeature@insertRoAgencyContact | Inserted');
    }

    public function updateRoAgencyContact($operation, $data)
    {
        $condition = array(array('id', $operation));
        $this->RoAgencyContact->updateData($condition, $data);
        log_message('INFO', 'In CreateExtRoFeature@updateRoAgencyContact | Data updated successfully');
    }

    public function getClientContactInfo($agencyContactId)
    {
        $condition = array(array('id', $agencyContactId));
        $result = $this->RoClientContact->getColumnsWhere($condition, array('*'));
        $result = $result->toArray();
        log_message('INFO', 'In CreateExtRoFeature@getClientContactInfo | Data is ' . print_r($result, True));
        return $result[0];
    }

    public function getAdvertiserDetails($clientDisplay)
    {
        log_message('INFO', 'In CreateExtRoFeature@getAdvertiserDetails ');
        return $this->SvAdvertiserDisplay->getAdvertiserDetails($clientDisplay);
    }

    public function checkClientInRoClient($clientName)
    {
        $condition = array(array('client_name', $clientName));
        $result = $this->RoClientContact->getColumnsWhere($condition, array('*'));
        $result = $result->toArray();
        log_message('INFO', 'In CreateExtRoFeature@checkClientInRoClient | Data is ' . print_r($result, True));
        return $result;
    }

    public function insertRoClientContact($data)
    {
        $this->RoClientContact->insertData($data);
        log_message('INFO', 'In CreateExtRoFeature@insertRoClientContact | Inserted');
    }

    public function updateRoClientContact($operation, $data)
    {
        $condition = array(array('id', $operation));
        $this->RoClientContact->updateData($condition, $data);
        log_message('INFO', 'In CreateExtRoFeature@updateRoClientContact | Data updated successfully');
    }

    /**
     * @param $RoId
     * @return array
     */
    public function getMailData($RoId)
    {
        $condition = array(array('ro_id', $RoId));
        log_message('INFO', 'In CreateExtRoFeature@getMailData | Fetching mail record for - ' . print_r($condition, true));
        $result = $this->RoMail->getColumnsWhere($condition, array('*'));
        if ($result->count() > 0) {
            log_message('INFO', 'In CreateExtRoFeature@getMailData | Mail Found - ' . print_r($result, true));
            return $result->toArray();
        }
        log_message('INFO', 'In CreateExtRoFeature@getMailData | Mail does not exist');
        return array();
    }

    /**
     * @param $RoId
     * @return array |null
     */
    public function getRoDetailsForRoId($RoId)
    {
        $condition = array(array('id', $RoId));
        log_message('DEBUG', 'In CreateExtRoFeature@getRoDetailsForRoId | Fetching records for - ' . print_r($condition, true));
        $result = $this->RoAmExternalRo->getColumnsWhere($condition, array('*'));
        if ($result->count() > 0) {
            log_message('INFO', 'In CreateExtRoFeature@getRoDetailsForRoId | RO FOUND - ' . print_r($result, true));
            return $result->toArray();
        }
        log_message('INFO', 'In CreateExtRoFeature@getRoDetailsForRoId | RO does not exist');
        return array();
    }

    /**
     * @param $userId
     * @return array
     */
    public function getUserNameForUserId($userId)
    {
        $condition = array(array('user_id', $userId));
        log_message('DEBUG', 'In CreateExtRoFeature@getUserNameForUserId | Fetching records for - ' . print_r($condition, true));
        $result = $this->RoUser->getColumnsWhere($condition, array('*'));
        if ($result->count() > 0) {
            log_message('INFO', 'In CreateExtRoFeature@getUserNameForUserId | RO FOUND - ' . print_r($result, true));
            return $result->toArray();
        }
        log_message('INFO', 'In CreateExtRoFeature@getUserNameForUserId | RO does not exist');
        return array();
    }

    /**
     * Author: Yash
     * @param $regionId
     * @param $testUser
     * @return array|Collection|mixed
     */
    public function getRdMailRegionWise($regionId, $testUser)
    {
        log_message('INFO', 'In CreateExtRoFeature@getRdMailRegionWise | Fetching user details for ' . print_r(array('istestuser' => $testUser, 'regionId' => $regionId), true));
        $result = DB::table('ro_user AS ru')
            ->select('ru.user_email')
            ->join('ro_user_region AS rur', 'ru.user_id', '=', 'rur.user_id')
            ->where(array(array('ru.active', 1),
                array('rur.region_id', $regionId),
                array('ru.profile_id', 11),
                array('ru.is_test_user', $testUser)))
            ->get();

        if ($result->count() > 0) {
            $result = $result->toArray();
            $result = json_decode(json_encode($result), true);
            log_message('INFO', 'In CreateExtRoFeature@getRdMailRegionWise | Record fetched - ' . print_r($result, true));
            return $result[0];
        }
        log_message('INFO', 'In CreateExtRoFeature@getRdMailRegionWise | No record found');
        return array();
    }

    public function updateRoDetails($roId, $data)
    {
        $condition = array(array('id', $roId));
        log_message('INFO', 'In CreateExtRoFeature@updateRoDetails | Updating where - ' . print_r($condition, true) . ' with ' . print_r($data, true));
        $this->RoAmExternalRo->updateData($condition, $data);
        log_message('INFO', 'In CreateExtRoFeature@updateRoDetails | Record updates successfully');
    }

    public function updateRoAmount($internalRoNo, $data)
    {
        $condition = array(array('internal_ro_number', $internalRoNo));
        log_message('INFO', 'In CreateExtRoFeature@updateRoAmount | Updating where - ' . print_r($condition, true) . ' with ' . print_r($data, true));
        $this->RoAmount->updateData($condition, $data);
        log_message('INFO', 'In CreateExtRoFeature@updateRoAmount | Record updates successfully');
    }

    public function updateMailSentData($condition, $data)
    {
        log_message('INFO', 'In CreateExtRoFeature@updateMailSentData | Updating mail status for - ' . print_r($condition, true));
        $this->RoMail->updateData($condition, $data);
        log_message('INFO', 'In CreateExtRoFeature@updateMailSentData | Mail status updated successfully');
    }
    
    public function checkDataInRoMarketPrice($condition)
    {
        $result = $this->RoMarketPrice->getColumnsWhere($condition, array('*'));
        $result = $result->toArray();
        log_message('INFO', 'In CreateExtRoFeature@checkDataInRoMarketPrice | Data is ' . print_r($result, True));
        return $result;
    }

    public function checkIfRoForwaded($roId)
    {
        $conditions = array(array('ro_id', $roId), array('mail_type','submit_ro_approval'));
        $result = $this->RoMail->getColumnsWhere($conditions, array('approval_level'));
        if($result->count() > 0){
            $result = $result->toArray();
            if($result[0]['approval_level'] != 1)
                return true;
            else
                return false;
        }
        return true;
    }
}
