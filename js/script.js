$( document ).ready(function() {
  
  

  function addArcodion(ac, table, what){
  	var h2 = $("<h2></h2>").append(table);
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
          url: "http://localhost/mydbdiff/function.php",
          data: data,
          success: function(tables){
            tables = JSON.parse(tables);
            console.log(tables);         
            if(tables != "")   {
              addArcodion(ac,tables['name'],tables['what']);
              $( "#accordion" ).accordion( "refresh" );
            }
            if(counter<tablesD.length)  
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
        data['password'] = "123456";
        data['db1']      = "dev1";
        data['db2']      = "d7";
        data['host']     = "localhost";
      }

      console.log(data);
      var counter = 0;
      var tablesD = [];
     // return;
  		$.ajax({
          type: "GET",
          url: "http://localhost/mydbdiff/function.php",
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
});