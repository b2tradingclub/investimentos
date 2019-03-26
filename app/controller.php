<?php
namespace vini\app;

class Controller
{
    protected $home;
    protected $orderby;
    protected $model;
    protected $output;
    protected $params;
    
    protected function includeView($file){
        
        if (file_exists($file)){
            ob_start();
            include $file;
            return ob_get_clean();
        }
        
        return false;
    }
}
?>