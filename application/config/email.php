<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
$config['protocol'] = 'sendmail';
$config['mailpath'] = '/usr/sbin/sendmail';
$config['charset'] = 'iso-8859-1';
$config['wordwrap'] = FALSE;
$config['mailtype'] = 'html';
*/

/*Configurations changed to send mail using Sendgrid:29-07-2015*/
/*$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['protocol'] = 'smtp';
$config['smtp_host'] = 'smtp.sendgrid.net';
#$config['smtp_user'] = 'deepakts';
$config['smtp_user'] = 'surewaves';
$config['smtp_pass'] = 'Sure$$123';
$config['smtp_port'] = 587;
$config['crlf'] = "\r\n";
$config['newline'] = "\r\n";
*/
$config['mailtype'] = 'html';
$config['charset'] = 'iso-8859-1';
$config['protocol'] = 'smtp';
$config['smtp_host'] = 'ssl://email-smtp.ap-southeast-1.amazonaws.com';
#$config['smtp_user'] = 'deepakts';
$config['smtp_user'] = 'AKIAWH74HDRAVUCB3SW2';
$config['smtp_pass'] = 'BGgGvHg4a8xhMh9IA1dLj75sD8U62kMXYncoOhS9mUUS';
$config['smtp_port'] = 465;
$config['crlf'] = "\n";
$config['newline'] = "\r\n";
/* End of file email.php */
/* Location: ./application/config/email.php */
