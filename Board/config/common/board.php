<?php
Class BOARD {

	private $_argv;
	
	public function __construct($argv) { //must be public
		this->_argv = "";
	}

	public function __destruct() {
		
	}

	public function getArgv($argv) {
		/*
		$argv must be association array, ex) $argv = Array('SearchOpt'=>'title', 'SearchVal'=>'제목');
		*/
		foreach($argv as $key => $value) {
			//echo "key: {$key} value:{$value}<br />";
			this->_argv .= ({$key}.'='.{$value}.'&');
			this->_argv = substr_replace(this->_argv,"&",-1,1);

			return this->_argv;
		}
	}
}
?>
