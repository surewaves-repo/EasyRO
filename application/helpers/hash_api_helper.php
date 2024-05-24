<?php
/**
 * Function for check the auth key
 * Private function
 * @parmas
 * AUTHKEY
 * user_id
 * and api method name
 */
function apiAuth($input, $apiName)
{
    $hash = crypt($input['timestamp'], SALTKEY);
    $hash = crypt($input['appkey'], $hash);
    $hash = crypt($apiName, $hash);
    return $hash;
}

/**
 * Function for check the inputs
 * @param $postValues
 */
function checkInputs($postValues)
{
    if (isset($postValues['timestamp']) && $postValues['timestamp'] != "") {
        if (isset($postValues['appkey']) && $postValues['appkey'] != "") {
            if (isset($postValues['authkey']) && $postValues['authkey'] != "") {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}
