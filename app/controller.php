<?php
namespace vini\app;

class Controller
{
    protected $home;
    protected $orderby;
    protected $model;
    protected $output;
    
    protected function includeView($file){
        
        if (file_exists($file)){
            ob_start();
            include $file;
            return ob_get_clean();
        }
        
        return false;
    }
    
    protected function params(){
        $i=0;
        $params = '';
        foreach ($_GET as $key => $value){
            $params .= (($i)?'&':'').$key.'='.$value;
            $i++;
        }
        return $params;
    }
}
?>