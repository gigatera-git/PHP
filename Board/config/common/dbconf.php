<?php
Require_once('const.php');

Class DB {

	private $_user;
	private $_password;
	private $_server;
	private $_db;

	public $_conn; //must be public
	
	public function __construct() { //must be public
		$this->_user = DB_USER;
		$this->_password = DB_PASSWORD;
		$this->_server = DB_HOST;
		$this->_db = DB_NAME;

		$this->_conn = mysqli_connect ($this->_server,$this->_user,$this->_password,$this->_db) or die(mysqli_connect_error());		
	}

	public function __destruct() {
		mysqli_close($this->_conn);
	}
}
?>
