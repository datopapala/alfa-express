<?php

class asterisk_sql_db {
	
	public $db_connect_id;
	public $user;
	public $password;
	public $server;
	public $database;
	public $table;
	public $result;
	
	function sql_db($sqlserver, $sqluser, $sqlpassword, $database) {
		$this->user = $sqluser;
		$this->password = $sqlpassword;
		$this->server = $sqlserver;
		$this->dbname = $database;
		
		$this->db_connect_id = mysql_connect ( $this->server, $this->user, $this->password );
		
		if ($this->db_connect_id) {
			if ($database != "") {
				$this->dbname = $database;
				$dbselect = mysql_select_db ( $this->dbname );
				$dbselect = mysql_set_charset ( 'utf8', $this->db_connect_id );
				
				if (! $dbselect) {
					mysql_close ( $this->db_connect_id );
					$this->db_connect_id = $dbselect;
				}
			}
			
			return $this->db_connect_id;
		} else {
			return false;
		}
	}
	
}

$asteriskdb = new asterisk_sql_db("212.72.155.176", "root", "Gl-1114", "stats");

?>
