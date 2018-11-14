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
		$file = explode('/',$_SERVER['SCRIPT_NAME']);
		array_pop($file);
		$path=explode('?',explode(implode('/',$file),$_SERVER['REQUEST_URI'])[1])[0];
		$this->path=$path;
	}

	private function setPathAndQuery()
	{
		$file = explode('/',$_SERVER['SCRIPT_NAME']);
		array_pop($file);
		$path=explode('?',explode(implode('/',$file),$_SERVER['REQUEST_URI'])[1]);
		$this->path=$path[0];
		$this->query=[];
		if(isset($path[1])){
			$path=explode('&',$path[1]);
			foreach($path as $p) {
				if($p=="") continue;
				$split=explode('=',$p);
				$this->query[$split[0]]=($split[1]?$split[1]:"");
			}
		}
	}

	private function setQuery()
	{
		$file = explode('/',$_SERVER['SCRIPT_NAME']);
		array_pop($file);
		$path=explode('?',explode(implode('/',$file),$_SERVER['REQUEST_URI'])[1]);
		$this->query=[];
		if(isset($path[1])){
			$path=explode('&',$path[1]);
			foreach($path as $p) {
				$split=explode('=',$p);
				if(!isset($this->query[$split[0]])) $this->query[$split[0]]=($split[1]?$split[1]:"");
				elseif(is_string($this->query[$split[0]])) $this->query[$split[0]]=[$this->query[$split[0]],($split[1]?$split[1]:"")];
				elseif(is_array($this->query[$split[0]])) $this->query[$split[0]][]=($split[1]?$split[1]:"");
			}
		}
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
		print_r($this->body);
	}
}
