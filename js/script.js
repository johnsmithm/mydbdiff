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


  $("input[name=data]").click(function(){
  		var data={'action':'data'};
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
     // return;
  		$.ajax({
				  type: "GET",
				  url: "http://localhost/mydbdiff/function.php",
				  data: data,
				  success: function(tables){
            tables = JSON.parse(tables);
            console.log(tables);
				  	var ac = $('<div id="accordion"></div>');

            console.log(tables.length);
				  	for(var i =0;i< tables.length;++i){
				  		addArcodion(ac,tables[i]['name'],tables[i]['what']);
				  	}
					  $("#contentTable").html(ac);
					  $( "#accordion" ).accordion();
				  }
				});
  });
});