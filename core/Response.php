<?php
namespace Restful\Core;

use Restful\Core\Responsemsg as Msg;

/**
 *
 */
class Response
{
    const HTTP_VERSION = "HTTP/1.1";

    public static function send($response)
    {
        header(self::HTTP_VERSION . " " . $response['code'] . " " . $response['message']);

        $content_type = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : $_SERVER['HTTP_ACCEPT'];
        
        if (strpos($content_type, 'application/json') !== false) {
            header("Content-Type: application/json");
            echo self::encodeJson($response);
        } elseif (strpos($content_type, 'application/xml') !== false) {
            header("Content-type: text/xml");
            echo self::encodeXml($response);
        } else {
            header("Content-Type: text/html");
            echo self::encodeHtml($response);
        }
        exit();
    }

    private static function encodeJson($response)
    {
        return json_encode($response->content);
    }

    private static function encodeXml($response)
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><rest />');

        foreach ($response->content as $key => $content) {
            $record = $xml->addchild("response");
            if (is_array($content)) {
                foreach ($content as $k => $user) {
                    if (is_array($user)) {
                        $item = $xml->addchild("user");
                        foreach ($user as $key => $value) {
                            $item->addChild($key, $value);
                        }
                    } else {
                        $record->addChild($k, $user);
                    }
                }
            } else {
                $record->addChild($key, $content);
            }
            $record->addAttribute("id", $key);
        }
        return $xml->asXML();
    }

    private static function encodeHtml($response)
    {
        $html = "<table border='1'>";
        foreach ($response->content as $key => $value) {
            $html .= "<tr>";
            if (is_array($value)) {
                $sub = '';
                foreach ($value as $k => $v) {
                    if (is_array($v)) {
                        foreach ($v as $key => $value) {
                            $sub .= "<tr><td>" . $key . "</td><td>" . $value . "</td></tr>";
                        }
                    } else {
                        $sub .= "<tr><td>" . $k . "</td><td>" . $v . "</td></tr>";
                    }
                }
                $sub = substr($sub, 0, strlen($sub)-5);
                $sub = substr($sub, 4);
                $html .= $sub;
            } else {
                $html .= "<td>" . $key . "</td><td>" . $value . "</td>";
            }
            $html .= "</tr>";
        }
        $html .= "</table>";
        return $html;
    }
}
