$(document).ready(function() {

  $('#clave').on('click',function(event) {
    $('#clave').val('')
  });

  $('#nit').on('keydown', function (event) {
    if (event.keyCode == 8 || event.keyCode == 37 || event.keyCode == 39) {
        // ignorando tecla espacio y las de desplazamiento
    } else {
      if (event.keyCode < 48 || event.keyCode > 57) {
        if(event.keyCode < 96 || event.keyCode > 105)
        {
          event.preventDefault();

        }
        else
        {
          inputval = $(this).val();
          var string = inputval.replace(/[^0-9]/g, "");
          var bloque1 = string.substring(0,4);
          var blocque2 = string.substring(4,10);
          var blocque3 = string.substring(10,13);
          var blocque4 = string.substring(13,13);
          var string = (bloque1  + "-" + blocque2 + "-" + blocque3 + "-" +blocque4);
          $(this).val(string);
        }
      }
      else {
        // validar el nit
        inputval = $(this).val();
        var string = inputval.replace(/[^0-9]/g, "");
        var bloque1 = string.substring(0,4);
        var blocque2 = string.substring(4,10);
        var blocque3 = string.substring(10,13);
        var blocque4 = string.substring(13,13);
        var string = (bloque1  + "-" + blocque2 + "-" + blocque3 + "-" +blocque4);
        $(this).val(string);
      }
    }
  });

  $('#telefono1').on('keydown', function (event) {
    if (event.keyCode == 8 || event.keyCode == 37 || event.keyCode == 39) {
        // ignorando tecla espacio y las de desplazamiento
    } else {
      if (event.keyCode < 48 || event.keyCode > 57) {
        if(event.keyCode < 96 || event.keyCode > 105)
        {
          event.preventDefault();

        }
        else
        {
          inputval = $(this).val();
          var string = inputval.replace(/[^0-9]/g, "");
          var bloque1 = string.substring(0,4);
          var blocque2 = string.substring(4,7);
          var string = (bloque1  + "-" + blocque2);
          $(this).val(string);
        }
      }
      else {
        // validar el nit
        inputval = $(this).val();
        var string = inputval.replace(/[^0-9]/g, "");
        var bloque1 = string.substring(0,3);
        var blocque2 = string.substring(4,7);
        var string = (bloque1  + "-" + blocque2);
        $(this).val(string);
      }
    }
  });

  $('#telefono2').on('keydown', function (event) {
    if (event.keyCode == 8 || event.keyCode == 37 || event.keyCode == 39) {
        // ignorando tecla espacio y las de desplazamiento
    } else {
      if (event.keyCode < 48 || event.keyCode > 57) {
        if(event.keyCode < 96 || event.keyCode > 105)
        {
          event.preventDefault();

        }
        else
        {
          inputval = $(this).val();
          var string = inputval.replace(/[^0-9]/g, "");
          var bloque1 = string.substring(0,4);
          var blocque2 = string.substring(4,7);
          var string = (bloque1  + "-" + blocque2);
          $(this).val(string);
        }
      }
      else {
        // validar el nit
        inputval = $(this).val();
        var string = inputval.replace(/[^0-9]/g, "");
        var bloque1 = string.substring(0,3);
        var blocque2 = string.substring(4,7);
        var string = (bloque1  + "-" + blocque2);
        $(this).val(string);
      }
    }
  });

  $('#dui').on('keydown', function(event) {
    if (event.keyCode == 8 || event.keyCode == 37 || event.keyCode == 39) {
        // ignorando tecla espacio y las de desplazamiento
    } else {

      if (event.keyCode < 48 || event.keyCode > 57) {
        if(event.keyCode < 96 || event.keyCode > 105)
        {
          event.preventDefault();

        }
        else
        {
          inputval = $(this).val();
          var string = inputval.replace(/[^0-9]/g, "");
          var bloque1 = string.substring(0,8);
          var blocque2 = string.substring(9,9);
          var string = (bloque1  + "-" + blocque2);
          $(this).val(string);
        }
      }
      else {
        // validar el nit
        inputval = $(this).val();
        var string = inputval.replace(/[^0-9]/g, "");
        var bloque1 = string.substring(0,8);
        var blocque2 = string.substring(9,9);
        var string = (bloque1  + "-" + blocque2);
        $(this).val(string);
      }
    }
  });

  $('#formulario').validate({
      rules: {
              nombre: {
                required: true,
              },

              usuario: {
                required: true,
              },
              clave: {
                required: true,
              },
              dui:
              {
                minlength: 10,
              },
              nit: {
                minlength: 17
              },
              correo:{
                email: true,
              }
          },
          messages:
          {
            nombre: {
              required: "Ingrese su nombre",
            },

            usuario: {
              required: "Ingrese un nombre de Usuario",
            },
            clave: {
              required: "Ingrese una clave",
            },
            nit: {
                required: "Ingrese número de NIT",
                minlength: "Nit no valido"
                },
            dui: {
                minlength: "DUI no valido"
                },
            correo:{
                email:"Ingrese una direccion de correo valida",
            },
          },

      submitHandler: function (form)
      {
          senddata();
      }
    });

});

function senddata()
{
    //Get the value from form if edit or insert
	var process=$('#process').val();
	if(process=='insert')
	{
		var urlprocess='perfil.php';
		}
    var form = $("#formulario");
    var formdata = false;
    if(window.FormData)
    {
        formdata = new FormData(form[0]);
    }
    var formAction = form.attr('action');
    $.ajax({
        type        : 'POST',
        url         : urlprocess,
        cache       : false,
        data        : formdata ? formdata : form.serialize(),
        contentType : false,
        processData : false,
        dataType : 'json',
        success: function(data)
        {
        display_notify(data.typeinfo,data.msg,data.process);
        if(data.typeinfo=="Success")
        {
           setInterval("reload1();", 1500);
        }
      }
    });
}
function reload1()
{
   location.href = 'perfil.php';
}
