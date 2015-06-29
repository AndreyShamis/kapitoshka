<?php
/**
 * Created by PhpStorm.
 * User: Andrey Shamis
 * Date: 6/29/15
 * Time: 12:19 PM
 */


$status = session_status();
if($status == PHP_SESSION_NONE){
    //There is no active session
    session_start();
}else if($status == PHP_SESSION_DISABLED){
        //Sessions are not available
}else if($status == PHP_SESSION_ACTIVE){
            //Destroy current and start new one
            //session_destroy();
            //session_start();
}


function CheckIP($ip){
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return true;
    }
    return false;
}


function GetClientIP(){
    return $_SERVER['REMOTE_ADDR'];
}

/**
 *
 * @param any $object
 */
function pre_print_r(&$object,$desc = ""){
    echo "$desc<pre>";
    print_r($object);
    echo "</pre>";
}







function getTestFeatureId($thisId){
    global $db;

    $sql = "SELECT * FROM tbl_test_features
            WHERE id_test_id='".$thisId."' ";
    $q      = $db->query($sql);
    $count  = $db->count($q);
    if($count >1){
        $ret = array();
    }
    else{
        $ret = 0;
    }
    for($i=0;$i<$count;$i++){
        $row = $db->fetch_assoc($q);
        if($count > 1)
            $ret[$row['id_feature_id']]=$row['id_feature_id'];
        else
            $ret = $row['id_feature_id'];
    }
    return $ret;
}

/**
 *
 * @param type $valueOf
 * @param type $valueFrom
 * @return type
 */
function getPercentage($valueOf,$valueFrom){
    $ret        = 0;
    if($valueFrom>0)
        $ret    = ($valueOf*100)/$valueFrom;

    return(round($ret,5));
}