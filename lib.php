<?php
require_once(dirname(__FILE__).'/../../config.php');

function query_acclaim_api(){
    $config = get_config('block_acclaim');
    $url="{$config->url}/api/v1/organizations/{$config->org}/badge_templates";
    $username=$config->token;
    $password = "";
    return return_json_badges($url,$username);
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

function return_json_badges($url,$username){
    $password = "";
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

