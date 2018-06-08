<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="initial-scale=1.0">
<link rel="stylesheet" href="styles.css">
<script>
<? include("code.js"); ?>
</script>
<title>Christmas List</title>
</head><body onload='onstart()'>
<div class="container">

<?
$usercode = $_GET["code"];
if ( !is_null ($usercode)){
// echo "user found";

include 'database.php';
//default to wishlist before Christmas
if (date() <= strtotime("15 October")){
$wishtab = "id='defaultOpen'";
$beforechristmasmessage = "<p>Gift lists will be visible on 15 October. In the meantime please update your list.</p>";
}

$sql = "SELECT * FROM Users WHERE code = '$usercode'";
// echo $sql;
$result = mysql_query($sql);


if (mysql_num_rows($result) > 0){
//user is valid
while($row = mysql_fetch_object($result))
			{
		$d1=strtotime("December 25");
		$d2=ceil(($d1-time())/60/60/24);

 		echo "<p>Welcome $row->Name there are " . $d2 ." days until Christmas.</p>";
		echo $beforechristmasmessage;
		$userid = $row->ID;
	}






//---------------------------------------
//--------------------------remove item--------------------------
if (isset($_POST["remove"])){
$sql = "UPDATE  `List` SET removed = 1 WHERE `List`.`giftid` = " . $_POST["giftid"] ;
$result = mysql_query($sql);
$wishtab = "id='defaultOpen'";

}

//--------------------------readd item--------------------------
if (isset($_POST["readd"])){
$sql = "UPDATE  `List` SET removed = 0 WHERE `List`.`giftid` = " . $_POST["giftid"] ;
$result = mysql_query($sql);
$wishtab = "id='defaultOpen'";

}

//--------------------------edit or save new item--------------------------
if (isset($_POST["saveedit"])){
	if ($_POST["giftid"] != 'new'){
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
		$result = mysql_query($sql);
	}
	else{
		$website = $_POST["url"];
		if (!preg_match("~^(?:f|ht)tps?://~i", $website)) {
		        $website = "http://" . $website;
		    }
		if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$website)) {
		  $websiteErr = "Invalid URL";
		  	$website = "";
		  	$warningmessage =  "<p style='color:red;'>Warning: URL was not valid and has not been saved. Please copy and paste from the address bar.</p>";
		}

		if ($_POST["title"] != ''
		|| $website != ''
		|| $_POST["price"] != ''
		|| $_POST["description"] != ''){

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
	}
	else {
		$warningmessage = '';
	}
// echo $sql;

}

$wishtab = "id='defaultOpen'";
}
//---------claim item
if (isset($_POST["claim"])){
$sql = "UPDATE `List` SET
		Giver = '$userid'
		WHERE `List`.`giftid` = " . $_POST["giftid"];
$result = mysql_query($sql);
$availabletab = "id='defaultOpen'";
}
//---------unclaim item
if (isset($_POST["unclaim"])){
$sql = "UPDATE `List` SET
		Giver = NULL
		WHERE `List`.`giftid` = " . $_POST["giftid"];
$result = mysql_query($sql);
// echo $sql;
$shoppingtab = "id='defaultOpen'";
}
//---------purchase item
if (isset($_POST["purchase"])){
$sql = "UPDATE `List` SET
		purchased = 1
		WHERE `List`.`giftid` = " . $_POST["giftid"];
$result = mysql_query($sql);
// echo $sql;
$shoppingtab = "id='defaultOpen'";
}
//---------unpurchase item
if (isset($_POST["unpurchase"])){
$sql = "UPDATE `List` SET
		purchased = 0
		WHERE `List`.`giftid` = " . $_POST["giftid"];
$result = mysql_query($sql);
// echo $sql;
$shoppingtab = "id='defaultOpen'";
}



//-----------Tab bar----------------------------
//check for item
if (isset($_POST["edit"]) ){

// echo $sql;
 $edittab = "id='defaultOpen'";
 $wishtab = '';
 // echo $edittab;
}




if (is_null($shoppingtab) && is_null($wishtab) && is_null($edittab)){ $availabletab = "id='defaultOpen'";}
echo "		<div class='tab'>
					  <button class='tablinks' name='Available' $availabletab onclick='openCity(event, \"Available\")'>Ideas</button>
					  <button class='tablinks' name='Shopping' $shoppingtab onclick='openCity(event, \"Shopping\")'>Shopping</button>
					  <button class='tablinks' name='Wish' $wishtab onclick='openCity(event, \"Wish\")'>My List</button>
						<button class='tablinks' name='Item' $edittab style='display:none;' onclick='openCity(event, \"Item\")'>Edit Tab</button>
					</div>";

//---------Available


// echo "<h4>Welcome</h4>";
	if (date() <= strtotime("15 October")){
echo '<div id="Available" class="tabcontent" style="background-color:#ebebeb;"></div>';
	}else{
echo '<div id="Available" class="tabcontent">';
	$sql = "SELECT * FROM Users WHERE ID != $userid";

	$result = mysql_query($sql);

	while($row = mysql_fetch_object($result))
		{
		$sql2 = "SELECT * FROM List WHERE Receiver = $row->ID AND Giver is null AND removed = 0 ORDER BY  Price";
		$result2 = mysql_query($sql2);
		if (mysql_num_rows($result2) > 0){

			echo "<h2>$row->Name's List</h2>" ;
			// echo "<table class='tables'><tr><th>Title</th><th>Price</th><th>Description</th><th></th></tr>";

			while($row2 = mysql_fetch_object($result2))
				{
				if ($row2->URL == ''){
				$url = str_replace(" ","+",$row2->Title);
				$url = "http://www.google.com/search?q=$url";
				}
				else
				{$url = $row2->URL;}

			// 	echo "<tr><td><a target='_blank' href='$url'>$row2->Title</a></td><td>$row2->Price</td><td>$row2->Description</td>";
			// 	echo "<td>
			// 		<form  method='post'>
			// 		<input type='hidden' name='giftid' value='$row2->giftid'>
			// 		<input type='hidden' name='action' value='claim'>​
			// 		<input type='submit' value='Claim!'></form>
			// 		</td>";
			//
			// 	echo "</tr>";
			// 	}
			// echo "</table><br/>";

			echo "
			<form  method='post'>
			<div class='box'><a target='_blank' href='$url'>

			  <div class='details'>

			      <div class='title'>$row2->Title</div>
			   		<div class='price'>$row2->Price</div>
						<div class='description'>$row2->Description</div>

			  </div></a>
				<input type='hidden' name='giftid' value='$row2->giftid'>

				 <input type='submit' class='icon plus' name='claim' value=''>



			</div>
			</form>";
		}
		}
		else {
		echo "<h2>$row->Name's List is empty</h2>" ;
		}
		}
		echo '</div>';
	}


//-------Shopping list
echo '<div id="Shopping" class="tabcontent">';


		// $sql2 = "SELECT * FROM List LEFT JOIN WHERE Receiver != $userid AND Giver = $userid ORDER BY Receiver";
		$sql2 = "SELECT List.*, Users.Name as ReceiverName FROM List LEFT JOIN Users on List.Receiver = Users.ID WHERE Receiver != $userid AND Giver = $userid  AND purchased = 0 ORDER BY Receiver";
		$result2 = mysql_query($sql2);


			while($row2 = mysql_fetch_object($result2))
				{
					if ($row2->URL == ''){
					$url = str_replace(" ","+",$row2->Title);
					$url = "http://www.google.com/search?q=$url";
					}
					else
					{$url = $row2->URL;}

				if ($receiver != $row2->ReceiverName){
					$receiver = $row2->ReceiverName;
					echo "<h2>For $receiver</h2>";
				}
				//add removed badge
				$sql = "SELECT removed FROM List WHERE  giftid = $row2->giftid AND removed = 1";
				$result = mysql_query($sql);
				if (mysql_num_rows($result) > 0){
					$removed = "<span class='notify-badge'>removed</span>";
				} else{ $removed = ""; }

				echo "
				<div class='box'>
					<form  method='post'>
				  	<div class='double details '>
				      <div class='title '><a target='_blank' href='$url'>$row2->Title</a></div>
				   		<div class='price'>$row2->Price</div>
							<div class='description'>$row2->Description</div>
						</div>
						<input type='hidden' name='giftid' value='$row2->giftid'>
						<input type='submit' class='icon tick' name='purchase' value=''>
						<input type='submit' class='icon minus' style='margin-right:10px' name='unclaim' value=''>
					</form>
					$removed
				</div>";


				}
//purchased
$receiver = '';
$sql2 = "SELECT List.*, Users.Name as ReceiverName FROM List LEFT JOIN Users on List.Receiver = Users.ID WHERE Receiver != $userid AND Giver = $userid AND purchased = 1 ORDER BY Receiver";
$result2 = mysql_query($sql2);

	$first = 0;
	while($row2 = mysql_fetch_object($result2))
		{
			if ($row2->URL == ''){
			$url = str_replace(" ","+",$row2->Title);
			$url = "http://www.google.com/search?q=$url";
			}
			else
			{$url = $row2->URL;}

			if ($first == 0) {

				echo "<h2 class='bottomheader'>Purchased</h2>";
				echo "<div class='bottom'>";
				$first = 1;
			}

			if ($receiver != $row2->ReceiverName){
				$receiver = $row2->ReceiverName;
				echo "<h2>For $receiver</h2>";
			}
			//add removed badge
			$sql = "SELECT removed FROM List WHERE  giftid = $row2->giftid AND removed = 1";
			$result = mysql_query($sql);
			if (mysql_num_rows($result) > 0){
				$removed = "<span class='notify-badge'>removed</span>";
			} else{ $removed = ""; }
		echo "
		<div class='box'>
			<form  method='post'>
				<div class='details'>
					<div class='title removed'><a target='_blank' href='$url'>$row2->Title</a></div>
					<div class='price removed'>$row2->Price</div>
					<div class='description removed'>$row2->Description</div>
				</div>
				<input type='hidden' name='giftid' value='$row2->giftid'>
				<input type='submit' class='icon unpurchase' name='unpurchase' value=''>
			</form>
			$removed
		</div>";


		}

echo '</div>';
echo '</div>';
//-------Wish List
echo '<div id="Wish" class="tabcontent">';
echo $warningmessage;

		$sql2 = "SELECT * FROM List WHERE Receiver = $userid  AND removed = 0 ORDER BY giftid DESC";
		$result2 = mysql_query($sql2);
		echo "<form  method='post' id='add' name='add' >
					<h3>Add a new item   <input type='submit' class='icon plus' style='float:none;vertical-align:middle;' name='edit' value=''></h3>
					</form>";
			echo "<p>Please think before you remove or edit any items as someone may have already purchased them.</p>";
			// echo "<table class='tables'><tr><th>Title</th><th>Price</th><th>Description</th><th></th width='100px'></tr>";

			while($row2 = mysql_fetch_object($result2))
				{
					if ($row2->URL == ''){
					$url = str_replace(" ","+",$row2->Title);
					$url = "http://www.google.com/search?q=$url";
					}
					else
					{$url = $row2->URL;}
					echo "
					<div class='box'>
						<form  method='post'>
					  	<div class='details double'>
					      <div class='title'><a target='_blank' href='$url'>$row2->Title</a></div>
					   		<div class='price'>$row2->Price</div>
								<div class='description'>$row2->Description</div>
					  	</div>
							<input type='hidden' name='giftid' value='$row2->giftid'>
							<input type='submit' class='icon edit' name='edit' value=''>
							<input type='submit' class='icon cross'  style='margin-right:10px' name='remove' value=''>
						</form>
					</div>";
				}
					$sql2 = "SELECT * FROM List WHERE Receiver = $userid AND removed = 1 ORDER BY giftid";
					$result2 = mysql_query($sql2);


						$first = 1;
						while($row2 = mysql_fetch_object($result2))
							{
								if ($first == 1 ){
										echo "<h2 class='bottomheader'>Removed</h2>";
										echo "<div class='bottom'>";
										$first = 2;
									}
								if ($row2->URL == ''){
								$url = str_replace(" ","+",$row2->Title);
								$url = "http://www.google.com/search?q=$url";
								}
								else
								{$url = $row2->URL;}
								echo "
								<div class='box'>
									<form  method='post'>
								  	<div class='details'>
								      <div class='title removed'><a target='_blank' href='$url'>$row2->Title</a></div>
								   		<div class='price removed'>$row2->Price</div>
											<div class='description removed'>$row2->Description</div>
								  	</div>
										<input type='hidden' name='giftid' value='$row2->giftid'>
										<input type='submit' class='icon plus' name='readd' value=''>
									</form>
								</div>";
								}

echo '</div>';
echo '</div>';

//---------------------adding a new item---------------------

echo "<div id='Item' $edittab class='tabcontent'>";

//--------------------------edit item--------------------------
if (isset($edittab)){
	// print_r($_POST);
	$temp->giftid = 'new';
$sql = "SELECT * FROM List WHERE giftid = " . $_POST["giftid"];

$result = mysql_query($sql);
while($row = mysql_fetch_object($result))
			{
				$temp->Title = $row->Title;
				$temp->Price = $row->Price;
				$temp->URL = $row->URL;
				$temp->Description = $row->Description;
				$temp->giftid = $_POST["giftid"];
			}
			//edit item
			echo "<div class='container'>
					<h2>Edit gift idea</h2>
					<form  method='post' style='font-size: 110%;'>


					<div class='box'>
					  <div class='details'>
								<input class='title' type='text' id='title' placeholder='Title' name='title' style='width:100%' value='$temp->Title'>
								<input class='price' type='text' id='price' placeholder='Price' name='price' style='width:100%' value='$temp->Price'>
								</br>
								<input type='text' id='url' name='url' placeholder='Web Link' style='width:100%;color:#006621;text-decoration:underline;' value='$temp->URL'>
								<textarea class='description' name='description'  placeholder='Please add some details or a description.' style='height:100px;width:100%'>$temp->Description</textarea>
							</div>
								<input type='submit' class='icon tick' name='saveedit' value=''>
					</div>
					<input type='hidden' style='display:none' name='giftid' value='$temp->giftid'>​
					</form>";

}

echo '</div>';




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
</div>
</body></html>
