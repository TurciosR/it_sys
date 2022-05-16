$(document).ready(function() {
  $('.select2').select2({
    placeholder: {
      id: '-1',
      text: 'Seleccione'
    },
    allowClear: true
  });

  $('.demo2').bootstrapDualListbox({
    nonSelectedListLabel: 'Pendientes de pago',
    selectedListLabel: 'A pagar',
    preserveSelectionOnMove: 'moved',
    moveOnSelect: true,
    filterTextClear: '<span class="fa fa-eye">Mostrar todo</span>',
    filterPlaceHolder: 'Filtro',
    infoText: 'Mostrando todo {0}',
    moveSelectedLabel: 'Mover seleccionado',
    moveAllLabel: 'Mover todo',
    removeSelectedLabel: 'Remover Seleccionado',
    removeAllLabel: 'Remover todo',
    infoTextEmpty: 'Lista vacia',
    infoTextFiltered: '<span class="label label-warning">Filtrando</span>  {0} de {1} facturas',
  });
  $("#select_proveedores").change(function(event) {

    cambio_proveedor();

  });
});
$(function() {

  var dualListContainer = $('select[name="duallistbox_demo2"]').bootstrapDualListbox('getContainer');
  dualListContainer.find('.moveall i').removeClass().addClass('fa fa-arrow-right');
  dualListContainer.find('.moveall').removeClass('btn-default').addClass('btn-success');
  dualListContainer.find('.removeall i').removeClass().addClass('fa fa-arrow-left');
  dualListContainer.find('.removeall').removeClass('btn-default').addClass('btn-success');
  /*dualListContainer.find('.move i').removeClass().addClass('fa fa-arrow-right');
  dualListContainer.find('.move').removeClass('btn-default').addClass('btn-info');
  dualListContainer.find('.remove i').removeClass().addClass('fa fa-arrow-left');
  dualListContainer.find('.remove').removeClass('btn-default').addClass('btn-info');*/
    //binding event click for button in modal form
  $(document).on("click", "#btnDelete", function(event) {
    deleted();
  });
  $(document).on("click", "#submit1", function(event) {
    alert($('[name="duallistbox_demo2"]').val());
  });
  // Clean the modal form
  $(document).on('hidden.bs.modal', function(e) {
    var target = $(e.target);
    target.removeData('bs.modal').find(".modal-content").html('');
  });
  $('[name="duallistbox_demo2"]').change(function(event) {
    /* Act on the event */
    agregar_Factura($(this).val());

  });

  $(document).on('click', '.lndelete', function(e) {
    var idtransace = $(this).closest('tr').attr('class');
    $(this).closest('tr').remove();
    calcSald(idtransace);
  });
  $(document).on('click', '.alldelete', function(e) {
    var id = $(this).closest('tr').attr('class');

    $('#tabla tr').each(function(index) {
      if (index > 0) {
        var con = 0;
              if($(this).attr('class')==id)
              {
                $(this).remove();
              }
      }
    });
  });


  $('html').click(function() {
    /* Aqui se esconden los menus que esten visibles*/
    var number=$('#value').val();
    var a = $('#value').closest('td');

    var idtransace=a.closest('tr').attr('class');
    a.html(number);
    if(a.hasClass('nm'))
    {
      console.log('tc');
      if(isNaN(number))
      {
      }
      else
      {

        number = number/100;
        number = round(number, 4);
        if(number!=0)
        {
          var monto_inicial = parseFloat(a.closest('tr').find('td:eq(2)').html());
          monto_inicial = round(monto_inicial, 2);
          var descuento = number*monto_inicial;
          descuento=round(descuento, 2)

          a.closest('tr').find('td:eq(4)').html(descuento);

        }

      }
      calcSald(idtransace)
    }
    else
    {

      if(a.hasClass('ed'))
      {
        calcSald(idtransace)
      }
    }

  });

  $(document).on('click', 'td', function(e) {
    if($(this).hasClass('ed'))
    {
      var av=$(this).html();
      $(this).html('');
      $(this).html('<input class="form-control in" type="text" id="value" name="value" value="">');
      $('#value').val(av);
      $('#value').focus();
      $('#value').numeric({negative:false,decimalPlaces:2});
      e.stopPropagation();
    }
    if($(this).hasClass('nm'))
    {
      var av=$(this).html();
      $(this).html('');
      $(this).html('<input class="form-control in" type="text" id="value" name="value" value="">');
      $('#value').val(av);
      $('#value').focus();
      $('#value').numeric({negative:false,decimal: false});
      e.stopPropagation();
    }
  });

  $(document).on('keypress', '.in', function(event) {

    if(event.key=='Enter')
    {
      $('html').click();
    }
  });

  $(document).on('keypress', '.in', function(event) {
      console.log(event.key);

      if(event.key=='Tab')
      {
        var a= $(this).parents('tr');

        var idtransace=a.attr('class');
        var cargo = a.find('td:eq(2)').html();
        var saldo = a.find('td:eq(9)').html();
          var app = "<tr class='"+idtransace+"' style='height:35px;'> <td></td> <td></td> <td>"+cargo+"</td> <td class='nm'></td> <td></td> <td class='ed'></td> <td class='ed'></td> <td class='ed'></td> <td class='ed'></td> <td class='"+idtransace+"'>"+saldo+"</td> <td class='text-center'><a class=' lndelete' type='button' name='button'> <span class='fa fa-trash'></span> </a></td></tr>";
          a.after(app);
          $('html').click();
      }
  });

});

function calcSald(id_transace) {
  var id = id_transace
  var con=1;
  var sum=0;
  var saldo=0;
  $('#tabla tr ').each(function(index) {
    if (index > 0) {
            if($(this).attr('class')==id)
            {
              sum=0;
              if(con==1)
              {
                monto_inicial = parseFloat($(this).find('td:eq(2)').html());
                monto_inicial = round(monto_inicial, 2);
                saldo=monto_inicial;
              }
              for (var i = 4; i < 9; i++) {
                a=parseFloat($(this).find('td:eq('+i+')').html());
                if(isNaN(a))
                {
                }
                else
                {
                  sum=sum+a;
                  sum=round(sum, 2);
                }

              }
              saldo=saldo-sum;
              console.log(saldo);
              saldo=round(saldo,2)
              $(this).find('td:eq(9)').html(saldo);

              console.log(con);
              con=con+1;
            }
    }
  });
}

function agregar_Factura(values) {
  if (values != null) {
    var arrayFacturas = values;
    for (var i = 0; i < arrayFacturas.length; i++) {
      var contador = 0;
      $('#tabla tr').each(function(index) {
        if ($(this).find("#fact_idtransace").val() == arrayFacturas[i]) {
          contador++;
        }

      });
      if (contador == 0) {
        dataString = 'process=addFactura' + '&idtransace=' + arrayFacturas[i];
        $.ajax({
          url: 'pago_proveedores.php',
          type: 'POST',
          dataType: 'json',
          data: dataString,
          success: function(datax) {
            $('#tabla').append(datax.fact);
          }

        })
      }
    }

    $('#tabla tr').each(function(index) {
      if (index > 0) {
        var con = 0;
            for (var i = 0; i < values.length; i++) {
              if($(this).attr('class')==values[i])
              {
                con++;
              }
            }
            if(con==0)
            {
              $(this).remove();
            }
      }
    });




  } else {
    console.log("NO values");
		$('#tabla tr').each(function(index) {

			if (index > 0) {
					$(this).remove();
			}
		});
  }





}

function cambio_proveedor() {
  var id = $('#select_proveedores').val();
  $.post("pago_proveedores.php", {
    process: 'genera_select',
    id: id
  }, function(data) {

    $('.demo2').html(data);
    $('.demo2').bootstrapDualListbox('refresh');

  });
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

  var dataString = $("#formulario").serialize();

  var process = $('#process').val();

  var urlprocess = 'pago_proveedores.php';

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

function round(value, decimals) {
  return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
}

function reload1() {
  location.href = 'admin_color.php';
}

function deleted() {
  var id_color = $('#id_color').val();
  var dataString = 'process=deleted' + '&id_color=' + id_color;
  $.ajax({
    type: "POST",
    url: "borrar_color.php",
    data: dataString,
    dataType: 'json',
    success: function(datax) {
      display_notify(datax.typeinfo, datax.msg);
      if (datax.typeinfo == "Success") {
        setInterval("location.reload();", 1000);
        $('#deleteModal').hide();
      }
    }
  });
}
