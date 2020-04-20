<?php
class Request {
    private $method;
    private $path;
    private $query;
    private $headers;
    private $body;

    function __construct(String $method = null, String $path = null, String $query = null, array $headers = null, String $body = null)
    {
        if(isset($method)) $this->method=$method;
        else $this->setMethod();

        if(!isset($path) && !isset($query)){
            $this->setPathAndQuery();
        }
        else{
            if(isset($path)) $this->path=$path;
            else $this->setPath();

            if(isset($query)) $this->query=$query;
            else $this->setQuery();
        }


        if(isset($headers)) $this->headers=$headers;
        else $this->setHeaders();

        if(isset($body)) $this->body=$body;
        else $this->setBody();
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getBody()
    {
        return $this->body;
    }

    private function setMethod()
    {
        $this->method=strtoupper($_SERVER['REQUEST_METHOD']);
    }

    private function setPath()
    {
        $prefix = implode('/',explode('/',$_SERVER['SCRIPT_NAME'],-1));
        if ($prefix != '') $this->path = str_replace($prefix, '', explode('?', $_SERVER['REQUEST_URI'])[0]);
        else $this->path = explode('?', $_SERVER['REQUEST_URI'])[0];
    }

    private function setPathAndQuery()
    {
        $this->setPath();
        $this->setQuery();
    }

    private function setQuery()
    {
        if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
            $path = explode('&', explode('?', $_SERVER['REQUEST_URI'])[1]);
            $query = [];
            foreach($path as $p) {
                $split=explode('=',$p);
                if (isset($query[$split[0]])) {
                    if(is_array($query[$split[0]])) $query[$split[0]][] = ($split[1] ? $split[1] : '');
                    else $query[$split[0]] = [$query[$split[0]], ($split[1] ? $split[1] : '')];
                } else $query[$split[0]] = ($split[1] ? $split[1] : '');
            }
            $this->query = $query;
        } else $this->query = [];
    }

    private function setHeaders()
    {
        $this->headers=getallheaders();
    }

    private function setBody()
    {
        $this->body=[];
        $body=file_get_contents('php://input');
        if($body=="") return;
        if($body[0]=="{" || $body[0]=="[") $this->body=json_decode($body,true);
        else {
            $body=explode('&',$body);
            foreach($body as $b) {
                $split=explode('=',$b);
                if(!isset($this->body[$split[0]])) $this->body[$split[0]]=($split[1]?$split[1]:"");
                elseif(is_string($this->body[$split[0]])) $this->body[$split[0]]=[$this->body[$split[0]],($split[1]?$split[1]:"")];
                elseif(is_array($this->body[$split[0]])) $this->body[$split[0]][]=($split[1]?$split[1]:"");
            }
        }
    }
}
