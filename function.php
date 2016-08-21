<?php

	
	function getTables($db, $conn){
		$tables = array();
		$sql = "show tables from `$db`"; 
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
		    	$tables[] = $row["Tables_in_$db"];
		    }
		}
		return $tables;
	}

	function getFields($table,$db,$conn){
		$fields = array();
		$index = "";
		$sql = "show index from `$db`.$table"; 
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
		    	if($row['Key_name']=='PRIMARY')
		    		$index = $row["Column_name"];
		    	$fields[] = $row["Column_name"];
		    }
		}
		return array($index,$fields);
	}

	function getDiff($table, $fields, $index, $db2, $conn){
		$diff = array();
		$condition = array();
		foreach($fields as $field){
			if($field!=$index)
				$condition[] = "a.$field!=b.$field ";
		}
		if(count($condition)!=0){
			$condition = '('.implode(" OR ", $condition).")";
		}
		else{
			$condition = "";
		}
		$sql = "
	    SELECT *
	    FROM   $table a, `$db2`.`$table` b
	    WHERE  $condition AND a.$index = b.$index
		";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
		    	$diff[] = $row[$index];
		    }
		}
		return $diff;
	}

	$host = $_REQUEST["host"];
	$user = $_REQUEST["user"];
	$password = $_REQUEST["password"];
	$db1 = $_REQUEST["db1"];//dev
	$db2 = $_REQUEST["db2"];//life
	

	// Create connection
	$conn = new mysqli($host, $user, $password, $db1);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 

	$result = "";

	switch($_REQUEST["action"]){
		case  "data" :
			
			$tablesDB1 = getTables($db1,$conn);
			$tablesDB2 = getTables($db2,$conn);

			$newTable = array_diff($tablesDB1,$tablesDB2);
			$dropTable = array_diff($tablesDB2,$tablesDB1);
			$result = array();
			///(0)
			foreach ($tablesDB1 as  $value) {
				//echo $value.'<br/>';
				if(!in_array($value, $tablesDB2)){
					$result[] = array('name'=>$value, 'what'=>"table does not exists!");
					continue;
				}
				list($index,$fieldsDB1) = getFields($value,$db1,$conn);
				list($index,$fieldsDB2) = getFields($value,$db2,$conn);		
					
				$fd1 = array_diff($fieldsDB1,$fieldsDB2);
				$fd2 = array_diff($fieldsDB2,$fieldsDB1);
				if(count($fd1) != 0 || count($fd2)!=0){
					$result[] = array('name'=>$value, 'what'=>"fields does not exists!");
					continue;
				}
				$diff = getDiff($value,$fieldsDB2,$index,$db2,$conn);
				if(count($diff)!=0){
					$result[] = array('name'=>$value, 'what'=>"fields diff does exist!");
				}
			}
			
			

		break;
		case 'tables':
			$result = getTables($db1,$conn);
		break;
		case "table":
				$tablesDB2 = getTables($db1,$conn);
				$value = $_REQUEST['table'];
				if(!in_array($value, $tablesDB2)){
					$result = array('name'=>$value, 'what'=>"table does not exists!");
					break;
				}
				list($index,$fieldsDB1) = getFields($value,$db1,$conn);
				list($index,$fieldsDB2) = getFields($value,$db2,$conn);		
					
				$fd1 = array_diff($fieldsDB1,$fieldsDB2);
				$fd2 = array_diff($fieldsDB2,$fieldsDB1);
				if(count($fd1) != 0 || count($fd2)!=0){
					$result = array('name'=>$value, 'what'=>"fields does not exists!");
					break;
				}
				$diff = getDiff($value,$fieldsDB2,$index,$db2,$conn);
				if(count($diff)!=0){
					$result = array('name'=>$value, 'what'=>"fields diff does exist!");
				}
		break;
	}

	$conn->close();
	exit(json_encode($result));
	
  	 

	
?>