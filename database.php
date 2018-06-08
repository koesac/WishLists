<?php

// $username="epiz_20969244";
// $password="RmbUXloQdCca";
// $database= "epiz_20969244_christmas";
// $servername= "sql308.epizy.com";

$servername = "localhost";
$username = "root";
$password = "root";
$database = "christmas";

mysql_connect($servername,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");
?>
