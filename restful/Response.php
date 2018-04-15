<?php
/**
 * 
 */
class Response
{
    const HTTP_VERSION = "HTTP/1.1";

    public static function sendResponse($data)
    {

        if ($data) {
            $code = 200;
            $message = 'OK';
        } else {
            $code = 404;
            $data = array('error' => 'Not Found');
            $message = 'Not Found';
        }

        header(self::HTTP_VERSION . " " . $code . " " . $message);
        $content_type = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : $_SERVER['HTTP_ACCEPT'];
        
        if (strpos($content_type, 'application/json') !== false) {
            header("Content-Type: application/json");
            echo self::encodeJson($data);
        } else if (strpos($content_type, 'application/xml') !== false) {
            header("Content-type: text/xml");
            echo self::encodeXml($data);
        } else {
            header("Content-Type: text/html");
            echo self::encodeHtml($data);
        }
        exit();
    }

    
    private static function encodeJson($responseData)
    {
        return json_encode($responseData);
    }

    private static function encodeXml($responseData)
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><rest />');
        foreach ($responseData as $key => $value) {
            $class=$xml->addchild("class");
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $class->addChild($k, $v);
                }
            } else {
                $class->addChild($key, $value);
            }
            $class->addAttribute("id",$key);
        }
        return $xml->asXML();
    }

    private static function encodeHtml($responseData)
    {
        $html = "<table border='1'>";
        foreach ($responseData as $key => $value) {
            $html .= "<tr>";
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $html .= "<td>" . $k . "</td><td>" . $v . "</td>";
                }
            } else {
                $html .= "<td>" . $key . "</td><td>" . $value . "</td>";
            }
            $html .= "</tr>";
        }
        $html .= "</table>";
        return $html;
    }
}