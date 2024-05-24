<?php
/**
 * Created by PhpStorm.
 * Author: Yash | Ravishankar
 * Date: August, 2019
 */

namespace application\feature_dal;

use application\repo\BaseDAL;

include_once APPPATH . 'repo/base_dal.php';

class UserLoginFeature extends BaseDAL
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Author : Ravishankar Singh
     * @param $email
     * @param $password
     * @return array | null
     *
     * This function fetches record of the corresponding email & password
     */
    public function userLogin($email, $password)
    {
        $conditions = array(array('user_email', $email), array('user_password', md5($password)), array('active', 1));
        log_message('DEBUG', 'In userLogin | Fetching user record for ' . print_r($conditions, true));
        $result = $this->RoUser->getColumnsWhere($conditions, array('*'));
        if ($result->count() > 0) {
            $result[0]['url'] = $this->getUrl($result[0]['profile_id']);
            log_message('DEBUG', 'In userLogin | user record fetched successfully for ' . print_r($conditions, true));
            return $result->toArray();
        }
        log_message('DEBUG', 'In userLogin | No record found for ' . print_r($conditions, true));
        return NULL;
    }

    /**
     * Author : Ravishankar Singh
     * @param $profileId
     * @return mixed
     *
     * This function fetches the landing page url for a given profile id
     */
    public function getUrl($profileId)
    {
        log_message('DEBUG', 'In UserLoginFeature@getUrl |Fetching landing URL for profile id = ' . print_r($profileId, true));
        $result = $this->RoUserProfile->getUrl($profileId);
        log_message('DEBUG', 'In UserLoginFeature@getUrl |Landing URL fetched successfully  = ' . print_r($result, true));
        return $result;
    }

    /**
     * Author : Ravishankar Singh
     * @param $userId
     * @return array|null
     *
     * This function fetches the region id of a given user
     */
    public function setUserNoneRegion($userId)
    {
        log_message('DEBUG', 'In UserLoginFeature@setUserNoneRegion |Fetching userRegion record for user id = ' . print_r($userId, true));
        $conditions = array(array('user_id', $userId));
        $result = $this->RoUserRegion->getColumnsWhere($conditions, array('*'));
        if ($result->count() > 0) {
            log_message('DEBUG', 'In UserLoginFeature@setUserNoneRegion |userRegion successfully fetched for user id = ' . print_r($userId, true));
            return $result->toArray();
        }
        log_message('DEBUG', 'In UserLoginFeature@setUserNoneRegion |No record found for user id = ' . print_r($userId, true));
        return NULL;
    }

    /**
     * Author : Ravishankar Singh
     * @param $profileId
     * @return array
     *
     * This function fetches details of a given profile id
     */
    public function getProfileDetails($profileId)
    {
        log_message('INFO', 'In UserLoginFeature@getProfileDetails |Fetching profile records for profile id = ' . $profileId);
        $conditions = array(array('profile_id', $profileId));
        $result = $this->RoUserProfile->getColumnsWhere($conditions, array('*'));
        if ($result->count() > 0) {
            log_message('DEBUG', 'In UserLoginFeature@getProfileDetails |profileRecord successfully fetched for profile id = ' . $profileId);
            return $result->toArray();
        }
        log_message('DEBUG', 'In UserLoginFeature@getProfileDetails |No record found for profile id = ' . $profileId);
        return array();
    }
}