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

    public function __construct($bucket)
    {
        $this->BucketName = $bucket;
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
}
