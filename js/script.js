  var urlGlobal = "http://localhost/mydbdiff/function.php";
 
 // Compute the edit distance between the two given strings
function getEditDistance(a, b) {
  console.log(a);
  console.log(b);
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
  console.log(matrix[b.length][a.length]);
  console.table(matrix);

  var edit = "", ne="", de="";
  i = a.length-1; j = b.length-1;

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
    }else{
      if((matrix[j][i+1]-1) == matrix[j+1][i+1]){
        if(ne!=""){
          edit="<span style='color:red' >"+ne+'</span>'+edit;
          ne = "";
        }
        de = b[j]+de;
        j--;
      }else if((matrix[j+1][i]-1)==matrix[j+1][i+1]){
        if(de!=""){
          edit="<span style='color:red' >"+de+'</span>'+edit;
          de = "";
        }
        ne = a[i]+ne;
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
        ne = a[i]+ne;
        de = b[j]+de;
        --j;
        --i;
      }
    }
    console.log(edit);
  }
  for(;i>=0;--i)ne=a[i]+ne;
  if(ne!="")
    edit="<span style='color:green' >"+ne+'</span>'+edit;
  for(;j>=0;--j)de=b[j]+de;
  if(de!="")
    edit="<span style='color:red' >"+de+'</span>'+edit;
  return edit;
  //return matrix[b.length][a.length];
}

 function showText(obj){
    //alert("todo: use small cost edit algorithm!");
    var a = $(obj).next().html();
    var b = a.split("###");
    var base = difflib.stringAsLines(b[0]);
    var newtxt = difflib.stringAsLines(b[1]);

    // create a SequenceMatcher instance that diffs the two sets of lines
    var sm = new difflib.SequenceMatcher(base, newtxt);

    // get the opcodes from the SequenceMatcher instance
    // opcodes is a list of 3-tuples describing what changes should be made to the base text
    // in order to yield the new text
    var opcodes = sm.get_opcodes();
    var contextSize = null;

    // build the diff view and add it to the current DOM
    $("#dialogText").html(diffview.buildView({
        baseTextLines: base,
        newTextLines: newtxt,
        opcodes: opcodes,
        // set the display titles for each resource
        baseTextName: "Base Text",
        newTextName: "New Text",
        contextSize: contextSize,
        viewType: true ? 1 : 0
    })).append(getEditDistance(b[0],b[1]));
    $( "#dialogText" ).dialog( "open" );
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
  console.log(getEditDistance("abcaionel","abbionel"));
});