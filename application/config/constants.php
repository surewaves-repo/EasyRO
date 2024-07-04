<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

define('ENABLE_PROFILER', '0');
define('ROOT_FOLDER', '/surewaves_easy_ro');
define('ITEM_PER_PAGE_USERS', '20');
define('APPROVAL_RO_PAGINATION', '2');

define('BH_EmailID', 'anant@surewaves.com,tripur@surewaves.com');
define('CID_TIMEBAND', '06:00#10:00,10:01#13:00,13:01#18:00,18:01#21:00,21:01#23:59');
define('CID_TIMEBAND_GTPL', '00:00#06:00,06:01#12:00,12:01#16:00,16:01#20:00,20:01#23:59');

//(Service Tax Amount +1/100)
define('SERVER_NAME', 'http://tv.mediagrid100.surewaves.com');
//define('SERVER_NAME', 'http://18.136.210.229');
define('SERVICE_TAX', '1.0');
define('TEST_NW_RO_EMAIL', 'era@surewaves.com,smitha@surewaves.com');
//define('NW_RO_EMAIL','nandini@surewaves.com,mani@surewaves.com,deepak@surewaves.com,era@surewaves.com,nitish@surewaves.com,shahrukh@surewaves.com') ;
define('NW_RO_EMAIL', 'anant@surewaves.com,tripur@surewaves.com,sathish@surewaves.com,sony@surewaves.com,vivek@surewaves.com,nilanjan@surewaves.com,shivaraj@surewaves.com,deepakr@surewaves.com');
define('OPS_SUPPORT_EMAIL','support@surewaves.com') ;
//define('ADV_NW_RO_EMAIL','deepak.gupta@surewaves.com') ;
//define('ADV_NW_RO_EMAIL', 'sudheer@surewaves.com');
define('ADV_NW_RO_EMAIL', 'biswabijayee@surewaves.com');
define('AMAZON_KEY', "10W51DTTBDR9TBACB3G2");
define('AMAZON_VALUE', "p5wWY/7HQcEzcXK5A45PqSm0ghy0Yfpqtkk3LrMm");
define('NETWORK_RO_BUCKET', "sw_easy_ro");
define('S3_BUCKET', "ad-ingester-content-test");
define("BUCKET_URL", "https://s3.amazonaws.com/ad-ingester-content-test/");
define('MEMCACHE_HOST', '127.0.0.1');
define('MEMCACHE_PORT', '11211');
/* Defining constants for Ro Progression */

// Types

define('SUBMIT', 'submit');
define('APPROVED', 'approved');
define('CAMPAIGN_START', 'campaign_start');
define('CAMPAIGN_PRE_CLOSURE', 'campaign_preclousure');
define('CAMPAIGN_END', 'campaign_end');
define('CAMPAIGN_CANCEL', 'campaign_cancel');
define('RO_CANCEL', 'ro_cancel');
define('EDIT_RO', 'edit_ro');
define('MARKET_CANCEL', 'market_cancel');

// Types Conditions

define('SUBMIT_CON', json_encode(array('submit_status' => 'submitted')));
define('APPROVED_CON', json_encode(array('approved_status' => 'approved')));
define('CAMPAIGN_START_CON', json_encode(array('approved_status' => 'mail_sent', 'campaign_start_status' => 'mail_not_sent')));
define('CAMPAIGN_PRE_CLOSURE_CON', json_encode(array('approved_status' => 'mail_sent', 'campaign_start_status' => 'mail_sent', 'campaign_preclousure_status' => 'mail_not_sent')));
define('CAMPAIGN_END_CON', json_encode(array('approved_status' => 'mail_sent', 'campaign_end_status' => 'mail_not_sent')));
define('RO_CANCEL_CON', json_encode(array('approved_status' => 'mail_sent', 'ro_cancel_status' => 'send_mail')));
define('EDIT_RO_CON', json_encode(array('approved_status' => 'mail_sent', 'edit_ro_action' => 'send_mail')));
define('MARKET_CANCEL_CON', json_encode(array('submit_status' => 'mail_sent')));

/* Encryption  data for email */

define("ENCRYPTION_METHOD", "AES-256-CBC");
define("SECRET_KEY", "098A78A7A6A7A6A5");
define("SECRET_IV", "A7D6FS6S5D4F4D6S5");


define('AMAZON_S3_KEY', "AKIAJKBBD5GPSP4LJXNQ");
define('AWS_SECRET_KEY', "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");

//Not showing profiles when create user

define('NOT_SHOWING_PROFILE_ID', json_encode(array(5, 6, 11, 12)));
define('PROFILE_IMAGE_UPLOAD_PATH', json_encode(array(5, 6, 11, 12)));
define("SALES_PROFILE_SNAP_BUCKET", "surewaves-sales-profile-snap");
define('PROFILE_APPROVAL_POSITION', serialize(array('6' => 0, '12' => 0, '11' => 1, '10' => 2, '1' => 3)));

define('PROFILE_NETCONTRIBUITION_PERCENT', serialize(array('11' => 41, '10' => 30, '1' => 27)));

//Account Manager array('No_of_Days=>Amount') ;
define('USER_ACHIEVED_AMOUNT', serialize(array('10' => 50)));
define('PDF_CROWD_USER', 'anant');
define('PDF_CROWD_PASSWORD', '86092539eedfd7f154909b708c30ac16');
define('SALTKEY', '$2a$07$2NAKIAIRC3SCXSWPGUBS6A$');
define('INGESTXPRESS_API', 'http://test.broadcastreporter.com/api/ingestXpress');
define('APPKEY', 'AKIAJBMMAF64R3QI6DKA');
define('DOCUMENT_ROOT', '/opt/lampp/htdocs/');
define('BR_MAPPING_MAIL', 'biswabijayee@surewaves.com,gagana@surewaves.com');

/*
 * ------------------------------------------
 * AMAZON S3 Details
 * ------------------------------------------
 *
 */

/**
 * Yash Bansal
 */
define('ACL_PUBLIC_READ', 'public-read');
define('Ro_Bucket', 'sw_easy_ro_am_pdf');

/**
 * Ravishankar Singh 2019-09-09
 */
define('MAIL_TYPE',
    serialize(array(
        'CREATE_EXT_RO' => 'create_ext_ro',
        'EDIT_EXT_RO' => 'edit_ext_ro',
        'APPROVE_EXT_RO' => 'approve_ext_ro',
        'REJECT_EXT_RO' => 'reject_ext_ro',
        'FORWARD_EXT_RO' => 'forward_ext_ro',
        'AM_CANCEL_EXT_RO' => 'am_cancel_ext_ro',
        'RO_PROGRESS_ORDER_BOOKED' => 'ro_progress_order_booked'
    )
));

/**
 * Ravishankar Singh 2019-09-09
 */
define('MAKE_GOOD_TYPE',
    serialize(array(
        '0' => 'Auto Make Good',
        '1' => 'Client approved make good',
        '2' => 'No Make Good'
    )
));

/**
 * Ravishankar Singh 2019-11-21
 * How many days later an APPROVED CHANNEL will be cancelled from current date
 */
define('DATE_OF_CHANNEL_CANCEL', date("Y-m-d", strtotime("+2 day")));

/* End of file constants.php */
/* Location: ./application/config/constants.php */
