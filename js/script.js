  var urlGlobal = "http://localhost/mydbdiff/function.php";
 function showText(obj){
    alert("todo: use small cost edit algorithm!");
    $( "#dialogText" ).html($(obj).next().html()).dialog( "open" );
 }
 function makeTable(diff){
	 console.log(diff);
	 var table = $('<table>').attr('border','1');
	 var tr = $('<tr>');
	 for(var i=0;i<diff['fields'].length;++i)
		 tr.append($('<th>').text(diff['fields'][i]));
   tr.append($('<th>').text("Option"));
	 table.append(tr);
	 for(var j=0;j<diff['diff'].length;++j){
		 var tr = $('<tr>');
		for(var i=0;i<diff['fields'].length;++i){      
			var a = diff['diff'][j][diff['fields'][i]], b = diff['diff'][j]['b'+diff['fields'][i]];
      if((typeof a === 'string' || a instanceof String) && (a.length>100 || b.length>100)){
        var aa="<span onclick='showText(this)' style='color:"+(a!=b?"red":"blue")+";cursor:pointer;'>view</span>";
        aa+= "<span style='display:none;'>" +a+"###" + b + "</span>";
        a = aa;
      }else	if(a!=b)
        a='<span style="color:green">'+a+'</span>/<span style="color:red">'+b+'</span>';
			tr.append($('<td>').html(a));
		}
    tr.append($('<td>').html("<input type='checkbox' checked='checked' onclick=''>"));
		table.append(tr);
	 }
	 return table;
 }
 
 function showTableDiff(obj){
	  var name = $(obj).text();
	  
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
            $( "#dialog" ).append('<input type="text" name="pager">');
            $( "#dialog" ).append('<button onclick="makeSqlFile();" id="button">Next</button>');
            $( "#dialog" ).append('<button onclick="makeSqlFile();" id="button">Make sql file - all</button>');
            $( "#dialog" ).append('<button onclick="makeSqlFile();" id="button">Make sql file - current</button>');
            $( "#dialog" ).attr('title',name).dialog( "open" );
          }
        });
  }

  function makeSqlFile(){
    alert("todo");
  }

$( document ).ready(function() {
 
  function addList(ac, table, what){
  	var span = "<span onclick='showTableDiff(this)' style='color:blue;cursor:pointer'>"+
    table+"</span>";
  	var div = $("<div>").append('<input class="checkallInsideTable" checked="cheched" type="checkbox" name="'
  		+table+'checkallname"" id="'
  		+table+'checkall">'+span+"<span style=''>["+what+"]</span>");  	
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
              addList(ac,tables['name'],tables['what']);
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
            var ac = $('<ul id="checkboxList"></ul>');
            $("#contentTable").html(ac);
            if(counter < tablesD.length){
              recursiveDiff(counter,tablesD,ac,data);
            }
            console.log(tablesD.length);           
            $("#contentTable").append('<button onclick="makeSqlFile();" id="button">Make sql file</button>');
          }
        });
  });
  
  $( "#dialog,#dialogText" ).dialog({
		autoOpen: false,
		width: 800,
    height: 600,
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