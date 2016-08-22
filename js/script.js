  var urlGlobal = "http://192.168.148.199/mediathek/mydbdiff/function.php";
 
 function makeTable(diff){
	 console.log(diff);
	 var table = $('<table>').attr('border','1');
	 var tr = $('<tr>');
	 for(var i=0;i<diff['fields'].length;++i)
		 tr.append($('<th>').text(diff['fields'][i]));
	 table.append(tr);
	 for(var j=0;j<diff['diff'].length;++j){
		 var tr = $('<tr>');
		for(var i=0;i<diff['fields'].length;++i){
			var a = diff['diff'][j][diff['fields'][i]], b = diff['diff'][j]['b'+diff['fields'][i]];
			if(a!=b)a='<span style="color:green">'+a+'</span>/<span style="color:red">'+b+'</span>';
			tr.append($('<td>').html(a));
		}
		table.append(tr);
	 }
	 return table;
 }
 
 function showTableDiff(obj){
	  var name = $(obj).next().find('input[class=checkallInsideTable]').attr('name');
	  var arr = name.split('checkallname');
	  name = arr[0];
		var data = {};
		data['user']     = "root";
		data['table']     = name;
        data['password'] = "password";
        data['db1']      = "dev1";
        data['db2']      = "dev2";
        data['host']     = "192.168.148.199";
		data['action']='diffTable';
	  $.ajax({
          type: "GET",
          url: urlGlobal,
          data: data,
          success: function(diff){
            diff = JSON.parse(diff);
            console.log(diff); 
			$( "#dialog" ).html(diff['what'] == 'diff'? makeTable(diff):diff['what']);	
            $( "#dialog" ).dialog( "open" );
          }
        });
  }

$( document ).ready(function() {
 
  function addArcodion(ac, table, what){
  	var h2 = $("<h2></h2>").append(table).attr('onclick','showTableDiff(this)');
  	var div = $("<div>").append('<label for="'
  		+table+'checkall">Check All</label><input class="checkallInsideTable" type="checkbox" name="'
  		+table+'checkallname"" id="'
  		+table+'checkall"><br />'+what);
  	ac.append(h2);
  	ac.append(div);
  	return ac;
  }

  function recursiveDiff(counter,tablesD,ac,data){
    data['action'] = 'table';
    data['table'] = tablesD[counter];
    counter++;
    $.ajax({
          type: "GET",
          url: urlGlobal,
          data: data,
          success: function(tables){
            tables = JSON.parse(tables);
            console.log(tables);         
            if(tables != "")   {
              addArcodion(ac,tables['name'],tables['what']);
              $( "#accordion" ).accordion( "refresh" );
            }
            if(counter<tablesD.length && counter<1000)  
              recursiveDiff(counter,tablesD,ac,data);
          }
        });
  }


  $("input[name=data]").click(function(){
  		var data={'action':'tables'};
  		data['user']     = $("input[name=user]").val();
  		data['password'] = $("input[name=password]").val();
  		data['db1']      = $("input[name=db1]").val();
  		data['db2']      = $("input[name=db2]").val();
  		data['host']     = $("input[name=host]").val();

      if(data['host'] == ""){
        data['user']     = "root";
        data['password'] = "password";
        data['db1']      = "dev1";
        data['db2']      = "dev2";
        data['host']     = "192.168.148.199";
      }

      console.log(data);
      var counter = 0;
      var tablesD = [];
     // return;
  		$.ajax({
          type: "GET",
          url: urlGlobal,
          data: data,
          success: function(tables){
            tablesD = JSON.parse(tables);
            console.log(tablesD);
            var ac = $('<div id="accordion"></div>');
            $("#contentTable").html(ac);
            $( "#accordion" ).accordion();
            if(counter < tablesD.length){
              recursiveDiff(counter,tablesD,ac,data);
            }
            console.log(tablesD.length);           
            
          }
        });
  });
  
  $( "#dialog" ).dialog({
		autoOpen: false,
		width: 400,
		buttons: [
			{
				text: "Ok",
				click: function() {
					$( this ).dialog( "close" );
				}
			},
			{
				text: "Cancel",
				click: function() {
					$( this ).dialog( "close" );
				}
			}
		]
	});

});