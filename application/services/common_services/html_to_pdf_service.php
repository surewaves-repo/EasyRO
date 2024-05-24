<?php
/**
 * Created by PhpStorm.
 * Author: Yash
 * Date: November, 2019
 */

namespace application\services\common_services;

include_once ("pdfcrowd/pdfcrowd.php");

use pdfcrowd\PdfCrowd;

class HtmlToPdfService
{
    private $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function convertHtmlToPdf($html, $fileLocation)
    {
        log_message('info','In HtmlToPdfService@covertHtmlToPdf | Entered File Location is '.print_r($fileLocation,True));

        //$client = new PdfCrowd(PDF_CROWD_USER, PDF_CROWD_PASSWORD);
	try
	{	
		$client = new \Pdfcrowd\HtmlToPdfClient("anant", "86092539eedfd7f154909b708c30ac16");
		log_message('info', 'In HtmlToPdfService@covertHtmlToPdf | Client Created '.print_r($client,True));

        //$out_file = fopen($fileLocation, "wb"); // creates a empty file at location FileLocation
	//log_message('info', 'In HtmlToPdfService@covertHtmlToPdf | Out File '.print_r($out_file,True));
//        $pdfResponse =  $client->convertHtml($html, $out_file);
		$pdfResponse =  $client->convertStringToFile($html, $fileLocation);
	//$curlCMD = 'curl -f -u "anant:86092539eedfd7f154909b708c30ac16" -o "'.$fileLocation.'" --form-string "text='.$html.'" https://api.pdfcrowd.com/convert/20.10/';
	//$pdfResponse = shell_exec($curlCMD);
		log_message('info', 'In HtmlToPdfService@covertHtmlToPdf | PdfCrowd Response is '.print_r($pdfResponse,True));
        //fclose($out_file);

        	log_message('info', 'In HtmlToPdfService@covertHtmlToPdf | PdfCrowd Response is '.print_r($pdfResponse,True));

        	log_message('info','In HtmlToPdfService@covertHtmlToPdf | Leaving ');
    }catch(\Pdfcrowd\Error $why){
	log_message('error', 'In HtmlToPdfService@covertHtmlToPdf | PdfCrowd-exception is '.print_r($why,True));
	throw $why;
    }

}
}
