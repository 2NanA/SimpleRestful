<?php

class Requester
{
    public static function sendRequest($url, $type, $params = null, $headers = null)
    {
        $ch = curl_init($url);
        $timeout = 10;
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        } else {
            // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/html'));
        }
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // do not output to the browser
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        
        switch (strtoupper($type)) {
            case "GET":
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case "POST":
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
            case "PUT":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
            case "PATCH":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
        }
        $file_contents = curl_exec($ch);
        return $file_contents;
        curl_close($ch);
    }
}

$url='http://test.lzk.com:81/restful/user'; // change to your domain:port

// $type= 'post';
// $type= 'get';
// $type= 'patch';
// $type= 'put';
$type= 'delete';

$headers = "";

$params = [
    "Username" => "fafafadadada",
    "Password" => "312",
    "Department" => 4656,
    "NullIfDeleted" =>  null,
    "Email" => "lala@qq.com",
]; 
$strParam = http_build_query($params); // remeber array can not receive by php://input




# POST
// Requester::sendRequest($url, $type, $strParam, $headers);

# get
Requester::sendRequest($url, $type);
// $id = 20; // users table id
// Requester::sendRequest($url . "/$id", $type);

# patch
// $id = 20;
// Requester::sendRequest($url . "/$id", $type, $strParam);

# put
// $id = 20;
// Requester::sendRequest($url . "/$id", $type, $strParam);

# delete
// $id = 21;
// Requester::sendRequest($url . "/$id", $type);
