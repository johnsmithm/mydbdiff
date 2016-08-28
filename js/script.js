  var urlGlobal = "function.php";
  var data = {}, fileName = "dev.sql";
 // Compute the edit distance between the two given strings
function getEditDistance(a, b) {
	console.log(a.length);
	console.log(b.length);
  if(a.length > 10000 && b.length > 10000)
	  return "<span style='color:red' >"+b+'</span>'+"<span style='color:green' >"+a+'</span>';
  var matrix = [];
  // increment along the first column of each row
  var i;
  for (i = 0; i <= b.length; i++) {
    matrix[i] = [i];
  }
  // increment each column in the first row
  var j;
  for (j = 0; j <= a.length; j++) {
    matrix[0][j] = j;
  }
  // Fill in the rest of the matrix
  for (i = 1; i <= b.length; i++) {
    for (j = 1; j <= a.length; j++) {
      if (b.charAt(i-1) == a.charAt(j-1)) {
        matrix[i][j] = matrix[i-1][j-1];
      } else {
        matrix[i][j] = Math.min(matrix[i-1][j-1] + 2, // substitution
                                Math.min(matrix[i][j-1] + 1, // insertion
                                         matrix[i-1][j] + 1)); // deletion
      }
    }
  }
  //console.log(matrix[b.length][a.length]);
  //console.table(matrix);

  var edit = "", ne="", de="";
  i = a.length-1; j = b.length-1;
  var Ade=[],Ane=[];

  while(i>=0 && j>=0){
    if(a[i]==b[j]){
      if(de!=""){
        edit="<span style='color:red' >"+de+'</span>'+edit;
        de = "";
      }
      if(ne!=""){
        edit="<span style='color:green' >"+ne+'</span>'+edit;
        ne = "";
      }
      edit=a[i]+edit;
      --i;--j;
	  Ade[j]=1;
	  Ane[i]=1;
    }else{
      if((matrix[j][i+1]+1) == matrix[j+1][i+1]){
        if(ne!=""){
          edit="<span style='color:red' >"+ne+'</span>'+edit;
          ne = "";
        }
        de = b[j]+de; Ade[j]=0;
        j--;
      }else if((matrix[j+1][i]+1)==matrix[j+1][i+1]){
        if(de!=""){
          edit="<span style='color:red' >"+de+'</span>'+edit;
          de = "";
        }
        ne = a[i]+ne;Ane[i]=0;
        i--;
      }else{
        if(de!=""){
          edit="<span style='color:red' >"+de+'</span>'+edit;
          de = "";
        }
        if(ne!=""){
          edit="<span style='color:green' >"+ne+'</span>'+edit;
          ne = "";
        }
        ne = a[i]+ne; Ane[i]=0;
        de = b[j]+de; Ade[j]=0;
        --j;
        --i;
      }
    }
  
  }
  for(;i>=0;--i)ne=a[i]+ne;
  if(ne!="")
    edit="<span style='color:green' >"+ne+'</span>'+edit;
  for(;j>=0;--j)de=b[j]+de;
  if(de!="")
    edit="<span style='color:red' >"+de+'</span>'+edit;

   
  return edit;
}

 function showText(obj){
	$("body").addClass("loading");
    var a = $(obj).next().html();
    var b = a.split("###");  
    $("#dialogText").empty().append(getEditDistance(b[0],b[1]));
    $( "#dialogText" ).dialog('option', 'title', $(obj).attr('fieldname')).dialog( "open" );
	$("body").removeClass("loading");
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
      if((typeof a === 'string' || a instanceof String) && (typeof b === 'string' || b instanceof String)
       && (a.length>100 || b.length>100)){
        var aa="<span onclick='showText(this)' fieldname='"+diff['fields'][i]+"' style='color:"+(a!=b?"red":"blue")+";cursor:pointer;'>view</span>";
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
 
 function showFields(diff){
	 var table = $('<table>').attr('border','1');
	 var tr = $('<tr>');
	 tr.append($('<th>').text("Fieldname"));
	 tr.append($('<th>').text("Option"));
	 table.append(tr);
	 for(var j=0;j<diff['new'].length;++j){
		var tr = $('<tr>');
		
		var a='<span style="color:green">'+diff['new'][j]+'</span>';
		tr.append($('<td>').html(a));
		
		tr.append($('<td>').html("<input type='checkbox' checked='checked' onclick=''>"));
		table.append(tr);
	 }
	 for(var j=0;j<diff['drop'].length;++j){
		var tr = $('<tr>');
		
		var a='<span style="color:red">'+diff['drop'][j]+'</span>';
		tr.append($('<td>').html(a));
		
		tr.append($('<td>').html("<input type='checkbox' checked='checked' onclick=''>"));
		table.append(tr);
	 }
	 return table;
 }
 
 function nextPage(obj){
	 var name = $(obj).attr('tableName');
	 var s = $('<p>').text(name);
	 showTableDiff(s);
 }
 
 function showTableDiff(obj){
	  var name = $(obj).text();
	  console.log(name);
		data['action']='diffTable';
		data['table']=name;
		data['offset']=0;
		data['range']=10;
		if($( "#dialog" ).find('input[name=pager]').length>0 && $( "#dialog" ).find('input[name=pager]').val()!=0){
			var a = $( "#dialog" ).find('input[name=pager]').val().split('-');
			if(a.length==2){
				data['offset']=a[0];
				data['range']=a[1];				
			}
		}
	  $.ajax({
          type: "GET",
          url: urlGlobal,
          data: data,
          success: function(diff){
            diff = JSON.parse(diff);
            console.log(diff); 
			if(diff['what']=='nothing'){
				alert('no records!');
				return;
			}
			$( "#dialog" ).html(diff['what'] == 'diff'? makeTable(diff):showFields(diff));	
			$( "#dialog" ).prepend('<p>range:'+data['range']+'; offset:'+data['offset']+'</p>');
            $( "#dialog" ).append('<input type="text" name="pager">');
            $( "#dialog" ).append('<button onclick="nextPage(this);" tableName="'+name+'" id="button">Next</button>');
            $( "#dialog" ).append('<button onclick="makeSqlFileAll(this);" tableName="'+name+'" id="button">Make sql file - all</button>');
            $( "#dialog" ).append('<button onclick="makeSqlFile();" id="button">Make sql file - current</button>');
            $( "#dialog" ).dialog('option', 'title', name).dialog( "open" );
          },
		  beforeSend: function() { $("body").addClass("loading");    },
		  complete: function() { $("body").removeClass("loading");  } 
        });
  }
  
  function makeSqlFileAll(obj){
	  var table = $(obj).attr('tableName');
	  data['action'] = 'table';
	  data['table'] = table;  
	  data['offset']=0;
	  data['range']=10;
	  data['fileName']=fileName;
	  $.ajax({
          type: "GET",
          url: urlGlobal,
          data: data,
          success: function(tables){
            tables = JSON.parse(tables);
            console.log(tables);         
            if(tables != "")   {
      				var nr = 1;
      				if(tables['what']!='NewTable' && tables['what']!='DropTable' && tables['what']!='field' ){
      					nr = tables['what'].split(':')[2];
      					console.log(nr);
      				}
      					data['action'] = 'tableDiffExport';
      					for(var i=0;i<nr;i+=10){
      						$.ajax({
      							  type: "GET",
      							  url: urlGlobal,
      							  data: data,
      							  success: function(result){},
      							  beforeSend: function() {
      								  // todo: do not close when we do not end the batch! $("body").find();
      								  $("body").addClass("loading");    
      								  },
      							  complete: function() {  
                       $("body").removeClass("loading"); 
                       alert(table+" was added to sql file "+fileName+" !!!");
                       $("ul#checkboxList span[tableClass="+table+"]").css("color","green");
                       } 
      						});
      						data['offset']+=10;
      					}
				
            }
          }
      });
  }

  function makeSqlFile(){
    alert("todo");
  }

$( document ).ready(function() {
 
  function addList(ac, table, what){
  	var span = "<span onclick='showTableDiff(this)' tableClass='"+table+"' style='color:blue;cursor:pointer'>"+
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
  	  data['action']='tables';
  		data['user']     = $("input[name=user]").val();
  		data['password'] = $("input[name=password]").val();
  		data['db1']      = $("input[name=db1]").val();
  		data['db2']      = $("input[name=db2]").val();
  		data['host']     = $("input[name=host]").val();      
      data['fileName'] = fileName;

      if(data['host'] == ""){
        data['user']     = "root";
        data['password'] = "123456";
        data['db1']      = "d7";//life
        data['db2']      = "d7-copy";//dev
        data['host']     = "localhost";
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