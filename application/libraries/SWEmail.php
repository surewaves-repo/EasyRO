<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once 'aws-library/aws-autoloader.php';

use Aws\Ses\SesClient;
use Aws\Sns\MessageValidator\Message;

/**
 * SureWaves AWS Email Library | CodeIgniter
 * =========================================
 * Author    : Roopak A N
 * Email    : roopak@surewaves.com | anroopak@gmail.com
 *
 * Description :
 *        + Used to send emails using AWS SES Service.
 *        + Used to recieve notifications of Email Delivery
 */
class SWEmail
{

    private $config;
    private $client;

    /**
     * Constructor method which Loads the Configuration Details
     * Sets the Config Object and the Client Object
     *
     * @param Configuration File (.ini format) | String
     */
    public function __construct($config_file)
    {
        if (!file_exists($config_file)) {
            $this->swLogger('Config File Not Found');
            die('Config File not Found');
        }
        try {
            $this->config = parse_ini_file($config_file);
        } catch (Exception $e) {
            $this->swLogger('Error Parsing Config File');
            die('Error Parsing Config File');
        }
        try {
            $this->client = SesClient::factory(array('key' => $this->config['SW_AWS_KEY'], 'secret' => $this->config['SW_AWS_SECRET'], 'region' => $this->config['SW_REGION'],));
        } catch (Exception $e) {
            return "Could not create client";
        }
    }

    /**
     * Method to Log the Details into a File
     * @param String    |    Message to be Logged
     */
    public function swLogger($msg)
    {
        $file = fopen("log.txt", "a") or die('Could not Open log file');

        $msg = "Logged at : " . date('Y/M/d H:i:s') . " | " . $msg . "\n";

        fwrite($file, $msg);

        fclose($file);
    }

    /*
     * Method to Recieve Communication from the AWS SNS
     * Called when SNS is recieved
     * 
     * @param 	Array	|	Passes the POST Data- use $post = file_get_contents('php://input');
     * @return	Array	|	Contains - Message Type, Message ID, Bounced Emails, Delivered Emails 
     * 
     */

    /**
     * Method to send email using AWS SES Service.
     *
     * @param string    | Subject
     * @param string    | Body
     * @param string    | File to be Attached
     * @param array    | To Addresses
     * @param array    | Cc Addresses
     * @param array    | Bcc Addresses
     *
     * @return string    | Message ID returned from the SendEmail action.
     */

    public function sendEmail($subject, $body, $attachment, $to, $cc = array(), $bcc = array())
    {

        $result = FALSE;

        try {
            //$result = $client -> sendEmail(array('Source' => $this -> config['SW_SOURCE'], 'Destination' => array('ToAddresses' => $to, 'CcAddresses' => $cc, 'BccAddresses' => $bcc), 'Message' => array('Subject' => array('Data' => $subject), 'Body' => array('Html' => array('Data' => $body, 'Charset' => 'UTF-8'))), ));

            //Preparing Raw Email Content
            $content = $this->prepareEmail($this->config['SW_SOURCE'], $subject, $body, $attachment, $to, $cc, $bcc);
            $destinations = array_merge($to, $cc, $bcc);
            $result = $this->client->sendRawEmail(array(
                'Source' => $this->config['SW_SOURCE'],
                'Destinations' => $destinations,
                'RawMessage' => array(
                    'Data' => base64_encode($content),
                ),
            ));
        } catch (Exception $e) {
            return "Error Occurred";
        }

        return isset($result['MessageId']) ? $result['MessageId'] : FALSE;
    }

    private function prepareEmail($source, $subject, $body, $attachment, $to, $cc = array(), $bcc = array())
    {
        $to = implode(",", $to);
        $cc = implode(",", $cc);
        $bcc = implode(",", $bcc);

        $file_size = filesize($attachment);
        $handle = fopen($attachment, "r");
        $content = fread($handle, $file_size);
        fclose($handle);

        $content = chunk_split(base64_encode($content));
        $uid = md5(uniqid(time()));
        $name = basename($attachment);

        $header = "From: <" . $source . ">\r\n";
        $header = "To: <" . $to . ">\r\n";
        if (strlen($cc) != 0) $header = "Cc: " . $cc . "\r\n";
        if (strlen($bcc) != 0) $header = "Bcc: <" . $bcc . ">\r\n";
        $header .= "Reply-To: " . $source . "\r\n";
        $header .= "Subject: " . $subject . "\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: multipart/mixed; boundary=\"" . $uid . "\"\r\n\r\n";
        $header .= "This is a multi-part message in MIME format.\r\n";
        $header .= "--" . $uid . "\r\n";
        $header .= "Content-type:text/html; charset=iso-8859-1\r\n";
        $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $header .= $body . "\r\n\r\n";
        $header .= "--" . $uid . "\r\n";
        $header .= "Content-Type: application/octet-stream; name=\"" . $attachment . "\"\r\n"; // use different content types here
        $header .= "Content-Transfer-Encoding: base64\r\n";
        $header .= "Content-Disposition: attachment; filename=\"" . $attachment . "\"\r\n\r\n";
        $header .= $content . "\r\n\r\n";
        $header .= "--" . $uid . "--";

        return $header;
    }

    public function recieveSNS($post)
    {

        //To Prevent Fraudulent GET Access
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->swLogger('Not POST request, quitting');
            exit();
        }

        //Checking if the Message and SNS is Authentic
        /*
         * Requires OpenSSL. Uncomment the Code if exists
         $message = Message::fromRawPostData();
         $validator = new MessageValidator();
         $validator -> isValid($message);
         *
         */

        $msg = json_decode($post);

        $return = array("Type" => $msg->Type, "MessageId" => $msg->MessageId, "delivered" => array(), "bounced" => array());

        if ($msg->Type == 'SubscriptionConfirmation') {
            $response = $this->client->get($msg->SubscribeURL)->send();
        } elseif ($msg->Type == 'Notification') {
            $message = json_decode($msg->Message);
            $return['MessageId'] = $this->getMessageId($message->mail);
            if (isset($message->bounce)) {
                $return['bounced'] = $this->getBouncedEmails($message->bounce);
            }
            if (isset($message->delivery)) {
                $return['delivered'] = $this->getDeliveredEmails($message->delivery);
            }
        }
        return $return;
    }

    /**
     * Method to Extract the Message Id
     *
     * @param JSON Obj    | Details of Mail Object
     *
     * @return String    | Message Id
     */
    private function getMessageId($mailObj)
    {
        return $mailObj->messageId;
    }

    /**
     * Method to Extract the Bounced (NOT Delivered) Emails
     *
     * @param JSON Obj    | Details of Bounced Emails
     *
     * @return Array    | Bounced Emails
     */
    private function getBouncedEmails($bounceObj)
    {
        $return = array();
        $bounces = $bounceObj->bouncedRecipients;
        foreach ($bounces as $b) {
            $return[] = $b->emailAddress;
        }
        return $return;
    }

    /*
     * Method to prepare the Email
     * 
     */

    /**
     * Method to Extract the Delivered Emails
     *
     * @param JSON Obj    | Details of Delivered Emails
     *
     * @return Array    | Delivered Emails
     */
    private function getDeliveredEmails($deliveryObj)
    {
        $return = array();
        $deliveries = $deliveryObj->recipients;
        return $deliveries;
    }

}

/* End of file SWEmail.php */