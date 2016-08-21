<?php
	$host = "localhost";
	$user = "root";
	$password = "123456";
	$db1 = "dev1";
	$db2 = "d7";
	$table = "role";
	$index = "name";
	$reference = "rid";
	

	// Create connection
	$conn = new mysqli($host, $user, $password, $db1);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 

	$sql = "
	    SELECT b.name, a.name
	    FROM    `dev1`.$table a, `d7`.`$table` b
	    WHERE  a.$reference != b.$reference AND a.$index != b.$index
	";
	//$sql = "show tables from `d7`"; //Tables_in_dev1
	//$sql = "show index from `d7`.$table";// Column_name // [Key_name] => PRIMARY
	$result = $conn->query($sql);
	echo "difference <br />";
	if ($result->num_rows > 0) {
	    // output data of each row
	    while($row = $result->fetch_assoc()) {
		echo "<pre>";
		print_r($row);
		echo "</pre>";
		//echo "id: " . $row["rid"]. " - Name: " . $row["name"].  "<br>";
	    }
	} else {
	    echo "0 results <br />";
	}


	$conn->close();

	/*exec('/var/www/html/mysql_coldiff-1_0/mysql_coldiff -uroot -p123456 -hlocalhost -i name d7.role dev1.role > /var/www/html/mysql_coldiff-1_0/diff/test', $output, $return);

	// Return will return non-zero upon an error
	if (!$return) {
		echo "<pre>";
		print_r($output);
		echo "</pre>";
	    echo "PDF Created Successfully";
	} else {
		echo "<pre>";
		print_r($output);
		echo "</pre>";
	    echo "PDF not created";
	}*/

/*
- check structure - and update it// no options!!! - have already??? 
- check data from each table and show the table with differences - easy - find the index field
	- option to see the differences in one table - easy - use 20 limit or 1 for blob data
	- option to check what tables to update - use table name - checkbox - checkall by default -
	- option to check what row in table to update - use row index - checkbox
	- option to check what fields in the row to update - use field name + row index - checkbox
	!!! - a lot of checks
*/

?>
