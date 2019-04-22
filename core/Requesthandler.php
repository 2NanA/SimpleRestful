<?php
namespace Restful\Core;

use Restful\Core\User as User;
use Restful\Core\Responsemsg as Msg;

/**
 *
 */
class Requesthandler
{
    private static $httpMethod = [
        'get',
        'post',
        'put',
        'patch',
        'delete'
    ];

    private static $user = null;

    private static function initUser($id = null)
    {
        self::$user = new User($id);
    }

    public static function getRequest()
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        if (in_array($method, self::$httpMethod)) {
            $function = $method;
            $data = file_get_contents("php://input"); // only string, cannot receive array
        
            if ($data) {
                parse_str($data, $inputData);
                $_REQUEST['inputData'] = $inputData;
            } else {
                $_REQUEST['inputData'] = $_POST;
            }
   
            return self::$function($_REQUEST);
        }
        return false;
    }

    private static function get($request)
    {
        self::initUser();
        $userId = $request['user'] ?? 0;
        $notFound = 'User not found';

        if (intval($userId) > 0) {
            $body = self::$user->getArrayByORMapping($userId);
        } else {
            $allUsers = self::$user->findAll();
            $body = [];
            foreach ($allUsers as $key => $user) {
                $body[] = self::$user->getArrayByORMapping($user['id']);
            }
        }
        $empty = !empty($body);
        $response = self::generateResponse($empty, $body, $notFound, true);
        return $response;
    }

    private static function post($request)
    {
        $post = $request['inputData'];

        if (empty($post['Username']) || empty($post['Password'])) {
            throw new \Exception("Username and password cannot be empty");
        }

        self::initUser();
        $inserted = self::$user->create($post);
        $code = $inserted ? 200 : 202;
        $body = $inserted ? (self::$user->getArrayByORMapping($inserted)) : null;

        $response = new Msg($code, $body);

        return $response;
    }

    // Username=hhh595&Password=2312&ActiveDirectoryID=3213123123&Email=test@qq.com   postman
    private static function patch($request)
    {
        $user = $request['user'] ?? null;
        if (empty($user)) {
            throw new \Exception("Please specify a user id");
        }

        if (empty($request['inputData'])) {
            throw new \Exception("There is no item to be updated");
        }

        self::initUser($user);

        $updated = self::$user->patch($request['inputData']);
        $message = $updated ? "patch successfully" : null;
        $response = new Msg(200, self::$user->getArrayByORMapping(self::$user->id), $message);
        return $response;
    }

    private static function put($request)
    {
        $user = $request['user'] ?? null;
        if (empty($user)) {
            throw new \Exception("Please specify a user id");
        }

        if (empty($request['inputData'])) {
            throw new \Exception("There is no item to be updated");
        }

        self::initUser($user);

        $updated = self::$user->put($request['inputData']);
        $message = $updated ? "put successfully" : null;
        $response = new Msg(200, self::$user->getArrayByORMapping(self::$user->id), $message);
        return $response;
    }

    private static function delete($request)
    {
        $user = $request['user'] ?? null;
        if (empty($user)) {
            throw new \Exception("Please specify a user id");
        }
        self::initUser($user);
        $deleted = self::$user->delete($user);
        $response = self::generateResponse($deleted, null, "delete successfully");

        return $response;
    }

    private static function generateResponse($done, $result, $alert, $isGet = false)
    {
        $code = $done ? 200 : 202;
        $body = $result ? $result : null;
        if ($isGet) {
            $message = $done ? null : $alert;
        } else {
            $message = $done ? $alert : null;
        }
        $response = new Msg($code, $body, $message);
        return $response;
    }
}
