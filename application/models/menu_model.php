<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Menu_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_header_menu()
    {
        //Get Profile Id
        $profile_id = $this->get_profile_id();
        //Get Master Operation Id For Given Profile
        $operation_id = $this->get_operation_id_for_given_profile($profile_id);
        if (isset($operation_id)) {
            //Get All Task For Given Operation ID
            $task_detail = $this->get_task_detail_for_operation_id($operation_id, $profile_id);
            if (count($task_detail) > 0) {
                //$header_menu = $this->convert_into_header_menu($task_detail);echo print_r($header_menu,true);exit;
                $header_menu = $this->convert_into_header_menu_v1($task_detail);
                $html = $this->convert_into_html($header_menu);
                return $html;
            } else {
                //Make Default Menu Array
            }
        } else {
            //Make Default Menu Array
        }
    }

    public function get_profile_id()
    {
        $logged_in = $this->session->userdata("logged_in_user");
        return $logged_in[0]['profile_id'];
    }

    public function get_operation_id_for_given_profile($profile_id)
    {
        $data = array('Profile_FK_Id' => $profile_id);
        $result = $this->db->get_where('ro_profile_operation', $data);
        if ($result->num_rows() > 0) {
            $res = $result->result("array");
            return $res[0]['Operation_FK_Id'];
        }
        return array();
    }

    public function get_task_detail_for_operation_id($operation_id, $profile_id)
    {
        //$query = "select t.Id,Name,Url from ro_task as t inner join ro_operation_task as ot on t.Id=ot.Task_Fk_Id inner join ro_profile_operation as po on ot.Operation_Fk_Id=po.Operation_FK_Id where ot.Operation_Fk_Id = '$operation_id' and po.Profile_FK_Id='$profile_id' and t.Is_Menu='1' and t.Parent_Id is null " ;
        $query = "select t.Id,Name,Url,Parent_Id from ro_task as t inner join ro_operation_task as ot on t.Id=ot.Task_Fk_Id inner join ro_profile_operation as po on ot.Operation_Fk_Id=po.Operation_FK_Id where ot.Operation_Fk_Id = '$operation_id' and po.Profile_FK_Id='$profile_id' and t.Is_Menu='1' order by t.Id";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();

    }

    public function convert_into_header_menu_v1($task_detail)
    {
        $data = array(); //echo print_r($task_detail,true);exit;
        foreach ($task_detail as $dtls) {
            $name = $dtls['Name'];
            $url = $dtls['Url'];
            $id = $dtls['Id'];

            //Verify Whether Menu Having SubHeader
            $is_subheader = $this->having_subHeader_for_header_id($id);
            if (!isset($dtls['Parent_Id']) || empty($dtls['Parent_Id'])) {
                $data[$name] = $url;

                //Get child of given id
                $child_menu = $this->get_child_header_menu_v1($id, $task_detail);
                if (count($child_menu) > 0) {
                    $data[$name] = array();
                    $data[$name][$name] = $url;
                    $data[$name]['child'] = $child_menu;
                }
            }
        }
        return $data;
    }

    public function having_subHeader_for_header_id($id)
    {
        $query = "select * from ro_task where Parent_Id='$id' ";
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }

    public function get_child_header_menu_v1($id, $total_menu)
    {
        $data = array();
        foreach ($total_menu as $value) {
            if (!isset($value['Parent_Id']) || empty($value['Parent_Id'])) {
                continue;
            } else {
                if ($id == $value['Parent_Id']) {
                    $name = $value['Name'];
                    $url = $value['Url'];
                    $data[$name] = $url;
                }
            }
        }
        return $data;
    }

    public function convert_into_html($header_menu)
    {
        $html = '<ul id="nav">';
        foreach ($header_menu as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $key_1 => $value_1) {
                    if (!is_array($value_1)) {
                        $html .= '<li><a href="' . ROOT_FOLDER . '' . $value_1 . '">' . $key_1 . '</a>';
                    } else {
                        $html .= '<ul id="nav">';
                        foreach ($value_1 as $key_2 => $value_2) {
                            $html .= '<li><a href="' . ROOT_FOLDER . '' . $value_2 . '">' . $key_2 . '</a></li>';
                        }
                    }
                }
                $html .= '</ul></li>';
            } else {
                $html .= '<li><a href="' . ROOT_FOLDER . '' . $value . '">' . $key . '</a></li>';
            }
        }
        $html .= '</ul>';
        return $html;
    }

    public function convert_into_header_menu($task_detail)
    {
        $data = array(); //echo print_r($task_detail,true);exit;
        foreach ($task_detail as $dtls) {
            $name = $dtls['Name'];
            $url = $dtls['Url'];
            $id = $dtls['Id'];

            //Verify Whether Menu Having SubHeader
            $is_subheader = $this->having_subHeader_for_header_id($id);
            if (count($is_subheader) > 0) {
                $data[$name] = array();
                $data[$name][$name] = $url;

                //Get Child menu
                $child_menu = $this->get_child_header_menu($id);
                $data[$name]['child'] = $child_menu;
            } else {
                $data[$name] = $url;
            }
        }
        return $data;
    }

    public function get_child_header_menu($parent_id)
    {
        $profile_id = $this->get_profile_id();
        $operation_id = $this->get_operation_id_for_given_profile($profile_id);

        $child_details = $this->get_child_detail_for_parent_id($parent_id, $operation_id, $profile_id);
        $data = array();
        foreach ($child_details as $dtls) {
            $name = $dtls['Name'];
            $url = $dtls['Url'];
            $data[$name] = $url;
        }
        return $data;
    }

    public function get_child_detail_for_parent_id($parent_id, $operation_id, $profile_id)
    {
        $query = "select t.Id,Name,Url from ro_task as t inner join ro_operation_task as ot on t.Id=ot.Task_Fk_Id inner join ro_profile_operation as po on ot.Operation_Fk_Id=po.Operation_FK_Id where ot.Operation_Fk_Id = '$operation_id' and po.Profile_FK_Id='$profile_id' and t.Parent_Id='$parent_id' ";
        //$query = "select Name,Url from ro_task where Parent_Id='$parent_id' " ; 
        $res = $this->db->query($query);

        if ($res->num_rows() > 0) {
            return $res->result("array");
        }
        return array();
    }
}
/* End of file Menu_model.php */
/* Location: ./application/models/Menu_model.php */