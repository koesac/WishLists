<html><head>

<link rel="stylesheet" href="styles.css">
</head><body>


<?
$usercode = $_GET["code"];
if ( !is_null ($usercode)){
// echo "user found";


include 'database.php';

$sql = "SELECT * FROM Users WHERE code = '$usercode'";
// echo $sql;
$result = mysql_query($sql);


if (mysql_num_rows($result) > 0){

//user is valid
while($row = mysql_fetch_object($result))
			{
		echo "<p>Welcome $row->Name</p>";
		$userid = $row->ID;
	}

	//-----------Tab bar----------------------------

	if (is_null($shoppingtab) && is_null($wishtab)){ $availabletab = "id='defaultOpen'";}
	echo "<div class='tab'>
	  <a href='index.php?code=$usercode'><button class='tablinks'>Gift Ideas</button></a>
	  <a href='index.php?code=$usercode&tab=shopping'><button class='tablinks' >Shopping List</button></a>
	  <a href='index.php?code=$usercode&tab=wish'><button class='tablinks' >Wish List</button></a>

	</div>";



//--------------------------edit item--------------------------

$sql = "SELECT * FROM List WHERE giftid = " . $_GET["id"];

$result = mysql_query($sql);
while($row = mysql_fetch_object($result))
			{

			//edit item
			echo "<div class='container'>
					<h2>Edit gift idea</h2>
					  <form  method='post' action='.?code=$usercode&tab=wish'>
					  <input type='hidden' name='action' value='edit'>
					  <input type='hidden' name='giftid' value='". $_POST["giftid"]."'>​
					  <label for='title'>Item Title</label>
						<input type='text' id='title' name='title' value='$row->Title'>

						<label for='url'>Web Link</label>
						<input type='text' id='url' name='url' value='$row->URL'>

						<label for='price'>Price</label>
						<input type='text' id='price' name='price' value='$row->Price'>

						<label for='description'>Description</label>
						<textarea id='description' name='description'  style='height:100px'>$row->Description</textarea>

						<input type='submit' value='Save'>

					  </form>
					</div>";
			}




}





	else{
	echo "please connect with your personal link";
	//user had a link but it wasnt recognised
	}

mysql_close();
}
else{
echo "please connect with your personal link";
// didnt include a link
}


?>

</body></html>
