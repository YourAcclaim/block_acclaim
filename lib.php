<?php

function query_acclaim_api(){
    $org_id="6bb2e1c7-c66b-4d47-9301-4a6b9e792e2c";
    $url="https://jefferson-staging.herokuapp.com/api/v1/organizations/".$org_id."/badge_templates";

    //pass token as argument so it's not in code base
    $username = "FZ1QZ4sDtEwNR7Tcv-Yi";
    $password = "";

    return return_json_badges($url,$username,$password);
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
    return $json;
}

function build_radio_buttons($json){
    if(!empty($json)){
        $stack = array();
        foreach($json['data'] as $item){
            array_push($stack,
                html_writer::tag('img', '', array('alt' => 'test', 'height' => '150', 'width' => '150' ,'src' => $item["image_url"]))
                );
            }
        }else{
            //put in the invalid url
            error_log("invalid api, token or unable to connect");
        }
    return $stack;
}

function block_acclaim_images() {
    $json = query_acclaim_api();
    return build_radio_buttons($json);
}

?>

