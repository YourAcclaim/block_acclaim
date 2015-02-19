<?php

//pass token as argument so it's not in code base
$username = $argv[1];
$password = "";

function enum_badges($url,$username,$password){
    print 'enum badges';
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
    print $httpCode;
    curl_close($ch);
    return $json;
}

function unravel($json){
    foreach($json['data'] as $item){
        print_r($item['name']);
    } 
}

$json = enum_badges($url,$username,$password);
?>
