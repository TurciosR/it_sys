$(document).ready(function() {
  $('.select').select2({
    placeholder: {
      id: '', // the value of the option
      text: 'Seleccione'
    },
    allowClear: true
  });
  $("#monto").numeric({
    negative: false
  });
  $(".decimal").numeric({
    decimal: ".",
    negative: false,
    decimalPlaces: 2
  });

  $('#formulario').validate({
    rules: {

      descripcion: {
        required: true,
      },
    },
    messages: {
      descripcion: "Ingrese el nombre del almacen",
    },
    highlight: function(element) {
      $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
    },
    success: function(element) {
      $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
    },
    submitHandler: function(form) {
      senddata();
    }
  });


});

$(function() {
  // Clean the modal form
  $(document).on('hidden.bs.modal', function(e) {
    var target = $(e.target);
    target.removeData('bs.modal').find(".modal-content").html('');
  });

  $(document).on("click", "#agregar", function(event) {
    agregarCheque();
  });

  $(document).on("keyup", "#monto", function(event) {
    var monto = parseFloat($('#monto').val());
    var saldo = parseFloat($('#saldo').val());
    if (monto == undefined || monto == 0 || monto == '') {
      monto = 0;
    }
    if (monto > saldo) {
      $('#monto').val("" + saldo);
    }

  });
  $(document).on("keyup", "#abono", function(event) {
    var cheque = $(this).attr('cheque');
    var factura = $(this).attr('numeroDoc');
    var monto = parseFloat($(this).attr('monto'));
    var sumaMontos = 0;
    var saldoPend = parseFloat($(this).attr('saldoPend'));
    var valor = parseFloat($(this).val());
    var abono = 0;
    var sumaAbonos = 0;
    var sumaAbonosPorFactura = 0;

    $('#tabla tr table tr').each(function(index) {
      if (index > 0) {

        var chequeR = $(this).find('#abono').attr('cheque');
        /*console.log(chequeR);*/
        if (chequeR != undefined) {
          if (cheque == chequeR) {
            abono = $(this).find('#abono').val();

            if (abono != undefined && abono != '') {
              sumaAbonos = parseFloat(sumaAbonos) + parseFloat(abono);
            }
          }
        }


        var facturaR = $(this).find('#abono').attr('numeroDoc');
        if (facturaR != undefined) {
          if (factura == facturaR) {
            abono = $(this).find('#abono').val();

            if (abono != undefined && abono != '') {
              sumaAbonosPorFactura = parseFloat(sumaAbonosPorFactura) + parseFloat(abono);
            }
          }
        }

      }
    });
    /*console.log(cheque);
    console.log(sumaAbonos);
    console.log(monto);*/
    sumaAbonosPorFactura = round(sumaAbonosPorFactura, 2);
    sumaAbonos = round(sumaAbonos, 2);
    console.log(sumaAbonos);
    console.log(sumaAbonosPorFactura);
    console.log(saldoPend);
    var newval = 0
    if (sumaAbonos > monto) {
      newval = round((monto - (sumaAbonos - valor)), 2);
      if (isNaN(newval)) {
        $(this).val(0);
        sumaAbonos = monto;
      } else {
        $(this).val(newval);
        sumaAbonos = round((sumaAbonos - valor + newval), 2);
        console.log("t " + sumaAbonos);
      }

    }

    if (sumaAbonosPorFactura > saldoPend) {
      var newval2 = parseFloat(round((saldoPend - (sumaAbonosPorFactura - valor)), 2));
      console.log(newval2);
      if (newval == 0 || newval2 < newval) {

        if (isNaN(newval2)) {
          $(this).val(0);
        } else {
          $(this).val(newval2);
        }
      }
    }

    if (sumaAbonos == monto) {
      $('#d' + cheque).removeClass("btn-warning");
      $('#d' + cheque).addClass("btn-success");
      $('#d' + cheque).attr('onclick', 'ok()');
      $('#e' + cheque).removeClass("fa-exclamation-circle");
      $('#e' + cheque).addClass("fa-check-circle");
      $('#e' + cheque).html(" OK");


    } else {
      $('#d' + cheque).addClass("btn-warning");
      $('#d' + cheque).removeClass("btn-success");
      $('#d' + cheque).attr('onclick', 'revise()');
      $('#e' + cheque).addClass("fa-exclamation-circle");
      $('#e' + cheque).removeClass("fa-check-circle");
      $('#e' + cheque).html(" Revise abonos");
    }

    suma();


  });

  $(document).on("change", "#banco", function(event) {

    $('#cuenta').empty().trigger('change');
    $('#saldo').val('');
    $('#fecha').val('');
    $('#cheque').val('');
    $('#monto').val('');
    $('#monto').prop('disabled', true);
    $('#cheque').prop('disabled', true);
    $('#agregar').prop('disabled', true);
    $('#fecha').prop('disabled', true);

    var id_banco = $(this).val();
    if (id_banco > 0) {
      $.ajax({
        type: "POST",
        url: "admin_cxp_abonar.php",
        data: "process=val&id_banco=" + id_banco,
        dataType: "JSON",
        success: function(datax) {
          if (datax.typeinfo == "Success") {
            $("#cuenta").html(datax.opt);
          }
        }
      });
    }
  });
  $(document).on("change", "#cuenta", function(event) {

    $('#saldo').val('');
    $('#fecha').val('');
    $('#cheque').val('');
    $('#monto').val('');
    $('#monto').prop('disabled', true);
    $('#cheque').prop('disabled', true);
    $('#agregar').prop('disabled', true);
    $('#fecha').prop('disabled', true);
    var id_cuenta = $(this).val();
    if (id_cuenta > 0) {
      $.ajax({
        type: "POST",
        url: "admin_cxp_abonar.php",
        data: "process=saldo&id_cuenta=" + id_cuenta,
        dataType: "JSON",
        success: function(datax) {
          if (datax.typeinfo == "Success") {
            var saldo = parseFloat(datax.saldo);
            if (isNaN(saldo)) {
              saldo = 0;
            }
            var reservado = 0;
            var montoCheque = 0;
            var reservado = 0;

            $('#tabla tr').each(function(index) {
              if (index > 0) {
                var cuenta = $(this).find('#chequeabono').attr('cuenta');
                if (cuenta != undefined) {
                  if (id_cuenta == cuenta) {
                    montoCheque = $(this).find('#chequeabono').attr('monto');
                    if (montoCheque != undefined) {
                      /*console.log($(this).find('#chequeabono').val());*/
                      reservado = parseFloat(reservado) + parseFloat(montoCheque);
                    }

                  }
                }
              }
            });
            /*console.log(saldo);
            console.log(reservado);*/
            saldo = saldo - reservado;
            if (saldo == 0) {
              display_notify("Error", "Esta cuenta no tiene fondos");

              $('#monto').prop('disabled', true);
              $('#cheque').prop('disabled', true);
              $('#agregar').prop('disabled', true);
              $('#fecha').prop('disabled', true);
            } else {
              $('#saldo').val(saldo);
              $('#monto').prop('disabled', false);
              $('#cheque').prop('disabled', false);
              $('#agregar').prop('disabled', false);
              $('#fecha').prop('disabled', false);
            }
          }
        }
      });
    }
  });

});

function agregarCheque() {
  var input = "";
  var banco = $('#banco').val();
  var cuenta = $('#cuenta').val();
  var saldo = $('#saldo').val();
  var cheque = $('#cheque').val();
  var monto = $('#monto').val();
  var fecha = $('#fecha').val();
  var id_proveedor = $('#id_proveedor').val();
  var deuda = parseFloat($('#total_deuda').val());
  var chequeExiste = 0; /*valida si un cheque existe*/
  var totalCheques = 0; /*suma de los montos totales de los cheques*/
  var validaMontos = 0; /*valida que los montos totales de los cheques no sea superior a la deuda*/

  $('#tabla tr').each(function(index) {
    if (index > 0) {
      var id_cuenta = $(this).find('#chequeabono').attr('cuenta');
      if (cuenta != undefined) {
        if (id_cuenta == cuenta) {
          if ($(this).find('#chequeabono').val() == cheque) {
            chequeExiste = 1;
            display_notify("Error", "Ya existe un cheque de esta cuenta con el mismo numero");
          }
          totalCheques = parseFloat(totalCheques) + (parseFloat($(this).find('#chequeabono').attr('monto')));
        }


      }
    }

  });

  totalCheques = parseFloat(totalCheques) + parseFloat(monto);
  totalCheques = round(totalCheques, 2);
  console.log(deuda);
  console.log(totalCheques);

  if (totalCheques > deuda) {
    validaMontos = 1;
    display_notify("Error", "El monto total de los cheque ingresados es mayor a a deuda");
  }

  var validaCheque = 0; /*valida que se ingrese el cheque*/
  var validaMonto = 0; /*valida el monto de cheque que se quiere ingresar*/
  var validaFecha = 0; /*valida que se ingrese la fecha*/

  if (cheque == '' || cheque == 0 || cheque == undefined) {
    validaCheque = 1;
    display_notify("Error", "Ingrese el numero de cheque");
  } else {
    validaCheque = 0;
  }

  if (monto == '' || monto == 0 || monto == undefined) {
    validaMonto = 1;
    display_notify("Error", "Ingrese el Monto del cheque");
  } else {
    validaMonto = 0;
  }

  if (fecha == '' || fecha == 0 || fecha == undefined) {
    validaFecha = 1;
    display_notify("Error", "Ingrese la fecha del cheque");
  } else {
    validaFecha = 0;
  }

  if (validaCheque == 0 && validaMonto == 0 && validaFecha == 0 && chequeExiste == 0 && validaMontos == 0) {
    $.ajax({
      type: "POST",
      url: "admin_cxp_abonar.php",
      data: "process=agregarCheque" + "&id_proveedor=" + id_proveedor + "&id_banco=" + banco + "&id_cuenta=" + cuenta + "&cheque=" + cheque + "&monto=" + monto + "&fecha=" + fecha,
      dataType: "JSON",
      success: function(datax) {
        $('#tabla').append(datax.opt);
        $(".decimal").numeric({
          decimal: ".",
          negative: false,
          decimalPlaces: 2
        });

        $('.select').val('').trigger('change');
        $('#cuenta').empty().trigger('change');
        $('#saldo').val('');
        $('#fecha').val('');
        $('#cheque').val('');
        $('#monto').val('');

      }
    });
  }



}

function autosave(val) {
  var name = $('#name').val();
  if (name == '' || name.length == 0) {
    var typeinfo = "Info";
    var msg = "The field name is required";
    display_notify(typeinfo, msg);
    $('#name').focus();
  } else {
    senddata();
  }
}

function senddata() {
	var i = 0;
	var j = 0;
	var array_json = new Array();
	var array_json2 = new Array();

  $('#tabla tr table tr').each(function(index) {
    if (index > 0) {
      abono = $(this).find('#abono').val();
      if (abono != undefined && abono != ''&&isNaN(abono)==false&&abono!=0) {
	        var obj = new Object();
	        obj.idtransace = $(this).find('#abono').attr('idtransace');
	        obj.id_banco = $(this).find('#abono').attr('banco');
					obj.id_cuenta = $(this).find('#abono').attr('cuenta');
					obj.cheque = $(this).find('#abono').attr('cheque');
					obj.monto = $(this).find('#abono').val();
          obj.fecha=$(this).find('#abono').attr('fecha');
          obj.montocheque=$(this).find('#abono').attr('monto');

	        //convert object to json string
	        text = JSON.stringify(obj);
	        array_json.push(text);
	        i = i + 1;
      }
    }
  });
	json_arr = '[' + array_json + ']';

  $('#tabla tr').each(function(index) {
    if (index > 0) {
      var id_cuenta = $(this).find('#chequeabono').attr('cuenta');
      if (id_cuenta != undefined) {
				var obj = new Object();
				obj.id_cuenta = $(this).find('#chequeabono').attr('cuenta');
				obj.numero_doc = $(this).find('#chequeabono').val();
				obj.salida=$(this).find('#chequeabono').attr('monto');

				//convert object to json string
				text = JSON.stringify(obj);
				array_json2.push(text);
				i = i + 1;
      }
    }
  });

	json_arr2 = '[' + array_json2 + ']';

  var process = $('#process').val();

  if (process == 'insert') {
    var urlprocess = 'admin_cxp_abonar.php';
    var dataString = 'process=' + process + '&json_arr=' + json_arr+ '&json_arr2=' + json_arr2;

    $.ajax({
      type: 'POST',
      url: urlprocess,
      data: dataString,
      dataType: 'json',
      success: function(datax) {
        display_notify(datax.typeinfo, datax.msg);
        if (datax.typeinfo == "Success") {
        setInterval("reload1();", 1000);
        }
      }
    });
  }
}

function reload1() {
  location.href = 'admin_cxp_p.php';
}

function remover(id) {
  $("#a" + id).remove();
  $("#b" + id).remove();
}

function round(value, decimals) {
  return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
}

function revise() {
  display_notify("Warning", "Los abonos a las facturas no concuerdan con el monto del cheque");
}

function ok() {
  display_notify("Info", "Los abonos a las facturas concuerdan con el monto del cheque");
}

function suma() {

  var totalAbonos = 0
  $('#tabla tr table tr').each(function(index) {
    if (index > 0) {
      abono = $(this).find('#abono').val();
      if (abono != undefined && abono != '') {
        totalAbonos = parseFloat(totalAbonos) + (parseFloat($(this).find('#abono').val()));
      }
    }
  });


  var totalCheques = 0; /*suma de los montos totales de los cheques*/
  $('#tabla tr').each(function(index) {
    if (index > 0) {
      var id_cuenta = $(this).find('#chequeabono').attr('cuenta');
      if (id_cuenta != undefined) {
        totalCheques = parseFloat(totalCheques) + (parseFloat($(this).find('#chequeabono').attr('monto')));
      }
    }
  });

	totalCheques=round(totalCheques, 2);
	totalAbonos=round(totalAbonos, 2);

  if (totalCheques == totalAbonos) {
    $('#submit1').prop('disabled', false);
  } else {
    $('#submit1').prop('disabled', true);
  }
}
