/* enable strict mode */
"use strict";

var redipsProcedures;   // function sets dropMode parameter
// redips initialization
redipsProcedures = function () {
   // reference to the REDIPS.drag lib
   var rd = REDIPS.drag;
   // initialization
   rd.init();

   rd.event.rowDroppedBefore = function (sourceTable, sourceRowIndex) {
      var pos = rd.getPosition();
      var old_index = sourceRowIndex;
      var new_index = pos[1];
      var container = document.getElementById('plugin_fields_containers_id').value;
	  var valores = "";
	  
      jQuery.ajax({
         type: "POST",
         url: "../ajax/reorder.php",
         data: {
            old_order:     old_index,
            new_order:     new_index,
            container_id:  container
         },
	   success: function(data){
		
		   	  var orden="";
			  var orden_salto="" ; 
			  var index=1;
		    $("#drag").find(".linea").each(function() {

        var $me = $(this);
        var tipo = $me.prop("tagName");
        var id= $me.attr("id");
        var name = $me.attr("name");
		var elemento = $("#elemento_"+id);       
		       

		if (elemento.text()=="Condición" || elemento.text()=="Salto") {
			
			orden_salto = orden_salto+id+"|";
			
		}
		
		$me.html(index);
		orden = orden+id + "," +$me.html()+"|";
		index++;
        }); 
		
		//alert(orden_salto);
		
		      jQuery.ajax({
         type: "POST",
         url: "../ajax/reorder.php",
         data: {
            old_order:     old_index,
            new_order:     new_index,
            container_id:  container,
			orden:         orden,
			orden_salto:   orden_salto,
         },
	   success: function(data){
	   //alert(data);
		$(".msg2").html(data);
		
		}
      })
      .fail(function() {
         return false;
      });
		
		   
  //  alert(data);
  }
      })
      .fail(function() {
         return false;
      });
   }
};
