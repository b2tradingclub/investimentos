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
            return ob_get_clean();;
        }
        return false;
    }

	public function render($output){
		 echo $this->includeView($output);
	}


	public function home()
	{
        return $_SERVER['PHP_SELF'].'?'.$this->params();
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

    protected function addParams($key, $value){
        $_GET[$key] = $value;
    }

    protected function removeParams($key){
        unset($_GET[$key]);
    }
}
?>