<?php

namespace Aws\CloudTrail;

use Aws\S3\S3Client;

/**
 * This class provides an easy way to read log files generated by AWS CloudTrail. CloudTrail log files contain data
 * about your AWS API calls and are stored in Amazon S3. The log files are gzipped and contain structured data in JSON
 * format. This class will automatically ungzip and decode the data, and return the data as a array of log records
 */
class LogFileReader
{
    /**
     * @var S3Client The Amazon S3 client used to perform GetObject operations
     */
    private $s3Client;

    /**
     * @param S3Client $s3Client
     */
    public function __construct(S3Client $s3Client)
    {
        $this->s3Client = $s3Client;
    }

    /**
     * Downloads, unzips, and reads a CloudTrail log file from Amazon S3
     *
     * @param string $s3BucketName The bucket name of the log file in Amazon S3
     * @param string $logFileKey The key of the log file in Amazon S3
     *
     * @return array
     */
    public function read($s3BucketName, $logFileKey)
    {
        // Create a command for getting the log file object
        $command = $this->s3Client->getCommand('GetObject', array(
            'Bucket' => (string)$s3BucketName,
            'Key' => (string)$logFileKey,
        ));

        // Make sure gzip encoding header is sent and accepted in order to inflate the response data
        $command->set('ResponseContentEncoding', 'x-gzip');
        $command->prepare()->addHeader('Accept-Encoding', 'gzip');

        // Get the JSON response data and extract the log records
        $command->execute();
        $logData = $command->getResponse()->json();
        if (isset($logData['Records'])) {
            return $logData['Records'];
        } else {
            return array();
        }
    }
}
