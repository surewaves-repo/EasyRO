<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function is_user_logged_in($javascript_redirect = 0)
{
    $CI = &get_instance();
    $logged_in_user = $CI->session->userdata("logged_in_user");
    $logged_user = $logged_in_user[0];
    if (!isset($logged_user) || empty($logged_user)) {
        if ($javascript_redirect == '1') {
            echo '<script language="javascript">top.location.href="' . ROOT_FOLDER . '/login";</script>';
            //RC:Deepak - redirect to root in else block
            //above RC is fixed
        } else {
            redirect("/");
        }
    } else {
        is_menu_hit();
    }
}

function is_menu_hit()
{
    $CI = &get_instance();
    $is_hit = $CI->ro_model->is_menu_hit();

    if ($is_hit) {
        find_data_for_cache();
    }
}

function find_data_for_cache()
{
    $CI = &get_instance();
    $user_id = get_user_id();

    $confirmation_data = $CI->ro_model->get_data_for_confirmation_customer($user_id);
    foreach ($confirmation_data as $val) {
        $order_id_ser = $val['order_id'];
        $confirmation_id = $val['confirmation_id'];
        $order_id = unserialize($order_id_ser);

        $key_channel = 'add_cancel_channel_' . $user_id . "_" . $order_id;
        $key_posted = "loadSave_" . $user_id . "_" . $order_id;

        delete_from_cache($key_channel);
        delete_from_cache($key_posted);

        $CI->ro_model->delete_from_confirmation($order_id_ser);
        $CI->ro_model->delete_from_confirmation_customer($confirmation_id, $user_id);
    }
}

function get_user_id()
{
    $CI = &get_instance();
    $logged_in_user = $CI->session->userdata("logged_in_user");
    return $logged_in_user[0]['user_id'];

}

//Caching
function store_caching($key, $value, $ttl = 86400)
{
    $memcache = new Memcached;
    $memcache->addServer(MEMCACHE_HOST, MEMCACHE_PORT);
    if (fetch($key)) {
        delete($key);
    }
    $memcache->set($key, $value, $ttl);
}

function fetch_from_caching($key)
{
    $memcache = new Memcached;
    $memcache->addServer(MEMCACHE_HOST, MEMCACHE_PORT);
    $value = $memcache->get($key);
    if (!empty($value) || isset($value)) {
        return $value;
    }
    return false;
}

function delete_from_cache($key)
{
    $memcache = new Memcached;
    $memcache->addServer(MEMCACHE_HOST, MEMCACHE_PORT);
    $memcache->delete($key);
}

function get_month($given_date)
{
    $month = date('M', strtotime($given_date));
    return $month;
}

function get_year($given_date)
{
    $year = date('y', strtotime($given_date));
    return $year;
}

function get_nw_ro_email($internal_ro_number)
{
    $CI = &get_instance();
    $ro_detail = $CI->am_model->get_ro_details_for_internal_ro($internal_ro_number);
    $nw_ro_email = '';
    switch ($ro_detail[0]['test_user_creation']) {
        case 0:
            $nw_ro_email = NW_RO_EMAIL;
            break;
        case 1:
            $nw_ro_email = TEST_NW_RO_EMAIL;
            break;
        case 2:
            $nw_ro_email = ADV_NW_RO_EMAIL;
            break;
        default:
            $nw_ro_email = NW_RO_EMAIL;
            break;
    }
    return $nw_ro_email;
}

function get_nw_ro_to_email_list($internal_ro_number, $network_details)
{
    $CI = &get_instance();
    $ro_detail = $CI->am_model->get_ro_details_for_internal_ro($internal_ro_number);
    $nw_ro_to_email_list = '';
    switch ($ro_detail[0]['test_user_creation']) {
        case 0:
            $nw_ro_to_email_list = $network_details['customer_email'];
            break;
        case 1:
            $nw_ro_to_email_list = $network_details['customer_email'];
            break;
        case 2:
            $nw_ro_to_email_list = ADV_NW_RO_EMAIL;
            break;
        default:
            $nw_ro_to_email_list = $network_details['customer_email'];
            break;
    }
    return $nw_ro_to_email_list;
}

function convert_into_array($getData, $key)
{
    $data = array();
    foreach ($getData as $val) {
        array_push($data, $val[$key]);
    }
    return $data;
}

function make_market_price($market_price)
{
    $data = "<table>";
    foreach ($market_price as $mp) {
        $data .= "<tr>";
        $data .= "<td>" . $mp['market_name'] . "</td>";
        $data .= "<td>" . $mp['spot_price'] . "</td>";
        $data .= "<td>" . $mp['banner_price'] . "</td>";
        $data .= "</tr>";
    }
    $data .= "</table>";
    return $data;
}

function make_market_price_tmp($market_price)
{
    $data = "<table>";
    foreach ($market_price as $mp) {
        $data .= "<tr>";
        $data .= "<td>" . $mp['market'] . "</td>";
        $data .= "<td>" . $mp['spot_price'] . "</td>";
        $data .= "<td>" . $mp['banner_price'] . "</td>";
        $data .= "</tr>";
    }
    $data .= "</table>";
    return $data;
}

