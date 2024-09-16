<?php
/**
 * Created by PhpStorm.
 * Author: Yash Bansal
 * Date: September, 2019
 */

namespace application\services\common_services;


class S3UploadService
{
    private $BucketName;
    private $CI;
    private $s3folderPath;

    public function __construct($s3Details = [])
    {
        $this->BucketName = $s3Details['BUCKET_NAME'];
        $this->s3folderPath = $s3Details['FOLDER_PATH'];
        $this->CI = &get_instance();
        $this->CI->load->library('s3');
    }

    public function uploadFile($filePath, $fileName)
    {
        $status = $this->CI->s3->putObjectFile(
            $filePath,
            $this->BucketName,
            $fileName,
            ACL_PUBLIC_READ
        );
        log_message('INFO', 'In S3UploadService@uploadFile | File upload status is - ' . print_r($status,true));
        return $status;
    }
    public function uploadFileOverApi($filePath , $fileName = '' , $isPublic = "true")
    {
        log_message('INFO', 'In S3UploadService@uploadFileOverApi | Entering function with arguments - '.print_r(func_get_args(),true));
        $mimeType = $this->getMimeTypeOfFile($filePath);
        log_message('INFO', 'In S3UploadService@uploadFileOverApi | Mime type of file is - '.print_r($mimeType,true));
        $fileName = $this->getFileNameFromFilePath($filePath , $fileName);
        log_message('INFO', 'In S3UploadService@uploadFileOverApi | File name if not passed as arguments - '.print_r($fileName,true));
        $target_url = THIRD_PARTY_AWS_URL_S3 . "/"."api/S3Service/UploadFileToS3";



        $headers = ["Content-Type:multipart/form-data"];
        $postData = [
            
            'destinationFileName' => $fileName,
            'bucketName' => $this->BucketName,
            'destinationFolderName' => $this->s3folderPath,
            'isPublic' => $isPublic,
            'sourceFilePath' => new \CurlFile($filePath, $mimeType, $fileName)
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$target_url);
//        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST , 'POST');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION , true);
        $result     = curl_exec($ch);
        $httpcode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
	    log_message('INFO', 'In S3UploadService@uploadFileOverApi | The httpcode is - ' . print_r($httpcode, true));
        if($httpcode === 200){
		log_message('INFO', 'In S3UploadService@uploadFileOverApi | file uploaded successfully to s3 with value - '.print_r($result, true));
        	return ['status'=> true , 's3Url' => $result];
	    }else{
		    log_message('ERROR', 'In S3UploadService@uploadFileOverApi | file could not be uploaded to s3: '. $httpcode .' The reason is - ' . print_r($result, true));			
		    return ['status'=> false , 's3Url' => ''];
	    }
    }
    function generateURL($uri)
    {
        $url = $this->CI->s3->getURL($this->BucketName, $uri);
        log_message('INFO', 'In S3UploadService@generateURL | File path to S3 - ' . print_r($uri,true));
        return $url;
    }

    public function deleteFile($fileName)
    {
        log_message('INFO', 'In S3UploadService@deleteFile | Deleting File from S3 - '.print_r($fileName,true));
        $this->CI->s3->deleteObject($this->BucketName, $fileName);
        log_message('DEBUG', 'In S3UploadService@deleteFile | Delete successful!');
    }
    public function deleteFileOverApi($fileName)
    {
        log_message('INFO', 'In S3UploadService@deleteFileOverApi | Deleting File from S3 - '.print_r($fileName,true));
     
        $target_url = THIRD_PARTY_AWS_URL_S3 . "/"."api/S3Service/DeleteObject";

        log_message('INFO', 'In S3UploadService@deleteFileOverApi | Target url before deleting File from S3 - '.print_r($target_url,true));

        $headers = ["Content-Type:application/x-www-form-urlencoded"];
        $postData = [
            
            's3FileName' => $fileName,
            'bucketName' => $this->BucketName,
            's3FolderPath' => $this->s3folderPath
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$target_url);
//        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST , 'DELETE');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION , true);
        $result     = curl_exec($ch);
        $httpcode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
	    log_message('INFO', 'In S3UploadService@deleteFileOverApi | The httpcode is - ' . print_r($httpcode, true));
        if($httpcode === 200){
		    log_message('INFO', 'In S3UploadService@deleteFileOverApi | s3 file deleted successfully with value - '.print_r($result, true));
        	return true;
	    }else{
		    log_message('ERROR', 'In S3UploadService@deleteFileOverApi | could not deleted s3 file . The http code is : '. $httpcode .' The reason is - ' . print_r($result, true));			
		    return false;
	    }
    }
    private function getMimeTypeOfFile($filePath){
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        
        $type = $finfo->file($filePath);
        return $type ;

    }
    private function getFileNameFromFilePath($filePath , $fileName){
        if($fileName == ''){
            return basename($filePath);
        }else{
            return $fileName;
        }
    }
    
}
