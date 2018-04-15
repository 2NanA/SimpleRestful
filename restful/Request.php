<?php

/**
 * 
 */
class Request
{
    
    private static $method_type = array('get', 'post', 'put', 'patch', 'delete');
    private static $test_class = array(
       1=> array('name' => 'tofful', 'count' => 18),
       2=> array('name' => 'ars', 'count' => 20),
       );

    public static function getRequest()
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        if (in_array($method, self::$method_type)) {
            $data_name = $method . 'Data';
            $data = file_get_contents("php://input",'r'); 
            parse_str($data, $post_data);
            $_REQUEST['post_data'] = $post_data;   
            return self::$data_name($_REQUEST);
        }
        return false;
    }

    private static function getData($request_data)
    {
        $class_id = (int)$request_data['class'];
        if ($class_id > 0) {
            $data[] = self::$test_class[$class_id];
        } else {
            $data = self::$test_class;
        }
        return $data;
    }

    //POST /class：
    private static function postData($request_data)
    {
        if (!empty($request_data['name'])) {
            $data['name'] = $request_data['name'];
            $data['count'] = (int)$request_data['count'];
            self::$test_class[] = $data;
            return self::$test_class;
        } else {
            return false;
        }
    }

    //PUT /class/ID：
    private static function putData($request_data)
    {
        $class_id = (int)$request_data['class'];
        if ($class_id == 0) {
            return false;
        }
        $data = array();

        if (!empty($request_data['name']) && isset($request_data['count'])) {
            $data['name'] = $request_data['name'];
            $data['count'] = (int)$request_data['count'];
            self::$test_class[$class_id] = $data;
            return self::$test_class;
        } elseif(!empty($request_data['post_data']['name']) && isset($request_data['post_data']['count'])) {
            $data['name'] = $request_data['post_data']['name'];
            $data['count'] = (int)$request_data['post_data']['count'];
            self::$test_class[$class_id] = $data;
            return self::$test_class;
        } else {
            return false;
        }
    }

    //PATCH /class/ID：
    private static function patchData($request_data)
    {
        $class_id = (int)$request_data['class'];
        if ($class_id == 0) {
            return false;
        }
        if (!empty($request_data['name'])) {
            self::$test_class[$class_id]['name'] = $request_data['name'];
        }
        if (isset($request_data['count'])) {
            self::$test_class[$class_id]['count'] = (int)$request_data['count'];
        }
        if($request_data['post_data']['name']){
            self::$test_class[$class_id]['name'] = $request_data['post_data']['name'];
        }

        if(isset($request_data['post_data']['count'])){
            self::$test_class[$class_id]['count'] = (int)$request_data['post_data']['count'];    
        }
        return self::$test_class;
    }

    //DELETE /class/ID
    private static function deleteData($request_data)
    {
        $class_id = (int)$request_data['class'];
        if ($class_id == 0) {
            return false;
        }
        unset(self::$test_class[$class_id]);
        return self::$test_class;
    }
}