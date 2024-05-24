<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//Admin section
$config['login_form'] = array(
    array
    (
        'key' => 'email',
        'value' => 'Email',
        'rule' => 'trim|required|valid_email|xss_clean'
    ),
    array
    (
        'key' => 'passwd',
        'value' => 'Password',
        'rule' => 'trim|required|callback_password_verification|xss_clean'
    )
);
$config['login_form_error_code_1'] = 'The email or password you entered is incorrect';

$config['add_user_form'] = array(
    array(
        'key' => 'user_email',
        'value' => 'User Email',
        'rule' => 'trim|required|valid_email|callback_duplicate_user_email_check'
    ),
    array(
        'key' => 'user_name',
        'value' => 'User Name',
        'rule' => 'trim|required|xss_clean'
    ),
    array(
        'key' => 'user_phone',
        'value' => 'Mobile',
        'rule' => 'trim|required|integer|min_length[10]|max_length[10]|xss_clean'
    ),


);
$config['add_ro_form'] = array(
    array(
        'key' => 'agency_name',
        'value' => 'Agency Name',
        'rule' => 'trim|required|alpha_numeric|xss_clean'
    ),
    array(
        'key' => 'client_name',
        'value' => 'Client Name',
        'rule' => 'trim|required|alpha_numeric|xss_clean'
    ),
    array(
        'key' => 'campaign_name',
        'value' => 'Campaign Name',
        'rule' => 'trim|required|xss_clean'
    ),
    array(
        'key' => 'brand_name',
        'value' => 'Brand Name',
        'rule' => 'trim|alpha_numeric|xss_clean'
    ),
    array(
        'key' => 'brand_owner',
        'value' => 'Brand Owner',
        'rule' => 'trim|alpha_numeric|xss_clean'
    ),
    array(
        'key' => 'product',
        'value' => 'Product',
        'rule' => 'trim|alpha_numeric|xss_clean'
    ),
    array(
        'key' => 'contact_email',
        'value' => 'Contact Email',
        'rule' => 'trim|valid_email|xss_clean'
    ),
    array(
        'key' => 'contact_phone',
        'value' => 'Mobile',
        'rule' => 'trim|integer|min_length[10]|max_length[10]|xss_clean'
    )
);

$config['add_price_form'] = array(
    array(
        'key' => 'amount',
        'value' => 'Amount',
        'rule' => 'trim|integer|xss_clean'
    ),
);
$config['approve_form'] = array(
    array(
        'key' => 'channel_avg_rate[][]',
        'value' => 'channel_avg_rate',
        'rule' => 'required|decimal|xss_clean'
    ),
    array(
        'key' => 'channel_amount[][]',
        'value' => 'channel_amount',
        'rule' => 'required|decimal|xss_clean'
    ),
    array(
        'key' => 'final_amount[]',
        'value' => 'final_amount',
        'rule' => 'required|decimal|xss_clean'
    ),
    array(
        'key' => 'network_share[]',
        'value' => 'network_share',
        'rule' => 'required|decimal|greater_than[-1]|less_than[101]|xss_clean'
    ),
);
$config['add_ro_amount_form'] = array(
    array(
        'key' => 'ro_amount',
        'value' => 'ro_amount',
        'rule' => 'required|is_natural|xss_clean'
    ),
    array(
        'key' => 'agency_amount',
        'value' => 'agency_amount',
        'rule' => 'required|is_natural|xss_clean'
    ),

);
$config['add_other_expenses_form'] = array(
    array(
        'key' => 'agency_rebate',
        'value' => 'agency_rebate',
        'rule' => 'required|is_natural|greater_than[-1]|less_than[101]|xss_clean'
    ),
    array(
        'key' => 'marketing_promotion_amount',
        'value' => 'marketing_promotion_amount',
        'rule' => 'required|is_natural|xss_clean'
    ),
    array(
        'key' => 'field_activation_amount',
        'value' => 'field_activation_amount',
        'rule' => 'required|is_natural|xss_clean'
    ),
    array(
        'key' => 'sales_commissions_amount',
        'value' => 'sales_commissions_amount',
        'rule' => 'required|is_natural|xss_clean'
    ),
    array(
        'key' => 'creative_services_amount',
        'value' => 'creative_services_amount',
        'rule' => 'required|is_natural|xss_clean'
    ),
    array(
        'key' => 'other_expenses_amount',
        'value' => 'other_expenses_amount',
        'rule' => 'required|is_natural|xss_clean'
    ),


);

$config['edit_user_form'] = array(
    array(
        'key' => 'user_name',
        'value' => 'User Name',
        'rule' => 'trim|required|xss_clean'
    ),
    array(
        'key' => 'user_phone',
        'value' => 'Mobile',
        'rule' => 'trim|required|integer|min_length[10]|max_length[10]|xss_clean'
    ),

);

$config['activate_advertiser_form'] = array(
    'Category' => array(
        'field' => 'act_category',
        'label' => 'Category',
        'rules' => 'trim|required|xss_clean'
    ),
    'Product' => array(
        'field' => 'act_product',
        'label' => 'Product',
        'rules' => 'trim|required|xss_clean'
    ),
    'Advertiser' => array(
        'field' => 'act_advertiser',
        'label' => 'Advertiser',
        'rules' => 'trim|required|xss_clean'
    )
);
$config['add_category_form'] = array(
    'Advertiser' => array(
        'field' => 'addNewCategory',
        'label' => 'New Category',
        'rules' => 'trim|required|alpha_numeric|xss_clean'
    )
);
$config['add_product_form'] = array(
    'Category' => array(
        'field' => 'product_category',
        'label' => 'Category',
        'rules' => 'trim|required|xss_clean'
    ),
    'Product' => array(
        'field' => 'addNewProduct',
        'label' => 'New Product',
        'rules' => 'trim|required|alpha_numeric|xss_clean'
    )
);
$config['add_advertiser_form'] = array(
    'Category' => array(
        'field' => 'advertiser_category',
        'label' => 'Category',
        'rules' => 'trim|required|xss_clean'
    ),
    'Product' => array(
        'field' => 'advertiser_product',
        'label' => 'Product',
        'rules' => 'trim|required|xss_clean'
    ),
    'Advertiser' => array(
        'field' => 'addNewAdvertiser',
        'label' => 'New Advertiser',
        'rules' => 'trim|required|alpha_numeric|xss_clean'
    )
);
$config['add_brand_form'] = array(
    'Category' => array(
        'field' => 'brand_category',
        'label' => 'Category',
        'rules' => 'trim|required|xss_clean'
    ),
    'Product' => array(
        'field' => 'brand_product',
        'label' => 'Product',
        'rules' => 'trim|required|xss_clean'
    ),
    'Advertiser' => array(
        'field' => 'brand_advertiser',
        'label' => 'Advertiser',
        'rules' => 'trim|required|xss_clean'
    ),
    'Brand' => array(
        'field' => 'addNewBrand',
        'label' => 'New Brand',
        'rules' => 'trim|required|alpha_numeric|xss_clean'
    )
);
$config['create_ext_ro_form'] = array(
    array
    (
        'key' => 'txt_ext_ro',
        'value' => 'External RO',
        'rule' => 'trim|required|xss_clean'
    ),
    array
    (
        'key' => 'txt_ro_date',
        'value' => 'RO Date',
        'rule' => 'trim|required|valid_date|xss_clean'
    ),
    array
    (
        'key' => 'sel_agency',
        'value' => 'Agency',
        'rule' => 'trim|required|xss_clean'
    ),
    array
    (
        'key' => 'sel_agency_contact',
        'value' => 'Agency Contact',
        'rule' => 'trim|required|xss_clean'
    ),
    array
    (
        'key' => 'sel_client',
        'value' => 'Client',
        'rule' => 'trim|required|xss_clean'
    ),
    array
    (
        'key' => 'sel_client_contact',
        'value' => 'Client Contact',
        'rule' => 'trim|xss_clean'
    ),
    array
    (
        'key' => 'sel_brand',
        'value' => 'Brand',
        'rule' => 'trim|required|xss_clean'
    ),
    array
    (
        'key' => 'rd_make_good',
        'value' => 'Make Good Type',
        'rule' => 'required|xss_clean'
    ),
    array
    (
        'key' => 'txt_camp_start_date',
        'value' => 'Campaign Start Date',
        'rule' => 'trim|required|valid_date|xss_clean'
    ),
    array
    (
        'key' => 'txt_camp_end_date',
        'value' => 'Campaign End Date',
        'rule' => 'trim|required|valid_date|xss_clean'
    ),
    array
    (
        'key' => 'regionSelectBox',
        'value' => 'Region',
        'rule' => 'required|xss_clean'
    ),
    array
    (
        'key' => 'markets',
        'value' => 'Markets',
        'rule' => 'required|xss_clean'
    ),
    array
    (
        'key' => 'txt_gross',
        'value' => 'Gross RO Amount',
        'rule' => 'required|is_decimal_no_zero|xss_clean'
    ),
    array
    (
        'key' => 'txt_agency_com',
        'value' => 'Agency Commission',
        'rule' => 'is_decimal_no_zero|required|xss_clean'
    ),
    array
    (
        'key' => 'txt_net_agency_com',
        'value' => 'Agency Commission',
        'rule' => 'is_decimal_no_zero|required|xss_clean'
    ),
    array(
        'key' => 'file_pdf',
        'value' => 'Ro Attachment',
        'rule' => 'callback_validateRoAttachment'
    ),
    array(
        'key' => 'client_aproval_mail',
        'value' => 'Mail Attachment',
        'rule' => 'callback_validateMailAttachment'
    )
);

$config['addEditAgencyContact'] = array(
    array(
        'key' => 'txt_agency_contact_name',
        'value' => 'Agency Contact Name',
        'rule' => 'required|xss_clean'
    ),
    array(
        'key' => 'txt_agency_contact_no',
        'value' => 'Agency Contact Number',
        'rule' => 'required|valid_phone|xss_clean'
    ),
    array(
        'key' => 'txt_agency_email',
        'value' => 'Agency Email',
        'rule' => 'required|valid_emails|xss_clean'
    ),
    array(
        'key' => 'agency_state',
        'value' => 'Agency State',
        'rule' => 'required|xss_clean'
    )
);

$config['addEditClientContact'] = array(
    array(
        'key' => 'txt_client_contact_name',
        'value' => 'Client Contact Name',
        'rule' => 'required|xss_clean'
    ),
    array(
        'key' => 'txt_client_contact_no',
        'value' => 'Client Contact Number',
        'rule' => 'required|valid_phone|xss_clean'
    ),
    array(
        'key' => 'txt_client_email',
        'value' => 'Client Email',
        'rule' => 'required|valid_emails|xss_clean'
    ),
    array(
        'key' => 'client_state',
        'value' => 'Client State',
        'rule' => 'required|xss_clean'
    )
);

/* End of file form_validation.php */
/* Location: ./application/config/form_validation.php */
