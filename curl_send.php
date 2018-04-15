<?php

class Requester{
    static function sendRequest($url,$type,$params,$headers){
        $ch = curl_init($url);
        $timeout = 5;
        if($headers!=""){
            curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
        }else {
            curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        }
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        switch ($type){
            case "GET" : 
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case "POST": 
                curl_setopt($ch, CURLOPT_POST,true);
                curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
                break;
            case "PUT" : 
                curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
                break;
            case "PATCH": 
                curl_setopt($ch, CULROPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
                break;
        }
        $file_contents = curl_exec($ch);
        return $file_contents;
        curl_close($ch);
    }
}



$ch = curl_init();
$header = array('User-Agent: php test','Accept: text/json'); //设置一个你的浏览器agent的header

curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

# GET request, set your local site at 3rd param.
//curl_setopt($ch, CURLOPT_URL, "http://test.local.com/restful/class");


# POST 
// curl_setopt($ch, CURLOPT_URL, "http://test.local.com/restful/class");
// curl_setopt($ch, CURLOPT_URL, "http://test.local.com/restful/class");
// curl_setopt($ch, CURLOPT_POST, 1);
// // $post_data = array(
// //          "name" => "coder",
// //          "count" => "12345"
// //         );
// $post_data = "name=SAT&count=223";
// curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);


# PUT  
// curl_setopt($ch, CURLOPT_URL, "http://test.local.com/restful/class/1");
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'put');
// //curl_setopt($ch, CURLOPT_HTTPHEADER,array("X-HTTP-Method-Override: PUT"));
// // $put_data = array(
// //          "name" => "SAT",
// //          "count" => "56"
// //         );
// $put_data = "name=SAT&count=223";
// curl_setopt($ch, CURLOPT_POSTFIELDS, $put_data);




# PATCH  
curl_setopt($ch, CURLOPT_URL, "http://test.local.com/restful/class/1");
//curl_setopt($ch, CURLOPT_URL, "http://test.local.com/restful/class/1?name=SATPO");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");  
// $put_data = array(
//          "name" => "SAT",
//          "count" => "56"
//         );
$patch_data = "name=Big";
curl_setopt($ch, CURLOPT_POSTFIELDS, $patch_data);





# DELETE   
// curl_setopt($ch, CURLOPT_URL, "http://test.local.com/restful/class/2");
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'delete');




//curl_setopt($ch, CURLOPT_HEADER, true);

//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//curl_exec($ch);

$post_response = curl_exec($ch);

curl_close($ch);



