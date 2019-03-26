<?php
use \vini\bovespa\bovespa;

error_reporting(E_ALL);

spl_autoload_register(function (String $class) 
{ 
    $vendor = 'vini';
    $file = str_replace( '\\', 
                             DIRECTORY_SEPARATOR, 
                             str_replace($vendor, __DIR__, $class)
                                  ).'.php';
     
    if (file_exists($file)) {
        require($file);
        }
});

$app = new Bovespa;