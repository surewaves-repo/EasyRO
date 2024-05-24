<?php

namespace application\repo;

use application\interfaces\RepositoryInterface;
use Illuminate\Container\Container as App;

include_once APPPATH . 'interfaces/RepositoryInterface.php';
include_once APPPATH . 'models2/ro_am_external_ro_model.php';
include_once APPPATH . 'models2/ro_amount_model.php';
include_once APPPATH . 'models2/ro_cancel_external_ro_model.php';
include_once APPPATH . 'models2/ro_external_ro_user_map_model.php';
include_once APPPATH . 'models2/ro_mail_model.php';
include_once APPPATH . 'models2/ro_market_price_model.php';
include_once APPPATH . 'models2/ro_master_operation_model.php';
include_once APPPATH . 'models2/ro_operation_task_model.php';
include_once APPPATH . 'models2/ro_profile_operation_model.php';
include_once APPPATH . 'models2/ro_progression_mail_status_model.php';
include_once APPPATH . 'models2/ro_task_model.php';
include_once APPPATH . 'models2/ro_user_model.php';
include_once APPPATH . 'models2/ro_user_profile_model.php';
include_once APPPATH . 'models2/ro_user_region_model.php';
include_once APPPATH . 'models2/sv_category_model.php';
include_once APPPATH . 'models2/sv_new_advertiser_model.php';
include_once APPPATH . 'models2/sv_new_brand_model.php';
include_once APPPATH . 'models2/sv_product_group_model.php';

include_once APPPATH . 'models2/sv_agency_display_model.php';
include_once APPPATH . 'models2/sv_new_agency_model.php';
include_once APPPATH . 'models2/sv_advertiser_display_model.php';
include_once APPPATH . 'models2/ro_status_model.php';
include_once APPPATH . 'models2/ro_am_external_ro_files_model.php';
include_once APPPATH . 'models2/ro_static_emails_model.php';
include_once APPPATH . 'models2/sv_sw_market_model.php';
include_once APPPATH . 'models2/ro_agency_contact_model.php';
include_once APPPATH . 'models2/sv_sw_states_model.php';
include_once APPPATH . 'models2/ro_client_contact_model.php';

include_once APPPATH . 'models2/ro_approved_networks_model.php';
include_once APPPATH . 'models2/sv_advertiser_campaign_model.php';
include_once APPPATH . 'models2/sv_advertiser_campaign_screens_dates_model.php';
include_once APPPATH . 'models2/sv_customer_model.php';
include_once APPPATH . 'models2/sv_job_queue_model.php';
include_once APPPATH . 'models2/sv_screen_model.php';
include_once APPPATH . 'models2/sv_tv_channel_model.php';
include_once APPPATH . 'models2/ro_orders_model.php';
include_once APPPATH . 'models2/ro_cancel_invoice_model.php';
include_once APPPATH . 'models2/ro_external_ro_report_details_model.php';
include_once APPPATH . 'models2/ro_cancel_channel_model.php';

include_once APPPATH . 'models2/ro_channel_file_location_model.php';
include_once APPPATH . 'models2/ro_approval_remarks_model.php';
include_once APPPATH . 'models2/ro_network_ro_report_details_model.php';
include_once APPPATH . 'models2/ro_mail_data_model.php';
abstract class Repository implements RepositoryInterface
{
    private $model;

    public function __construct()
    {
        $this->makeModel(new App());
    }

    /**
     * Author : Ravishankar Singh
     * @param $app
     *
     * This function loads a given model
     * @throws Exception
     */
    public function makeModel($app)
    {
        log_message('DEBUG', 'In Repository@makeModel | Loading Model: ' . print_r($this->model(), true));
        $this->model = $app->make($this->model());
        log_message('DEBUG', 'In makeModel |Above Model Loaded successfully');
    }

    /**
     * Author : Ravishankar Singh
     * @return mixed
     *
     * This function is over-ridden and it returns path of a model
     */
    abstract function model();

    /**
     * Author : Ravishankar Singh
     * @return mixed
     *
     * This functin fetches the records for a given model
     */
    public function get()
    {
        log_message('DEBUG', 'In Repository@get | Fetching records from ' . print_r($this->model(), true));
        return $this->model->get();
    }

    public function getOrder($colOrder, $order)
    {
        return $this->model->orderBy($colOrder, $order)->get();
    }

    /**
     * @param $data
     */
    public function insertData($data)
    {
        log_message('DEBUG', 'In Repository@insertData | Inserting records in ' . print_r($this->model(), true));
        $this->model->insert($data);
    }

    /**
     * Author : Ravishankar Singh
     * @param $conditions
     * @param $columns
     * @return mixed
     *
     * This function fetches records with given conditions
     */
    public function getColumnsWhere($conditions, $columns)
    {
        log_message('DEBUG', 'In Repository@getWhere | Fetching records from ' . print_r($this->model(), true));
        return $this->model->where($conditions)->get($columns);
    }

    /**
     * @param $col
     * @param $data
     * @param $columns
     * @return mixed
     */
    public function getColumnsWhereIn($col, $data, $columns)
    {
        log_message('DEBUG', 'In Repository@getColumnsWhereIn | Fetching record from ' . print_r($this->model(), true));
        return $this->model->whereIn($col, $data)->get($columns);
    }

    /**
     * @param $Conditions
     * @param $colOrder
     * @param $order
     * @return mixed
     */
    public function getWhereOrder($Conditions, $colOrder, $order)
    {
        log_message('DEBUG', 'In Repository@getWhereOrder | Fetching record from ' . print_r($this->model(), true));
        return $this->model->where($Conditions)->orderBy($colOrder, $order)->get();
    }

    /**
     * @param $conditions
     * @param $colOrder
     * @param $order
     * @param $colDistinct
     * @return mixed
     */
    public function getWhereOrderDistinct($conditions, $colOrder, $order, $colDistinct)
    {
        log_message('DEBUG', 'In Repository@getWhereOrderDistinct | Fetching record from - ' . print_r($this->model(), true));
        return $this->model->where($conditions)->distinct($colDistinct)->orderBy($colOrder, $order)->get();
    }

    /**
     * Author : Ravishankar Singh
     * @param $profileId
     * @return mixed
     *
     * This function fetches operation id for a given profile id using relationships
     */
    public function getOperationId($profileId)
    {
        log_message('DEBUG', 'In Repository@getOperationId |Fetching Operation Id from RoMasterOperation using relation for profile id = ' . $profileId);
        $result = $this->model->with(
            [
                'RoMasterOperation' => function ($query) {
                    $query->select('Operation_Fk_Id');
                }
            ]
        )->find($profileId)->toArray();
        return $result['ro_master_operation'];
    }

    /**
     * Author : Ravishankar Singh
     * @param $operationIds
     * @return mixed
     *
     * This function fetches task details for given operation id using relationships
     */
    public function getTasks($operationIds)
    {
        log_message('DEBUG', 'In getTasks |Fetching records from RoTask for operation id = ' . json_encode($operationIds));
        $result = $this->model->whereIn('Id', $operationIds)->with(
            [
                'RoTask' => function ($query) {
                    $query->select('Task_Fk_Id', 'Name', 'Url', 'Parent_Id')->orderBy('Task_Fk_Id');
                }
            ]
        )->get()->toArray();
        return $result[0]['ro_task'];
    }

    /**
     * Author : Ravishankar Singh
     * @param $profileId
     * @return mixed
     *
     * This function fetches taskRecord for a given profile id
     */
    public function getUrl($profileId)
    {
        log_message('DEBUG', 'In getUrl |Fetching landing URL for profile id = ' . $profileId);
        $result = ($this->model->find($profileId))->RoTask->toArray();
        return $result['Url'];
    }

    /**
     * Author : Ravishankar Singh 2019-09-10
     * @param $data
     * @return mixed
     */
    public function insertGetId($data)
    {
        log_message('DEBUG', 'In Repository@insertGetId | Inserting records into ' . print_r($this->model(), true));
        return $this->model->insertGetId($data);
    }

    /**
     * Author :  Ravishankar Singh 2019-09-10
     * @param $condition
     * @param $data
     */
    public function updateData($condition, $data)
    {
        log_message('DEBUG', 'In Repository@updateData | Updating records in  ' . print_r($this->model(), true));
        $this->model->where($condition)->update($data);
    }

    /**
     * @param $agency
     * @return mixed
     */
    public function getNewAgencyDetails($agency)
    {
        log_message('DEBUG', 'In Repository@getNewAgencyDetails | Fetching records from ' . print_r($this->model(), true));
        $result = $this->model->where('agency_display_name', $agency)->with(
            [
                'SvNewAgency' => function ($query) {
                    $query->select('*');
                }
            ]
        )->get()->toArray();

        return $result[0];
    }

    /**
     * @param $advertiser
     * @return mixed
     */
    public function getAdvertiserDetails($advertiser)
    {
        log_message('DEBUG', 'In Repository@getAdvertiserDetails | Fetching records from ' . print_r($this->model(), true));
        $result = $this->model->with(
            [
                'SvNewAdvertiser' => function ($query) {
                    $query->select('*');
                }
            ]
        )->where('advertiser_display_name', $advertiser)->groupBy('advertiser_display_name')->get()->toArray();
        return $result[0];
    }

    /**
     * @param $typeArr
     * @return mixed
     */
    public function getStaticMail($typeArr)
    {
        log_message('DEBUG', 'In Repository@getStaticMail | Fetching record from - ' . print_r($this->model(), true));
        $result = $this->model->whereIn('type', $typeArr)
            ->groupBy('type')
            ->selectRaw('GROUP_CONCAT(email_id) as static_emails')
            ->get();
        return $result;
    }

    /**
     * @param $internalRoId
     * @param $customerIds
     * @return mixed
     */
    public function getRevenueShareForRoCustomer($internalRoId, $customerIds)
    {
        log_message('DEBUG', 'In Repository@getRevenueShareForRoCustomer | Fetching record from - ' . print_r($this->model(), true));
        $result = $this->model
            ->where(array(array('internal_ro_number', $internalRoId)))
            ->whereIn('customer_id', $customerIds)
            ->groupBy('customer_id')
            ->get(array('customer_id', 'customer_share'));
        return $result;
    }

    /**
     * @param $whereCondition
     * @param $whereInCondition
     * @param $columns
     * @return mixed
     */
    public function getColumnsWhereWhereIn($whereCondition, $whereInCondition, $columns)
    {
        log_message('DEBUG', 'In Repository@getColumnsWhereWhereIn | Fetching record from - ' . print_r($this->model(), true));
        $result = $this->model
            ->where($whereCondition)
            ->whereIn($whereInCondition['whereInColumn'], $whereInCondition['whereInData'])
            ->get($columns);
        return $result;
    }

    /**
     * @param $condition
     * @param $orderByCondition
     * @param $limitCondition
     * @param array $columns
     * @return mixed
     */
    public function getColumnsWhereOrderByLimit($condition, $orderByCondition, $limitCondition, $columns)
    {
        log_message('DEBUG', 'In Repository@getWhereOrderByLimit | Fetching record from - ' . print_r($this->model(), true));
        $result = $this->model
            ->where($condition)
            ->orderBy($orderByCondition['orderColumn'], $orderByCondition['order'])
            ->offset($limitCondition['offset'])
            ->limit($limitCondition['limit'])
            ->get($columns);
        return $result;
    }

    /**
     * @param $whereInCondition
     * @param $updateData
     */
    public function updateWhereIn($whereInCondition, $updateData)
    {
        log_message('DEBUG', 'In Repository@updateWhereWhereIn | Updating record in - ' . print_r($this->model(), true));
        $this->model
            ->whereIn($whereInCondition['whereInColumn'], $whereInCondition['whereInData'])
            ->update($updateData);
    }

    /**
     * @param $whereCondition
     * @param $whereInCondition
     * @param $updateData
     */
    public function updateWhereWhereIn($whereCondition, $whereInCondition, $updateData)
    {
        log_message('DEBUG', 'In Repository@updateWhereWhereIn | Updating record in - ' . print_r($this->model(), true));
        $this->model
            ->where($whereCondition)
            ->whereIn($whereInCondition['whereInColumn'], $whereInCondition['whereInData'])
            ->update($updateData);
    }

    /**
     * @param $data
     */
    public function deleteWhere($data)
    {
        log_message('DEBUG', 'In Repository@updateWhereWhereIn | Deleting record from - ' . print_r($this->model(), true));
        $this->model->where($data)->delete();
    }
}
