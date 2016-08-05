<?php
$url = 'http://localhost/test/version/getRequest.php';
$ch = curl_init();
$header = array(
    "ver:v0.0.1",
    "channel:tuiguang",
    "Content-type:application/json",
    "uid:123456",
    "ctype:0",
    "rate:100"
);
$content = array(0);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
curl_setopt($ch,CURLOPT_POST,true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($content));
curl_setopt($ch,CURLOPT_HEADER,1);
$response = curl_exec($ch);
if(curl_errno($ch)){
    echo curl_error($ch);die;
}
if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200') {
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
}
curl_close($ch);

var_dump($body);die;

