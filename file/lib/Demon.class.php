<?php
class Demon{
	//构造函数会传入文件名
	//至于怎么处理文件，自己写
	public function __construct($filename){
		if(file_exists($filename)){
			$this->readfile($filename);
		}	
	}
	
	private function readfile($filename){
		$fp = fopen($filename, 'r');	
		while(($line = fgets($fp)) !== false){
			$line = trim($line);
			//.....coding
		}
	}

}