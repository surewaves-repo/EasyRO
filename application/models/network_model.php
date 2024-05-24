<?php

class NETWORK_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function getAllEnterprise()
    {
        $query = "select * from sv_customer order by customer_name";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function getEnterpriseUploadAttachment($customerId)
    {
        $query = "select sc.billing_number as billingNumber,sc.customer_name,scua.* from sv_customer sc "
            . "Left Join sv_customer_upload_attachment scua ON sc.customer_id = scua.customer_id "
            . "where sc.customer_id = " . $customerId;
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return NULL;
    }

    public function getEnterpriseContactDetails($customerId)
    {
        $query = "select * from sv_customer_contact_details where customer_id = " . $customerId;
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return NULL;
    }

    public function getEnterpriseFinanceDetails($customerId)
    {
        $query = "select * from sv_customer_finance_details where customer_id = " . $customerId;
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return NULL;
    }

    public function setEntepriseData($table_name, $data_array)
    {
        $this->db->insert($table_name, $data_array);
        if ($this->db->_error_message()) {
            return 0;
        } else {
            if ($table_name == 'sv_customer_contact_details') {
                $update_query = "UPDATE sv_customer SET customer_email = '" . $data_array['email_id'] . "' WHERE customer_id ='" . $data_array['customer_id'] . "'";

                $res = $this->db->query($update_query);
            }
            return $this->db->insert_id();
        }

    }

    public function updateEntepriseData($table_name, $data, $fieldName, $fieldValue)
    {
        $this->db->where($fieldName, $fieldValue);
        $this->db->update($table_name, $data);
        if ($this->db->_error_message()) {
            return 0;
        } else {
            if ($table_name == 'sv_customer_contact_details') {
                $update_query = "UPDATE sv_customer SET customer_email = '" . $data['email_id'] . "' WHERE " . $fieldName . "='" . $fieldValue . "'";

                $res = $this->db->query($update_query);
            }
            return 1;
        }
    }

    public function getAllChannelsForEnterprise($customerId)
    {
        $query = "select * from sv_tv_channel where enterprise_id = " . $customerId;
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return NULL;
    }

    public function getEnterpriseChannelDetails($channel_id)
    {
        $query = "select * from sv_channel_contact_details where tv_channel_id = " . $channel_id;
        $query = $this->db->query($query);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $data['contact_person1'] = $row->contact_person1;
            $data['contact_person2'] = $row->contact_person2;
            $data['contact_person3'] = $row->contact_person3;
            $data['email_id1'] = $row->email_id1;
            $data['email_id2'] = $row->email_id2;
            $data['email_id3'] = $row->email_id3;
            $data['language'] = $row->language;
            $data['contact_number1'] = $row->contact_number1;
            $data['contact_number2'] = $row->contact_number2;
            $data['contact_number3'] = $row->contact_number3;
            $data['address'] = $row->address;
            $data['genre'] = $row->genre;
            $data['dominant_content'] = $row->dominant_content;
            $data['has_value'] = 1;
        } else {
            $data['contact_person1'] = '';
            $data['contact_person2'] = NULL;
            $data['contact_person3'] = NULL;
            $data['email_id1'] = '';
            $data['email_id2'] = NULL;
            $data['email_id3'] = NULL;
            $data['contact_number1'] = '';
            $data['contact_number2'] = NULL;
            $data['contact_number3'] = NULL;
            $data['address'] = '';
            $data['genre'] = '';
            $data['language'] = '';
            $data['dominant_content'] = '';
            $data['has_value'] = 0;
        }
        return $data;
    }

    public function getMarketWiseData()
    {
        $query = "SELECT ssm.id,ssm.sw_market_name as state,ssm.sw_reach as share,ssm.tv_house_holds as tvHH,ssm.cns_house_holds as cns,ssm.ctv_dth_house_holds as cabletv,ssm.sw_partner as sw_partner,GROUP_CONCAT(stc.tv_channel_id) as channel_id,GROUP_CONCAT(stc.display_name) as channel_names FROM sv_sw_market AS ssm
INNER JOIN sv_market_x_channel AS svmxc ON ssm.id = svmxc.market_fk_id
INNER JOIN sv_tv_channel AS stc ON svmxc.channel_fk_id = stc.tv_channel_id
INNER JOIN sv_customer AS sc ON stc.enterprise_id = sc.customer_id
WHERE ssm.is_cluster =0 AND ssm.id != 26  AND  stc.tv_channel_id NOT IN (select channel_id from sv_all_india_channel_for_reach)
 AND stc.is_blocked = 0 AND stc.is_satellite_channel = 0 
group by ssm.sw_market_name";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return NULL;
    }

    public function save_spotTvReach_data($table_name, $data, $fieldName, $fieldValue)
    {
        $this->db->where($fieldName, $fieldValue);
        $this->db->update($table_name, $data);
        if ($this->db->_error_message()) {
            return 0;
        } else {
            return 1;
        }
    }

    public function getTamOverViewData()
    {
        $query = "SELECT total_households,total_tv_households,total_cns_households FROM sv_tam_overview";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return NULL;
    }

    public function generateReachReport($channel_list)
    {
        $retArr = array();
        $search_str = '';
        if ($channel_list != '') {
            $search_str = "and stc.tv_channel_id NOT IN($channel_list)";
        }
        $query = "SELECT ssm.id,ssm.sw_market_name as state,ssm.sw_reach as share,ssm.tv_house_holds as tvHH,ssm.cns_house_holds as cns,ssm.ctv_dth_house_holds as cabletv,
                ssm.sw_partner as sw_partner, GROUP_CONCAT(stc.tv_channel_id) as channel_id,GROUP_CONCAT(stc.display_name) as channel_names,GROUP_CONCAT(stc.locale) as market,
                GROUP_CONCAT(sc.customer_display_name) as network_name,GROUP_CONCAT(sc.customer_name) as customer_name  FROM sv_sw_market AS ssm
                    INNER JOIN sv_market_x_channel AS svmxc ON ssm.id = svmxc.market_fk_id
                    INNER JOIN sv_tv_channel AS stc ON svmxc.channel_fk_id = stc.tv_channel_id
                    INNER JOIN sv_customer AS sc ON stc.enterprise_id = sc.customer_id
                    WHERE ssm.is_cluster = 0 AND ssm.id != 26 AND stc.is_satellite_channel = 0 AND stc.tv_channel_id NOT IN (select channel_id from sv_all_india_channel_for_reach)  AND stc.is_blocked = 0 $search_str  group by ssm.sw_market_name";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            $retArr[0] = $res->result("array");
        }

        $query_new = "SELECT total_households,total_tv_households,total_cns_households FROM sv_tam_overview";
        $res_new = $this->db->query($query_new);
        if ($res_new->num_rows() > 0) {
            $retArr[1] = $res_new->result("array");
        }
        $query = $query_new = $res = '';

        $query = "SELECT stc.tv_channel_id as channel_id, stc.display_name, stc.spot_avg as rate,saicr.market FROM `sv_all_india_channel_for_reach` AS saicr INNER JOIN sv_tv_channel AS stc
                  ON saicr.`channel_id` = stc.tv_channel_id";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            $retArr[2] = $res->result("array");
        }
        $query = $query_new = $res = '';
        $query = "SELECT stc.tv_channel_id as channel_id, stc.display_name ,stc.spot_avg as rate,stc.channel_share,ssm.sw_market_name as market FROM
                  sv_sw_market AS ssm INNER JOIN sv_market_x_channel AS svmxc ON ssm.id = svmxc.market_fk_id
                                      INNER JOIN sv_tv_channel AS stc ON svmxc.channel_fk_id = stc.tv_channel_id
                                      WHERE stc.is_satellite_channel = 1 and ssm.is_cluster = 0";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            $retArr[3] = $res->result("array");
        }
        return $retArr;
    }

    public function fetchGenereAndLanguageByChannelWise($startLimit, $endlimit, $searchBy = '')
    {
        $query = "Select ssm.id,ssm.sw_market_name as state,sc.customer_display_name as network_name,stc.display_name as channel_names,
					stc.tv_channel_id as channel_id, stc.deployment_status,sccd.genre AS genre, sccd.language AS language,sccd.dominant_content AS dominant_content  FROM sv_sw_market AS ssm 
					INNER JOIN sv_market_x_channel AS svmxc ON ssm.id = svmxc.market_fk_id
					INNER JOIN sv_tv_channel AS stc ON svmxc.channel_fk_id = stc.tv_channel_id
					INNER JOIN sv_customer AS sc ON stc.enterprise_id = sc.customer_id
					LEFT OUTER JOIN sv_channel_contact_details AS sccd ON stc.tv_channel_id = sccd.tv_channel_id
					WHERE  stc.is_blocked = 0  AND ssm.is_cluster = 0  AND sc.customer_display_name != '' AND stc.is_blocked = 0  ORDER BY ssm.sw_market_name limit $startLimit,$endlimit";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            $retArr = $res->result("array");
        }
        return $retArr;
    }

    public function getAllGenre()
    {
        $query = "SELECT * FROM sv_master_genre";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            $retArr = $res->result("array");
        }
        return $retArr;
    }

    public function getAllLanguage()
    {
        $query = "SELECT * FROM sv_master_language";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $retArr = $res->result("array");
        }
        return $retArr;
    }

    public function getNetworkDetails($customer_id)
    {
        $query = 'SELECT customer_id,customer_name,customer_display_name FROM sv_customer WHERE customer_id = "' . $customer_id . '"';
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $retArr = $res->result("array");
        }
        return $retArr;
    }

    public function getGenre($genreIds = '')
    {
        if ($genreIds != '') {
            $query = "SELECT id,genre FROM sv_master_genre WHERE id IN('" . $genreIds . "')";
        } else {
            $query = 'SELECT id,genre FROM sv_master_genre';
        }
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $retArr = $res->result("array");
        }
        return $retArr;
    }

    public function getlanguage($languageIds = '')
    {
        if ($languageIds != '') {
            $query = "SELECT id,language FROM sv_master_language WHERE id IN('" . $languageIds . "')";
        } else {
            $query = 'SELECT id,language FROM sv_master_language';
        }
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $retArr = $res->result("array");
        }
        return $retArr;
    }

    public function updateLanguageGenre($whereData, $data)
    {
        if ($this->checkForChannelContact($whereData)) {
            $this->db->update('sv_channel_contact_details', $data, array('tv_channel_id' => $whereData));
        } else {

            $data['tv_channel_id'] = $whereData;
            $this->db->insert('sv_channel_contact_details', $data);
        }
        if ($this->db->_error_message()) {
            return 0;
        } else {
            return 1;
        }
    }

    public function checkForChannelContact($channel_id)
    {
        $query = "SELECT * FROM sv_channel_contact_details WHERE tv_channel_id='" . $channel_id . "'";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function showGenereAndLanguageByChannelWise()
    {
        $query = "Select ssm.id, ssm.sw_market_name as state,sc.customer_display_name as network_name,stc.display_name as channel_names,
					stc.tv_channel_id as channel_id, stc.deployment_status,sccd.genre AS genre, sccd.language AS language,sccd.dominant_content AS dominant_content  FROM sv_sw_market AS ssm 
					INNER JOIN sv_market_x_channel AS svmxc ON ssm.id = svmxc.market_fk_id
					INNER JOIN sv_tv_channel AS stc ON svmxc.channel_fk_id = stc.tv_channel_id
					INNER JOIN sv_customer AS sc ON stc.enterprise_id = sc.customer_id
					LEFT OUTER JOIN sv_channel_contact_details AS sccd ON stc.tv_channel_id = sccd.tv_channel_id
					WHERE  stc.is_blocked = 0  AND ssm.is_cluster = 0  AND sc.customer_display_name != '' AND stc.is_blocked = 0 ORDER BY ssm.sw_market_name ";

        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            $retArr = $res->result("array");
        }
        return $retArr;
    }

    public function insertUploadAttachment($userData)
    {
        $this->db->insert('sv_customer_upload_attachment', $userData);
    }

    public function getUploadAttachment($userData)
    {
        $result = $this->db->get_where('sv_customer_upload_attachment', $userData);
        if ($result->num_rows() > 0) {
            return $result->result("array");
        }
        return array();
    }

    public function updateUploadAttachment($userData, $where)
    {
        $this->db->update('sv_customer_upload_attachment', $userData, $where);
    }

    public function getDistrictReachData($channel_list)
    {
        $search_str = '';
        $retArr = array();
        if ($channel_list != '') {
            $search_str = "and stc.tv_channel_id NOT IN($channel_list)";
        }

        /*$query = "select ssws.state_id,ssws.state_name,sswm.id as market_id,sswm.sw_market_name as market_name,
        sswd.district_id,sswd.district_name,sc.customer_id,sc.customer_display_name as network_name,
        stc.tv_channel_id as channel_id,stc.display_name as channel_name from sv_sw_market as sswm
        INNER JOIN sv_market_x_states as smxs ON sswm.id =  smxs.market_id
        INNER JOIN sv_sw_states as ssws  ON ssws.state_id = smxs.state_id
        INNER JOIN sv_sw_districts as sswd ON sswd.state_id = ssws.state_id
        LEFT OUTER JOIN sv_channel_x_district as scxd ON scxd.district_id = sswd.district_id
        LEFT OUTER JOIN sv_tv_channel as stc ON stc.tv_channel_id = scxd.channel_id and stc.tv_channel_id NOT IN (select tv_channel_id from sv_tv_channel where is_blocked = 1 and visible_in_cx_online = 0 )
        LEFT OUTER JOIN sv_customer as sc ON stc.enterprise_id = sc.customer_id
        where 1 $search_str
        order by ssws.state_name asc,
        sw_market_name asc,sswd.district_name asc";*/
        $query = "select x.state_id,x.state_name,
x.district_id,x.district_name,x.customer_id,x.network_name,
x.channel_id,x.channel_name from
((select ssws.state_id,ssws.state_name,
sswd.district_id,sswd.district_name,sc.customer_id,sc.customer_display_name as network_name,
stc.tv_channel_id as channel_id,stc.display_name as channel_name, stc.is_blocked = 1 and stc.visible_in_cx_online = 0 from sv_sw_market as sswm
INNER JOIN sv_market_x_states as smxs ON sswm.id =  smxs.market_id
INNER JOIN sv_sw_states as ssws  ON ssws.state_id = smxs.state_id
INNER JOIN sv_sw_districts as sswd ON sswd.state_id = ssws.state_id
INNER JOIN sv_channel_x_district as scxd ON scxd.district_id = sswd.district_id
INNER JOIN sv_tv_channel as stc ON stc.tv_channel_id = scxd.channel_id and stc.tv_channel_id NOT IN (select tv_channel_id from sv_tv_channel where is_blocked = 1)
INNER JOIN sv_customer as sc ON stc.enterprise_id = sc.customer_id
where 1 $search_str
)
UNION
(select ssws.state_id,ssws.state_name,
sswd.district_id,sswd.district_name,sc.customer_id,sc.customer_display_name as network_name,
stc.tv_channel_id as channel_id,stc.display_name as channel_name , stc.is_blocked = 1 and stc.visible_in_cx_online = 0 from sv_sw_market as sswm
INNER JOIN sv_market_x_states as smxs ON sswm.id =  smxs.market_id
INNER JOIN sv_sw_states as ssws  ON ssws.state_id = smxs.state_id
INNER JOIN sv_sw_districts as sswd ON sswd.state_id = ssws.state_id
LEFT OUTER JOIN sv_channel_x_district as scxd ON scxd.district_id = sswd.district_id
LEFT OUTER JOIN sv_tv_channel as stc ON stc.tv_channel_id = scxd.channel_id and stc.tv_channel_id NOT IN (select tv_channel_id from sv_tv_channel where is_blocked = 1)
LEFT OUTER JOIN sv_customer as sc ON stc.enterprise_id = sc.customer_id  where 1  $search_str
group by  sswd.district_id
having count(sswd.district_id) = 1))x 
order by x.state_name asc,x.district_name asc";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $retArr = $res->result("array");
        }
        return $retArr;
    }

    public function getMarketListForStates($state_id)
    {
        $retArr = array();
        $query = "Select ssm.id ,ssm.sw_market_name from sv_sw_market as ssm
					INNER JOIN sv_market_x_states as smxs ON smxs.market_id =  ssm.id
					INNER JOIN sv_sw_states as sss ON sss.state_id = smxs.state_id
					where sss.state_id = '$state_id'";
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $retArr = $res->result("array");
        }
        return $retArr;
    }

    public function getdominantContent($dominanatContentIds = '')
    {
        $retArr = array();
        if ($dominanatContentIds != '') {
            $query = "SELECT id,dominant_content FROM sv_master_dominant_content WHERE id IN('" . $dominanatContentIds . "')";
        } else {
            $query = 'SELECT id,dominant_content FROM sv_master_dominant_content';
        }
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $retArr = $res->result("array");
        }
        return $retArr;
    }

    public function getRDNEnterprises()
    {
        $retArr = array();
        $query = 'select distinct  isc.customer_id_mso from ir_screen_config isc';
        $res = $this->db->query($query);
        if ($res->num_rows() > 0) {
            $retArr = $res->result("array");
        }
        return $retArr;
    }

    public function updateNetworkPrimaryEmails($table, $whereArr, $updateArr)
    {
        $this->db->where($whereArr);
        $this->db->update($table, $updateArr);

    }
}

?>
