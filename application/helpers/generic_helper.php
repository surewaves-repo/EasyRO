<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function create_page_links($base_url, $per_page, $total_rows, $serch_set = 0)
{

    $CI = &get_instance();
    $CI->load->library('pagination');

    $config['base_url'] = $base_url;
    $config['total_rows'] = $total_rows;
    $config['per_page'] = $per_page;
    $config['serch_set'] = $serch_set;

    $CI->pagination->initialize($config);

    return $CI->pagination->create_links();

}

function generate_internal_ro_number($agency_name, $client_name, $campaign_startdate, $order_number = 1)
{

    // Internal RO number nomenclature SW/<ClientName>/<agency Name>/<month>-<year yyyy>-<ro-number>/
    $ro_startdate = date('M-Y', strtotime($campaign_startdate));
    $internal_ro_number = 'SW' . '/' . $client_name . '/' . $agency_name . '/' . $ro_startdate . '-' . $order_number;

    return $internal_ro_number;

}

// This function will assume the $sch_array in predefined format
// key of the array is channel name, followed by array of 'date'=> total impression values
// array['channel_one'] => array('day1' => 2, 'day2'=> 8)

function update_schedule_helper(&$sch_array, $channel_name, $date, $impressions)
{
    if (array_key_exists($channel_name, $sch_array)) {
        if ((array_search($date, $sch_array[$channel_name]) === TRUE)) {
            $sch_array['$channel_name'][$date] = $impressions;
        } else {
            array_push($sch_array[$channel_name], array('date' => $date, 'imp' => $impressions));
        }
    } else {
        $sch_array[$channel_name] = array('date' => $date, 'imp' => $impressions);
    }

}

function merge_schedule_helper(&$sch1, &$sch2)
{

}

function filter_using_search_str($items, $keys, $search_str)
{
    $search_str = strtolower($search_str);
    $filtered_items = array();
    foreach ($items as $item) {
        $found = 0;
        foreach ($keys as $key) {
            $heysteak = strtolower($item[$key]);
            if (stripos($heysteak, $search_str) !== FALSE) {
                $found = 1;
                break;
            }
        }
        if ($found == 1) {
            array_push($filtered_items, $item);
        }
    }

    return $filtered_items;
}

function mail_send($to, $email_text_key, $subject_key_values, $message_key_values, $file, $cc, $url)
{
    $CI = &get_instance();
    $CI->config->load('email_text');
    $subject = $CI->config->item($email_text_key . "_email_subject");
    $message = $CI->config->item($email_text_key . "_email_body");
    send_mail($to, $subject, $message, $subject_key_values, $message_key_values, $file, $cc, $url);
}


//RC::Kiran (2012-01-05) - Please make this a generic function so that we can use it from any controller
function send_mail($to, $subject, $message, $subject_key_values, $message_key_values, $file, $cc, $url)
{

    $CI = &get_instance();
    $CI->config->load('email_text');
    foreach ($subject_key_values as $key => $val) {
        $subject = str_replace("%$key%", $val, $subject);
    }

    foreach ($message_key_values as $key => $val) {
        $message = str_replace("%$key%", $val, $message);
    }
    $message = str_replace("%FAQ%", base_url() . "index.php/welcome/faq", $message);
    $message = str_replace("%SUREWAVES_LOGO%", base_url() . "images/sureview.png", $message);
    $message = str_replace("%ICON_LOGO%", base_url() . "images/Red-Curve-Icon.ico", $message);
    $CI->load->library('email');
    $CI->email->clear(TRUE);
    $CI->email->from($CI->config->item('from_email'), $CI->config->item('from_email_name'));
    $CI->email->to($to);
    $CI->email->cc($cc);
    $CI->email->subject($subject);
    $CI->email->message($message);
    if ($file != '') {
        $fileArr = explode(",", $file);
        foreach ($fileArr as $eachfile) {
            log_message('info', 'network pdf mail in send_mail attaching file name to email - ' . $eachfile);
            $CI->email->attach($eachfile);
            if (strpos($eachfile, '_cancel') === false) {
                $file_type = 'pdf';
                $file_name = basename($eachfile);
                $file_size = filesize($eachfile);
                $fp = fopen($eachfile, 'r');
                $content = fread($fp, $file_size);
                $content = addslashes($content);
                fclose($fp);
            }
        }


        $CI->email->send();
    } else {
        $CI->email->send();
    }
	log_message('info', 'send_mail for networks_mail - ' . $CI->email->print_debugger());
    //make mail copied
    $CI->load->model('ro_model');
    $user_data = array(
        'to_email' => $to,
        'subject' => $subject,
        'message' => $message,
        'date_time' => date("Y-m-d H:i:s"),
        'file_name' => $file_name,
        'file_type' => $file_type,
        'file_size' => $file_size,
        'content_url' => $url,
    );
    $CI->ro_model->make_email_copy($user_data);

    foreach ($fileArr as $eachfile) {
        unlink($eachfile);
    }

}

function send_mail_through_sendgrid($to, $subject, $message, $subject_key_values, $message_key_values, $file, $cc, $url)
{

    $CI = &get_instance();
    $CI->config->load('email_text');
    foreach ($subject_key_values as $key => $val) {
        $subject = str_replace("%$key%", $val, $subject);
    }

    foreach ($message_key_values as $key => $val) {
        $message = str_replace("%$key%", $val, $message);
    }
    $message = str_replace("%FAQ%", base_url() . "index.php/welcome/faq", $message);
    $message = str_replace("%SUREWAVES_LOGO%", base_url() . "images/sureview.png", $message);
    $message = str_replace("%ICON_LOGO%", base_url() . "images/Red-Curve-Icon.ico", $message);
    $CI->load->library('email');
    $CI->email->clear(TRUE);
    $CI->email->from($CI->config->item('from_email'), $CI->config->item('from_email_name'));
    $CI->email->to($to);
    $CI->email->cc($cc);
    $CI->email->subject($subject);
    $CI->email->message($message);
    if ($file != '') {
        $CI->email->attach($file);
        $file_type = 'pdf';
        $file_size = filesize($file);
        $fp = fopen($file, 'r');
        $content = fread($fp, $file_size);
        $content = addslashes($content);
        fclose($fp);
        $CI->email->send();
    } else {
        $CI->email->send();
    }
    //make mail copied
    $CI->load->model('ro_model');
    $user_data = array(
        'to_email' => $to,
        'subject' => $subject,
        'message' => $message,
        'date_time' => date("Y-m-d H:i:s"),
        'file_name' => $file,
        'file_type' => $file_type,
        'file_size' => $file_size,
        'content_url' => $url,
    );
    $CI->ro_model->make_email_copy($user_data);
    unlink($file);
}

function embedMessagesForNoticeRO($message, $noticedRoNetwork)
{
    $channelIds = explode(",", $noticedRoNetwork);
    if (count($channelIds) > 0) {
        $CI = &get_instance();
        $CI->config->load('email_text');

        if (count($channelIds) == 1) {
            $message_body = $CI->config->item($email_text_key . "_email_body");

        } else {

        }
    }

}

function store_mail_data($emails_list, $text, $subject, $message, $file_location, $cc, $file_name)
{
    $CI = &get_instance();
    $CI->load->model('ro_model');
    $fileLocationArr = explode(",", $file_location);
    $file_nameArr = explode(",", $file_name);
    $insertedId = array();
    foreach ($file_nameArr as $fileKey => $fileName) {

        if (strpos($fileName, '_cancel') !== false) {
            $user_data = array(
                'emails_list' => serialize($emails_list),
                'text' => $text,
                'subject' => $subject[$fileKey],
                'message' => serialize($message),
                'file_location' => serialize($fileLocationArr[$fileKey]),
                'cc' => serialize($cc),
                'file_name' => $fileName,
                'mail_sent' => 1,
                'pdf_generation_date' => date('Y-m-d')
            );
        } else {
            $user_data = array(
                'emails_list' => serialize($emails_list),
                'text' => $text,
                'subject' => $subject[$fileKey],
                'message' => serialize($message),
                'file_location' => serialize($fileLocationArr[$fileKey]),
                'cc' => serialize($cc),
                'file_name' => $fileName,
                'mail_sent' => 0,
                'pdf_generation_date' => date('Y-m-d')
            );
        }
        $insertedId[$fileKey] = $CI->ro_model->store_mail_data($user_data);
        log_message('info', 'network pdf mail in store_mail_data function - ' . print_r($user_data, TRUE));
    }
    log_message('info', 'network pdf mail in store_mail_data function insertedId - ' . print_r($insertedId, TRUE));
    //$insertedId = $CI->ro_model->store_mail_data($user_data);

    //mail send
    if (count($insertedId) > 0) {
        $CI->ro_model->sendMailForNetworkRo($emails_list, $text, $message, $file_location, $cc, $file_name, $insertedId);
    }

}

function verify_lock($type)
{
}


// added by lokanath for sending mail with attachment of diff file types
function mail_send_v1($to, $email_text_key, $subject_key_values, $message_key_values, $file, $cc, $url, $file_type)
{
    $CI = &get_instance();
    $CI->config->load('email_text');
    $subject = $CI->config->item($email_text_key . "_email_subject");
    $message = $CI->config->item($email_text_key . "_email_body");
    send_mail_v1($to, $subject, $message, $subject_key_values, $message_key_values, $file, $cc, $url, $file_type);
}


//RC::Abhijit (2012-01-05) - Please make this a generic function so that we can use it from any controller 
function send_mail_v1($to, $subject, $message, $subject_key_values, $message_key_values, $file, $cc, $url, $file_type)
{

    $CI = &get_instance();
    $CI->config->load('email_text');
    foreach ($subject_key_values as $key => $val) {
        $subject = str_replace("%$key%", $val, $subject);
    }

    foreach ($message_key_values as $key => $val) {
        $message = str_replace("%$key%", $val, $message);
    }
    //$message = str_replace ("%FAQ%", base_url()."index.php/welcome/faq", $message);
    //$message = str_replace ("%SUREWAVES_LOGO%", base_url()."images/sureview.png", $message);
    //$message = str_replace ("%ICON_LOGO%", base_url()."images/Red-Curve-Icon.ico", $message);
    $CI->load->library('email');
    $CI->email->clear(TRUE);
    $CI->email->from($CI->config->item('from_email'), $CI->config->item('from_email_name'));
    $CI->email->to($to);
    $CI->email->cc($cc);
    $CI->email->subject($subject);
    $CI->email->message($message);
    if ($file != '') {
        $fileNumbers = explode(",", $file);
        foreach ($fileNumbers as $fn) {
            if (!isset($fn) || empty($fn)) continue;
            $CI->email->attach($fn);
            sleep(1);
            //$file_type='pdf';
            //$file_size = filesize($file);
            // $fp = fopen($file,'r');

            //$content = fread($fp,$file_size);
            //$content = addslashes($content);
            //fclose($fp);
        }
        $CI->email->send();
    } else {
        $CI->email->send();
    }
	log_message('info', 'mails for MIS_REPORT_MAILS & CHANNEL_PRFORMANCE_MAILS- ' . $CI->email->print_debugger());

}

function update_into_external_ro_report_detail($internal_ro_number)
{
    $CI = &get_instance();
    $ro_campaign_amount = $CI->mg_model->get_external_ro_report_details($internal_ro_number);

    $gross_ro_amount = $ro_campaign_amount[0]['gross_ro_amount'];
    $agency_commission_amount = $ro_campaign_amount[0]['agency_commission_amount'];
    if ($ro_campaign_amount[0]['agency_rebate_on'] == "ro_amount") {
        $agency_rebate = $ro_campaign_amount[0]['gross_ro_amount'] * ($ro_campaign_amount[0]['agency_rebate'] / 100);
    } else {
        $agency_rebate = ($ro_campaign_amount[0]['gross_ro_amount'] - $ro_campaign_amount[0]['agency_commission_amount']) * ($ro_campaign_amount[0]['agency_rebate'] / 100);
    }
    $other_expenses = $ro_campaign_amount[0]['other_expenses'];

    $total_network_payout = $CI->mg_model->get_total_network_payout($internal_ro_number);
    $actual_net_amount = $gross_ro_amount - $agency_commission_amount - $agency_rebate - $other_expenses;
    $net_contribution_amount = $actual_net_amount - $total_network_payout[0]['network_payout'];

    $net_revenue = $gross_ro_amount - $agency_commission_amount;
    $net_revenue = round(($net_revenue * SERVICE_TAX), 2);
    $net_contribution_amount_per = round(($net_contribution_amount / $net_revenue) * 100, 2);

    $total_scheduled_seconds = $CI->mg_model->get_total_network_seconds_internal_ro($internal_ro_number);

    $report_data = array(
        'gross_ro_amount' => $gross_ro_amount,
        'agency_commission_amount' => $agency_commission_amount,
        'other_expenses' => $other_expenses,
        'agency_rebate' => $agency_rebate,
        'total_seconds_scheduled' => $total_scheduled_seconds[0]['total_scheduled_seconds'],
        'total_network_payout' => $total_network_payout[0]['network_payout'],
        'net_contribution_amount' => $net_contribution_amount,
        'net_contribution_amount_per' => $net_contribution_amount_per,
        'net_revenue' => $net_revenue
    );
    $where_data = array(
        'internal_ro_number' => $internal_ro_number
    );
    $CI->mg_model->update_external_ro_report_detail($report_data, $where_data);
}

function email_send($to, $cc, $email_text_key, $subject_key_values, $message_key_values)
{
    $CI = &get_instance();
    $CI->config->load('email_text');
    $subject = $CI->config->item($email_text_key . "_email_subject");
    $message = $CI->config->item($email_text_key . "_email_body");
    send_email($to, $cc, $subject, $message, $subject_key_values, $message_key_values);

}

function send_email($to, $cc, $subject, $message, $subject_key_values, $message_key_values)
{
    $CI = &get_instance();
    $CI->config->load('email_text');

    foreach ($subject_key_values as $key => $val) {
        $subject = str_replace("%$key%", $val, $subject);
    }

    foreach ($message_key_values as $key => $val) {
        $message = str_replace("%$key%", $val, $message);
    }

    $CI->load->library('email');
    $CI->email->clear(TRUE);
    $CI->email->from($CI->config->item('from_email'), $CI->config->item('from_email_name'));
    $CI->email->to($to);
    $CI->email->cc($cc);
    $CI->email->subject($subject);
    $CI->email->message($message);


    $CI->email->from($CI->config->item('from_email'), $CI->config->item('from_email_name'));
    $CI->email->to($to);

    $CI->email->subject($subject);
    $CI->email->message($message);
    $CI->email->send();


}

function email_send_bcc($to, $cc, $bcc, $email_text_key, $subject_key_values, $message_key_values)
{
    $CI = &get_instance();
    $CI->config->load('email_text');
    $subject = $CI->config->item($email_text_key . "_email_subject");
    $message = $CI->config->item($email_text_key . "_email_body");
    send_email_bcc($to, $cc, $bcc, $subject, $message, $subject_key_values, $message_key_values);

}

function send_email_bcc($to, $cc, $bcc, $subject, $message, $subject_key_values, $message_key_values)
{
    $CI = &get_instance();
    $CI->config->load('email_text');

    foreach ($subject_key_values as $key => $val) {
        $subject = str_replace("%$key%", $val, $subject);
    }

    foreach ($message_key_values as $key => $val) {
        $message = str_replace("%$key%", $val, $message);
    }

    $CI->load->library('email');
    $CI->email->clear(TRUE);
    $CI->email->from($CI->config->item('from_email'), $CI->config->item('from_email_name'));
    $CI->email->to($to);
    $CI->email->cc($cc);
    $CI->email->bcc($bcc);
    $CI->email->subject($subject);
    $CI->email->message($message);


    $CI->email->from($CI->config->item('from_email'), $CI->config->item('from_email_name'));
    $CI->email->to($to);

    $CI->email->subject($subject);
    $CI->email->message($message);
    $CI->email->send();


}

//RC:Deepak- generic method for cache CRUD
//Above RC comment is fixed
function set_cache($id, $data)
{
    //memcache implementation
    $memcache = new Memcached;
    $memcache->addServer("localhost", 11211);
    $memcache->set($id, $data,1000);
}

function fetch_cache_data($id)
{
    $memcache = new Memcached;
    $memcache->addServer("localhost", 11211);
    $memcache->get($id);
}

function delete_cache_data($id)
{
    $memcache = new Memcached;
    $memcache->addServer("localhost", 11211);
    $memcache->delete($id);
}

/* Creating method for progressing ro send mail Jan 2015*/

function test_send_mail()
{
    $CI = &get_instance();
    $from_email = "do_not_reply@SureWaves.com";
    $from_email_name = "Madhup Mani";
    $to = "mani@surewaves.com";
    $cc = "deepak@surewaves.com,nitish@surewaves.com";
    $subject = "Hello";
    $message = "Hello,How r u.Its a test mail";
    $CI->load->library('email');
    //$CI->email->clear(TRUE);
    $CI->email->from($from_email, $from_email_name);
    $CI->email->to($to);
    $CI->email->cc($cc);
    $CI->email->subject($subject);
    $CI->email->message($message);
    $CI->email->send();
    //mail($to,$subject,$message,"From:" . $from_email);
}

function store_invoice_pdf($ro_id, $month_name, $split_criteria, $file_location, $clientName, $agencyName, $invoiceAmount, $invoice_number, $alias_invoice_no)
{
    $CI = &get_instance();
    $CI->invoice_model->storeInvoicePdf($ro_id, $month_name, $split_criteria, $file_location, $clientName, $agencyName, $invoiceAmount, $invoice_number, $alias_invoice_no);
}

function zipArchived()
{
    $zip = new ZipArchive();
    //create the file and throw the error if unsuccessful
    $archive_file_name = "Invoice_pdf_" . $ro_id . ".zip";
    if ($zip->open($pathName . $archive_file_name, ZIPARCHIVE::CREATE) !== TRUE) {
        exit("cannot open <$archive_file_name>\n");
    } else {
        $zip->addFile($pathName . $file_name, $file_name);
        $zip->close();
    }
}

function convert_number($number)
{
    if (($number < 0) || ($number > 999999999)) {
        throw new Exception("Number is out of range");
    }

    $Cn = floor($number / 10000000);  /* Crores (giga) */
    $number -= $Cn * 10000000;
    $Gn = floor($number / 100000);  /* Lacs (giga) */
    $number -= $Gn * 100000;
    $kn = floor($number / 1000);     /* Thousands (kilo) */
    $number -= $kn * 1000;
    $Hn = floor($number / 100);      /* Hundreds (hecto) */
    $number -= $Hn * 100;
    $Dn = floor($number / 10);       /* Tens (deca) */
    $n = $number % 10;               /* Ones */

    $res = "";

    if ($Cn) {
        $res .= convert_number($Cn) . " Crores";
    }

    if ($Gn) {
        $res .= convert_number($Gn) . " Lacs";
    }

    if ($kn) {
        $res .= (empty($res) ? "" : " ") .
            convert_number($kn) . " Thousand";
    }

    if ($Hn) {
        $res .= (empty($res) ? "" : " ") .
            convert_number($Hn) . " Hundred";
    }

    $ones = array("", "One", "Two", "Three", "Four", "Five", "Six",
        "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen",
        "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen",
        "Nineteen");
    $tens = array("", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty",
        "Seventy", "Eighty", "Ninety");

    if ($Dn || $n) {
        if (!empty($res)) {
            $res .= " and ";
        }

        if ($Dn < 2) {
            $res .= $ones[$Dn * 10 + $n];
        } else {
            $res .= $tens[$Dn];

            if ($n) {
                $res .= "-" . $ones[$n];
            }
        }
    }

    if (empty($res)) {
        $res = "zero";
    }

    return $res;
}

