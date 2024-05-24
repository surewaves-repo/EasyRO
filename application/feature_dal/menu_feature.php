<?php
/**
 * Created by PhpStorm.
 * Author: Yash | Ravishankar
 * Date: November, 2019
 */

namespace application\feature_dal;

use application\repo\BaseDAL;
use Illuminate\Database\Capsule\Manager as DB;

include_once APPPATH . 'repo/base_dal.php';

class MenuFeature extends BaseDAL
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Author : Ravishankar Singh
     * @param $profileId
     * @return string
     *
     * This function converts tasks of a given profile into menu
     */
    public function getHeaderMenu($profileId)
    {
        log_message('DEBUG', 'In MenuFeature@getHeaderMenu | Entered');
        $tasks = $this->getTaskDetails($profileId);
        log_message('Info', 'In MenuFeature@getHeaderMenu | Tasks => ' . print_r($tasks,True));
        $menu = $this->convertIntoHeader($tasks);
        log_message('DEBUG', 'In MenuFeature@getHeaderMenu | Header menu is  = ' . print_r($menu, true));
        $html = $this->convertIntoHtml($menu);
        log_message('DEBUG', 'In MenuFeature@getHeaderMenu | Html For Menu is => ' .print_r($html,True));
        return $html;
    }

    /**
     * Author : Ravishankar Singh
     * @param $profileId
     * @return array
     *
     * This function fetches tasks related to a given profile id
     */
    public function getTaskDetails($profileId)
    {
        log_message('Info', 'In MenuFeature@getTaskDetails | Entered with ProfileId => ' .print_r($profileId, true));
        $operationIds = array();
        $operationRecords = $this->RoUserProfile->getOperationId($profileId);
        log_message('Info', 'In MenuFeature@getTaskDetails | OperationRecords => ' . print_r($operationRecords,True));
        foreach ($operationRecords as $record) {
            array_push($operationIds, $record['Operation_Fk_Id']);
        }
        log_message('Info', 'In MenuFeature@getTaskDetails | OperationIds => ' . print_r($operationIds,True));

        $tasks = array();
        $taskRecords = $this->RoMasterOperation->getTasks($operationIds);
        log_message('Info', 'In MenuFeature@getTaskDetails | TaskRecords => ' . print_r($taskRecords,True));
        foreach ($taskRecords as $task) {
            array_push($tasks, array('Id' => $task['Task_Fk_Id'], 'Name' => $task['Name'], 'Url' => $task['Url'], 'Parent_Id' => $task['Parent_Id']));
        }
        return $tasks;
    }

    /**
     * Author : Yash Bansal
     * @param $task
     * @return array
     *
     * Getting Menu Header
     */
    public function convertIntoHeader($task)
    {
        log_message('INFO', 'In MenuFeature@convertIntoHeader | Entered');
        $menu = array();
        foreach ($task as $dtls) {
            $name = $dtls['Name'];
            $url = $dtls['Url'];

            if (empty($dtls['Parent_Id'])) {
                $menu[$name] = $url;

                $child_menu = $this->getChildHeaderMenu($dtls['Id'], $task);
                log_message('INFO', 'In MenuFeature@convertIntoHeader | Child Menu is = ' . print_r($child_menu, true));
                if (count($child_menu) > 0) {
                    $menu[$name] = array();
                    $menu[$name][$name] = $url;
                    $menu[$name]['child'] = $child_menu;
                }
            }
        }
        return $menu;
    }

    /**
     * Author : Yash Bansal
     * @param $id
     * @param $task
     * @return array
     *
     * Getting sub menu options
     */
    private function getChildHeaderMenu($id, $task)
    {
        $child = array();
        foreach ($task as $value) {
            if ($value['Parent_Id'] == NULL) {
                continue;
            } else {
                if ($id == $value['Parent_Id']) {
                    $name = $value['Name'];
                    $url = $value['Url'];
                    $child[$name] = $url;
                }
            }
        }
        return $child;
    }

    /**
     * Author :  Yash Bansal
     * @param $header_menu
     * @return string
     *
     * This function converts menu to a html tag
     */
    public function convertIntoHtml($header_menu)
    {
        $html = '<ul id="nav">';
        foreach ($header_menu as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $key_1 => $value_1) {
                    if (!is_array($value_1)) {
                        $html .= '<li><a href="' . ROOT_FOLDER . '' . $value_1 . '">' . $key_1 . '</a>';
                    } else {
                        $html .= '<ul id="nav">';
                        foreach ($value_1 as $key_2 => $value_2) {
                            $html .= '<li><a href="' . ROOT_FOLDER . '' . $value_2 . '">' . $key_2 . '</a></li>';
                        }
                    }
                }
                $html .= '</ul></li>';
            } else {
                $html .= '<li><a href="' . ROOT_FOLDER . '' . $value . '">' . $key . '</a></li>';
            }
        }
        $html .= '</ul>';
        log_message('DEBUG', 'In MenuFeature@convertIntoHtml | HTML is  ' . print_r($html, true));
        return $html;
    }
}