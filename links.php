<html><head>

<link rel="stylesheet" href="styles.css">
</head><body>


<?

// echo "user found";


include 'database.php';

$sql = "SELECT * FROM Users";
// echo $sql;


$result = mysql_query($sql);


//user is valid
while($row = mysql_fetch_object($result))
			{
		echo "$row->Name: <a href='mailto:?subject=Christmas%20Lists&body=http://koesac.epizy.com/?code=$row->code'>$row->code</a></br>";

	}






?>

</body></html>
