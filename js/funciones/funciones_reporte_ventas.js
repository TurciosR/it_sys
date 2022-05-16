$(document).ready(function() {

$('#formulario').validate({		
	    rules: {
                    fecha_inicio: {  
                    required: true,           
                     }, 
                    fecha_fin: {  
                    required: true, 
                    number: true,        
                     },  
                 },
       
});
 });                   
//function to round 2 decimal places
function round(value, decimals) {
    return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
    //round "original" to two decimals
	//var result=Math.round(original*100)/100  //returns 28.45
}

 function reload1(){
	location.href = 'admin_ventas.php';	
}

// actualize table 
$(document).on("click","#print1",function(){
	imprimir_pdf();
});
function imprimir_pdf(){
	var fecha_inicio=$("#fecha_inicio").val();		
	var fecha_fin=$("#fecha_fin").val();
	var tipo=$("select#tipo option:selected").val(); //get the value
	var dataString='fecha_inicio='+fecha_inicio+'&fecha_fin='+fecha_fin+'&tipo='+tipo; 
	
		cadena = "imprimir_ventas_pdf.php?"+dataString ;	
		window.open(cadena, '', '');	 
     
}
