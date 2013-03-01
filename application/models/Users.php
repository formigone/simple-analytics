<?php 
class Application_Model_Users {
	private $db;

	// ------------------------------------------------------------
	//
	// ------------------------------------------------------------
	public function __construct() {
		$this->db = $this->db = Zend_Db_Table::getDefaultAdapter();
	}

	public function getUsers() {
		return $this->db->fetchAll(
				$this->db->select()
				->from(array("User" => "users"))
				->order(array("User.username"))
		);
	}
}