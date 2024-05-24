<?php

namespace application\repo;

use application\models_repo\RoAmExternalRoRepo;
use application\models_repo\RoAmountRepo;
use application\models_repo\RoCancelExternalRoRepo;
use application\models_repo\RoExternalRoUserMapRepo;
use application\models_repo\RoMailRepo;
use application\models_repo\RoMarketPriceRepo;
use application\models_repo\RoMasterOperationRepo;
use application\models_repo\RoOperationTaskRepo;
use application\models_repo\RoProfileOperationRepo;
use application\models_repo\RoProgressionMailStatusRepo;
use application\models_repo\RoTaskRepo;
use application\models_repo\RoUserProfileRepo;
use application\models_repo\RoUserRegionRepo;
use application\models_repo\RoUserRepo;
use application\models_repo\SvCategoryRepo;
use application\models_repo\SvNewAdvertiserRepo;
use application\models_repo\SvNewBrandRepo;
use application\models_repo\SvProductGroupRepo;

use application\models_repo\SvAgencyDisplayRepo;
use application\models_repo\SvNewAgencyRepo;
use application\models_repo\SvAdvertiserDisplayRepo;
use application\models_repo\RoStatusRepo;
use application\models_repo\RoAmExternalRoFilesRepo;
use application\models_repo\RoStaticEmailsRepo;
use application\models_repo\SvSwMarketRepo;
use application\models_repo\RoClientContactRepo;
use application\models_repo\RoAgencyContactRepo;
use application\models_repo\SvSwStatesRepo;

use application\models_repo\SvAdvertiserCampaignRepo;
use application\models_repo\SvAdvertiserCampaignScreensDatesRepo;
use application\models_repo\SvTvChannelRepo;
use application\models_repo\SvCustomerRepo;
use application\models_repo\SvJobQueueRepo;
use application\models_repo\SvScreenRepo;
use application\models_repo\RoApprovedNetworksRepo;
use application\models_repo\RoOrdersRepo;
use application\models_repo\RoCancelInvoiceRepo;
use application\models_repo\RoExternalRoReportDetailsRepo;
use application\models_repo\RoCancelChannelRepo;

use application\models_repo\RoChannelFileLocationRepo;
use application\models_repo\RoApprovalRemarksRepo;

include_once APPPATH . 'models_repo/ro_am_external_ro_repo.php';
include_once APPPATH . 'models_repo/ro_amount_repo.php';
include_once APPPATH . 'models_repo/ro_cancel_external_ro_repo.php';
include_once APPPATH . 'models_repo/ro_external_ro_user_map_repo.php';
include_once APPPATH . 'models_repo/ro_mail_repo.php';
include_once APPPATH . 'models_repo/ro_market_price_repo.php';
include_once APPPATH . 'models_repo/ro_master_operation_repo.php';
include_once APPPATH . 'models_repo/ro_operation_task_repo.php';
include_once APPPATH . 'models_repo/ro_profile_operation_repo.php';
include_once APPPATH . 'models_repo/ro_progression_mail_status_repo.php';
include_once APPPATH . 'models_repo/ro_task_repo.php';
include_once APPPATH . 'models_repo/ro_user_repo.php';
include_once APPPATH . 'models_repo/ro_user_profile_repo.php';
include_once APPPATH . 'models_repo/ro_user_region_repo.php';
include_once APPPATH . 'models_repo/sv_category_repo.php';
include_once APPPATH . 'models_repo/sv_new_advertiser_repo.php';
include_once APPPATH . 'models_repo/sv_new_brand_repo.php';
include_once APPPATH . 'models_repo/sv_product_group_repo.php';

include_once APPPATH . 'models_repo/sv_agency_display_repo.php';
include_once APPPATH . 'models_repo/sv_new_agency_repo.php';
include_once APPPATH . 'models_repo/sv_advertiser_display_repo.php';
include_once APPPATH . 'models_repo/ro_status_repo.php';
include_once APPPATH . 'models_repo/ro_am_external_ro_files_repo.php';
include_once APPPATH . 'models_repo/ro_static_emails_repo.php';
include_once APPPATH . 'models_repo/sv_sw_market_repo.php';
include_once APPPATH . 'models_repo/ro_client_contact_repo.php';
include_once APPPATH . 'models_repo/ro_agency_contact_repo.php';
include_once APPPATH . 'models_repo/sv_sw_states_repo.php';

include_once APPPATH . 'models_repo/sv_advertiser_campaign_repo.php';
include_once APPPATH . 'models_repo/sv_advertiser_campaign_screens_dates_repo.php';
include_once APPPATH . 'models_repo/sv_tv_channel_repo.php';
include_once APPPATH . 'models_repo/sv_customer_repo.php';
include_once APPPATH . 'models_repo/sv_job_queue_repo.php';
include_once APPPATH . 'models_repo/sv_screen_repo.php';
include_once APPPATH . 'models_repo/ro_approved_networks_repo.php';
include_once APPPATH . 'models_repo/ro_orders_repo.php';
include_once APPPATH . 'models_repo/ro_cancel_invoice_repo.php';
include_once APPPATH . 'models_repo/ro_external_ro_report_details_repo.php';
include_once APPPATH . 'models_repo/ro_cancel_channel_repo.php';

include_once APPPATH . 'models_repo/ro_channel_file_location_repo.php';
include_once APPPATH . 'models_repo/ro_approval_remarks_repo.php';

class BaseDAL
{
    public $RoAmExternalRo;
    public $RoAmount;
    public $RoCancelExternalRo;
    public $RoExternalRoUserMap;
    public $RoMail;
    public $RoMarketPrice;
    public $RoMasterOperation;
    public $RoOperationTask;
    public $RoProfileOperation;
    public $RoProgressionMailStatus;
    public $RoTask;
    public $RoUser;
    public $RoUserProfile;
    public $RoUserRegion;
    public $SvCategory;
    public $SvNewAdvertiser;
    public $SvNewBrand;
    public $SvProductGroup;

    public $SvAgencyDisplay;
    public $SvNewAgency;
    public $SvAdvertiserDisplay;
    public $RoStatus;
    public $RoAmExternalRoFiles;
    public $RoStaticEmails;
    public $SvSwMarket;
    public $RoAgencyContact;
    public $RoClientContact;
    public $SvSwStates;

    public $SvAdvertiserCampaign;
    public $SvAdvertiserCampaignScreensDates;
    public $SvTvChannel;
    public $SvCustomer;
    public $SvJobQueue;
    public $SvScreen;
    public $RoApprovedNetworks;
    public $RoOrders;
    public $RoCancelInvoice;
    public $RoExternalRoReportDetails;
    public $RoCancelChannel;

    public $RoChannelFileLocation;
    public $RoApprovalRemarks;

    public function __construct()
    {
        $this->RoAmExternalRo = new RoAmExternalRoRepo();
        $this->RoAmount = new RoAmountRepo();
        $this->RoCancelExternalRo = new RoCancelExternalRoRepo();
        $this->RoExternalRoUserMap = new RoExternalRoUserMapRepo();
        $this->RoMail = new RoMailRepo();
        $this->RoMarketPrice = new RoMarketPriceRepo();
        $this->RoMasterOperation = new RoMasterOperationRepo();
        $this->RoOperationTask = new RoOperationTaskRepo();
        $this->RoProfileOperation = new RoProfileOperationRepo();
        $this->RoProgressionMailStatus = new RoProgressionMailStatusRepo();
        $this->RoTask = new RoTaskRepo();
        $this->RoUser = new RoUserRepo();
        $this->RoUserProfile = new RoUserProfileRepo();
        $this->RoUserRegion = new RoUserRegionRepo();
        $this->SvCategory = new SvCategoryRepo();
        $this->SvNewAdvertiser = new SvNewAdvertiserRepo();
        $this->SvNewBrand = new SvNewBrandRepo();
        $this->SvProductGroup = new SvProductGroupRepo();

        $this->SvAgencyDisplay = new SvAgencyDisplayRepo();
        $this->SvNewAgency = new SvNewAgencyRepo();
        $this->SvAdvertiserDisplay = new SvAdvertiserDisplayRepo();
        $this->RoStatus = new RoStatusRepo();
        $this->RoAmExternalRoFiles = new RoAmExternalRoFilesRepo();
        $this->RoStaticEmails = new RoStaticEmailsRepo();
        $this->SvSwMarket = new SvSwMarketRepo();
        $this->RoAgencyContact = new RoAgencyContactRepo();
        $this->RoClientContact = new RoClientContactRepo();
        $this->SvSwStates = new SvSwStatesRepo();

        $this->SvAdvertiserCampaign = new SvAdvertiserCampaignRepo();
        $this->SvAdvertiserCampaignScreensDates = new SvAdvertiserCampaignScreensDatesRepo();
        $this->SvTvChannel = new SvTvChannelRepo();
        $this->SvCustomer = new SvCustomerRepo();
        $this->SvJobQueue = new SvJobQueueRepo();
        $this->SvScreen = new SvScreenRepo();
        $this->RoApprovedNetworks = new RoApprovedNetworksRepo();
        $this->RoOrders = new RoOrdersRepo();
        $this->RoCancelInvoice = new RoCancelInvoiceRepo();
        $this->RoExternalRoReportDetails = new RoExternalRoReportDetailsRepo();
        $this->RoCancelChannel = new RoCancelChannelRepo();

        $this->RoChannelFileLocation = new RoChannelFileLocationRepo();
        $this->RoApprovalRemarks = new RoApprovalRemarksRepo();
    }
}