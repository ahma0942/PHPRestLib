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

	private function _validate_path($entry)
	{
		$paths = explode('/', $this->request->getPath());
		$ps = explode('/', $entry);
		if (count($paths) != count($ps)) return false;
		for ($i = 0; $i < count($ps); $i++)
			if ($ps[$i] !== $paths[$i] && $ps[$i][0] != ':') return false;
		return true;
	}

	public function get($entry, $callable, $middlewares=[])
    {
        if($this->request->getMethod()!='GET' || !$this->_validate_path($entry)) return;
        $this->HandleRequest($callable, $middlewares, $entry);
    }

    private function HandleRequest($callable, $middlewares, $entry)
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

		$pathdata = [];
		if (strpos($entry, ':') !== false) {
			$paths = explode('/', $this->request->getPath());
			$ps = explode('/', $entry);
			$pathdata = [];
			for($i=0; $i<count($ps); $i++) {
				if (!empty($ps[$i]) && $ps[$i][0] == ':') $pathdata[substr($ps[$i], 1)] = $paths[$i];
			}
		}

		if(!is_callable($callable)){
			if(strpos($callable,'.')!==false){
				$callable=explode('.',$callable);
				$callable[0]::{$callable[1]}($this->request,$data,$pathdata);
				exit;
			}
		}
		$callable($this->request,$data,$pathdata);
		exit;
	}

	public function post($entry, $callable, $middlewares=[])
    {
		if($this->request->getMethod()!='POST' || !$this->_validate_path($entry)) return;
		$this->HandleRequest($callable, $middlewares, $entry);
    }

	public function put($entry, $callable, $middlewares=[])
    {
		if($this->request->getMethod()!='PUT' || !$this->_validate_path($entry)) return;
		$this->HandleRequest($callable, $middlewares, $entry);
    }

	public function delete($entry, $callable, $middlewares=[])
    {
		if($this->request->getMethod()!='DELETE' || !$this->_validate_path($entry)) return;
		$this->HandleRequest($callable, $middlewares, $entry);
    }

	public function patch($entry, $callable, $middlewares=[])
    {
		if($this->request->getMethod()!='PATCH' || !$this->_validate_path($entry)) return;
		$this->HandleRequest($callable, $middlewares, $entry);
    }

    public function resource($entry, $callable, $middlewares=[])
    {
		if(!$this->_validate_path($entry)) return;
		$this->HandleRequest($callable, $middlewares, $entry);
    }
}
