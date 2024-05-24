<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function set_form_validation($rules)
{
    $CI = &get_instance();

    $CI->load->library('form_validation');
    foreach ($rules as $rule) {
        $CI->form_validation->set_rules($rule['key'], $rule['value'], $rule['rule']);
    }

    if ($CI->form_validation->run() == FALSE) {
        return true;
    } else {
        return false;
    }
}

function set_form_validation_error_messages( $messages ){
    $CI = & get_instance();
    $CI->load->library('form_validation');

    $CI->form_validation->set_message($messages['rule'], $messages['errorMessage']);
}

function show_form_validation_error($url, $model = null)
{
    $CI = &get_instance();

    $CI->load->library('form_validation');
    $CI->form_validation->set_error_delimiters('<span style="color:#990000; font-weight:normal; font-size:10px;">', '</span>');
    if (!isset($model) || empty($model)) {
        $CI->load->view($url);
    } else {
        $CI->load->view($url, $model);
    }
}

function duplicate_user_email_check($str)
{
    $CI = &get_instance();

    $ret = $CI->user_model->get_ro_user_from_email($str);
    if (isset($ret) && !empty($ret[0]['user_email'])) {
        $CI->form_validation->set_message('duplicate_user_email_check', 'Email is already in use');
        return FALSE;
    } else {
        return TRUE;
    }
}

function edit_duplicate_user_email_check($str)
{
    $CI = &get_instance();

    $ret = $CI->user_model->get_ro_user_from_email($str);
    if (isset($ret) && count($ret) > 1) {
        $CI->form_validation->set_message('edit_duplicate_user_email_check', 'Email is already in use');
        return FALSE;
    } else {
        return TRUE;
    }
}
/*function user_email_check($str) {
	$CI = & get_instance();

        $ret = $CI->user_model->get_ro_user_from_email ($str);
        if ( isset($ret) && count($ret)>=2 )
        {
                $CI->form_validation->set_message('user_email_check', 'Email is not in use');
                return FALSE;
        }
        else
        {
                return TRUE;
        }
}
*/
/* Location: ./system/helpers/app_validation_helper.php */
