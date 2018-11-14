<?php
include "Request.php";

class PHPRestLib {
    private $root;
    private $request;

	function __construct($root = __DIR__.'/')
	{
	    if($root!=__DIR__.'/') $root=__DIR__.($root=='' || $root[0]!='/'?'/':'').$root;
	    $this->root=$root;
	    $this->request=new Request();
	}

	public function custom()
    {

    }

	public function get($entry, $callable, $middlewares=[])
    {
        if($this->request->getMethod()!='GET' || $this->request->getPath()!=$entry) return;
        $this->HandleRequest($callable, $middlewares);
    }

    private function HandleRequest($callable, $middlewares)
	{
		$data=[];
		foreach($middlewares as $middleware) {
			if(strpos($middleware,'.')!==false){
				$m=explode('.',$middleware);
				$mdat=$m[0]::{$m[1]}($this->request);
			}
			else $mdat=$middleware();

			if($mdat===false) exit;
			foreach($mdat as $name => $val) $data[$name]=$val;
		}
		if(!is_callable($callable)){
			if(strpos($callable,'.')!==false){
				$callable=explode('.',$callable);
				$callable[0]::{$callable[1]}($this->request,$data);
				exit;
			}
		}
		$callable($this->request,$data);
		exit;
	}

	public function post($entry, $callable, $middlewares=[])
    {
		if($this->request->getMethod()!='POST' || $this->request->getPath()!=$entry) return;
		$this->HandleRequest($callable, $middlewares);
    }

	public function put($entry, $callable, $middlewares=[])
    {
		if($this->request->getMethod()!='PUT' || $this->request->getPath()!=$entry) return;
		$this->HandleRequest($callable, $middlewares);
    }

	public function delete($entry, $callable, $middlewares=[])
    {
		if($this->request->getMethod()!='DELETE' || $this->request->getPath()!=$entry) return;
		$this->HandleRequest($callable, $middlewares);
    }

	public function patch($entry, $callable, $middlewares=[])
    {
		if($this->request->getMethod()!='PATCH' || $this->request->getPath()!=$entry) return;
		$this->HandleRequest($callable, $middlewares);
    }

    public function resource($entry, $callable, $middlewares=[])
    {
		if($this->request->getPath()!=$entry) return;
		$this->HandleRequest($callable, $middlewares);
    }
}
