<html><head>
<meta name="viewport" content="initial-scale=1.0">
<link rel="stylesheet" href="styles.css">
</head><body>


<?
$usercode = $_GET["code"];
if ( !is_null ($usercode)){
// echo "user found";


$username="epiz_20969244";
$password="RmbUXloQdCca";
$database= "epiz_20969244_christmas";
mysql_connect("sql308.epizy.com",$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

$sql = "SELECT * FROM Users WHERE code = '$usercode'";
// echo $sql;
$result = mysql_query($sql);

	
if (mysql_num_rows($result) > 0){
//user is valid
while($row = mysql_fetch_object($result))
			{ 
// 		echo "Ho Ho Ho $row->Name!";
		$userid = $row->ID;
	}



	
//-------save new item--------------------------
if (($_POST["action"] == "add")){


$website = $_POST["url"];
if (!preg_match("~^(?:f|ht)tps?://~i", $website)) {
        $website = "http://" . $website;
    }
if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$website)) {
  $websiteErr = "Invalid URL"; 
  	$website = "";  
  	echo "Warning: URL was not valid and has not been saved. Please copy and paste from the address bar.";
}


$sql = sprintf("INSERT INTO `List`(`Receiver`, `Title`, `URL`, `Price`, `Description`) \n
		VALUES ($userid, 
		\"%s\",
		\"%s\",
		\"%s\",
		\"%s\")",
		$_POST["title"],
		$website,
		$_POST["price"],
		$_POST["description"]
		);
	$result = mysql_query($sql);
$wishtab = "id='defaultOpen'";
}

//---------------------------------------
//--------------------------remove item--------------------------
if (($_POST["action"] == "remove")){
$sql = "DELETE FROM `List` WHERE `List`.`giftid` = " . $_POST["giftid"] ;
$result = mysql_query($sql);
$wishtab = "id='defaultOpen'";
}
//--------------------------edit item--------------------------
if (($_POST["action"] == "edit")){
$sql = sprintf("UPDATE `List` SET 
		`Title` = '%s',
		`URL` =  '%s',
		`Price` =  '%s',
		`Description` =  '%s'
		WHERE `List`.`giftid` = " . $_POST["giftid"],
		$_POST["title"],
		$_POST["url"],
		$_POST["price"],
		$_POST["description"]
		);
// echo $sql;
$result = mysql_query($sql);
$wishtab = "id='defaultOpen'";
}
//---------claim item
if (($_POST["action"] == "claim")){
$sql = "UPDATE `List` SET 
		Giver = '$userid' 
		WHERE `List`.`giftid` = " . $_POST["giftid"];
$result = mysql_query($sql);
$availabletab = "id='defaultOpen'";
}
//---------unclaim item
if (($_POST["action"] == "unclaim")){
$sql = "UPDATE `List` SET 
		Giver = NULL 
		WHERE `List`.`giftid` = " . $_POST["giftid"];
$result = mysql_query($sql);
// echo $sql;
$shoppingtab = "id='defaultOpen'";
}


//-----------Tab bar----------------------------	
if (is_null($shoppingtab) && is_null($wishtab)){ $availabletab = "id='defaultOpen'";}
echo "<div class='tab'>
  <button class='tablinks' $availabletab onclick='openCity(event, \"Available\")'>Gift Ideas</button>
  <button class='tablinks' $shoppingtab onclick='openCity(event, \"Shopping\")'>Your Shopping List</button>
  <button class='tablinks' $wishtab onclick='openCity(event, \"Wish\")'>Your Wish List</button>
</div>";

//---------Available

echo '<div id="Available" class="tabcontent">';

	$sql = "SELECT * FROM Users WHERE ID != $userid";

	$result = mysql_query($sql);

	while($row = mysql_fetch_object($result))
		{ 
		$sql2 = "SELECT * FROM List WHERE Receiver = $row->ID AND Giver is null ORDER BY  Price";
		$result2 = mysql_query($sql2);
		if (mysql_num_rows($result2) > 0){
		
			echo "<h4>$row->Name's List</h4>" ;
			echo "<table class='tables'><tr><th>Title</th><th>Price</th><th>Description</th><th></th></tr>";
			
			while($row2 = mysql_fetch_object($result2))
				{ 
				echo "<tr><td><a target='_blank' href='$row2->URL'>$row2->Title</a></td><td>$row2->Price</td><td>$row2->Description</td>";
				echo "<td>
					<form  method='post'>
					<input type='hidden' name='giftid' value='$row2->giftid'>
					<input type='hidden' name='action' value='claim'>​
					<input type='submit' value='Claim!'></form>
					</td>";
			
				echo "</tr>";
				}
			echo "</table><br/>";
		}
		else {
		echo "<h4>$row->Name's List is empty</h4>" ;
		}
		}
echo '</div>';	

//-------Shopping list
echo '<div id="Shopping" class="tabcontent">';


		$sql2 = "SELECT * FROM List LEFT JOIN WHERE Receiver != $userid AND Giver = $userid ORDER BY Receiver";
		$sql2 = "SELECT List.*, Users.Name as ReceiverName FROM List LEFT JOIN Users on List.Receiver = Users.ID WHERE Receiver != $userid AND Giver = $userid ORDER BY Receiver";
		$result2 = mysql_query($sql2);
	
	
			echo "<table class='tables'><tr><th>Receiver</th><th>Title</th><th>Price</th><th>Description</th><th></th></tr>";
			
			while($row2 = mysql_fetch_object($result2))
				{ 
				echo "<tr><td>$row2->ReceiverName</td><td><a target='_blank' href='$row2->URL'>$row2->Title</a></td><td>$row2->Price</td><td>$row2->Description</td>";
				
				echo "<td>
					<form  method='post'>
					<input type='hidden' name='giftid' value='$row2->giftid'>
					<input type='hidden' name='action' value='unclaim'>​
					<input type='submit' value='Remove' style='background-color: #f44336;'></form>
					</td>";
			
				echo "</tr>";
				
				}
			echo "</table><br/>";
	
		
echo '</div>';	
//-------Wish List
echo '<div id="Wish" class="tabcontent">';


		$sql2 = "SELECT * FROM List WHERE Receiver = $userid ";
		$result2 = mysql_query($sql2);
	
			echo "Please think before you remove any items as someone may have already purchased them.";
			echo "<table class='tables'><tr><th>Title</th><th>Price</th><th>Description</th><th></th width='100px'></tr>";
			
			while($row2 = mysql_fetch_object($result2))
				{ 
				echo "<tr><td><a target='_blank' href='$row2->URL'>$row2->Title</a></td><td>$row2->Price</td><td>$row2->Description</td>";
				echo "<td>
					
					
					<form  method='post' action='edit.php?code=".$_GET["code"]."'>
					<input type='hidden' name='giftid' value='$row2->giftid'>
					<input type='hidden' name='action' value='edit'>​
					<input type='submit' value='Edit'></form>
					
					<form  method='post'>
					<input type='hidden' name='giftid' value='$row2->giftid'>
					<input type='hidden' name='action' value='remove'>​
					<input type='submit' value='Remove' class='remove' style='background-color: #f44336;'></form>
					</td>";
				echo "</tr>";
				}
			echo "</table><br/>";
	
		

//adding a new item
echo '<div class="container">
<h4>Add a new gift idea</h4>
  <form  method="post">
  <input type="hidden" name="action" value="add">​
  <label for="title">Item Title</label>
    <input type="text" id="title" name="title" placeholder="Title of Item...">

    <label for="url">Web Link</label>
    <input type="text" id="url" name="url" placeholder="i.e. https://www.google.co.uk/search?q=santa">
	
	<label for="price">Price</label>
    <input type="text" id="price" name="price" placeholder="Price Estimate...">
    
    <label for="description">Description</label>
    <textarea id="description" name="description" placeholder="Add any additional details if required.." style="height:100px"></textarea>

    <input type="submit" value="Save">

  </form>
</div>';


echo '</div>';	
		
	
	echo "<script>
function openCity(evt, cityName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName('tabcontent');
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = 'none';
    }
    tablinks = document.getElementsByClassName('tablinks');
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(' active', '');
    }
    document.getElementById(cityName).style.display = 'block';
    evt.currentTarget.className += ' active';
}
// Get the element with id='defaultOpen' and click on it
document.getElementById('defaultOpen').click();

</script>";
	

	
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