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

	function getIndex($fields, $table, $db, $conn){
		//find primary and unique and autoicriment
			//add a sort criteria auto>primary>unique>hasID>others
				// loop over
					//if the index is unique in both table take is as index!!!

	}

	function getFields($table,$db,$conn){
		$possibleIndex = array('rid','id','uuid','entity_id','uid');
		$fields = array();
		$index = "";
		$sql = "DESCRIBE `$db`.$table"; 
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
		    	if(strpos('id', $row["Field"]) && $row['Null']=='NO')
		    		$index = $row["Field"];
				if($index == "" && $row['Key']=='PRI')
					$index = $row["Field"];
				//if($row['Key_name']==$row['Column_name'])
					$fields[] = $row["Field"];
				//echo "<pre>";
				//print_r($row);
				//echo "</pre>";
		    }
		}
		foreach($possibleIndex as $id)
			if(in_array($id,$fields))
					$index = $id;
		if(in_array('uuid',$fields))
				$index = 'uuid';
		if($index == "" && count($fields)>0)
			$index = $fields[0];
		$temp = array_unique($fields);
		$fields = array();
		foreach($temp as $v)
			$fields[]=$v;
		return array($index,$fields);
	}
	
	function getCreateTable($table,$conn){
		$sql = "SHOW CREATE TABLE $table";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {			
		    while($row = $result->fetch_assoc()) {
		    	return $row['Create Table'];				
		    }
		}
		return null;
	}

	function getDiff($table, $fields, $index, $db2, $conn,$offset, $limit){
	
		if($index == '')
			return null;
		$diff = array();
		$condition = array();
		$notCondition = array();
		$select = '';$selectA="";$selectB="";
		foreach($fields as $i=>$field){
			if($field!=$index)
				$condition[] = "COALESCE(a.$field,'')!=COALESCE(b.$field,'') ";
			$notCondition[] = "COALESCE(b.$field,'')=COALESCE(aa.$field,'') ";
			$select .= " b.$field AS b$field, a.$field ";
			$selectA .= " '' AS b$field, aaa.$field";
			$selectB .= " bbb.$field AS b$field, '' AS $field";
			if($i<count($fields)-1){
				$select .= ', ';
				$selectA .= ', ';
				$selectB .= ', ';
			}
		}
		if(count($condition)!=0){
			$condition = '('.implode(" OR ", $condition).")";
			$notCondition = implode(" AND ", $notCondition);
		}
		else{
			$condition = "";
			$notCondition  = '';
		}
		$notConditionB = str_replace('aa.','bb.',str_replace('b.','a.',$notCondition));
		$limitString = " LIMIT $limit OFFSET $offset";
		if($limit == 0){
			$limitString = "";
			$select = " COUNT(a.$index) AS mediathekTableRowsNumber ";
			$selectA = " COUNT(aaa.$index) AS mediathekTableRowsNumber ";
			$selectB = " COUNT(bbb.$index) AS mediathekTableRowsNumber ";
		}
		//changed
		$sql = "
	    SELECT $select
	    FROM   $table a, `$db2`.`$table` b
	    WHERE  $condition AND a.$index = b.$index AND 
		NOT EXISTS  (SELECT * FROM  `$table` aa WHERE  $notCondition LIMIT 1)  AND
		NOT EXISTS  (SELECT * FROM  `$db2`.`$table` bb WHERE  $notConditionB LIMIT 1) 
		";

		$notConditionA = str_replace('aa.','aaa.',str_replace('b.','bb1.',$notCondition));
		$notConditionA1 = str_replace('aa.','aaa.',str_replace('b.','a.',$notCondition));
		//new
		$sqlA = "
		SELECT  $selectA
		FROM  $table AS aaa 
		WHERE (NOT EXISTS (SELECT bb1.$index FROM  `$db2`.`$table` AS bb1 WHERE  $notConditionA)) AND 
		(NOT EXISTS (
			SELECT a.$index
		    FROM   $table AS a, `$db2`.`$table` AS b
		    WHERE  $condition AND a.$index = b.$index  
		    AND $notConditionA1 AND
			NOT EXISTS  (SELECT * FROM  $table AS aa WHERE  $notCondition LIMIT 1)  AND
			NOT EXISTS  (SELECT * FROM  `$db2`.`$table` AS bb WHERE  $notConditionB LIMIT 1) LIMIT 1
		))
		";
		
		//echo $sqlA."<br/>";
		$notConditionA = str_replace('aa.','aa1.',str_replace('b.','bbb.',$notCondition));
		$notConditionA1 = str_replace('aa.','bbb.',str_replace('b.','b.',$notCondition));		
		//drop
		$sqlB = "
		SELECT  $selectB
		FROM $table aaa , `$db2`.`$table` bbb
		WHERE NOT EXISTS (SELECT * FROM  `$table` aa1 WHERE  $notConditionA LIMIT 1) AND 
		NOT EXISTS (
			SELECT a.$index
		    FROM   $table a, `$db2`.`$table` b
		    WHERE  $condition AND a.$index = b.$index  
		    AND $notConditionA1 AND
			NOT EXISTS  (SELECT * FROM  `$table` aa WHERE  $notCondition LIMIT 1)  AND
			NOT EXISTS  (SELECT * FROM  `$db2`.`$table` bb WHERE  $notConditionB LIMIT 1)LIMIT 1
		)  
		";//use grup by - for distinct rows

		//echo $sqlB."<br/>";
		//echo $index.'<br/>';
		//echo $sql;
		//return null;

		$bigSql = "SELECT * FROM (( ".$sqlA." ) UNION  ( ".$sqlB.")  UNION (".$sql.")) AS t $limitString";
	
		if(count($fields)==0)
			return null;
		if(count($fields)==1){		
		
			$sqlA = "SELECT $selectA
		    FROM   $table aaa
		    WHERE  NOT EXISTS  (SELECT * FROM  `$db2`.`$table` bbb1 WHERE   aaa.$index=bbb1.$index LIMIT 1) ";
			
			$sqlB="SELECT $selectB
		    FROM   `$db2`.`$table` bbb
		    WHERE  NOT EXISTS  (SELECT * FROM  `$table` aaa1 WHERE   aaa1.$index=bbb.$index LIMIT 1) 
			
		     ";

		$bigSql = "SELECT * FROM (( ".$sqlA." ) UNION  ( ".$sqlB.") ) AS t $limitString";
	
		}
		//echo $bigSql."<br/>";
		
		if($limit == 0){
			if(count($fields)==1)
				$bigSql = "SELECT SUM(mediathekTableRowsNumber) AS mediathekTableRowsNumber1 FROM (( ".$sqlA." ) UNION  ( ".$sqlB.")) AS t $limitString";
			else 
				$bigSql = "SELECT SUM(mediathekTableRowsNumber) AS mediathekTableRowsNumber1 FROM (( ".$sqlA." ) UNION  ( ".$sqlB.")  UNION (".$sql.")) AS t $limitString";
	
			$result = $conn->query($bigSql);
			$data = $result->fetch_assoc();
			
			if($data['mediathekTableRowsNumber1'] != '0')
				$diff[] = $data['mediathekTableRowsNumber1'];
						
		}else {
			//echo 1;
			$result = $conn->query($bigSql);
			if ($result->num_rows > 0) {
				
			    while($row = $result->fetch_assoc()) {
			    	$diff[] = $row;
					/*echo "<pre>";
					print_r($row);
					echo "</pre>";*/ 
			    }
			}
		}
		return $diff;
	}

	$host = $_REQUEST["host"];
	$user = $_REQUEST["user"];
	$password = $_REQUEST["password"];
	$db1 = $_REQUEST["db1"];//life
	$db2 = $_REQUEST["db2"];//dev2
	

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
				
				$diff = getDiff($value,$fieldsDB2,$index,$db2,$conn,0,1);
				if(count($diff)!=0){
					$result[] = array('name'=>$value, 'what'=>"fields diff does exist!");
				}
			}
			
			

		break;
		case 'tables':
			$result = getTables($db1,$conn);
			$path = 'diff/'.$_REQUEST['fileName'];
			$sql = 'SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\nSET time_zone = \"+00:00\"; \n -- Diff data';
			exec("echo \"$sql\" > $path");
			exec("echo \"\n\" >> $path");

		break;
		case "table":
				$tablesDB2 = getTables($db2,$conn);
				$value = $_REQUEST['table'];
				if(!in_array($value, $tablesDB2)){
					$result = array('name'=>$value, 'what'=>"table");
					break;
				}
				list($index,$fieldsDB1) = getFields($value,$db1,$conn);
				list($index,$fieldsDB2) = getFields($value,$db2,$conn);		
					
				$fd1 = array_diff($fieldsDB1,$fieldsDB2);
				$fd2 = array_diff($fieldsDB2,$fieldsDB1);
				if(count($fd1) != 0 || count($fd2)!=0){
					$result = array('name'=>$value, 'what'=>"field");
					break;
				}
				/*echo "<pre>";
				print_r($fieldsDB2);
				echo "</pre>";*/
				$diff = getDiff($value,$fieldsDB2,$index,$db2,$conn,0,0);
				if(count($diff)!=0){
					$result = array('name'=>$value, 'what'=>"data:".$index.':'.$diff[0]);
				}
		break;
		case "diffTable" :
				$tablesDB2 = getTables($db2,$conn);
				$value = $_REQUEST['table'];
				$offset =  $_REQUEST['offset'];
				$range =  $_REQUEST['range'];
				$result = array('what'=>"nothing");
				if(!in_array($value, $tablesDB2)){					
					list($index,$fieldsDB1) = getFields($value,$db1,$conn);
					$result = array('name'=>$value, 'what'=>"notable", 'new'=>$fieldsDB1, 'drop' => array());
					break;
				}
				list($index,$fieldsDB1) = getFields($value,$db1,$conn);
				list($index,$fieldsDB2) = getFields($value,$db2,$conn);		
					
				$fd1 = array_diff($fieldsDB1,$fieldsDB2);
				$fd2 = array_diff($fieldsDB2,$fieldsDB1);
				if(count($fd1) != 0 || count($fd2)!=0){
					$result = array('name'=>$value, 'what'=>"nofields", 'new'=>$fd1, 'drop' => $fd2);
					break;
				}
				$diff = getDiff($value,$fieldsDB2,$index,$db2,$conn,$offset,$range);
				if(count($diff)!=0){
					$result = array('fields'=>$fieldsDB2, 'diff'=>$diff,'what'=>"diff");
				}
		break;
		case "tableDiffExport":
				$value = $_REQUEST['table'];
				$offset =  $_REQUEST['offset'];
				$range =  $_REQUEST['range'];
				$result = array('what'=>"nothing");
				$path = 'diff/'.$_REQUEST['fileName'];
				$tablesDB2 = getTables($db2,$conn);
				if(!in_array($value, $tablesDB2)){					
					$sql = addcslashes  (getCreateTable($value,$conn),"`");
					
					exec("echo \"-- Creating the table $value\" >> $path");			
					exec("echo \"$sql;\" >> $path");
					exec("echo \"\n\" >> $path");
					break;
				}
				list($index,$fieldsDB1) = getFields($value,$db1,$conn);
				list($index,$fieldsDB2) = getFields($value,$db2,$conn);		
					
				$fd1 = array_diff($fieldsDB1,$fieldsDB2);
				$fd2 = array_diff($fieldsDB2,$fieldsDB1);
				if(count($fd1) != 0 || count($fd2)!=0){
					$result = array('name'=>$value, 'what'=>"nofields", 'new'=>$fd1, 'drop' => $fd2);
					break;
				}
				$diff = getDiff($value,$fieldsDB2,$index,$db2,$conn,$offset,$range);
				if(count($diff)!=0){
					$result = array('fields'=>$fieldsDB2, 'diff'=>$diff,'what'=>"diff");
					//make updates
				}
		break;
	}

	$conn->close();
	exit(json_encode($result));
	
  	 

	
?>