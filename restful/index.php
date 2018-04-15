<?php
# There must be no empty lines at the beginning of the file, otherwise, 
# XML files cannot be output correctly.


require('Request.php');
require('Response.php');
$_SERVER['CONTENT_TYPE'] = 'Content-Type: application/json';
$data = Request::getRequest();
Response::sendResponse($data);

// GET  http://test.local.com/restful/class
// GET  http://test.local.com/restful/class/1
// POST http://test.local.com/restful/class?name=SAT&count=239
// PUT  http://test.local.com/restful/class/1?name=SAT&count=23
// PATCH  http://test.local.com/restful/class/1?name=SAT
// DELETE  http://test.local.com/restful/class/1
