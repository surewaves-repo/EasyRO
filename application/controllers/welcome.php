<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
use application\services\common_services\S3UploadService;

use application\services\common_services\EmailService;
include_once APPPATH . 'services/common_services/s3_upload_service.php';

include_once APPPATH . 'services/common_services/email_service.php';
class Welcome extends CI_Controller
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *        http://example.com/index.php/welcome
     *    - or -
     *        http://example.com/index.php/welcome/index
     *    - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {
        $this->load->view('welcome_message');
    }
    public function faq()
    {
        echo "Welcome to Surewaves.<br>";
        echo "We are working on FAQs. Thanks for your patience!";
    } 
    public function testUpload()
	{
		$target_url = 'http://54.179.131.174:8081/UploadFile';
    //This needs to be the full path to the file you want to send.
$file_path = '/opt/lampp/htdocs/tables.txt';
$file_name = 'tables.txt';
    /*  the at sign '@' is required before the
     * file name.
     */
$post = array(
    'file' => new CURLFile($file_path, 'text/plain', $file_name)
);


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$target_url);
    curl_setopt($ch, CURLOPT_POST,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $result=curl_exec ($ch);
    curl_close ($ch);
    echo $result;
	}
    public function tests3apis(){
	$file = "/opt/lampp/htdocs/test.eml";
	$dest = "10_test.eml";
	$s3FileUploadObj      = new S3UploadService(NETWORK_RO);
        $retArr = $s3FileUploadObj->uploadFileOverApi($file, $dest);
        echo "<pre>";print_r($retArr);

        log_message('info','In GenerateRoPdfService@uploadPdfToS3 | After uploading to s3 , the status is' . print_r($retArr, True));
//       $status = $s3FileUploadObj->deleteFileOverApi($dest);

    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
