<?php

class Route
{
    private $_uri = array();
    private $_method = array();
    private $_data = 'data';

    public function add($uri, $method = null, $data)
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        $this->_uri[] = $uri;
        if ($data != null){
            $this->_data = $data;
        }
        if ($method != null){
            $this->_method[] = $method;
        }
    }

    public function submit()
    {   
        $uriGetParam = isset($_REQUEST['uri']) ? '/' . $_REQUEST['uri'] : '/';
        
        foreach ($this->_uri as $key => $value)
        {
            if (preg_match("#^$value$#", $uriGetParam))
            {
                echo $this->_method[$key];
                if(is_string($this->_method[$key])){
                    $useMethod = $this->_method[$key];
                    new $useMethod($this->_data != null ? $this->_data: null);
                }else{
                    call_user_func($this->_method[$key]);
                }
            }

        }

    }

}

?>