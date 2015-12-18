<?php
/* Configuration file for course critique */

//Search bar location (user facing)
$rootURL = "http://127.0.0.1/gt-course-critique/";
include('myconfig.php');

class database {

    //MySQL credentials, app only needs read only access
    private $RDBMType = "mysql";  //In the PHP PDO format
    private $username = PDO_USER;
    private $password = PDO_PASS;
    private $dbhost = "127.0.0.1";
    private $database = "critique_data";
    private $table = "Data";

    function __construct() {
        //Connection string
		try {
			$this->pdo = new PDO($this->RDBMType . ":host=" . $this->dbhost . ";dbname=" . $this->database, $this->username, $this->password);
		
		} catch (PDOException $e) {
			$this->error = $e->getMessage();
			echo $this->error;
		}
    }

}