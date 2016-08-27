<?php
	$host = "localhost";
	$user = "root";
	$password = "123456";
	$db1 = "d7";
	$db2 = "d7-copy";
	$table = "users";
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
	$sql = "DESCRIBE `$db1`.$table";
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
	$sql = "
	SELECT '' AS buid, aaa.uid, '' AS bname, aaa.name, '' AS bpass, aaa.pass, ''
	 AS bmail, aaa.mail, '' AS btheme, aaa.theme, '' AS bsignature, aaa.signature, '' 
	 AS bsignature_format, aaa.signature_format, '' AS bcreated, aaa.created, '' AS baccess,
	  aaa.access, '' AS blogin, aaa.login, '' AS bstatus, aaa.status, '' AS btimezone, aaa.timezone, 
	  '' AS blanguage, aaa.language, '' AS bpicture, aaa.picture, '' AS binit, aaa.init, '' AS bdata, 
	  aaa.data FROM `d7-copy`.`users` bbb, users aaa WHERE NOT EXISTS (SELECT * FROM `d7-copy`.`users` 
	  	bb1 WHERE bb1.uid=aaa.uid AND bb1.name=aaa.name AND bb1.pass=aaa.pass AND bb1.mail=aaa.mail AND 
	  	bb1.theme=aaa.theme AND bb1.signature=aaa.signature AND bb1.signature_format=aaa.signature_format 
	  	AND bb1.created=aaa.created AND bb1.access=aaa.access AND bb1.login=aaa.login AND bb1.status=aaa.status 
	  	AND bb1.timezone=aaa.timezone AND bb1.language=aaa.language AND bb1.picture=aaa.picture AND 
	  	bb1.init=aaa.init AND bb1.data=aaa.data LIMIT 1) AND NOT EXISTS ( SELECT a.uid FROM 
	  	users a, `d7-copy`.`users` b WHERE (a.name!=b.name OR a.pass!=b.pass OR a.mail!=b.mail OR 
	  		a.theme!=b.theme OR a.signature!=b.signature OR a.signature_format!=b.signature_format OR
	  		 a.created!=b.created OR a.access!=b.access OR a.login!=b.login OR a.status!=b.status OR
	  		  a.timezone!=b.timezone OR a.language!=b.language OR a.picture!=b.picture OR a.init!=b.init OR 
	  		  a.data!=b.data ) AND a.uid = b.uid AND a.uid=aaa.uid AND a.name=aaa.name AND a.pass=aaa.pass AND
	  		   a.mail=aaa.mail AND a.theme=aaa.theme AND a.signature=aaa.signature AND
	  		    a.signature_format=aaa.signature_format AND a.created=aaa.created AND 
	  		    a.access=aaa.access AND a.login=aaa.login AND a.status=aaa.status AND 
	  		    a.timezone=aaa.timezone AND a.language=aaa.language AND a.picture=aaa.picture AND 
	  		    a.init=aaa.init AND a.data=aaa.data AND NOT EXISTS (SELECT * FROM `users` aa WHERE 
	  		    	b.uid=aa.uid AND b.name=aa.name AND b.pass=aa.pass AND b.mail=aa.mail AND 
	  		    	b.theme=aa.theme AND b.signature=aa.signature AND b.signature_format=aa.signature_format
	  		    	 AND b.created=aa.created AND b.access=aa.access AND b.login=aa.login AND 
	  		    	 b.status=aa.status AND b.timezone=aa.timezone AND b.language=aa.language AND
	  		    	  b.picture=aa.picture AND b.init=aa.init AND b.data=aa.data LIMIT 1) AND 
NOT EXISTS (SELECT * FROM `d7-copy`.`users` bb WHERE a.uid=bb.uid AND a.name=bb.name AND
 a.pass=bb.pass AND a.mail=bb.mail AND a.theme=bb.theme AND a.signature=bb.signature AND 
 a.signature_format=bb.signature_format AND a.created=bb.created AND a.access=bb.access AND 
 a.login=bb.login AND a.status=bb.status AND a.timezone=bb.timezone AND a.language=bb.language AND 
		a.picture=bb.picture AND a.init=bb.init AND a.data=bb.data LIMIT 1)LIMIT 1 ) LIMIT 9 OFFSET 0 
	";
	$sql="
SELECT bbt.uid FROM `d7-copy`.`users`  bbt LEFT JOIN `d7`.`users`  aaa ON bbt.uid=aaa.uid WHERE  
bbt.name=aaa.name AND bbt.pass=aaa.pass AND bbt.mail=aaa.mail AND bbt.theme=aaa.theme AND
 bbt.signature=aaa.signature AND bbt.created=aaa.created 
 AND bbt.access=aaa.access AND bbt.login=aaa.login AND bbt.status=aaa.status
 AND bbt.language=aaa.language AND bbt.picture=aaa.picture AND bbt.init=aaa.init
	";//AND bbt.signature_format=aaa.signature_format  AND bbt.data=aaa.data  AND bbt.timezone=aaa.timezone 
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
