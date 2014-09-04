<?php

class sql_db {
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
	
	function increment($table){
		$this->table = $table;
	
		$result   		= mysql_query("SHOW TABLE STATUS LIKE '$this->table'");
		$row   			= mysql_fetch_array($result);
		$increment   	= $row['Auto_increment'];
		$next_increment = $increment+1;
		mysql_query("ALTER TABLE $this->table AUTO_INCREMENT=$next_increment");
	
		return $increment;
	}
	
}

if((time() - $_SESSION['lifetime'] > 360000) && $_SERVER['QUERY_STRING'] && strpos($_SERVER[REQUEST_URI],'worker') === false){
	
	session_start();
	session_destroy();
	unset($_SESSION['USERID']);
	unset($_SESSION['lifetime']);
	header('LOCATION: index.php');	
	unset($_SERVER['QUERY_STRING']);
	
}elseif((time() - $_SESSION['lifetime'] > 360000) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' && strpos($_SERVER[REQUEST_URI],'worker') === false){
	
	session_start();
	session_destroy();
	unset($_SESSION['USERID']);
	unset($_SESSION['lifetime']);
	header('LOCATION: 404.php');	
}else{
	$db = new sql_db ( "192.168.11.99", "root", "Gl-1114", "alfa" );
}

?>
