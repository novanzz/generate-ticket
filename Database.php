<?php
class Database
{
	private $host = "";
	private $db_name = "";
	private $username = "";
	private $password = "";
	private $conn;

	// DB Connect
	public function connect()
	{
		// close connection
		$this->conn = null;

		try {
			$this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
		} catch (PDOException $e) {
			echo 'Connection Error: ' . $e->getMessage();
		}

		return $this->conn;
	}
}
