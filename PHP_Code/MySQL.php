<?php
/**
 *
 * @author Vishnu T Suresh
 *
 */
class MySQL
{
	public $domain,$port,$username,$password,$database;
	private function __construct(){
		$MySQL=simplexml_load_file("$_SERVER[DOCUMENT_ROOT]/resources/MySQL.xml");
		$this->domain=$MySQL->domain;
		$this->port=(int)$MySQL->port;
		$this->username=$MySQL->username;
		$this->password=$MySQL->password;
		$this->database=$MySQL->database;
	}
	public static function getInstance(){
		static $instance;
		if(!isset($instance))
			$instance = new MySQL();
		return $instance;
	}
	public static function getConnection(){
		$mysql=MySQL::getInstance();
		$mysqli= new mysqli($mysql->domain, $mysql->username, $mysql->password, $mysql->database, $mysql->port);
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
		return $mysqli;
	}
}
?>