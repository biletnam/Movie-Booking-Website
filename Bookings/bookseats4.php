 <?php 
 session_start();
?>
<html>
<head>
	<title>Tickets</title>
	<style>
		* {
			font-size: 14px;
			font-family: arial;
		}
	</style>
</head>
<body>

<center>
<br/>
<br/>
<br/>
<?php


	if (isset($_POST['seats']))
	{
		//$user = $_SERVER['PHP_AUTH_USER'];
	
		$newStatusCode = $_POST['newStatusCode'];
		$oldStatusCode = $_POST['oldStatusCode'];

		// open database connection
		$linkID = @ mysql_connect("localhost", "root", "") or die("Could not connect to MySQL server");
		@ mysql_select_db("tickets4") or die("Could not select database");

		// prepare select statement
		$selectQuery = "SELECT rowId, columnId from seats where (";
		$count = 0;
		$i=0; //Copy this
		foreach($_POST['seats'] AS $seat) {
			if ($count > 0) {
				$selectQuery .= " || ";
			}
			$selectQuery .= " ( rowId = '" . substr($seat, 0, 1) . "'";
			$selectQuery .= " and columnId = " . substr($seat, 1) . " ) ";
			$count++;
			$arr[$i] = substr($seat, 0, 1).substr($seat, 1); //Copy this
			$i=$i+1;
		}
		$s = ""; //Copy this
		$arrlength = count($arr);  //Copy this
		for($i=0;$i<$arrlength;$i++)	//Copy this
		 $s = $s." ".$arr[$i]; //Copy this
			
		$_SESSION["seat"] = $s;  //Copy this
		$selectQuery .= " ) and status = $oldStatusCode";
		if ($oldStatusCode == 1) {
			$selectQuery .= " and updatedby = '$user'";
		}
		
		//echo $selectQuery;
		
		// execute select statement
		$result = mysql_query($selectQuery);
		//echo $result;
		
		$selectedSeats = mysql_num_rows($result);
		//echo "<br/>" . $selectedSeats;
		
		if ($selectedSeats != $count) {
			$problem = "<h3>Please reserve your seats</h3>";
			//$problem .= "Possible problems are:";
			//$problem .= "<ul>";
			//$problem .= "<li>Another process was able to book the same seat while you were still browsing.</li>";
			//$problem .= "<li>You were trying to Confirm an unreserved Seat.</li>";
			//$problem .= "<li>You were trying to Cancel an unreserved Seat.</li>";
			//$problem .= "<li>You were trying to Reserve a reserved Seat.</li>";
			//$problem .= "<li>There was a problem connecting to the database.</li>";
			//$problem .= "</ul>";
			$problem .= "<a href='seats4.php'>View Seat Plan</a>";
			
			die ($problem);
		}
		
		// prepare update statement
		$newStatusCode = $_POST['newStatusCode'];
		$oldStatusCode = $_POST['oldStatusCode'];
		
		$updateQuery = "UPDATE seats set status=$newStatusCode, updatedby='$user' where ( ";
		$count = 0;
		foreach($_POST['seats'] AS $seat) {
			if ($count > 0) {
				$updateQuery .= " || ";
			}
			$updateQuery .= " ( rowId = '" . substr($seat, 0, 1) . "'";
			$updateQuery .= " and columnId = " . substr($seat, 1) . " ) ";
			$count++;
		}
		$updateQuery .= " ) and status = $oldStatusCode";
		if ($oldStatusCode == 1) {
			$updateQuery .= " and updatedby = '$user'";
				
			
		}
		
		// perform update
		$result = mysql_query($updateQuery);
		$updatedSeats = mysql_affected_rows();

		if ($result && $updatedSeats == $count) {
			//$mysql->commit();
			echo "<h3>";
			echo "You have successfully updated $updatedSeats seat/s: ";
			echo "[";
			foreach($_POST['seats'] AS $seat) {
				$rowId = substr($seat, 0, 1);
				$columnId = substr($seat, 1);
				echo $rowId . $columnId . ", ";	
				
			}
			echo "]";
			echo "...</h3>";
		} else {
			//$mysql->rollback();
			echo "<h3>There was a problem executing your request. No seat/s were updated.</h3>";
			echo "Possible problems are:";
			echo "<ul>";
			echo "<li>Another process was able to book the same seat while you were still browsing.</li>";
			echo "<li>You were trying to Confirm an unreserved Seat.</li>";
			echo "<li>You were trying to Cancel an unreserved Seat.</li>";
			echo "<li>You were trying to Reserve a reserved Seat.</li>";
			echo "<li>There was a problem connecting to the database.</li>";
			echo "</ul>";
		}
		
		echo "<a href='seats4.php'>View Seat Plan</a>";
		
		// Enable the autocommit feature
		//$mysqldb->autocommit(TRUE);
		
		// Recuperate the query resources
		//$result->free();
		
		mysql_close();
		echo "<meta http-equiv='refresh' content='0;url=http://localhost/Project/Bookings/seats4.php' />";
	}



?>
</center>
</body>
</html>