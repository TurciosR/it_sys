<?php
include ("_core.php");
function initial()
{
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	?>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
		<h4 class="modal-title">Agregar Cliente</h4>
	</div>
	<div class="modal-body">
		<div class="wrapper wrapper-content  animated fadeInRight">
			<div class="row">
				<div class="col-lg-12">
					<div class="form-group has-info single-line">
          <label>Nombre <span style="color:red;">*</span></label>
          <input type="text" class="form-control" name="nombre" id="nombre">
					</div>
				</div>
      </div>
      <div class="row">
        <div class="col-lg-12">
					<div class="form-group has-info single-line">
          <label>Dirección <span style="color:red;">*</span></label>
          <input type="text" class="form-control" name="direccion" id="direccion">
					</div>
				</div>
      </div>
      <div class="row" hidden>
          <div class="col-md-6">
						<div class="form-group has-info single-line">
              <label>Departamento </label>
              <select class="col-md-12 select" style="width: 100%" id="departamento" name="departamento">
                  <option value="">Seleccione un departamento</option>
                  <?php
                      $sqld = "SELECT * FROM departamento";
                      $resultd=_query($sqld);
                      while($depto = _fetch_array($resultd))
                      {
                          echo "<option value='".$depto["id_departamento"]."'";

                          echo">".$depto["nombre_departamento"]."</option>";
                      }
                  ?>
              </select>
						</div>
          </div>
          <div class="col-md-6">
						<div class="form-group has-info single-line">
              <label>Municipio </label>
              <select class="col-md-6 select" style="width: 100%" id="municipio" name="municipio">
                  <option value="">Primero seleccione un departamento</option>
              </select>
          	</div>
					</div>
      </div>
      <div class="row">
          <div class="col-md-6" hidden>
						<div class="form-group has-info single-line">
              <label>Categoria del Cliente </label>
              <select class="col-md-12 select" style="width: 100%" id="categoria" name="categoria">
                  <?php
                      $sqld = "SELECT * FROM categoria_proveedor";
                      $resultd=_query($sqld);
                      while($depto = _fetch_array($resultd))
                      {
                          echo "<option value='".$depto["id_categoria"]."'";

                          echo">".$depto["nombre"]."</option>";
                      }
                  ?>
              </select>
          	</div>
					</div>
					<div class="col-md-6">
						<div class="form-group has-info single-line">
							<label>Teléfono <span style="color:red;">*</span></label>
							<input type="text" class="form-control tel" id="telefono" name="telefono">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group has-info single-line">
							<label>Correo </label>
							<input type="text" class="form-control" id="email" name="email">
						</div>
					</div>
      </div>
			<input type="hidden" name="process" id="process" value="insert">
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-primary" id="btnAdd">Guardar</button>
		<button type="button" class="btn btn-default" id="closeM" data-dismiss="modal">Cerrar</button>
	</div>
  <script type="text/javascript">
    $(document).ready(function()
    {
			$(".select").select2();
			$("#departamento").change(function()
		    {
		     	$("#municipio *").remove();
		     	$("#select2-municipio-container").text("");
		     	var ajaxdata = { "process" : "municipio", "id_departamento": $("#departamento").val() };
		        $.ajax({
		          	url:"agregar_cliente.php",
		          	type: "POST",
		          	data: ajaxdata,
		          	success: function(opciones)
		          	{
		    			$("#select2-municipio-container").text("Seleccione");
		    	        $("#municipio").html(opciones);
		    	        $("#municipio").val("");
		        	}
		        })
		    });
		    $('.tel').on('keydown', function (event)
		    {
			    if (event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 13 || event.keyCode == 37 || event.keyCode == 39)
			    {

			    }
			    else
			    {
			        if((event.keyCode>47 && event.keyCode<60 ) || (event.keyCode>95 && event.keyCode<106 ))
			        {
			        	inputval = $(this).val();
			        	var string = inputval.replace(/[^0-9]/g, "");
				        var bloc1 = string.substring(0,4);
				        var bloc2 = string.substring(4,7);
				        var string =bloc1 + "-" + bloc2;
				        $(this).val(string);
			        }
			        else
			        {
			        	event.preventDefault();
			        }

			    }
			});
    });
  </script>
<?php
}
function deleted()
{
	$id_ubicacion = $_POST ['id_ubicacion'];
	$table = 'ubicacion';
	$form_data = array(
		'borrado' => 1,
	);
	$where_clause = "id_ubicacion='" . $id_ubicacion . "'";
	$delete = _update( $table, $form_data, $where_clause );
	if ($delete)
	{
		$xdatos ['typeinfo'] = 'Success';
		$xdatos ['msg'] = 'Registro borrado con exito!';
	}
	else
	{
		$xdatos ['typeinfo'] = 'Error';
		$xdatos ['msg'] = 'Registro no pudo ser borrado!';
	}
	echo json_encode ( $xdatos );
}
if (! isset ( $_REQUEST ['process'] ))
{
	initial();
}
else
{
	if (isset ( $_REQUEST ['process'] ))
	{
		switch ($_REQUEST ['process'])
		{
			case 'formDelete' :
				initial();
				break;
				case 'deleted' :
				deleted();
				break;
			}
		}
	}
	?>
