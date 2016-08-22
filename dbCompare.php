<?php
	$host = "192.168.148.199";
	$user = "root";
	$password = "password";
	$db1 = "dev1";
	$db2 = "dev2";
	$table = "field_data_field_art";
	$index = "field_art_tid";
	$reference = "entity_id";
	

	// Create connection
	$conn = new mysqli($host, $user, $password, $db1);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 

	$sql = "
	    SELECT a.$index, b.$reference
	    FROM    `dev1`.$table a, `dev2`.`$table` b
	    WHERE  a.$reference = b.$reference AND a.$index!=b.$index
	";
	//$sql = "show tables from `d7`"; //Tables_in_dev1
	$sql = "show index from `$db1`.$table";// Column_name // [Key_name] => PRIMARY
	/*$sql = "SELECT a.entity_type , b.entity_type AS bentity_type, a.entity_id , b.entity_id AS bentity_id, a.deleted , b.deleted AS bdeleted, a.delta ,
	b.delta AS bdelta, a.language , b.language AS blanguage, a.entity_type , b.entity_type AS bentity_type, a.bundle , b.bundle AS bbundle, a.deleted ,
	b.deleted AS bdeleted, a.entity_id , b.entity_id AS bentity_id, a.revision_id , b.revision_id AS brevision_id, a.language , b.language AS blanguage,
	a.field_art_tid , b.field_art_tid AS bfield_art_tid FROM field_data_field_art a, `dev2`.`field_data_field_art` b WHERE (a.entity_type!=b.entity_type
	OR a.deleted!=b.deleted OR a.delta!=b.delta OR a.language!=b.language OR a.entity_type!=b.entity_type OR a.bundle!=b.bundle OR a.deleted!=b.deleted OR
	a.revision_id!=b.revision_id OR a.language!=b.language OR a.field_art_tid!=b.field_art_tid ) AND a.entity_id = b.entity_id AND NOT EXISTS 
	(SELECT * FROM `dev2`.`field_data_field_art` bb WHERE (a.entity_type=bb.entity_type AND a.entity_id=bb.entity_id AND a.deleted=bb.deleted AND
	a.delta=bb.delta AND a.language=bb.language AND a.entity_type=bb.entity_type AND a.bundle=bb.bundle
	AND a.deleted=bb.deleted AND a.entity_id=bb.entity_id AND a.revision_id=bb.revision_id AND a.language=bb.language AND 
	a.field_art_tid=bb.field_art_tid ) LIMIT 1) LIMIT 1 ";*/
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
