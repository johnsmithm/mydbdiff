<?php
include("fucntion.php");

?>

<!doctype html>
<html lang="us">
<head>
	<meta charset="utf-8">
	<title>jQuery UI Example Page</title>
	<link href="jquery-ui-1.12.0.custom/jquery-ui.css" rel="stylesheet">
	<style>
	body{
		font-family: "Trebuchet MS", sans-serif;
		margin: 50px;
	}
	
	</style>
</head>
<body>

<h1>Welcome to MI-dbDiff!</h1>

<div class="ui-widget">
	<label>Host</label><br/><input type="text" name ="host"><br/>
	<label>User</label><br/><input type="text" name ="user"><br/>
	<label>Password</label><br/><input type="text" name ="password"><br/>
	<label>DB1</label><br/><input type="text" name ="db1"><br/>
	<label>DB2</label><br/><input type="text" name ="db2"><br/>
	<p>Exclude tables, separates by comma<p/>
	<textarea></textarea><br/>
<!--	<input type="submit" name="str" value ="Get Structure diff">-->
	<input type="submit" name="data" value ="Get Data diff">
</div>




<!-- Accordion -->
<div id="contentTable"></div>



<script src="jquery-ui-1.12.0.custom/external/jquery/jquery.js"></script>
<script src="jquery-ui-1.12.0.custom/jquery-ui.js"></script>
<script src="js/script.js"></script>

</body>
</html>
