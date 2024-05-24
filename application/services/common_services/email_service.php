<?php

namespace application\services\common_services;
class EmailService
{
    private $to;
    private $cc;

    public function __construct($to, $cc)
    {
        log_message('DEBUG', 'In EmailService@constructor | EmailService object created');
        $this->to = $to;
        $this->cc = $cc;
    }

    /**
     * Author : Ravishankar Singh
     * @param $emailTextKey
     * @param $subjectKeyValues
     * @return mixed
     */
    public function prepareEmailSubject($emailTextKey, $subjectKeyValues)
    {
        log_message('INFO', 'In EmailService@prepareEmailSubject | Preparing email subject for - ' . print_r(array('emailTextKey' => $emailTextKey, 'subjectKeyValues' => $subjectKeyValues), true));
        $CI = &get_instance();
        $CI->config->load('email_text');
        $subject = $CI->config->item($emailTextKey . "_email_subject");
        foreach ($subjectKeyValues as $key => $val) {
            $subject = str_replace("%$key%", $val, $subject);
        }
        log_message('INFO', 'In EmailService@prepareEmailSubject | Email subject prepared ' . print_r($subject, true));
        return $subject;
    }

    /**
     * Author : Ravishankar Singh
     * @param $emailTextKey
     * @param $messageKeyValues
     * @return mixed
     */
    public function prepareEmailBody($emailTextKey, $messageKeyValues)
    {
        log_message('INFO', 'In EmailService@prepareEmailBody | Preparing email message for - ' . print_r(array('emailTextKey' => $emailTextKey, 'MessageKeyValues' => $messageKeyValues), true));
        $CI = &get_instance();
        $CI->config->load('email_text');
        $message = $CI->config->item($emailTextKey . "_email_body");
        foreach ($messageKeyValues as $key => $val) {
            $message = str_replace("%$key%", $val, $message);
        }

//        $message = str_replace("%FAQ%", base_url() . "index.php/welcome/faq", $message);
//        $message = str_replace("%SUREWAVES_LOGO%", base_url() . "images/sureview.png", $message);
//        $message = str_replace("%ICON_LOGO%", base_url() . "images/Red-Curve-Icon.ico", $message);

        log_message('INFO', 'In EmailService@prepareEmailBody | Email message prepared - ' . print_r($message, true));
        return $message;
    }

    /**
     * Author : Ravishankar Singh
     * @param $emailTextKey
     * @param $subjectKeyValues
     * @param $messageKeyValues
     * @param $file
     * @return bool
     */
    public function sendMail($emailTextKey, $subjectKeyValues, $messageKeyValues, $file)
    {
        $subject = $this->prepareEmailSubject($emailTextKey, $subjectKeyValues);
        $message = $this->prepareEmailBody($emailTextKey, $messageKeyValues);

        log_message('INFO', 'In EmailService@sendMail | File attachments - ' . print_r($file, true));
        $CI = &get_instance();
        $CI->load->library('email');
        $CI->email->clear(TRUE);
        $CI->email->from($CI->config->item('from_email'), $CI->config->item('from_email_name'));
        $CI->email->to($this->to);
        $CI->email->cc($this->cc);
        $CI->email->subject($subject);
        $CI->email->message($message);
        log_message('INFO', 'In EmailService@sendMail | Sending mail TO - ' . print_r($this->to, true) . ' CC - ' . print_r($this->cc, true));

        if ($file != '') {
            $fileNumbers = explode(",", $file);
            log_message('INFO', 'In EmailService@sendMail | All files - ' . print_r($fileNumbers, true));
            foreach ($fileNumbers as $fn) {
                log_message('INFO', 'In EmailService@sendMail | File Name for attachment - ' . print_r($fn, true));
                if (!isset($fn) || empty($fn) || !file_exists($fn)) {
                    log_message('INFO', 'In EmailService@sendMail | File does NOT Exist');
                    continue;
                }
                log_message('INFO', 'In EmailService@sendMail | File Exists and is being attached');
                $CI->email->attach($fn);
            }
        }

        if ($CI->email->send()) {
            log_message('INFO', 'In EmailService@sendMail | Mail sent successfully!');
	    log_message('info', 'In EmailService@sendMailPrintingMailLog - ' . $CI->email->print_debugger());
            return true;
        }
        log_message('INFO', 'In EmailService@sendMail | Mail not sent');
        return false;
    }
}
