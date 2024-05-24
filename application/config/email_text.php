<?php defined('BASEPATH') OR exit('No direct script access allowed');


//RC::Abhijit (2012-01-05) - Store all email template in this location

//$config['from_email'] = 'do_not_reply@SureWaves.com';
$config['from_email'] = 'RO-support@SureWaves.com';
//$config['from_email'] = 'do_not_reply@SureWaves.com';
$config['from_email_name'] = 'SureWaves Support';

//RO Approval Alert mail after BH or COO in EasyRO
$config['ro_approval_request_alert_email_subject'] = "Approval request alert for %EXTERNAL_RO%";
$config['ro_approval_request_alert_email_body'] = "
Dear %USER_NAME%,<br/>
<br/>
The following customer RO is pending approval <br/><br/>
<b>Customer RO Reference:</b> %EXTERNAL_RO%<br/>
<b>Internal RO Reference:</b> %INTERNAL_RO%<br/>
<b>Start Date:</b> %START_DATE%<br/>
<b>End Date:</b> %END_DATE%<br/>
<b>Requester:</b> %CURRENT_USER%<br/>

<b>Login Link</b>: %LOGIN_URL%<br/>
<br/>
Kindly approve the same.<br/><br/>
Regards,<br/>
SureWaves Team<br/>
<br/>
";

//Alert mail after BH or COO approved schedule of a network in EasyRO
$config['approval_alert_bh_email_subject'] = "%EXTERNAL_RO% - Schedule is now Approved";
$config['approval_alert_bh_email_body'] = "
The following Campaign is now approved for Telecasting. The campaign Details are given below:-<br>
<br/>
<b>Customer RO Reference:</b> %EXTERNAL_RO%<br/>
<b>Internal RO Reference:</b> %INTERNAL_RO%<br/>
<b>Campaign Start Date:</b> %START_DATE%<br/>
<b>Campaign End Date:</b> %END_DATE%<br/>
<b>Gross Ro Amount:</b> %RO_AMOUNT%<br/>
<b>Agency Commission:</b> %AGENCY_COMMISSION%<br/>
<b>Total Other Expenses:</b> %TOTAL_OTHER_EXPENSE%<br/>
<b>Total Network Payout:</b> %TOTAL_NW_PAYOUT%<br/>
<b>Net Surewaves Revenue:</b> %SUREWAVES_REVENUE%<br/>

<b>Make Good Methodology:</b> %MAKEGOOD%<br/>
<b>Approved By:</b>%CURRENT_USER%<br/><br/>
%TABLE_WITH_DATA%<br/><br/>
<br/>

Campaign is now scheduled on MediaGrid for the above networks.<br/><br/>
Regards,<br/>
SureWaves Team<br/>
<br/>
";

//Alert mail after BH or COO approved schedule of a network in EasyRO
$config['approval_alert_email_subject'] = "%EXTERNAL_RO% is now Approved";
$config['approval_alert_email_body'] = "
The following Campaign is now approved for Telecasting. The campaign Details are given below:-<br>
<br/>
<b>Customer RO Reference:</b> %EXTERNAL_RO%<br/>
<b>Internal RO Reference:</b> %INTERNAL_RO%<br/>
<b>Campaign Start Date:</b> %START_DATE%<br/>
<b>Campaign End Date:</b> %END_DATE%<br/>
<b>Channels:</b> %CHANNELS%<br/>
<b>Markets:</b> %MARKETS%<br/><br/>

<b>Approved By:</b> %CURRENT_USER%<br/><br/>
<b>Make Good Methodology:</b> %MAKEGOOD%<br/>
<br/>
Campaign is now scheduled on MediaGrid for the above channels.<br/><br/>
Regards,<br/>
SureWaves Team<br/>
<br/>
";
//Login url mail for users created by Admin
$config['login_url_email_subject'] = "Registration for SureWaves Easy RO";
$config['login_url_email_body'] = "
Dear %USER_NAME%,<br/>
<br/>
You have been registered for SureWaves Easy RO as %PROFILE_NAME%. Please find the login URL and Login Credentials below:
<br/><br/><br/>
<b>Login Id&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b> &nbsp;%USER_EMAIL%<br/>
<b>Password&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;%PASSWORD%<br/>
<b>Login Link&nbsp;&nbsp;:</b> %LOGIN_URL%<br/>
<br/>
Regards,<br/>
SureWaves Team<br/>
<br/>
";

//pdfcrowd token alert
$config['pdf_token_alert_email_subject'] = "Alert for PDFCrowd Tokens";
$config['pdf_token_alert_email_body'] = "
Dear Admin,<br/>
<br/>
You are left with only %TOKEN_COUNT% tokens in PDFCrowd. Please change your plan as soon as possible.
<br/><br/><br/>
<br/>
Regards,<br/>
SureWaves Team<br/>
<br/>
";

//Forgot password mail for users created by Admin
$config['forgot_password_email_subject'] = "SureWaves EasyRO %USER_NAME% login details";
$config['forgot_password_email_body'] = "
Dear %USER_NAME%,<br/>
<br/>
Greetings from SureWaves EasyRO. <br/>
<br/>
Your credentials for SureWaves EasyRO platform are:<br/>
<br/>
Email: %USER_EMAIL%<br/>
Password: %PASSWORD%<br/>
Login Link: %LOGIN_URL%<br/>
<br/>
Best Wishes,<br/>
SureWaves EasyRO Admin<br/>
<br/>
";

//Edit User mail for users created by Admin
$config['Edit_user_email_subject'] = "SureWaves EasyRO %USER_NAME% user details after editing";
$config['Edit_user_email_body'] = "
Dear %USER_NAME%,<br/>
<br/>
Greetings from SureWaves EasyRO. <br/>
<br/>
Your new credentials for SureWaves EasyRO platform after editing are:<br/>
<br/>
Email: %USER_EMAIL%<br/>
Login Link: %LOGIN_URL%<br/>
<br/>
Best Wishes,<br/>
SureWaves EasyRO Admin<br/>
<br/>
";

//user updated crdentails mail
$config['user_update_email_subject'] = "User details updated for %USER_NAME% in SureWaves EasyRO";
$config['user_update_email_body'] = "
Dear %USER_NAME%,<br/>
<br/>
Greetings from SureWaves! <br/>
<br/>
Your credentials  for SureWaves EasyRO have been updated, Please find login details below:-<br/>
<br/><br/>
<b>Login Id&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b> &nbsp;%USER_EMAIL%<br/>
<b>Password&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;%PASSWORD%<br/>
<b>Login Link&nbsp;&nbsp;:</b> %LOGIN_URL%<br/>
<br/>
Regards,<br/>
SureWaves Team<br/>
<br/>
";
//user crdentails mail
$config['user_email_subject'] = "User details for %USER_NAME% in SureWaves EasyRO";
$config['user_email_body'] = "
Dear %USER_NAME%,<br/>
<br/>
Greetings from SureWaves! <br/>
<br/>
Please find login credentials details below:-<br/>
<br/><br/>
<b>Login Id&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b> &nbsp;%USER_EMAIL%<br/>
<b>Password&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;%PASSWORD%<br/>
<b>Login Link&nbsp;&nbsp;:</b> %LOGIN_URL%<br/>
<br/>
Regards,<br/>
SureWaves Team<br/>
<br/>
";
//Reset Login Details  mail for users created by Admin
$config['reset_login_details_email_subject'] = "SureWaves EasyRO %USER_NAME% login detrails";
$config['reset_login_details_email_body'] = "
Dear %USER_NAME%,<br/>
<br/>
Greetings from SureWaves EasyRO. <br/>
<br/>
Your credential for SureWaves EasyRO platform is:<br/>
<br/>
Email: %USER_EMAIL%<br/>
Password: %PASSWORD%<br/>
Login Link: %LOGIN_URL%<br/>
<br/>
Best Wishes,<br/>
SureWaves EasyRO Admin<br/>
<br/>
";

//Network RO attachment mail for Network contacts by Admin
$config['networkro_email_subject'] = "Release Order from SureWaves for :%NETWORK_RO%";
$config['networkro_email_body'] = "
<br/>
We are pleased to forward  a new Release Order, whose details are given below:<br/>
<br/>
<b>Reference RO Number:</b>&nbsp;%NETWORK_RO%<br/><br/>
<b>Client Name:</b>&nbsp;%CLIENT_NAME%<br/>
<b>Campaign Start Date:</b>&nbsp;%START_DATE%<br/>
<b>Campaign End Date:</b>&nbsp;%END_DATE%<br/>
<b>Channels:</b>&nbsp;%CHANNELS%<br/>
<br/>
We look forward to your full cooperation in scheduling the campaign accordingly and ensuring 100% play-out as per the schedule.<br/>
<br/>
<br/>
In-case of any clarifications, please contact RO-support@surewaves.com.<br/><br/>

%INSERT_NOTICE_RO%<br/><br/>

Regards,<br/>
Sony Saha,<br/>
Assistant Manager – Business Support,<br/>
Land-line :+9180-49698956<br/>
<br/>
";

//create external ro by account manager
$config['create_ext_ro_email_subject'] = "%EXTERNAL_RO% is Submitted";
$config['create_ext_ro_email_body'] = "

We would like to inform you that the following external RO has been successfully submitted :<br/>

<b>Customer RO Reference:</b> %EXTERNAL_RO%<br/>
<b>Internal RO Reference:</b> %INTERNAL_RO%<br/>
<b>Agency:</b> %AGENCY%<br/>
<b>Client:</b> %CLIENT%<br/>
<b>Brand:</b> %BRAND%<br/>
<b>Makegood Type:</b> %MAKEGOOD_TYPE%<br/>
<b>Markets:</b> %MARKET%<br/>
<b>Special Instructions:</b> %INSTRUCTION%<br/>
<b>Campaign Start Date:</b> %START_DATE%<br/>
<b>Campaign End Date:</b> %END_DATE%<br/>
<b>Submitted by:</b> %AM_NAME%<br/>

The RO will be ready for scheduling after it is approved. If you notice any issue with the above RO please contact BH/COO immediately.<br/>
<br/>

Regards,<br/>
SureWaves Team<br/>
<br/>
";

//edit external ro by account manager
$config['edit_ext_ro_email_subject'] = "%EXTERNAL_RO% is Updated";
$config['edit_ext_ro_email_body'] = "

We would like to inform you that the following external RO has been successfully updated :<br/>

<b>Customer RO Reference:</b> %EXTERNAL_RO%<br/>
<b>Internal RO Reference:</b> %INTERNAL_RO%<br/>
<b>Agency:</b> %AGENCY%<br/>
<b>Client:</b> %CLIENT%<br/>
<b>Brand:</b> %BRAND%<br/>
<b>Makegood Type:</b> %MAKEGOOD_TYPE%<br/>
<b>Markets:</b> %MARKET%<br/>
<b>Special Instructions:</b> %INSTRUCTION%<br/>
<b>Campaign Start Date:</b> %START_DATE%<br/>
<b>Campaign End Date:</b> %END_DATE%<br/>
<b>Submitted by:</b> %AM_NAME%<br/>

The RO will be ready for scheduling after it is approved. If you notice any issue with the above RO please contact BH/COO immediately.<br/>
<br/>

Regards,<br/>
SureWaves Team<br/>
<br/>
";

//Approve external ro
$config['approve_ext_ro_email_subject'] = "%EXTERNAL_RO% - RO is Approved";
$config['approve_ext_ro_email_body'] = "

We would like to inform you that the following external RO has been successfully approved:<br/>

<b>Customer RO Reference:</b> %EXTERNAL_RO%<br/>
<b>Internal RO Reference:</b> %INTERNAL_RO%<br/>
<b>Agency:</b> %AGENCY%<br/>
<b>Client:</b> %CLIENT%<br/>
<b>Brand:</b> %BRAND%<br/>
<b>Makegood Type:</b> %MAKEGOOD_TYPE%<br/>
<b>Markets:</b> %MARKET%<br/>
<b>Special Instructions:</b> %INSTRUCTION%<br/>
<b>Campaign Start Date:</b> %START_DATE%<br/>
<b>Campaign End Date:</b> %END_DATE%<br/>
<b>Submitted by:</b> %AM_NAME%<br/>

The RO is now ready for scheduling. If you notice any issue with the above RO please contact BH/COO immediately.<br/>
<br/>

Regards,<br/>
SureWaves Team<br/>
<br/>
";

//Forward external ro
$config['forward_ext_ro_email_subject'] = "%EXTERNAL_RO% is Forwarded";
$config['forward_ext_ro_email_body'] = "

We would like to inform you that the following external RO has been forwarded to the %FORWARDED_TO% for approval:<br/>


<b>Customer RO Reference:</b> %EXTERNAL_RO%<br/>
<b>Internal RO Reference:</b> %INTERNAL_RO%<br/>
<b>Agency:</b> %AGENCY%<br/>
<b>Client:</b> %CLIENT%<br/>
<b>Brand:</b> %BRAND%<br/>
<b>Makegood Type:</b> %MAKEGOOD_TYPE%<br/>
<b>Markets:</b> %MARKET%<br/>
<b>Special Instructions:</b> %INSTRUCTION%<br/>
<b>Campaign Start Date:</b> %START_DATE%<br/>
<b>Campaign End Date:</b> %END_DATE%<br/>
<b>Submitted by:</b> %AM_NAME%<br/>

The RO will be ready for scheduling after it is approved. If you notice any issue with the above RO please contact BH/COO immediately.<br/>
<br/>

Regards,<br/>
SureWaves Team<br/>
<br/>
";

//Reject external ro
$config['reject_ext_ro_email_subject'] = "%EXTERNAL_RO% is Rejected";
$config['reject_ext_ro_email_body'] = "

We would like to inform you that the following external RO has been rejected as:%REASON%<br/>


<b>Customer RO Reference:</b> %EXTERNAL_RO%<br/>
<b>Internal RO Reference:</b> %INTERNAL_RO%<br/>
<b>Agency:</b> %AGENCY%<br/>
<b>Client:</b> %CLIENT%<br/>
<b>Brand:</b> %BRAND%<br/>
<b>Makegood Type:</b> %MAKEGOOD_TYPE%<br/>
<b>Markets:</b> %MARKET%<br/>
<b>Special Instructions:</b> %INSTRUCTION%<br/>
<b>Campaign Start Date:</b> %START_DATE%<br/>
<b>Campaign End Date:</b> %END_DATE%<br/>
<b>Submitted by:</b> %AM_NAME%<br/>

You may choose to resubmit the RO after modifying it to gain approval. If you notice any issue with the above RO please contact BH/COO immediately.<br/>
<br/>

Regards,<br/>
SureWaves Team<br/>
<br/>
";


//mail to CEO when net contribution is less than 25%
$config['ceo_notify_net_email_subject'] = "%EXTERNAL_RO%: Net Contribution is less than 25%";
$config['ceo_notify_net_email_body'] = "
Hi Raj,<br/><br/>

This is to notify you that the RO number %EXTERNAL_RO% was approved with a Net Contribution of less than 25%.<br/>

<b>Submitted by:</b> %AM_NAME%<br/>
<b>Approved by:</b> %APPROVED_BY%<br/>
<b>Gross RO Amount:</b> %GROSS%<br/>
<b>Markets:</b> %MARKET%<br/>
<b>Justification For Approval:</b> %JUSTIFICATION_FOR_APPROVAL%<br/>
<b>Corrective Action Plan For Future:</b> %CORRECTIVE_ACTION_PLAN%<br/>
<b>Net Contibution:</b> %NET_CONTRIBUTION%<br/><br/>

Regards,<br/>
SureWaves Team<br/>
<br/>
";

//create non fct ro by account manager
$config['create_non_fct_ro_email_subject'] = "%EXTERNAL_RO% is Submitted";
$config['create_non_fct_ro_email_body'] = "

We would like to inform you that the following Non FCT RO has been successfully submitted:<br/>

<b>Customer RO Reference:</b> %EXTERNAL_RO%<br/>
<b>Internal RO Reference:</b> %INTERNAL_RO%<br/>
<b>Agency:</b> %AGENCY%<br/>
<b>Client:</b> %CLIENT%<br/>
<b>Description:</b> %INSTRUCTION%<br/>
<b>Submitted by:</b> %AM_NAME%<br/>

We'll let you know once it is approved. If you notice any issue with the above RO please contact BH/COO immediately.<br/>
<br/>

Regards,<br/>
SureWaves Team<br/>
<br/>
";

//approve non fct ro
$config['approve_non_fct_ro_email_subject'] = "%EXTERNAL_RO% is Approved";
$config['approve_non_fct_ro_email_body'] = "

We would like to inform you that the following Non FCT RO has been successfully approved:<br/>

<b>Customer RO Reference:</b> %EXTERNAL_RO%<br/>
<b>Internal RO Reference:</b> %INTERNAL_RO%<br/>
<b>Agency:</b> %AGENCY%<br/>
<b>Client:</b> %CLIENT%<br/>
<b>Description:</b> %INSTRUCTION%<br/>
<b>Submitted by:</b> %AM_NAME%<br/>

If you notice any issue with the above RO please contact BH/COO immediately.<br/>
<br/>

Regards,<br/>
SureWaves Team<br/>
<br/>
";

//reject non fct ro
$config['reject_non_fct_ro_email_subject'] = "%EXTERNAL_RO% is Rejected";
$config['reject_non_fct_ro_email_body'] = "

We would like to inform you that the following Non FCT RO has been rejected as:<br/>

<b>Customer RO Reference:</b> %EXTERNAL_RO%<br/>
<b>Internal RO Reference:</b> %INTERNAL_RO%<br/>
<b>Agency:</b> %AGENCY%<br/>
<b>Client:</b> %CLIENT%<br/>
<b>Description:</b> %INSTRUCTION%<br/>
<b>Submitted by:</b> %AM_NAME%<br/>

You may choose to resubmit the Non FCT RO after modifying it to gain approval. If you notice any issue with the above RO please contact BH/COO immediately.<br/>
<br/>

Regards,<br/>
SureWaves Team<br/>
<br/>
";

// cancel external ro
$config['cancel_ext_ro_email_subject'] = "Cancel RO Request approved for %EXTERNAL_RO%";
$config['cancel_ext_ro_email_body'] = "
<br/>
The Cancellation request for the below RO is now approved: <br/><br/>
<b>Customer RO Reference:</b> %EXTERNAL_RO%<br/>
<b>Internal RO Reference:</b> %INTERNAL_RO%<br/>
<b>Client Name:</b> %CLIENT_NAME%<br/>
<b>Agency Name:</b> %AGENCY_NAME%<br/>
<b>Campaign End Date:</b> %CAMPAIGN_END_DATE%<br/>
<b>Cancel Date:</b> %RO_CANCEL_DATE%<br/>
<b>Cancel Reason:</b> %REASON%<br/>
<b>Billing Instruction:</b> %INVOICE_INST%<br/>
<br/><br/>
		All Campaigns corresponding to the RO will be cancelled on %RO_CANCEL_DATE% automatically by the system.  
Kindly communicate with the Network partners on the cancellation.<br/><br/>

Regards,<br/>
SureWaves Team<br/>
<br/>
";
// complete cancel external ro
$config['complete_cancel_ext_ro_email_subject'] = "Cancel Request approved for %EXTERNAL_RO%";
$config['complete_cancel_ext_ro_email_body'] = "
<br/>
The Cancellation request for the below RO is now approved: <br/><br/>
<b>Customer RO Reference:</b> %EXTERNAL_RO%<br/>
<b>Internal RO Reference:</b> %INTERNAL_RO%<br/>
<b>Client Name:</b> %CLIENT_NAME%<br/>
<b>Agency Name:</b> %AGENCY_NAME%<br/>
<b>Cancel Date:</b> %RO_CANCEL_DATE%<br/>
<b>Cancel Reason:</b> %REASON%<br/>
<b>Billing Instruction:</b> %INVOICE_INST%<br/>
<br/><br/>
		All Campaigns corresponding to the RO will be cancelled on %RO_CANCEL_DATE% automatically by the system.  
Kindly communicate with the Network partners on the cancellation.<br/><br/>

Regards,<br/>
SureWaves Team<br/>
<br/>
";
// daily email report
$config['daily_report_email_subject'] = "Daily Report";
$config['daily_report_email_body'] = "
Dear %USER_NAME%,<br/>
<br/>
Daily Email Report<br/><br/>
%DATA%<br/>

Regards,<br/>
SureWaves Team<br/>
<br/>
";
// MIS Monthly email report Internationally
$config['mis_month_report_international_email_subject'] = "MIS Report (Monthwise Internationally) for %MONTH%";
$config['mis_month_report_international_email_body'] = "
The following is the MIS report for %MONTH%,<br/>
<br/><br/>

<b>Region Wise MTD Revenue Summary (In INR) As on %TODAYS_DATE% :</b>
%SUMMARY_MONTH%
<br/><br/>
<b>Current Month FCT Revenue Summary (In INR) As on %TODAYS_DATE% :</b>
%MONTHLY_DATA%<br/>
<br/>
<b>Current Month NON FCT Revenue Summary (In INR) As on %TODAYS_DATE% :</b>
%MONTHLY_NON_FCT_DATA%<br/>
<br/><br/>
<b>Monthwise FCT Revenue Summary (In INR,India) As on %TODAYS_DATE% :</b>
%FINANCIAL_YEAR_DATA%<br/>
<b>Monthwise FCT Revenue Summary (In INR,International) As on %TODAYS_DATE% :</b>
%FINANCIAL_YEAR_DATA_INTERNATIONAL%<br/>
<b>Monthwise NON FCT Revenue Summary (In INR,India) As on %TODAYS_DATE% :</b>
%FINANCIAL_YEAR_NON_FCT_DATA%<br/>
<b>Monthwise NON FCT Revenue Summary (In INR,International) As on %TODAYS_DATE% :</b>
%FINANCIAL_YEAR_NON_FCT_DATA_INTERNATIONAL%<br/>
<b>Monthwise Total (FCT & NON FCT) Revenue Summary (In INR) As on %TODAYS_DATE% :</b>
%FINANCIAL_YEAR_TOTAL_DATA%<br/>

Regards,<br/>
SureWaves Team<br/>
<br/>
";
// MIS Monthly email report
$config['mis_month_report_email_subject'] = "MIS Report (Monthwise) for %MONTH%";
$config['mis_month_report_email_body'] = "
The following is the MIS report for %MONTH%,<br/>
<br/><br/>

<b>Region Wise MTD Revenue Summary (In INR) As on %TODAYS_DATE% :</b>
%SUMMARY_MONTH%
<br/><br/>
<b>Current Month FCT Revenue Summary (In INR) As on %TODAYS_DATE% :</b>
%MONTHLY_DATA%<br/>
<br/>
<b>Current Month NON FCT Revenue Summary (In INR) As on %TODAYS_DATE% :</b>
%MONTHLY_NON_FCT_DATA%<br/>
<br/><br/>
<b>Monthwise FCT Revenue Summary (In INR) As on %TODAYS_DATE% :</b>
%FINANCIAL_YEAR_DATA%<br/>
<b>Monthwise NON FCT Revenue Summary (In INR) As on %TODAYS_DATE% :</b>
%FINANCIAL_YEAR_NON_FCT_DATA%<br/>
<b>Monthwise Total (FCT & NON FCT) Revenue Summary (In INR) As on %TODAYS_DATE% :</b>
%FINANCIAL_YEAR_TOTAL_DATA%<br/>

Regards,<br/>
SureWaves Team<br/>
<br/>
";
// MIS Monthly email report Duplicate
$config['mis_month_report_prev_email_subject'] = "MIS Report (Monthwise) for %MONTH%";
$config['mis_month_report_prev_email_body'] = "
The following is the MIS report for %MONTH%,<br/>
<br/><br/>


<b>Current Month FCT Revenue Summary (IN INR) For %CURRENT_MONTH_YEAR% :</b>
%MONTHLY_DATA%<br/>
<br/>
<b>Current Month NON FCT Revenue Summary (IN INR) %CURRENT_MONTH_YEAR% :</b>
%MONTHLY_NON_FCT_DATA%<br/>
<br/><br/>
<b>Monthwise FCT Revenue Summary (IN INR) As on %TODAYS_DATE% :</b>
%FINANCIAL_YEAR_DATA%<br/>
<b>Monthwise NON FCT Revenue Summary (IN INR) As on %TODAYS_DATE% :</b>
%FINANCIAL_YEAR_NON_FCT_DATA%<br/>
<b>Monthwise Total(FCT & NON FCT) Revenue Summary (IN INR) As on %TODAYS_DATE% :</b>
%FINANCIAL_YEAR_TOTAL_DATA%<br/>

Regards,<br/>
SureWaves Team<br/>
<br/>
";
// MIS Monthly email report
$config['mis_month_report_adv_email_subject'] = "MIS Report - (Monthwise) for %MONTH%";
$config['mis_month_report_adv_email_body'] = "
The following is the MIS report for %MONTH%,<br/>
<br/><br/>
<b>Current Month Revenue (%CURRENT_MONTH_YEAR%):</b>
%MONTHLY_DATA%<br/>
<br/><br/>
<b>Monthwise Revenue (As on %TODAYS_DATE%):</b>
%FINANCIAL_YEAR_DATA%<br/>
Regards,<br/>
SureWaves Team<br/>
<br/>
";
// MIS Monthly email report
$config['mis_month_report_adv_prev_email_subject'] = "MIS Report - (Monthwise) for %MONTH%";
$config['mis_month_report_adv_prev_email_body'] = "
The following is the MIS report for %MONTH%,<br/>
<br/><br/>


<b>Current Month FCT Revenue (%CURRENT_MONTH_YEAR%):</b>
%MONTHLY_DATA%<br/>
<br/>
<b>Current Month Non Fct Revenue (%CURRENT_MONTH_YEAR%):</b>
%MONTHLY_NON_FCT_DATA%<br/>
<br/><br/>
<b>Monthwise FCT Revenue (As on %TODAYS_DATE%):</b>
%FINANCIAL_YEAR_DATA%<br/>
<b>Monthwise Non FCT Revenue (As on %TODAYS_DATE%):</b>
%FINANCIAL_YEAR_NON_FCT_DATA%<br/>
<b>Monthwise Total(Fct & Non-Fct) Revenue (As on %TODAYS_DATE%):</b>
%FINANCIAL_YEAR_TOTAL_DATA%<br/>

Regards,<br/>
SureWaves Team<br/>
<br/>
";
// cancel external ro request sent my AM
$config['am_cancel_ext_ro_email_subject'] = "Cancel RO Request received for %EXTERNAL_RO%";
$config['am_cancel_ext_ro_email_body'] = "
<br/>
%ACCOUNT_MGR_NAME% has requested cancellation of the following RO:
<br/>
<b>External RO Number:</b> %EXTERNAL_RO% <br/>
<b>Client Name:</b> %CLIENT_NAME% <br/>
<b>Agency Name:</b> %AGENCY_NAME% <br/>
<b>Campaign End Date:</b> %CAMPAIGN_END_DATE% <br/>
<b>Cancel Date:</b> %RO_CANCEL_DATE% <br/>
<b>Cancel Reason:</b> %CANCEL_REASON% <br/>
<b>Billing Instruction:</b> %BILLING_INSTRUCTION% <br/>
<br/><br/>
Kindly login to EasyRO and approve for RO cancellation to take effect.<br/><br/>

Regards,<br/>
SureWaves Team<br/>
<br/>
";

//Network RO attachment mail for Network contacts by Admin (Revision of release order [Amendment])
$config['amendment_email_subject'] = "Revision for  :%NETWORK_RO%";
$config['amendment_email_body'] = "
<br/>
Greetings from SureWaves!<br/>
<br/>
Please note that the '%OLD_NETWORK_RO%' as mentioned below stands cancelled with immediate effect and is being replaced with the '%NETWORK_RO%' attached herewith.<br/><br/>  
<b>Cancelled RO Number: %OLD_NETWORK_RO%</b><br/>
<b>New RO Number: %NETWORK_RO%</b><br/>
<b>Client Name:</b>&nbsp;%CLIENT_NAME%<br/>
<b>Campaign Start Date:</b>&nbsp;%START_DATE%<br/>
<b>Campaign End Date:</b>&nbsp;%END_DATE%<br/>
<b>Channels:</b>&nbsp;%CHANNELS%<br/>
<br/>
We request you to re-schedule the campaign immediately as per the attached RO and ensure 100% play-out as per the revised schedule. <br/><br/>
Please provide the reference of the '%NETWORK_RO%' in your invoice to be submitted to us on completion of the Campaign. Any invoice submitted with the '%OLD_NETWORK_RO%' as above will not be processed for payment.
<br/><br/>
In-case of any clarifications, please contact RO-support@surewaves.com.<br/><br/>

%INSERT_NOTICE_RO%<br/><br/>

Regards,<br/>
Sony Saha,<br/>
Assistant Manager – Business Support,<br/>
Land-line :+9180-49698956<br/>
<br/>
";

//Network RO attachment mail for Network contacts by Admin (Revision of release order [Stop Campaign])
$config['stop_email_subject'] = "Campaign Stop Advice for  :%NETWORK_RO%";
$config['stop_email_body'] = "
<br/>
Greetings from SureWaves!<br/>
<br/>
Please note that the '%OLD_NETWORK_RO%' as mentioned below stands cancelled with immediate effect and is being replaced with the '%NETWORK_RO%' attached herewith.<br/><br/>  
<b>Cancelled RO Number: %OLD_NETWORK_RO%</b><br/>
<b>New RO Number: %NETWORK_RO%</b><br/>
<b>Client Name:</b>&nbsp;%CLIENT_NAME%<br/>
<b>Campaign Start Date:</b>&nbsp;%START_DATE%<br/>
<b>Campaign End Date:</b>&nbsp;%END_DATE%<br/>
<b>Channels:</b>&nbsp;%CHANNELS%<br/>
<br/>
We request you to stop the campaign beyond the Campaign End Date as per the '%NETWORK_RO%' and ensure 100% play-out as per the revised schedule. <br/><br/>
Please provide the reference of the '%NETWORK_RO%' in your invoice to be submitted to us on completion of the Campaign. Any invoice submitted with the '%OLD_NETWORK_RO%' as above will not be processed for payment.
<br/><br/>
In-case of any clarifications, please contact RO-support@surewaves.com.<br/><br/>

%INSERT_NOTICE_RO%<br/><br/>

Regards,<br/>
Sony Saha,<br/>
Assistant Manager – Business Support,<br/>
Land-line :+9180-49698956<br/>
<br/>
";

//Network RO attachment mail for Network contacts by Admin (Revision of release order [Stop Campaign])
$config['complete_stop_email_subject'] = "Campaign Stop Advice for  :%NETWORK_RO%";
$config['complete_stop_email_body'] = "
<br/>
Greetings from SureWaves!<br/>
<br/>
Please note that the '%OLD_NETWORK_RO%' as mentioned below stands cancelled with immediate effect and is being replaced with the '%NETWORK_RO%' attached herewith.<br/><br/>  
<b>Cancelled RO Number: %OLD_NETWORK_RO%</b><br/>
<b>New RO Number: %NETWORK_RO%</b><br/>
<b>Client Name:</b>&nbsp;%CLIENT_NAME%<br/>
<b>Channels:</b>&nbsp;%CHANNELS%<br/>
<br/>
This campaign stands completely cancelled and any spot or banner seconds scheduled for this RO will not be considered for payment. <br/><br/>
Please provide the reference of the '%NETWORK_RO%' for any communication regarding this RO.
<br/><br/>
In-case of any clarifications, please contact RO-support@surewaves.com.<br/><br/>

%INSERT_NOTICE_RO%<br/><br/>

Regards,<br/>
Sony Saha,<br/>
Assistant Manager – Business Support,<br/>
Land-line :+9180-49698956<br/>
<br/>
";

//Intimation Mail to BH/COO on Market Cancellation request
$config['market_cancellation_request_email_subject'] = "Market Cancel Request received for %EXTERNAL_RO_NUMBER%";
$config['market_cancellation_request_email_body'] = "
<br/>
%ACCOUNT_MANAGER_NAME% has requested market cancellation for the following RO: <br/>
External RO Number: %EXTERNAL_RO_NUMBER% <br/>
Internal RO Number: %INTERNAL_RO_NUMBER% <br/>
Client Name: %CLIENT_NAME% <br/>
Agency Name: %AGENCY_NAME% <br/>
Markets Cancelled: %MARKET_CANCELLED% <br/>
Market Pricelist: %MARKET_EDITED_PRICELIST% <br/>
Market Cancel Reason: %REASON% <br/>
<br/>
Kindly login to EasyRO and approve the Cancel for the Market cancellations to take effect. <br/><br/>
Regards,<br/>
SureWaves Team

";

//Intimation email to BH/COO,Requesting AM and Scheduler after market cancellation is approved
$config['market_cancellation_approved_email_subject'] = "Market Cancel Request approved for  %EXTERNAL_RO_NUMBER%";
$config['market_cancellation_approved_email_body'] = "
<br/>
Market Cancellation Request for the below RO is now approved  <br/>
External RO Number: %EXTERNAL_RO_NUMBER% <br/>
Internal RO Number: %INTERNAL_RO_NUMBER% <br/>
Client Name: %CLIENT_NAME% <br/>
Agency Name: %AGENCY_NAME% <br/>
Market Cancelled: %MARKET_CANCELLED% <br/>
Market Pricelist: %MARKET_EDITED_PRICELIST% <br/>
Revised Gross RO Amount:%RO_AMOUNT% <br/>
Market Cancel Reason: %REASON% <br/>
<br/>

All Campaigns corresponding to the RO will be cancelled on %CANCELLED_DATE% automatically by the system. 
Kindly communicate with the Network partners on the cancellation. <br/>
<br/><br/>
Regards,<br/>
SureWaves Team 


";

//Intimation email to BH/COO,Requesting AM and Scheduler after market cancellation is Rejected
$config['market_cancellation_reject_email_subject'] = "Market Cancel Request rejected for %EXTERNAL_RO_NUMBER%";
$config['market_cancellation_reject_email_body'] = "
<br/>
Market Cancellation Request for the below RO is rejected <br/>
External RO Number: %EXTERNAL_RO_NUMBER% <br/>
Internal RO Number: %INTERNAL_RO_NUMBER% <br/>
Client Name: %CLIENT_NAME% <br/> 
Agency Name: %AGENCY_NAME% <br/> 
Market Cancelled: %MARKET_CANCELLED% <br/>
Market Pricelist: %MARKET_EDITED_PRICELIST% <br/>
Market Cancel Reject Reason: %REASON% <br/>
<br/><br/>
Regards,<br/>
SureWaves Team



";

$config['content_cancellation_request_email_subject'] = "Content Cancel Request received for %EXTERNAL_RO_NUMBER%";
$config['content_cancellation_request_email_body'] = "
<br/>
%ACCOUNT_MANAGER_NAME% has requested market cancellation for the following RO: <br/>
External RO Number: %EXTERNAL_RO_NUMBER% <br/>
Internal RO Number: %INTERNAL_RO_NUMBER% <br/>
Client Name: %CLIENT_NAME% <br/>
Agency Name: %AGENCY_NAME% <br/>
Content Cancelled: %CONTENT_CANCELLED% <br/>
Market Pricelist (after content cancellation approval):<br/> 
%MARKET_CANCELLED% <br/>
Content Cancel Reason: %REASON% <br/>
<br/>

Kindly login to EasyRO and approve the Cancel for the Market cancellations to take effect. <br/><br/>
Regards,<br/>
SureWaves Team

";

$config['brand_cancellation_request_email_subject'] = "Brand Cancel Request received for %EXTERNAL_RO_NUMBER%";
$config['brand_cancellation_request_email_body'] = "
<br/>
%ACCOUNT_MANAGER_NAME% has requested market cancellation for the following RO: <br/>
External RO Number: %EXTERNAL_RO_NUMBER% <br/>
Internal RO Number: %INTERNAL_RO_NUMBER% <br/>
Client Name: %CLIENT_NAME% <br/>
Agency Name: %AGENCY_NAME% <br/>
Brand Cancelled: %BRAND_CANCELLED% <br/>
Market Pricelist (after brand cancellation approval):<br/> 
%MARKET_CANCELLED% <br/>
Content Cancel Reason: %REASON% <br/>
<br/>

Kindly login to EasyRO and approve the Cancel for the Market cancellations to take effect. <br/><br/>
Regards,<br/>
SureWaves Team

";

//Intimation email to BH/COO,Requesting AM and Scheduler after market cancellation is approved
$config['content_cancellation_approved_email_subject'] = "content Cancel Request approved for  %EXTERNAL_RO_NUMBER%";
$config['content_cancellation_approved_email_body'] = "
<br/>
Content Cancellation Request for the below RO is now approved  <br/>
External RO Number: %EXTERNAL_RO_NUMBER% <br/>
Internal RO Number: %INTERNAL_RO_NUMBER% <br/>
Client Name: %CLIENT_NAME% <br/>
Agency Name: %AGENCY_NAME% <br/>
Content Cancelled: %CONTENT_CANCELLED% <br/>
Market Pricelist (after content cancellation):<br/> 
%MARKET_CANCELLED% <br/>
Revised Gross RO Amount:%REVISED_GROSS_RO_AMOUNT% <br/>
Content Cancel Reason: %REASON% <br/>
<br/>

All Campaigns corresponding to the RO will be cancelled on %CANCELLED_DATE% automatically by the system. 
Kindly communicate with the Network partners on the cancellation. <br/>
<br/><br/>
Regards,<br/>
SureWaves Team 


";
//Intimation email to BH/COO,Requesting AM and Scheduler after market cancellation is approved
$config['brand_cancellation_approved_email_subject'] = "Brand Cancel Request approved for  %EXTERNAL_RO_NUMBER%";
$config['brand_cancellation_approved_email_body'] = "
<br/>
Brand Cancellation Request for the below RO is now approved  <br/>
External RO Number: %EXTERNAL_RO_NUMBER% <br/>
Internal RO Number: %INTERNAL_RO_NUMBER% <br/>
Client Name: %CLIENT_NAME% <br/>
Agency Name: %AGENCY_NAME% <br/>
Brand Cancelled: %BRAND_CANCELLED% <br/>
Market Pricelist (after content cancellation):<br/> 
%MARKET_CANCELLED% <br/>
Revised Gross RO Amount:%REVISED_GROSS_RO_AMOUNT% <br/>
Brand Cancel Reason: %REASON% <br/>
<br/>

All Campaigns corresponding to the RO will be cancelled on %CANCELLED_DATE% automatically by the system. 
Kindly communicate with the Network partners on the cancellation. <br/>
<br/><br/>
Regards,<br/>
SureWaves Team 


";
//Intimation email to AM,GM and RD for collection of Invoice amount
$config['invoice_collection_email_subject'] = "Payment Collected - Invoice Number : %INVOICE_NUMBER%";
$config['invoice_collection_email_body'] = "
<br/>
Hi %AM_NAME%,<br/><br/>

This email confirms that a collection of Rs. %AMOUNT_COLLECTED% has been been processed against Invoice number <b> %INVOICE_NUMBER% </b> on %COLLECTION_DATE%.

Please find the details below:<br/><br/>

<b>RO Number:</b> %EXTERNAL_RO_NUMBER% <br/>
<b>Buyer:</b> %AGENCY_NAME% <br/>
<b>Client:</b> %CLIENT_NAME% <br/>
<b>Invoice Number:</b> %INVOICE_NUMBER% <br/>
<b>Invoice Amount:</b> %INVOICE_AMOUNT% <br/>
<b>Amount Collected:</b> %AMOUNT_COLLECTED% <br/>
<b>Amount Remaining:</b> %AMOUNT_REMAINING% <br/>
<b>Mode of Payment:</b> %MODE_OF_PAYMENT% <br/>

<br/><br/>
Regards,<br/>
SureWaves Team



";

//Intimation email to BH/COO,Requesting AM and Scheduler after Cancel RO is Rejected
$config['ro_cancellation_reject_email_subject'] = "Cancel Ro Request rejected for %EXTERNAL_RO_NUMBER%";
$config['ro_cancellation_reject_email_body'] = "
<br/>
External RO Number: %EXTERNAL_RO_NUMBER% <br/>
Internal RO Number: %INTERNAL_RO_NUMBER% <br/>
Client Name: %CLIENT_NAME% <br/> 
Agency Name: %AGENCY_NAME% <br/> 
Campaign End Date: %CAMPAIGN_END_DATE% <br/>
Cancel Date: %CANCEL_DATE% <br/>
Cancel Ro Reject Reason: %REASON% <br/>
Billing Instruction: %BILLING_INSTUCTION% <br/>
<br/><br/>
Regards,<br/>
SureWaves Team
";

//Intimation from surewaves for All Notice channel of an Enterprise
$config['all_notice_channel_email_subject'] = "Intimation from SureWaves";
$config['all_notice_channel_email_body'] = "
For Your Kind Information,<br/>

HANSA RESEARCH has commenced third party audit and validation of SureWaves Real Time Telecast Logs. With this, 
another Industry requirement to create Cable TV advertising as an effective platform for National Advertisers has 
been met by SureWaves. We expect this to have a positive result on overall revenues.

YOUR CHANNEL NEEDS TO BE ONLINE FOR PURPOSE OF AUDIT. %CHANNELS% ARE 
CURRENTLY OFFLINE AND HENCE NO RELEASE ORDER IS BEING GENERATED FOR THE THESE. CONTACT 
SUREWAVES STAFF OR UNDERSIGNED IMMEDIATELY TO TAKE THE CHANNEL ONLINE.


<br/><br/>
Regards,<br/>
Sony Saha,<br/>
Assistant Manager – Business Support,<br/>
Land-line :+9180-49698956<br/>
<br/>
";

$config['single_notice_channel_email_subject'] = "Market Cancel Request rejected";
$config['single_notice_channel_email_body'] = "
For Your Kind Information,<br/>

HANSA RESEARCH has commenced third party audit and validation of SureWaves Real Time Telecast Logs. With this, 
another Industry requirement to create Cable TV advertising as an effective platform for National Advertisers has 
been met by SureWaves. We expect this to have a positive result on overall revenues.

YOUR CHANNEL NEEDS TO BE ONLINE FOR PURPOSE OF AUDIT. %CHANNELS%, IS CURRENTLY OFFLINE AND HENCE NO RELEASE ORDER 
IS BEING GENERATED FOR THE SAME. CONTACT SUREWAVES STAFF OR UNDERSIGNED IMMEDIATELY TO TAKE THE CHANNEL ONLINE.


<br/><br/>
Regards,<br/>
Sony Saha,<br/>
Assistant Manager – Business Support,<br/>
Land-line :+9180-49698956<br/>
<br/>
";

//Intimation from surewaves for All Notice channel of an Enterprise
$config['all_channels_notice_ro_body'] = "
For your kind information : <br/>
HANSA RESEARCH has commenced third party audit and validation of SureWaves Real Time Telecast Logs. With this, 
another Industry requirement to create Cable TV advertising as an effective platform for National Advertisers has 
been met by SureWaves. We expect this to have a positive result on overall revenues.<br/>

SUREWAVES SYSTEM NEEDS TO BE ONLINE FOR PURPOSE OF AUDIT. THIS RO HAS BEEN GENERATED EVEN 
THOUGH YOUR %CHANNELS% ARE CURRENTLY OFFLINE. KEEPING WITH THE INDUSTRY REQUIREMENTS  FOR 
THIRD PARTY AUDIT RELEASE ORDERS WILL NOT BE GENERATED FOR OFFLINE CHANNEL/S IN THE COMING MONTH. CONTACT 
SUREWAVES STAFF OR UNDERSIGNED IMMEDIATELY TO TAKE SUREWAVES SYSTEM ONLINE.


";
$config['single_channel_notice_ro_body'] = "
For your kind information : <br/>
HANSA RESEARCH has commenced third party audit and validation of SureWaves Real Time Telecast Logs. With this, 
another Industry requirement to create Cable TV advertising as an effective platform for National Advertisers has 
been met by SureWaves. We expect this to have a positive result on overall revenues.<br/>

SUREWAVES SYSTEM NEEDS TO BE ONLINE FOR PURPOSE OF AUDIT. THIS RO HAS BEEN GENERATED EVEN 
THOUGH YOUR %CHANNELS% ARE CURRENTLY OFFLINE. KEEPING WITH THE INDUSTRY REQUIREMENTS  FOR 
THIRD PARTY AUDIT RELEASE ORDERS WILL NOT BE GENERATED FOR OFFLINE CHANNEL/S IN THE COMING MONTH. CONTACT 
SUREWAVES STAFF OR UNDERSIGNED IMMEDIATELY TO TAKE YOUR CHANNEL/S ONLINE.


";

//campaign performance report to user by email
$config['campaign_performance_report_email_subject'] = "Campaign Performance Report for month %MONTH%";
$config['campaign_performance_report_email_body'] = "
<br/>
Please find Campaign Performance Report for month %MONTH% <br/>
<br/><br/>
Regards,<br/>
SureWaves Team
";

//network remittance report to user by email
$config['network_remittance_report_email_subject'] = "Network Remittance Report for period '%START_DATE%' to '%END_DATE%'";
$config['network_remittance_report_email_body'] = "
<br/>
Please find Network Remittance Report for period '%START_DATE%' to '%END_DATE%' <br/>
<br/><br/>
Regards,<br/>
SureWaves Team
";
$config['mail_from_accounts_email_subject'] = "%SUBJECT%";
$config['mail_from_accounts_email_body'] = '<table cellpadding="0" cellspacing="0" align="center" width="620;" bgcolor="#292929" style="color:#e7e5e6;font-family:\'tahoma\'">
    <tbody>
    <tr>
    <td style="padding-top:4%;">
    <img style="padding-top:1%;padding-bottom:1%;" src="%HEADER%"/>
    </td>
    </tr>
    <!--  <tr>
 <td style="padding-top:4%;padding-left:31%;padding-right:5%;color:#fafafa">
 <label style="font-size: 15px;color: 3333FF;"> Please ignore if already submitted</label>
 </td>
 </tr>-->
	<tr>
    <td style="padding-top:4%;padding-left:5%;padding-right:5%;color:#FAFAFA">
    <label style="font-size:18px;"> Dear partner,</label>
    </td>
    </tr>
    <tr>
    <!-- <td style="padding-top:4%;color:#E6E6E6;padding-left:5%;padding-right:5%;font-size:14px;">
     Kindly submit all the pending invoices for financial year 2014-15 by 15th of April 2015 as we will be closing our books for financial year. Post this date we will not able to consider any invoices.
        Treat this important and send the invoices immediately to accounts@surewaves.com and original copy to below mentioned address.
    </td>-->
	<td style="padding-top:4%;color:#E6E6E6;padding-left:5%;padding-right:5%;font-size:14px;">
	As you all are aware that with effect from 1st June 2015 Service Tax rate has been enhanced to 14%. We hereby advised to send your invoices (with 12.36% service tax, if applicable) for activity done till 31st May 2015, on or before 5th June 2015. Invoice date has to be 31st May 2015 or earlier.<br/><br/>
	You can send the invoice to accounts@surewaves.com and original copy courier to below mentioned address:
	</td>
    </tr>

    <td style="padding-top:5%;color:#E6E6E6;padding-left:5%;padding-right:5%;font-size:14px;">
        Attn: Mr. Sathish <br/>
        SureWaves MediaTech Private Limited,<br/>
        3rd Floor, Ashok Chambers, 6th Cross, Koramangala,<br/>
        Srinivagulu, Near Ejipura Junction,<br/>
        25 Intermediate Ring Road,<br/>
        Bangalore &#45; 560 047<br/>
        Phone: +91 80 4969 8900

     </td>
    </tr>
    <tr>
    <td style="padding-top:5%;color:#E6E6E6;padding-left:5%;padding-right:5%;font-size:14px;">
        Warm Regards, <br/>
        Surewaves team.
    </td>
    </tr>
    <tr>
    <td style="padding-top:5%;">
    <img style="padding-top:1%;padding-bottom:1%;" src="%FOTTER%"/>

    </td>
    </tr>
    </tbody>
    </table> ';

//network report to user by email
$config['network_report_month_wise_email_subject'] = "Network Report for period '%START_DATE%' to '%END_DATE%'";
$config['network_report_month_wise_email_body'] = "
<br/>
Please find Network Report for period '%START_DATE%' to '%END_DATE%' <br/>
<br/><br/>
Regards,<br/>
SureWaves Team
";

$config['ro_progress_order_booked_email_subject'] = '%BOOKED%';
$config['ro_progress_order_booked_email_body'] = '<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#fff">
        <tbody><tr>
            <td>
                <table width="600" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center">
                    <tbody><tr>
                        <td>
                            <table width="600"  bgcolor="#000" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" style=" background: #25292a url(\'%HEAD_TEAXTURE%\') no-repeat; border-left: 1px solid #ddd; border-right: 1px solid #ddd;">
                                <tbody>
                                    <tr>
                                        <td style="font-family: \'Open Sans\', sans-serif; font-size: 14px; color: #304856; text-align:left; line-height: 15px;padding: 20px 20px 10px 20px;" width="600">

                                            <img height="70px" src="%LOGO%" class="logo">

                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" style=" border-left: 1px solid #ddd; border-right: 1px solid #ddd;background: linear-gradient(to bottom,  rgba(77,184,176,1) 0%,rgba(66,115,120,1) 100%);">
                                <tbody>
                                    <tr>
                                        <td style="height:70px;font-family: \'Open Sans\', sans-serif; font-size: 14px; color: #304856; text-align:left; line-height: 15px;padding: 20px 20px 10px 20px;" width="600">
                                            <h1 style="margin-top: 0px;font-weight: 600;font-family: \'Open Sans\', sans-serif;color: #fff;font-size: 22px;">%GREETINGS_HEADING%</h1>
                                           <!-- <p style="margin-bottom: 20px; width: 100%;color: #fff;font-family: \'Open Sans\';font-size: 13px;font-weight: 300;">%GREETINGS_SUB_HEADING%</p>
                                        --></td>
                                    </tr>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#e8e9e9">
                                <tbody><tr>
                                    <td width="5%">&nbsp;</td>
                                    <td width="90%" align="left" valign="top">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tbody><tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="left" valign="top">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tbody><tr>
                                                            <td>
                                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                    <tbody><tr>
                                                                        <td height="35" align="left" valign="middle" style="">
                                                                            <font style="font-family: Verdana, Geneva, sans-serif; color:#000000; font-size:13px; line-height:21px">
                                                                                Dear User,
                                                                                <p >
                                                                                %GREETINGS_HEADING_NEXT_LINE%
                                                                                </p>
                                                                            </font>
                                                                        </td>
                                                                    </tr>

                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                    </tbody></table>
                                                </td>
                                            </tr>

                                            <tr>
                                                                        <td align="" valign="top">
                                                                        <font style="font-family: Verdana, Geneva, sans-serif; color:#000000; font-size:13px; line-height:21px">
                                                                             %BODY_HEADER%<br/></font>

                                                                                    <font style="font-family:Verdana,Geneva,sans-serif;color:#000000;font-size:13px;/* padding-bottom: 21px; */line-height: 21px;/* margin-bottom: 14px; */">
                                                                                  <b>Client Name:</b> %CLIENT_NAME%
                                                                                    </font>
                                                                                    <br/>
                                                                                    <font style="font-family:Verdana,Geneva,sans-serif;color:#000000;font-size:13px;/* padding-bottom: 21px; */line-height: 21px;/* margin-bottom: 14px; */">
                                                                                  <b>Brand Name:</b> %BRAND_NAME%
                                                                                    </font>
                                                                        %DATA_TABLE%
                                                                    </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>&nbsp;</td>
                                                                    </tr>
                                                                    <tr>
                                                                <td>
                                                                  <!-- Contact Info Line-->
                                                                    <table bgcolor="#00000" cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
                                                                      <tbody>
                                                                        <tr>
                                                                          <td width="600" height="1">

                                                                           </td>
                                                                           </tr>
                                                                      </tbody>
                                                                    </table>
                                                                  <!-- Contact Info Line -->
                                                                 </td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                              <td>
                                                 <!-- Contact Info Table-->
 <table align="center" cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
      <tbody>
        <tr>
          <td width="600">
                 <table cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
                   <tbody>
                      <tr>
                      <td width="60" height="60" align="center" style="padding:10px 5px 10px 45px">
                  <img src="%PHONE%" alt="" border="0" width="65" style="border-radius:50%;display:inline-block;border:none;outline:none;text-decoration:none" class="CToWUd">
               </td>
                      <td style="font-family:\'Open Sans\',sans-serif;font-size:14px;color:#333333;width:100%;line-height:10px;padding-left:17px">
                  <h2 style="font-size:15px;">%CONTACT%</h2>
                   <p style="font-size:13px;padding-bottom:5px">%QUERY_TEXT%</p>
               </td>
                          </tr>
                      </tbody>
                        </table>
           </td>
           </tr>

 <tr>
          <td width="600">
                 <table cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
                   <tbody>
                      <tr>
                      <td width="60" height="60" align="center" style="padding:10px 5px 10px 45px">
                  <img src="%MAIL%" alt="" border="0" width="63" style="display:inline-block;border:none;outline:none;text-decoration:none">
               </td>
                      <td style="font-family:\'Open Sans\',sans-serif;font-size:14px;color:#333333;width:100%;line-height:10px;padding-left:17px">
                  <h2 style="font-size:15px;padding-top:15px">Next step</h2>
                   <p style="font-size:13px;padding-bottom:5px">%NEXT_STEP_TEXT%</p>
               </td>
                          </tr>
                      </tbody>
                        </table>
           </td>
           </tr>
      </tbody>
    </table>
 <!-- Contact Info Table-->
                    </td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>

                                        </tbody></table>
                                    </td>
                                    <td width="5%">&nbsp;</td>
                                </tr>
                            </tbody></table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table bgcolor="#e37272" cellpadding="0" cellspacing="0" border="0" style=" border-radius: 0 0 4px 4px;">
                                        <tbody>
                                            <!-- Spacing -->

                                            <tr>
                                                <td width="600" height="20"></td>
                                            </tr>
                                            <!-- Spacing -->

                                            <tr>
                                                <td align="left" valign="middle" style="font-family: \'Open Sans\', sans-serif; font-size: 13px;color: #fff;padding-left: 10px;font-weight: 300;">
    &#169; Copyright <a href="http://www.surewaves.com/" style="color: #fff;font-weight: 600;"> SureWaves</a>
                                                </td>
                                            </tr>
                                            <!-- Spacing -->

                                            <tr>
                                                <td width="100%" height="20" style="border-bottom: 4px solid #887272;"></td>
                                            </tr>
                                            <!-- Spacing -->
                                        </tbody>
                                    </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>

                </tbody></table>
            </td>
        </tr>
    </tbody></table> ';

$config['ro_progress_order_scheduled_email_subject'] = '%SCHEDULED%';
$config['ro_progress_order_scheduled_email_body'] = ' <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#fff">
        <tbody><tr>
            <td>
                <table width="600" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center">
                    <tbody><tr>
                        <td>
                            <table width="600" bgcolor="#000" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" style="background: #25292a url(\'%HEAD_TEAXTURE%\') no-repeat; border-left: 1px solid #ddd; border-right: 1px solid #ddd;">
                                <tbody>
                                    <tr>
                                        <td style="font-family: \'Open Sans\', sans-serif; font-size: 14px; color: #304856; text-align:left; line-height: 15px;padding: 20px 20px 10px 20px;" width="600">

                                            <img height="70px" src="%LOGO%" class="logo">

                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" style=" border-left: 1px solid #ddd; border-right: 1px solid #ddd;background: linear-gradient(to bottom,  rgba(77,184,176,1) 0%,rgba(66,115,120,1) 100%);">
                                <tbody>
                                    <tr>
                                        <td style="height:70px;font-family: \'Open Sans\', sans-serif; font-size: 14px; color: #304856; text-align:left; line-height: 15px;padding: 20px 20px 10px 20px;" width="600">
                                            <h1 style="margin-top: 0px;font-weight: 600;font-family: \'Open Sans\', sans-serif;color: #fff;font-size: 22px;">%GREETINGS_HEADING%</h1>
                                            <!--<p style="margin-bottom: 20px; width: 100%;color: #fff;font-family: \'Open Sans\';font-size: 13px;font-weight: 300;">Receipt on your booking Confirmation</p>
                                        --></td>
                                    </tr>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#e8e9e9">
                                <tbody><tr>
                                    <td width="5%">&nbsp;</td>
                                    <td width="90%" align="left" valign="top">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tbody><tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="left" valign="top">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tbody><tr>
                                                            <td>
                                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                    <tbody><tr>
                                                                        <td height="35" align="left" valign="middle" style="">
                                                                            <font style="font-family: Verdana, Geneva, sans-serif; color:#000000; font-size:13px; line-height:21px">
                                                                                Dear User,<p>
                                                                               %GREETINGS_HEADING_NEXT_LINE%
                                                                                    </p>
                                                                            </font>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                     <td>
                                                                     <font style="font-family:Verdana,Geneva,sans-serif;color:#000000;font-size:13px;/* padding-bottom: 21px; */line-height: 21px;/* margin-bottom: 14px; */">
                                                                      <b>Client Name:</b> %CLIENT_NAME%
                                                                        </font> 
                                                                     </td>
                                                                     </tr>
                                                                     
                                                                     <tr>
                                                                     <td>
                                                                     <font style="font-family:Verdana,Geneva,sans-serif;color:#000000;font-size:13px;/* padding-bottom: 21px; */line-height: 21px;/* margin-bottom: 14px; */">
                                                                      <b>Brand Name:</b> %BRAND_NAME%
                                                                        </font>
                                                                      </td>
                                                                     </tr>
                                                                     
                                                                    <tr>
                                                                        <td>&nbsp;</td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                    </tbody></table>
                                                </td>
                                            </tr>

                                            <tr>
                                                                        <td align="" valign="top">
                                                                       <!-- Start Inner table -->
                                                                       %DATA_TABLE%
																		<!-- End Inner table -->
                                                                        </td>
                                                                    </tr><tr>
                                                                        <td>&nbsp;</td>
                                                                    </tr><tr>
 <td>
  <!-- Contact Info Line-->
    <table bgcolor="#00000" cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
      <tbody>
        <tr>
          <td width="600" height="1">

           </td>
           </tr>
      </tbody>
    </table>
  <!-- Contact Info Line -->
 </td>
 </tr>
 <tr>
 <td>&nbsp;</td>
 </tr>
 <tr>
                                              <td>
                                                <table align="center" cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
      <tbody>
        <tr>
          <td width="600">
                 <table cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
                   <tbody>
                      <tr>
                      <td width="60" height="60" align="center" style="padding:10px 5px 10px 45px">
                  <img src="%PHONE%" alt="" border="0" width="65" style="border-radius:50%;display:inline-block;border:none;outline:none;text-decoration:none" class="CToWUd">
               </td>
                      <td style="font-family:\'Open Sans\',sans-serif;font-size:14px;color:#333333;width:100%;line-height:10px;padding-left:17px">
                  <h2 style="font-size:15px;">%CONTACT%</h2>
                   <p style="font-size:13px;padding-bottom:5px">%QUERY_TEXT%</p>
               </td>
                          </tr>
                      </tbody>
                        </table>
           </td>
           </tr>

 <tr>
          <td width="600">
                 <table cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
                   <tbody>
                      <tr>
                      <td width="60" height="60" align="center" style="padding:10px 5px 10px 45px">
                  <img src="%MAIL%" alt="" border="0" width="63" style="display:inline-block;border:none;outline:none;text-decoration:none">
               </td>
                      <td style="font-family:\'Open Sans\',sans-serif;font-size:14px;color:#333333;width:100%;line-height:10px;padding-left:17px">
                  <h2 style="font-size:15px;padding-top:15px">Next step</h2>
                   <p style="font-size:13px;padding-bottom:5px">%NEXT_STEP_TEXT%</p>
               </td>
                          </tr>
                      </tbody>
                        </table>
           </td>
           </tr>
      </tbody>
    </table>
 <!-- Contact Info Table-->
                                              </td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>

                                        </tbody></table>
                                    </td>
                                    <td width="5%">&nbsp;</td>
                                </tr>
                            </tbody></table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table bgcolor="#e37272" cellpadding="0" cellspacing="0" border="0" style=" border-radius: 0 0 4px 4px;">
                                        <tbody>
                                            <!-- Spacing -->

                                            <tr>
                                                <td width="600" height="20"></td>
                                            </tr>
                                            <!-- Spacing -->

                                            <tr>
                                                <td align="left" valign="middle" style="font-family: \'Open Sans\', sans-serif; font-size: 13px;color: #fff;padding-left: 10px;font-weight: 300;">
    &#169; Copyright <a href="http://www.surewaves.com/" style="color: #fff;font-weight: 600;"> SureWaves</a>
                                                </td>
                                            </tr>
                                            <!-- Spacing -->

                                            <tr>
                                                <td width="100%" height="20" style="border-bottom: 4px solid #887272;"></td>
                                            </tr>
                                            <!-- Spacing -->
                                        </tbody>
                                    </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>

                </tbody></table>
            </td>
        </tr>
    </tbody></table>
';


$config['ro_progress_start_email_subject'] = "%START%";
$config['ro_progress_start_email_body'] = '<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#fff">
        <tbody><tr>
            <td>
                <table width="600" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center">
                    <tbody><tr>
                        <td>
                            <table width="600" bgcolor="#000" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" style=" border-left: 1px solid #ddd; border-right: 1px solid #ddd;background: #25292a url(\'%HEAD_TEAXTURE%\') no-repeat;">
                                <tbody>
                                    <tr>
                                        <td style="font-family: \'Open Sans\', sans-serif; font-size: 14px; color: #304856; text-align:left; line-height: 15px;padding: 20px 20px 10px 20px;" width="600">

                                            <img height="70px" src="%LOGO%" class="logo">

                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" style=" border-left: 1px solid #ddd; border-right: 1px solid #ddd;background: linear-gradient(to bottom,  rgba(77,184,176,1) 0%,rgba(66,115,120,1) 100%);">
                                <tbody>
                                    <tr>
                                        <td style="height:70px;font-family: \'Open Sans\', sans-serif; font-size: 14px; color: #304856; text-align:left; line-height: 15px;padding: 20px 20px 10px 20px;" width="600">
                                            <h1 style="margin-top: 0px;font-weight: 600;font-family: \'Open Sans\', sans-serif;color: #fff;font-size: 22px;">%GREETINGS_HEADING%</h1>
                                            <!--<p style="margin-bottom: 20px; width: 100%;color: #fff;font-family: \'Open Sans\';font-size: 13px;font-weight: 300;">Receipt on your booking Confirmation</p>
                                        --></td>
                                    </tr>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#e8e9e9">
                                <tbody><tr>
                                    <td width="5%">&nbsp;</td>
                                    <td width="90%" align="left" valign="top">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tbody><tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="left" valign="top">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tbody><tr>
                                                            <td>
                                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                    <tbody><tr>
                                                                        <td height="35" align="left" valign="middle" style="">
                                                                            <font style="font-family: Verdana, Geneva, sans-serif; color:#000000; font-size:13px; line-height:21px">
                                                                                Dear User,
                                                                                <p>
                                                                                %GREETINGS_HEADING_NEXT_LINE%
                                                                            </p></font>
                                                                        </td>
                                                                    </tr>
                                                                            <tr>
                                                                             <td>
                                                                             <font style="font-family:Verdana,Geneva,sans-serif;color:#000000;font-size:13px;/* padding-bottom: 21px; */line-height: 21px;/* margin-bottom: 14px; */">
                                                                              <b>Client Name:</b> %CLIENT_NAME%
                                                                                </font>
                                                                             </td>
                                                                             </tr>
                                                                             
                                                                             <tr>
                                                                             <td>
                                                                             <font style="font-family:Verdana,Geneva,sans-serif;color:#000000;font-size:13px;/* padding-bottom: 21px; */line-height: 21px;/* margin-bottom: 14px; */">
                                                                              <b>Brand Name:</b> %BRAND_NAME%
                                                                                </font>
                                                                             </td>
                                                                             </tr>

                                                                    <tr>
                                                                        <td>&nbsp;</td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                    </tbody></table>
                                                </td>
                                            </tr>

                                            <tr>
                                                                        <td align="" valign="top">
                                                                      %DATA_TABLE%
                                                                        </td>
                                                                    </tr><tr>
                                                                        <td>&nbsp;</td>
                                                                    </tr>
																	<!-- Info box -->

																<tr>
																	   <td style="font-family: Verdana,Geneva,sans-serif;color:#000000;font-size: 13px;">
																		<!--  <table width="100%" style="width: 100%;margin: auto auto 32px;border:1px solid #d5d5d5;color:#304856">
																			 <tbody style="font-size:14px;background:rgba(255,255,255,0.69)">
																				<tr align="" style="border-top:1px dotted #ddd;border-bottom:1px dotted #cbc9c9;height:35px;font-weight:600;color:#000">
																				   <td>
																					  <table width="100%" style="width:100%;padding: 12px 15px 12px 19px;color:#304856;border-width:0px" border="0">
																						 <tbody>
																							<tr>
																							   <td>
																								 %INNER_BODY_TEXT%
																							   </td>
																							</tr>
																						 </tbody>
																					  </table>
																				   </td>
																				</tr>
																			 </tbody>
																		  </table>
																		 Info.
																		  <hr style="margin-top: 0px;">
                                                                         --><div style="padding: 5px 10px 35px 10px;font-family: Verdana, Geneva, sans-serif; color:#000000; font-size:13px;">
                                                                         %INNER_BODY_TEXT%
                                                                         </div>
                                                                        </td>
																	</tr>
																	<!--  Info box end-->
 <!-- Contact Info Line-->
                                                                    <table bgcolor="#00000" cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
                                                                      <tbody>
                                                                        <tr>
                                                                          <td width="600" height="1">

                                                                           </td>
                                                                           </tr>
                                                                      </tbody>
                                                                    </table>
                                                                  <!-- Contact Info Line -->
																<tr>
 <td>&nbsp;</td>
 </tr>	<tr>


                                              <td>
                                               <!-- Contact Info Table-->
 <table align="center" cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
      <tbody>
        <tr>
          <td width="600">
                 <table cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
                   <tbody>
                      <tr>
                      <td width="60" height="60" align="center" style="padding:10px 5px 10px 45px">
                  <img src="%PHONE%" alt="" border="0" width="65" style="border-radius:50%;display:inline-block;border:none;outline:none;text-decoration:none" class="CToWUd">
               </td>
                      <td style="font-family:\'Open Sans\',sans-serif;font-size:14px;color:#333333;width:100%;line-height:10px;padding-left:17px">
                  <h2 style="font-size:15px;">%CONTACT%</h2>
                   <p style="font-size:13px;padding-bottom:5px">%QUERY_TEXT%</p>
               </td>
                          </tr>
                      </tbody>
                        </table>
           </td>
           </tr>

 <tr>
          <td width="600">
                 <table cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
                   <tbody>
                      <tr>
                      <td width="60" height="60" align="center" style="padding:10px 5px 10px 45px">
                  <img src="%MAIL%" alt="" border="0" width="63" style="display:inline-block;border:none;outline:none;text-decoration:none">
               </td>
                      <td style="font-family:\'Open Sans\',sans-serif;font-size:14px;color:#333333;width:100%;line-height:10px;padding-left:17px">
                  <h2 style="font-size:15px;padding-top:15px">Next step</h2>
                   <p style="font-size:13px;padding-bottom:5px">%NEXT_STEP_TEXT%</p>
               </td>
                          </tr>
                      </tbody>
                        </table>
           </td>
           </tr>
      </tbody>
    </table>
 <!-- Contact Info Table-->
                                              </td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>

                                        </tbody></table>
                                    </td>
                                    <td width="5%">&nbsp;</td>
                                </tr>
                            </tbody></table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table bgcolor="#e37272" cellpadding="0" cellspacing="0" border="0" style=" border-radius: 0 0 4px 4px;">
                                        <tbody>
                                            <!-- Spacing -->

                                            <tr>
                                                <td width="600" height="20"></td>
                                            </tr>
                                            <!-- Spacing -->

                                            <tr>
                                                <td align="left" valign="middle" style="font-family: \'Open Sans\', sans-serif; font-size: 13px;color: #fff;padding-left: 10px;font-weight: 300;">
                                                    &copy; Copyright <a href="http://www.surewaves.com/" style="color: #fff;font-weight: 600;"> SureWaves</a>
                                                </td>
                                            </tr>
                                            <!-- Spacing -->

                                            <tr>
                                                <td width="100%" height="20" style="border-bottom: 4px solid #887272;"></td>
                                            </tr>
                                            <!-- Spacing -->
                                        </tbody>
                                    </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>

                </tbody></table>
            </td>
        </tr>
    </tbody></table>';


$config['ro_progress_complete_email_subject'] = "%COMPLETE%";
$config['ro_progress_complete_email_body'] = '
    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#fff">
        <tbody><tr>
            <td>
                <table width="600" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center">
                    <tbody><tr>
                        <td>
                            <table width="600" bgcolor="#000" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" style=" border-left: 1px solid #ddd; border-right: 1px solid #ddd;background: #25292a url(\'%HEAD_TEAXTURE%\') no-repeat;">
                                <tbody>
                                    <tr>
                                        <td style="font-family: \'Open Sans\', sans-serif; font-size: 14px; color: #304856; text-align:left; line-height: 15px;padding: 20px 20px 10px 20px;" width="600">

                                            <img height="70px" src="%LOGO%" >

                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" style=" border-left: 1px solid #ddd; border-right: 1px solid #ddd;background: linear-gradient(to bottom,  rgba(77,184,176,1) 0%,rgba(66,115,120,1) 100%);">
                                <tbody>
                                    <tr>
                                        <td style="height:70px;font-family: \'Open Sans\', sans-serif; font-size: 14px; color: #304856; text-align:left; line-height: 15px;padding: 20px 20px 10px 20px;" width="600">
                                            <h1 style="margin-top: 0px;font-weight: 600;font-family: \'Open Sans\', sans-serif;color: #fff;font-size: 22px;">
											%GREETINGS_HEADING%
											</h1>
                                            <!--<p style="margin-bottom: 20px; width: 100%;color: #fff;font-family: \'Open Sans\';font-size: 13px;font-weight: 300;">Receipt on your booking Confirmation</p>
                                        --></td>
                                    </tr>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#e8e9e9">
                                <tbody><tr>
                                    <td width="5%">&nbsp;</td>
                                    <td width="90%" align="left" valign="top">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tbody><tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="left" valign="top">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tbody><tr>
                                                            <td>
                                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                    <tbody><tr>
                                                                        <td height="35" align="left" valign="middle" style="">
                                                                            <font style="font-family: Verdana, Geneva, sans-serif; color:#000000; font-size:13px; line-height:21px">
                                                                                Dear User,
                                                                               <p>%GREETINGS_HEADING_NEXT_LINE%
																				</p>
																		    </font>
                                                                        </td>
                                                                    </tr>

<tr>
 <td>
 <font style="font-family:Verdana,Geneva,sans-serif;color:#000000;font-size:13px;/* padding-bottom: 21px; */line-height: 21px;/* margin-bottom: 14px; */">
  <b>Client Name:</b> %CLIENT_NAME%
	</font>
 </td>
 </tr>
 
<tr>
 <td>
 <font style="font-family:Verdana,Geneva,sans-serif;color:#000000;font-size:13px;/* padding-bottom: 21px; */line-height: 21px;/* margin-bottom: 14px; */">
  <b>Brand Name:</b> %BRAND_NAME%
	</font>
 </td>
 </tr>
                                                                    <tr>
                                                                        <td>&nbsp;</td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                    </tbody></table>
                                                </td>
                                            </tr>
																<tr>
																	    <td style="font-family: Verdana,Geneva,sans-serif;color:#000000;font-size: 13px;">
																		%DATA_TABLE%
																		</td>
																	</tr>
																	<!--  Info box end--><tr>
 <td>&nbsp;</td></tr>
<!-- Contact Info Line-->
                                                                    <table bgcolor="#00000" cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
                                                                      <tbody>
                                                                        <tr>
                                                                          <td width="600" height="1">

                                                                           </td>
                                                                           </tr>
                                                                      </tbody>
                                                                    </table>
                                                                  <!-- Contact Info Line -->
																<tr>
 <td>&nbsp;</td></tr>
																	<tr>


                                              <td>
                                                <!-- Contact Info Table-->
 <table align="center" cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
      <tbody>
        <tr>
          <td width="600">
                 <table cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
                   <tbody>
                      <tr>
                      <td width="60" height="60" align="center" style="padding:10px 5px 10px 45px">
                  <img src="%PHONE%" alt="" border="0" width="65" style="border-radius:50%;display:inline-block;border:none;outline:none;text-decoration:none" class="CToWUd">
               </td>
                      <td style="font-family:\'Open Sans\',sans-serif;font-size:14px;color:#333333;width:100%;line-height:10px;padding-left:17px">
                  <h2 style="font-size:15px;">%CONTACT%</h2>
                   <p style="font-size:13px;padding-bottom:5px">%QUERY_TEXT%</p>
               </td>
                          </tr>
                      </tbody>
                        </table>
           </td>
           </tr>

 <tr>
          <td width="600">
                 <table cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
                   <tbody>
                      <tr>
                      <td width="60" height="60" align="center" style="padding:10px 5px 10px 45px">
                  <img src="%MAIL%" alt="" border="0" width="63" style="display:inline-block;border:none;outline:none;text-decoration:none">
               </td>
                      <td style="font-family:\'Open Sans\',sans-serif;font-size:14px;color:#333333;width:100%;line-height:10px;padding-left:17px">
                  <h2 style="font-size:15px;padding-top:15px">Next step</h2>
                   <p style="font-size:13px;padding-bottom:5px">%NEXT_STEP_TEXT%</p>
               </td>
                          </tr>
                      </tbody>
                        </table>
           </td>
           </tr>
      </tbody>
    </table>
 <!-- Contact Info Table-->
                                              </td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>

                                        </tbody></table>
                                    </td>
                                    <td width="5%">&nbsp;</td>
                                </tr>
                            </tbody></table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table bgcolor="#e37272" cellpadding="0" cellspacing="0" border="0" style=" border-radius: 0 0 4px 4px;">
                                        <tbody>
                                            <!-- Spacing -->

                                            <tr>
                                                <td width="600" height="20"></td>
                                            </tr>
                                            <!-- Spacing -->

                                            <tr>
                                                <td align="left" valign="middle" style="font-family: \'Open Sans\', sans-serif; font-size: 13px;color: #fff;padding-left: 10px;font-weight: 300;">
                                                   &copy; Copyright <a href="http://www.surewaves.com/" style="color: #fff;font-weight: 600;"> SureWaves</a>
                                                </td>
                                            </tr>
                                            <!-- Spacing -->

                                            <tr>
                                                <td width="100%" height="20" style="border-bottom: 4px solid #887272;"></td>
                                            </tr>
                                            <!-- Spacing -->
                                        </tbody>
                                    </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>

                </tbody></table>
            </td>
        </tr>
    </tbody></table>
';

$config['ro_progress_pre_closure_email_subject'] = "%PRE_CLOSURE%";
$config['ro_progress_pre_closure_email_body'] = '<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#fff">
        <tbody><tr>
            <td>
                <table width="600" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center">
                    <tbody><tr>
                        <td>
                            <table width="600" bgcolor="#000" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" style=" border-left: 1px solid #ddd; border-right: 1px solid #ddd;background: #25292a url(\'%HEAD_TEAXTURE%\') no-repeat;">
                                <tbody>
                                    <tr>
                                        <td style="font-family: \'Open Sans\', sans-serif; font-size: 14px; color: #304856; text-align:left; line-height: 15px;padding: 20px 20px 10px 20px;" width="600">

                                            <img height="70px" src="%LOGO%" class="logo">

                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" style=" border-left: 1px solid #ddd; border-right: 1px solid #ddd;background: linear-gradient(to bottom,  rgba(77,184,176,1) 0%,rgba(66,115,120,1) 100%);">
                                <tbody>
                                    <tr>
                                        <td style="height:70px;font-family: \'Open Sans\', sans-serif; font-size: 14px; color: #304856; text-align:left; line-height: 15px;padding: 20px 20px 10px 20px;" width="600">
                                            <h1 style="margin-top:0px;font-weight: 600;font-family: \'Open Sans\', sans-serif;color: #fff;font-size: 22px;">%GREETINGS_HEADING%
											</h1>

                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#e8e9e9">
                                <tbody><tr>
                                    <td width="5%">&nbsp;</td>
                                    <td width="90%" align="left" valign="top">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tbody><tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="left" valign="top">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tbody><tr>
                                                            <td>
                                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                    <tbody><tr>
                                                                        <td height="35" align="left" valign="middle" style="">
                                                                            <font style="font-family: Verdana, Geneva, sans-serif; color:#000000; font-size:13px; line-height:21px">
                                                                                Dear User,
                                                                               <p>%GREETINGS_HEADING_NEXT_LINE%
																					</p>
                                                                            </font>
                                                                        </td>
                                                                    </tr>
<tr>
 <td>
 <font style="font-family:Verdana,Geneva,sans-serif;color:#000000;font-size:13px;/* padding-bottom: 21px; */line-height: 21px;/* margin-bottom: 14px; */">
  <b>Client Name:</b> %CLIENT_NAME%
	</font>
 </td>
 </tr>
 
<tr>
 <td>
 <font style="font-family:Verdana,Geneva,sans-serif;color:#000000;font-size:13px;/* padding-bottom: 21px; */line-height: 21px;/* margin-bottom: 14px; */">
  <b>Brand Name:</b> %BRAND_NAME%
	</font>
 </td>
 </tr>

                                                                    <tr>
                                                                        <td>&nbsp;</td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                    </tbody></table>
                                                </td>
                                            </tr>

                                           					<tr>
																	   <td>
																		 <table width="100%" style=";width: 100%;margin: auto auto 32px;border:1px solid #d5d5d5;color:#304856">
																			 <tbody style="font-size:14px;background:rgba(255,255,255,0.69)">
																				<tr align="" style="border-top:1px dotted #ddd;border-bottom:1px dotted #cbc9c9;height:35px;">
																				   <td>
																					  <table width="100%" style="width:100%;padding: 12px 11px 12px 19px;line-height: 22px;font-family: Verdana, Geneva, sans-serif; color:#000000; font-size:13px;border-width:0px" border="0">
																						 <tbody>
																							<tr>
																							   <td>
																								%INNER_BODY_TEXT%
																							   </td>
																							</tr>
																						 </tbody>
																					  </table>
																				   </td>
																				</tr>
																			 </tbody>
																		  </table>
																	</tr>
																	<!--  Info box end-->
<!-- Contact Info Line-->
                                                                    <table bgcolor="#00000" cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
                                                                      <tbody>
                                                                        <tr>
                                                                          <td width="600" height="1">

                                                                           </td>
                                                                           </tr>
                                                                      </tbody>
                                                                    </table>
                                                                  <!-- Contact Info Line -->
																<tr>
 <td>&nbsp;</td></tr>
																	<tr>


                                              <td>
                                               <!-- Contact Info Table-->
 <table align="center" cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
      <tbody>
        <tr>
          <td width="600">
                 <table cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
                   <tbody>
                      <tr>
                      <td width="60" height="60" align="center" style="padding:10px 5px 10px 45px">
                  <img src="%PHONE%" alt="" border="0" width="65" style="border-radius:50%;display:inline-block;border:none;outline:none;text-decoration:none" class="CToWUd">
               </td>
                      <td style="font-family:\'Open Sans\',sans-serif;font-size:14px;color:#333333;width:100%;line-height:10px;padding-left:17px">
                  <h2 style="font-size:15px;">%CONTACT%</h2>
                   <p style="font-size:13px;padding-bottom:5px">%QUERY_TEXT%</p>
               </td>
                          </tr>
                      </tbody>
                        </table>
           </td>
           </tr>

 <tr>
          <td width="600">
                 <table cellpadding="0" cellspacing="0" border="0" style="border-radius:0 0 4px 4px">
                   <tbody>
                      <tr>
                      <td width="60" height="60" align="center" style="padding:10px 5px 10px 45px">
                  <img src="%MAIL%" alt="" border="0" width="63" style="display:inline-block;border:none;outline:none;text-decoration:none">
               </td>
                      <td style="font-family:\'Open Sans\',sans-serif;font-size:14px;color:#333333;width:100%;line-height:10px;padding-left:17px">
                  <h2 style="font-size:15px;padding-top:15px">Next step</h2>
                   <p style="font-size:13px;padding-bottom:5px">%NEXT_STEP_TEXT%</p>
               </td>
                          </tr>
                      </tbody>
                        </table>
           </td>
           </tr>
      </tbody>
    </table>
 <!-- Contact Info Table-->
                                              </td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>

                                        </tbody></table>
                                    </td>
                                    <td width="5%">&nbsp;</td>
                                </tr>
                            </tbody></table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table bgcolor="#e37272" cellpadding="0" cellspacing="0" border="0" style=" border-radius: 0 0 4px 4px;">
                                        <tbody>
                                            <!-- Spacing -->

                                            <tr>
                                                <td width="600" height="20"></td>
                                            </tr>
                                            <!-- Spacing -->

                                            <tr>
                                                <td align="left" valign="middle" style="font-family: \'Open Sans\', sans-serif; font-size: 13px;color: #fff;padding-left: 10px;font-weight: 300;">
                                                   &copy; Copyright <a href="http://www.surewaves.com/" style="color: #fff;font-weight: 600;"> SureWaves</a>
                                                </td>
                                            </tr>
                                            <!-- Spacing -->

                                            <tr>
                                                <td width="100%" height="20" style="border-bottom: 4px solid #887272;"></td>
                                            </tr>
                                            <!-- Spacing -->
                                        </tbody>
                                    </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>

                </tbody></table>
            </td>
        </tr>
    </tbody></table>';

//Intimation Channel Performance Report
$config['channel_performance_weekly_email_subject'] = "Channel Performance Report (%DURATION%) For %TODAYS_DATE%";
$config['channel_performance_weekly_email_body'] = "
<br/>
<b>Summary Of Channels</b><br/>
%CHANNELS_SUMMARY%
<br/><br/>
<b>Online Channel Performance Summary</b> <br/>
%ONLINE_CHANNELS_SUMMARY%
<br/><br/>
<b>Online Channel Performance Report Greater Than 95%</b><br/>
%ONLINE_CHANNEL_DATA_GREATER_THAN_95%
<br/><br/>
<b>Online Channel Performance Report 85% - 95%</b><br/>
%ONLINE_CHANNEL_DATA_GREATER_THAN_85%
<br/><br/>
<b>Online Channel Performance Report 75% - 85%</b><br/>
%ONLINE_CHANNEL_DATA_GREATER_THAN_75%
<br/><br/>
<b>Online Channel Performance Report 60% - 75%</b><br/>
%ONLINE_CHANNEL_DATA_GREATER_THAN_60%
<br/><br/>
<b>Online Channel Performance Report 40% - 60%</b><br/>
%ONLINE_CHANNEL_DATA_GREATER_THAN_40%
<br/><br/>
<b>Online Channel Performance Report Less Than 40%</b><br/>
%ONLINE_CHANNEL_DATA_LESS_THAN_40%
<br/><br/>
<b>Offline Channel Performance Report</b><br/>
%OFFLINE_CHANNEL_DATA%
<br/><br/>
<b>Deployed But Not Pinging</b><br/>
%DEPLOYED_NOT_PINGING%
<br/><br/>
Regards,<br/>
SureWaves Team
";
$config['br_customer_email_subject'] = "Failed to get br customer details";
$config['br_customer_email_body'] = "Hi,
<br/><br/>The BR customer Api failed to give data. The error message is %ERROR% 
<br/><br/>";
$config['br_update_content_email_subject'] = "Failed to put br content details.";
$config['br_update_content_email_body'] = "Hi,
<br/><br/>The  BR Api failed Update content details.The error message is %ERROR%
<br/><br/>";
$config['br_ro_schdule_email_subject'] = "Failed to put br ro details";
$config['br_ro_schdule_email_body'] = "Hi,
<br/><br/>The  BR Api failed Update ro schedules.The error message is %ERROR%
<br/><br/>";

$config['channel_performance_monthly_email_subject'] = "Channel Performance Report (Monthly) For %TODAYS_DATE%";
$config['channel_performance_monthly_email_body'] = "
<br/>
<b>Summary Of Channels</b><br/>
%CHANNELS_SUMMARY%
<br/><br/>
<b>Online Channel Performance Summary</b> <br/>
%ONLINE_CHANNELS_SUMMARY%
<br/><br/>
<b>Online Channel Performance Report Greater Than 95%</b><br/>
%ONLINE_CHANNEL_DATA_GREATER_THAN_95%
<br/><br/>
<b>Online Channel Performance Report 85% - 95%</b><br/>
%ONLINE_CHANNEL_DATA_GREATER_THAN_85%
<br/><br/>
<b>Online Channel Performance Report 75% - 85%</b><br/>
%ONLINE_CHANNEL_DATA_GREATER_THAN_75%
<br/><br/>
<b>Online Channel Performance Report 60% - 75%</b><br/>
%ONLINE_CHANNEL_DATA_GREATER_THAN_60%
<br/><br/>
<b>Online Channel Performance Report 40% - 60%</b><br/>
%ONLINE_CHANNEL_DATA_GREATER_THAN_40%
<br/><br/>
<b>Online Channel Performance Report Less Than 40%</b><br/>
%ONLINE_CHANNEL_DATA_LESS_THAN_40%
<br/><br/>
<b>Offline Channel Performance Report</b><br/>
%OFFLINE_CHANNEL_DATA%
<br/><br/>
Regards,<br/>
SureWaves Team
";
?>
