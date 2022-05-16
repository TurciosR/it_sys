var cont_canasta = 0;
$(document).ready(function() {

  // Clean the modal form
  $(document).on('hidden.bs.modal', function(e) {
    var target = $(e.target);
    target.removeData('bs.modal').find(".modal-content").html('');

  });
  $('#viewModal').on('hidden.bs.modal', function () {
      $(this).removeData('bs.modal');
});
    searchFilter();
  $(document).on('keyup keypress', '.decimal_positive', function(){
    var aValue = $(this).val();
    if($.isNumeric(aValue) === false){
      $(this).val(aValue.slice(0,-1));
    }
  });
 $(document).keydown(function(e){
   if(e.which == 113){ //F2 Guardar
     e.stopPropagation();
     senddata();
   }
   if(e.which == 119) {//F8 Imprimir
      e.stopPropagation();
      finalizar2();
   }
   if(e.which == 120) { //F9 Salir
   //PENDIENTE
   }
 });

$(document).on('keyup keypress', '.integer_positive', function(e)
{
  if (/\D/g.test(this.value))
  {
    // Filter non-digits from input value.
    this.value = this.value.replace(/\D/g, '');
  }
});


  $('.select2').select2({
  	placeholder: {
      id: '-1',
      text: 'Seleccione'
    },
      allowClear: true
  });

 $('#keywords, #marca, #modelo, #barcode').on('keyup', function(event) {
    searchFilter();
  });
  $("#select_proveedores").change(function(event){
    searchFilter();
  });
  $('.loading-overlay').hide();
});
$(function (){
  //binding event click for button in modal form
  $(document).on("click", "#btnDelete", function(event) {
    deleted();
  });
  // Clean the modal form
  $(document).on('hidden.bs.modal', function(e) {
    var target = $(e.target);
    target.removeData('bs.modal').find(".modal-content").html('');
  });

});
function searchFilter(page_num){
  page_num = page_num ? page_num : 0;
  var keywords = $('#keywords').val();
  var id_proveedor= $('#select_proveedores :selected').val();
  var marca = $('#marca').val();
  var modelo = $('#modelo').val();
  var barcode = $('#barcode').val();
  var serie = $('#serie').val();
  //var limite = $('#limite').val();

  if(id_proveedor==undefined){
    id_proveedor=-1;
  }
  getData(keywords,id_proveedor,marca,modelo,serie,id_proveedor,barcode,page_num)
}

function getData(keywords,id_proveedor,marca,modelo,serie,id_proveedor,barcode,page_num){
  var sortBy = $('#sortBy').val();
  var records = $('#records').val();
  urlprocess = $('#urlprocess').val();

    var cuantos= 0
  $.ajax({
    type: 'POST',
    url: urlprocess,
    data: {
      process: 'traerdatos',
      page: page_num,
      keywords: keywords,
      id_proveedor: id_proveedor,
      marca: marca,
      modelo: modelo,
      serie:serie,
      barcode:barcode,
      sortBy: sortBy,
      records: records
    },
    beforeSend: function() {
      $('.loading-overlay').show();
    },
    success: function(html) {
        $('#mostrardatos').html(html);
      if (cuantos>0){
          $('.loading-overlay').html("<span class='text-warning'>Buscando....</span>");
          $('#reg_count').val(cuantos);
          $('.loading-overlay').fadeOut("slow");

      }
    else{
        $('.loading-overlay').fadeOut("slow");
        $('#reg_count').val(0);
      }
    }

  });
  /*
  function esperar(){

    setTimeout(function() {
      for (i = 0; i < 100; i++){text="abc";};
    }, 100);

  }
  esperar()

  var cuantos2=0;
    alert("cuantos:"+cuantos)
  $("#mostrardatos>tr").each(function(index) {
       cuantos= $(this).find("td:eq(0)").text();

});
cuantos2=$("#mostrardatos").find("tr:last").find("td:eq(0)").text();
alert("cuantos:"+cuantos+" -- "+cuantos2)

  $.ajax({
    type: 'POST',
    url: urlprocess,
    data: {
      process: 'traerpaginador',
      page: page_num,
      keywords: keywords,
      id_proveedor: id_proveedor,
      marca: marca,
      modelo: modelo,
      serie:serie,
      barcode:barcode,
      sortBy: sortBy,
      cuantos:cuantos,
      records: records,
    },
    success: function(value) {

      $('#encabezado_buscador').show();
      $('#paginador').html(value);
    }
  })
*/
}


//function to round 2 decimal places
function round(value, decimals) {
    return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
}

function deleted() {
	var id_producto = $('#id_producto').val();
	var dataString = 'process=deleted' + '&id_producto=' + id_producto;
	$.ajax({
		type : "POST",
		url : "borrar_producto.php",
		data : dataString,
		dataType : 'json',
		success : function(datax) {
			display_notify(datax.typeinfo, datax.msg);
			setInterval("location.reload();", 500);
			$('#deleteModal').hide();
		}
	});
}
$(document).on("click", "#btnPrintBcodes", function(event) {
		print_bcodes();
	});

	function print_bcodes(){
	  var id_producto = $('#id_producto').val();
		var qty = $('#qty').val();
	  var dataString = 'process=buscarprodcant' + '&id_producto=' + id_producto+ '&qty=' +qty;
	  $.ajax({
	    type: "POST",
	    url:"ver_producto.php",
	    data: dataString,
	    dataType: 'json',
	    success: function(datos) {
	      config=datos.pop()

	      var sist_ope = config.sist_ope;
	      var dir_print=config.dir_print;
	      var shared_printer_barcode=config.shared_printer_barcode;
	      //alert(dir_print+" "+sist_ope+" "+shared_printer_barcode)
	      if (sist_ope == 'win') {
	        $.post("http://"+dir_print+"printbcodewin1.php", {
	          datosproductos:datos,
	          shared_printer_barcode:shared_printer_barcode
	        })
	      } else {
	        $.post("http://"+dir_print+"printbcode1.php", {
	            datosproductos: datos
	        });
	      }
	    }
	});

	}
  $(function (){

   });
