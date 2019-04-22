<?php
require('autoload.php');

use Restful\Core\Requesthandler as Request;
use Restful\Core\Response as Response;

// $_SERVER['CONTENT_TYPE'] = 'Content-Type: application/json';
// $_SERVER['CONTENT_TYPE'] = 'Content-Type: application/xml';
$_SERVER['CONTENT_TYPE'] = 'Content-Type: text/html';
$data = Request::getRequest();
Response::send($data);
 
// GET      http://yourdomain:yourport/restful/user/1   user id 1
// GET      http://yourdomain:yourport/restful/user     all users

// POST     http://yourdomain:yourport/restful/user     all users
    // eg. Username=testuser&Password=123&ActiveDirectoryID=3213123123&Email=tes2t@qq.com
    
// PUT      http://yourdomain:yourport/restful/user/1   Update all fields, if not specified, to empty
    // eg. Username=testuser&Password=123&ActiveDirectoryID=3213123123&Email=tes2t@qq.com

// PATCH    http://yourdomain:yourport/restful/user/1   Update only specified fields
    // eg. Username=testuser&Password=123&ActiveDirectoryID=3213123123&Email=tes2t@qq.com

// DELETE   http://yourdomain:yourport/restful/user/1   Delete the specified user

