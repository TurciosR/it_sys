$(document).ready(function()
{
  $(document).on("click", "#btnPor", function()
  {
    senddata();
  });
  $(document).on("click", "#btnCan", function()
  {
    cambio();
  });
  $(document).on("click", "#btnDelete", function()
  {
    borrar();
  });
});

function senddata()
{
  var id_porcentaje = $("#id_porcentaje").val();
  var porcentaje = $("#porcentaje").val();
  var estado = $("#estado").val();
  var process = $("#process").val();
  if(process == "insertar")
  {
    var url = "agregar_porcentaje.php";
  }
  if(process == "editar")
  {
    var url = "editar_porcentaje.php";
  }

  var dataString = "process="+process+"&id_porcentaje="+id_porcentaje+"&porcentaje="+porcentaje+"&estado="+estado;
  $.ajax({
	  type: 'POST',
	  url: url,
	  data: dataString,
    dataType: 'json',
	  success: function(datax)
	  {
      display_notify(datax.typeinfo, datax.msg);
      if(datax.typeinfo == "Success")
      {
        $("#cerrar").click();
        setInterval("reload();", 1500);
      }
	  }
	});
}

function cambio()
{
  var id_porcentaje = $("#id_porcentaje").val();
  var estado = $("#estado").val();
  var process = $("#process").val();

  var dataString = "process="+process+"&id_porcentaje="+id_porcentaje+"&estado="+estado;
  $.ajax({
	  type: 'POST',
	  url: "cambiar_estado_por.php",
	  data: dataString,
    dataType: 'json',
	  success: function(datax)
	  {
      display_notify(datax.typeinfo, datax.msg);
      if(datax.typeinfo == "Success")
      {
        $("#cerrar").click();
        setInterval("reload();", 1500);
      }
	  }
	});
}

function borrar()
{
  var id_porcentaje = $("#id_porcentaje").val();
  var estado = $("#estado").val();
  var process = $("#process").val();

  var dataString = "process="+process+"&id_porcentaje="+id_porcentaje+"&estado="+estado;
  $.ajax({
	  type: 'POST',
	  url: "borrar_porcentaje.php",
	  data: dataString,
    dataType: 'json',
	  success: function(datax)
	  {
      display_notify(datax.typeinfo, datax.msg);
      if(datax.typeinfo == "Success")
      {
        $("#cerrar").click();
        setInterval("reload();", 1500);
      }
	  }
	});
}

function reload()
{
  location.href = "admin_porcentaje.php";
}
