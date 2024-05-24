<?php

class Utility_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function getBrands()
    {
        //get count
        $this->_buildQuery();
        $query = $this->db->get();
        $total_row = $query->num_rows();

        //get limit records
        $this->_buildQuery();
        $page = $this->input->post('page');
        $limit = $this->input->post('rp');
        $offset = 0;
        if ($page > 1)
            $offset = ($page - 1) * $limit;

        if (isset($limit) && isset($offset))
            $this->db->limit($limit, $offset);
        $query = $this->db->get();
        $records = $query->result_array();

        return $this->build_ro_record_json($records, $total_row, $page);
    }

    private function _buildQuery()
    {
        $this->db->select('sv_category.category_name, sv_product_group.product_group, sv_new_advertiser.advertiser, sv_new_brand.brand');
        $this->db->from('sv_new_brand');
        $this->db->join('sv_new_advertiser', 'sv_new_advertiser.id = sv_new_brand.new_advertiser_id', 'left');
        $this->db->join('sv_product_group', 'sv_product_group.id = sv_new_advertiser.product_group_id');
        $this->db->join('sv_category', 'sv_category.category_id = sv_product_group.cat_id');
        //$this->db->where("sv_new_advertiser.active", "1");
        $this->db->order_by("sv_category.category_name, sv_product_group.product_group, sv_new_advertiser.advertiser, sv_new_brand.brand");

        // filter by search
        $Search = $this->input->post('Search');
        $SearchType = $this->input->post('SearchType');
        if ($Search != "" && $SearchType != "")
            $this->db->like($SearchType, $Search);
    }

    public function build_ro_record_json($records, $total_row, $page)
    {
        $json = Array();
        $items = Array();
        foreach ($records as $key => $record) {
            $items[] = array(
                'id' => "",
                'cell' => array(
                    $record['category_name'],
                    $record['product_group'],
                    $record['advertiser'],
                    $record['brand'],
                )
            );
        }
        $json['page'] = $page;
        $json['total'] = $total_row;
        $json['rows'] = $items;
        return json_encode($json);
    }

    public function getCategory()
    {
        $this->db->select('*');
        $this->db->from("sv_category");
        $this->db->order_by("category_name");
        $query = $this->db->get();
        $records = $query->result_array();
        return $records;
    }

    public function getAgency()
    {
        $this->db->select('*');
        $this->db->from("sv_new_agency");
        $this->db->group_by('agency_name');
        $this->db->order_by("agency_name");
        $query = $this->db->get();
        $records = $query->result_array();
        return $records;
    }

    public function getAgencyDisplays($agency_id)
    {
        $this->db->select('*');
        $this->db->from("sv_agency_display");
        $this->db->order_by("agency_display_name");
        $this->db->where('agency_id =', $agency_id);
        $query = $this->db->get();
        $records = $query->result_array();
        return $records;
    }

    public function checkCategory($CategoryID, $search = "")
    {
        $this->db->select('*');
        $this->db->from("sv_category");
        if ($search != "")
            $this->db->where("LOWER(category_name)", strtolower(trim($search)));
        if ($CategoryID != "" && $CategoryID != 0)
            $this->db->where('category_id !=', $CategoryID);
        $this->db->order_by("category_name");
        $query = $this->db->get();
        $records = $query->result_array();
        return $records;
    }

    public function getProducts($CategoryID)
    {
        $this->db->select('*');
        $this->db->from("sv_product_group");
        $this->db->where('cat_id', $CategoryID);
        $this->db->order_by("product_group");
        $query = $this->db->get();
        $records = $query->result_array();
        return $records;
    }

    public function checkProducts($CategoryID, $ProductID, $search = "")
    {
        $this->db->select('*');
        $this->db->from("sv_product_group");
        $this->db->where('cat_id', $CategoryID);
        if ($search != "")
            $this->db->where("LOWER(product_group)", strtolower(trim($search)));
        if ($ProductID != "" && $ProductID != 0)
            $this->db->where('id !=', $ProductID);
        $this->db->order_by("product_group");
        $query = $this->db->get();
        $records = $query->result_array();
        return $records;
    }

    public function getAdvertiser($ProductID, $active = 1)
    {
        $this->db->select('*');
        $this->db->from("sv_new_advertiser");
        $this->db->where('product_group_id', $ProductID);
        $this->db->where('active', $active);
        $this->db->order_by("advertiser");
        $query = $this->db->get();
        $records = $query->result_array();
        return $records;
    }

    public function getAllAdvertiser($ProductID)
    {
        //$this->db->distinct();
        $this->db->select('*');
        $this->db->from("sv_new_advertiser");
        $this->db->where('product_group_id', $ProductID);
        $this->db->group_by('advertiser');
        $this->db->order_by("advertiser");
        $query = $this->db->get();
        $records = $query->result_array();
        return $records;
    }

    public function getAdvertiserDisplays($advertiser)
    {
        $this->db->select('*');
        $this->db->from("sv_advertiser_display");
        $this->db->where('advertiser_id =', $advertiser);
        $this->db->order_by("advertiser_display_name");
        $query = $this->db->get();
        $records = $query->result_array();
        return $records;
    }

    public function checkAdvertiser($ProductID, $AdvertiserID, $search = "")
    {
        $this->db->select('*');
        $this->db->from("sv_advertiser_display");
        //$this->db->where('product_group_id', $ProductID);
        if ($search != "")
            $this->db->where("LOWER(advertiser_display_name)", strtolower(trim($search)));
        /*if($AdvertiserID!="" && $AdvertiserID!=0)
            $this->db->where('id !=', $AdvertiserID);*/
        $this->db->order_by("advertiser_display_name");
        $query = $this->db->get();
        $records = $query->result_array();
        return $records;
    }

    public function getBrand($AdvertiserID)
    {
        $this->db->select('*');
        $this->db->from("sv_new_brand");
        $this->db->where('new_advertiser_id', $AdvertiserID);
        $this->db->order_by("brand");
        $query = $this->db->get();
        $records = $query->result_array();
        return $records;
    }

    public function get_advertiser_details($advertiser_display_name)
    {
        $this->db->select('*');
        $this->db->from("sv_advertiser_display");
        $this->db->join('sv_new_advertiser', 'sv_new_advertiser.id=sv_advertiser_display.advertiser_id', 'inner');
        $this->db->where('advertiser_display_name', $advertiser_display_name);
        $this->db->group_by('advertiser_display_name');
        $query = $this->db->get();
        $records = $query->result_array();
        return $records;
    }

    public function checkBrand($AdvertiserID, $BrandID, $search = "")
    {
        $this->db->select('*');
        $this->db->from("sv_new_brand");
        $this->db->where('new_advertiser_id', $AdvertiserID);
        if ($search != "")
            $this->db->where("LOWER(brand)", strtolower(trim($search)));
        if ($BrandID != "" && $BrandID != 0)
            $this->db->where('id !=', $BrandID);
        $this->db->order_by("brand");
        $query = $this->db->get();
        $records = $query->result_array();
        return $records;
    }

    public function saveAdvertiser($advertiserID)
    {
        $data = array('active' => 1);
        $this->db->where('id', $advertiserID);
        $res = $this->db->update('sv_new_advertiser', $data);
        return $res;
    }

    function addCategory($category_name, $category_id)
    {
        $data = array('category_name' => $category_name);
        $res = "Category added successfully !!";
        if ($category_id != 0) {
            $this->db->where('category_id', $category_id);
            $this->db->update('sv_category', $data);
            $res = "Category updated successfully !!";
        } else
            $this->db->insert('sv_category', $data);
        return $res;
    }

    function addProduct($cat_id, $product_group, $ProductID)
    {
        $res = "Product added successfully !!";
        $data = array('product_group' => $product_group, "cat_id" => $cat_id);
        if ($ProductID != 0) {
            $this->db->where('id', $ProductID);
            $this->db->update('sv_product_group', $data);
            $res = "Product updated successfully !!";
        } else
            $this->db->insert('sv_product_group', $data);
        return $res;
    }

    function addAdvertiser($product_group_id, $advertiser, $advertiser_display, $AdvertiserID, $direct_client, $billing_info = NULL, $billing_address = NULL, $bill_cycle = NULL, $postal_address = NULL, $edit_advertiser_id, $edit_advertiser_display, $selected_advertiser_name)
    {
        $res = "Advertiser added successfully !!";
        $adv_data = array('advertiser' => $advertiser);//,'product_group_id'=>$product_group_id);
        $adv_display_data = array('advertiser_display_name' => $advertiser_display, 'direct_client' => $direct_client,
            'billing_info' => $billing_info, 'billing_address' => $billing_address, 'billing_cycle' => $bill_cycle, 'client_address' => $postal_address);

        /*if($edit_advertiser_display !='')
		{
            //when an advertiser display is updated
			//$this->db->where('advertiser_display_name', $advertiser_display);
            $this->db->where('advertiser_display_name', $edit_advertiser_display);
			$this->db->update('sv_new_advertiser', $adv_data);
			$res = "Advertiser updated successfully !!";
		}
		else
		{

        }*/

        if ($edit_advertiser_id == '') {
            //when new advertiser is added
            //$adv_data['active'] = 1;
            $this->db->insert('sv_new_advertiser', array('advertiser' => $advertiser, 'product_group_id' => $product_group_id, 'active' => 1));
            //echo $this->db->last_query();exit;
            $advertiser_id = $this->db->insert_id();
            $adv_display_data['advertiser_id'] = $advertiser_id;
            $this->db->insert('sv_advertiser_display', $adv_display_data);
        } else {
            //when an advertiser is updated
            //$this->db->where('id', $edit_advertiser_id);
            $this->db->where('advertiser', $selected_advertiser_name);
            $this->db->update('sv_new_advertiser', $adv_data);
            //echo $this->db->last_query();exit;
            $this->updateAdvertiserForAllContacts($advertiser_display, $advertiser, $edit_advertiser_display, $selected_advertiser_name);
            $res = "Advertiser updated successfully !!";

            if ($edit_advertiser_display == '') {
                //when new advertiser display is added
                $adv_display_data['advertiser_id'] = $edit_advertiser_id;
                $this->db->insert('sv_advertiser_display', $adv_display_data);
            } else {
                //when an advertiser display is updated
                $this->db->where('advertiser_display_name', $edit_advertiser_display);
                $this->db->update('sv_advertiser_display', $adv_display_data);
                $res = "Advertiser updated successfully !!";
            }

        }

        /*if($advertiser != $edit_advertiser_id){
            $this->updateAdvertiserForAllContacts($advertiser_display,$advertiser);
        }*/

        return $res;
    }

    function updateAdvertiserForAllContacts($advertiser_display, $advertiser, $edit_advertiser_display, $selected_advertiser_name)
    {
        $data = array('client_name' => $advertiser, 'client_display_name' => $advertiser_display);
        $this->db->where('client_display_name', $edit_advertiser_display);
        $this->db->where('client_name', $selected_advertiser_name);
        $this->db->update('ro_client_contact', $data);
    }

    function addBrand($new_advertiser_id, $brand, $BrandID)
    {
        $res = "Brand added successfully !!";
        $data = array('brand' => $brand, "new_advertiser_id" => $new_advertiser_id);
        if ($BrandID != 0) {
            $this->db->where('id', $BrandID);
            $this->db->update('sv_new_brand', $data);
            $res = "Brand updated successfully !!";
        } else
            $this->db->insert('sv_new_brand', $data);
        return $res;
    }

    public function checkAgencyDisplay($agencyDis)
    {
        $this->db->select('*');
        $this->db->from("sv_agency_display");
        $this->db->where('agency_display_name', $agencyDis);
        $query = $this->db->get();
        $records = $query->result_array();
        return $records;
    }

    public function checkAgency($agency_name)
    {
        $this->db->select('*');
        $this->db->from("sv_new_agency");
        $this->db->where('agency_name', $agency_name);
        $query = $this->db->get();
        $records = $query->result_array();
        return $records;
    }

    function addAgency($agency_name, $agency_dis_name, $postal_address = NULL, $billing_info = NULL, $billing_address = NULL, $bill_cycle = NULL, $edit_agency_id, $edit_agency_dis_name)
    {
        $res = "Agency added successfully !!";
        $agency_data = array('agency_name' => $agency_name, 'internal_agency' => 0);

        $agency_display_data = array('agency_display_name' => $agency_dis_name,
            'agency_address' => $postal_address, 'billing_info' => $billing_info, 'billing_address' => $billing_address, 'billing_cycle' => $bill_cycle);

        if ($edit_agency_id == '') {
            //when new agency is added
            $this->db->insert('sv_new_agency', $agency_data);
            $agency_id = $this->db->insert_id();
            $agency_display_data['agency_id'] = $agency_id;
            $this->db->insert('sv_agency_display', $agency_display_data);
        } else {
            //when an agency is updated
            $this->db->where('id', $edit_agency_id);
            $this->db->update('sv_new_agency', $agency_data);
            $this->updateAgencyForAllContacts($agency_dis_name, $agency_name);
            $res = "Agency updated successfully !!";

            if ($edit_agency_dis_name == '') {
                //when new agency display is added
                $agency_display_data['agency_id'] = $edit_agency_id;
                $this->db->insert('sv_agency_display', $agency_display_data);
            } else {
                //when an agency display is updated
                $this->db->where('agency_display_name', $edit_agency_dis_name);
                $this->db->update('sv_agency_display', $agency_display_data);
                $res = "Agency updated successfully !!";
            }

        }

        /*if($agency_name != $edit_agency_dis_name){
            $this->updateAgencyForAllContacts($agency_dis_name,$agency_name);
        }*/

        return $res;
    }

    function updateAgencyForAllContacts($agency_dis_name, $agency_name)
    {
        $data = array('agency_name' => $agency_name);
        $this->db->where('agency_display_name', $agency_dis_name);
        $this->db->update('ro_agency_contact', $data);
    }

    public function updateMappedBrAdv($mg_advId, $br_advId)
    {
        $data = array('br_advertiser_id' => $br_advId);
        $this->db->where('id', $mg_advId);
        $this->db->update('sv_new_advertiser', $data);
    }

    public function updateMappedBrBrand($mg_advId, $mg_BrandId, $br_BrandId)
    {
        $whereArr = array('id' => $mg_BrandId, 'new_advertiser_id' => $mg_advId);
        $data = array('br_brand_id' => $br_BrandId);
        $this->db->where($whereArr);
        $this->db->update('sv_new_brand', $data);
    }

    public function getBrandWithoutBr($AdvertiserID)
    {
        $this->db->select('*');
        $this->db->from("sv_new_brand");
        $whereArr = array('new_advertiser_id' => $AdvertiserID, 'br_brand_id' => NULL);
        $this->db->where($whereArr);
        $this->db->order_by("brand");
        $query = $this->db->get();
        $records = $query->result_array();
        if (count($records) > 0) {
            return $records;
        } else {
            return array();
        }

    }

    public function getBrandWithBr($AdvertiserID)
    {
        $this->db->select('br_brand_id');
        $this->db->from("sv_new_brand");
        $whereArr = array('new_advertiser_id' => $AdvertiserID, 'br_brand_id IS NOT NULL' => NULL);
        $this->db->where($whereArr);
        $this->db->order_by("brand");
        $query = $this->db->get();
        $records = $query->result_array();
        if (count($records) > 0) {
            return $records;
        } else {
            return array();
        }
    }

    public function getmappedBrAdvId($AdvertiserID)
    {
        $this->db->select('br_advertiser_id');
        $this->db->from("sv_new_advertiser");
        $whereArr = array('id' => $AdvertiserID, 'br_advertiser_id IS NOT NULL' => NULL);
        $this->db->where($whereArr);
        $query = $this->db->get();
        $records = $query->result_array();
        if (count($records) > 0) {
            return $records;
        } else {
            return array();
        }
    }

    public function unMapMgAdvertiser($mg_advId)
    {
        $data = array('br_advertiser_id' => NULL);
        $this->db->where('id', $mg_advId);
        $this->db->update('sv_new_advertiser', $data);
    }

    public function unMapMgAdvertiserBrand($mg_advId)
    {
        $whereArr = array('new_advertiser_id' => $mg_advId);
        $data = array('br_brand_id' => NULL);
        $this->db->where($whereArr);
        $this->db->update('sv_new_brand', $data);
    }

    public function getMGMapInfo($br_advId)
    {
        $this->db->select('id');
        $this->db->from("sv_new_advertiser");
        $whereArr = array('br_advertiser_id' => $br_advId);
        $this->db->where($whereArr);
        $query = $this->db->get();
        $records = $query->result_array();
        if (count($records) > 0) {
            return $records;
        } else {
            return array();
        }
    }

    public function getOnlyAdvertisersBYName($advertiserName)
    {
        $this->db->select('id');
        $this->db->from("sv_new_advertiser");
        $whereArr = array('advertiser' => $advertiserName);
        $this->db->where($whereArr);
        $query = $this->db->get();
        $records = $query->result_array();
        return $records;
    }

    public function getAllAdvertiserList()
    {
        $this->db->select('*');
        $this->db->from("sv_new_advertiser");
        $this->db->group_by('advertiser');
        $query = $this->db->get();

        $records = $query->result_array();
        //echo $this->db->last_query();exit;
        return $records;
    }

    public function getAllAdvertiserDisplayNames($advertiser_name)
    {
        $this->db->distinct();
        $this->db->select('sv_new_advertiser.advertiser,sv_advertiser_display.advertiser_display_name,sv_advertiser_display.direct_client,sv_advertiser_display.billing_info,sv_advertiser_display.billing_address,sv_advertiser_display.billing_cycle,sv_advertiser_display.client_address');
        $this->db->from("sv_advertiser_display");
        $this->db->join('sv_new_advertiser', 'sv_new_advertiser.id=sv_advertiser_display.advertiser_id', 'inner');
        $this->db->where('sv_new_advertiser.advertiser', $advertiser_name);
        $query = $this->db->get();
        $records = $query->result_array();
        return $records;
    }

    public function getOnlyBrandsBYName($brandName)
    {
        $this->db->select('*');
        $this->db->from("sv_new_brand");
        $whereArr = array('brand' => $brandName);
        $this->db->where($whereArr);
        $query = $this->db->get();
        $records = $query->result_array();
        return $records;

    }

    public function activateAdvertiser($advertiserName)
    {
        $data = array('active' => 1);
        $this->db->where('advertiser', $advertiserName);
        $this->db->update('sv_new_advertiser', $data);
        return true;
    }

    public function getAdvertiserName($advertiserID)
    {

        $whereArr = array('id' => $advertiserID);
        $this->db->select('advertiser');
        $this->db->from("sv_new_advertiser");
        $this->db->where($whereArr);
        $query = $this->db->get();
        $records = $query->result_array();
        return $records[0]['advertiser'];
    }
}

?>
