<?php
include("fucntion.php");

?>

<!doctype html>
<html lang="us">
<head>
	<meta charset="utf-8">
	<title>jQuery UI Example Page</title>
	<link href="jquery-ui-1.12.0.custom/jquery-ui.css" rel="stylesheet">
	<link href="jsdifflib/diffview.css" rel="stylesheet">
	<style>
	body{
		font-family: "Trebuchet MS", sans-serif;
		margin: 50px;
	}
	/* Start by setting display:none to make this hidden.
	   Then we position it in relation to the viewport window
	   with position:fixed. Width, height, top and left speak
	   for themselves. Background we set to 80% white with
	   our animation centered, and no-repeating */
	.modal {
		display:    none;
		position:   fixed;
		z-index:    1000;
		top:        0;
		left:       0;
		height:     100%;
		width:      100%;
		background: rgba( 255, 255, 255, .8 ) 
					url('http://i.stack.imgur.com/FhHRx.gif') 
					50% 50% 
					no-repeat;
	}

	/* When the body has the loading class, we turn
	   the scrollbar off with overflow:hidden */
	body.loading {
		overflow: hidden;   
	}

	/* Anytime the body has the loading class, our
	   modal element will be visible */
	body.loading .modal {
		display: block;
	}
	</style>
</head>
<body>

<h1>Welcome to MI-dbDiff!</h1>

<div class="ui-widget">
	<label>Host</label><br/><input type="text" name ="host"><br/>
	<label>User</label><br/><input type="text" name ="user"><br/>
	<label>Password</label><br/><input type="text" name ="password"><br/>
	<label>DB1-dev(from)</label><br/><input type="text" name ="db1"><br/>
	<label>DB2-life(to)</label><br/><input type="text" name ="db2"><br/>
	<p>Exclude tables, separates by comma<p/>
	<textarea></textarea><br/>
<!--	<input type="submit" name="str" value ="Get Structure diff">-->
	<input type="submit" name="data" value ="Get Data diff">
</div>





<div id="contentTable"></div>
<!-- ui-dialog -->
<div id="dialog" title="Dialog Title">
	<p>Some random text</p>
</div>
<div id="dialogText" title="Dialog Title">
	<p>Some random text</p>
</div>

<div class="modal"><!-- Place at bottom of page --></div>

<script src="jquery-ui-1.12.0.custom/external/jquery/jquery.js"></script>
<script src="jquery-ui-1.12.0.custom/jquery-ui.js"></script>
<script src="jsdifflib/difflib.js"></script>
<script src="jsdifflib/diffview.js"></script>
<script src="js/script.js"></script>


</body>
</html>
