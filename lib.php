<?php
require_once(dirname(__FILE__).'/../../config.php');

function query_acclaim_api(){
    global $DB;
    $org_id="6bb2e1c7-c66b-4d47-9301-4a6b9e792e2c";
    $url="https://jefferson-staging.herokuapp.com/api/v1/organizations/".$org_id."/badge_templates";
    $token = $DB->get_record('config', array('name'=>'token'));

    //pass token as argument so it's not in code base
    $username = $token->value;
    $password = "";

    return return_json_badges($url,$username,$password);
}


function truncate($input){
    $max_length = 18;

    if(strlen($input) > $max_length){
        $len = strlen($input);
        $trim_amount = ($max_length - $len);
        return substr($input,0, $trim_amount)."...";
        }

    return $input;
}

function return_json_badges($url,$username,$password){
    $ch = curl_init();

    $curlConfig = array(
    CURLOPT_HTTPHEADER     => array('Accept: application/json'),
    CURLOPT_CUSTOMREQUEST  => "GET",
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_URL            => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERPWD        => $username . ":" . $password,
                                                                                                            );
    curl_setopt_array($ch, $curlConfig);

    $result = curl_exec($ch);
    $json = json_decode($result,true);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    error_log("json ".print_r($json,true));
    return $json;
}

function build_radio_buttons($json){
     $badge_items = "";
     
     if(isset($json['data'])){
         $badge_items = array();
         foreach($json['data'] as $item){
             $friendly_name = truncate($item["name"]);
             $badge_id = $item["id"];
             $badge_items[$badge_id] = $friendly_name;
             }
        }else{
            error_log("invalid api, token or unable to connect");
            }
    return $badge_items;
}


function block_acclaim_images() {
    $json = query_acclaim_api();
    return build_radio_buttons($json);
}

?>

