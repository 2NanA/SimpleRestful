<?php
namespace Restful;

class Autoloder
{
   
    public static function load($class)
    {
      
        $baseNamespace = 'Restful\\';
        $baseDir = __DIR__ . '/';

        // does the class use the namespace baseNamespace?
        $len = strlen($baseNamespace);

        if (strncmp($baseNamespace, $class, $len) !== 0) {
            return;
        }

        // get the relative class name
        $requiredClass = substr($class, $len);
 
        $file = $baseDir . str_replace('\\', '/', $requiredClass) . '.php';

        // if the file exists, require it
        if (file_exists($file)) {
            require $file;
        }
    }
}

spl_autoload_register(__NAMESPACE__ .'\Autoloder::load');
