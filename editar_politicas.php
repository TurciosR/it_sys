<?php
include "_core.php";
include ('num2letras.php');
//include ('facturacion_funcion_imprimir.php');
//include("escpos-php/Escpos.php");
function initial() {
		//permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

  $id_garantia = $_REQUEST["id_garantia"];
  $sql_politicas = _query("SELECT * FROM politicas_ga WHERE id_garantia = '$id_garantia'");
  $cuenta = _num_rows($sql_politicas);
	//$id_sucursal=$_SESSION['id_sucursal'];

	echo "<style type='text/css'>
    #inventable{
    	font-family: 'Open Sans';
    	 font-style: normal;
    	 font-size: small;
		font-weight: 400;
		src: local('Open Sans'), local('OpenSans'), url(fonts/apache/opensans/OpenSans-Regular.ttf) format('truetype'), url(fonts/apache/opensans/OpenSans.woff) format('woff');
    }
    .table thead tr > th.success{
		background-color: #428bca !important;
		color: white !important;
	}
	.table > tfoot > tr > .thick-line {
		border-top: 2px solid;
	}
	</style>";


?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Politicas de Garantia</h4>
</div>

<div class="modal-body">
		<div class="row" id="row1">
				<?php
					if ($links!='NOT' || $admin=='1' ){
				?>
						<div>
							<!--load datables estructure html-->
              <header>
                <div class="row">
                  <div class="col-lg-10">
                    <label for="">Descripción</label>
                    <textarea name="name"  id="politica" class="form-control" rows="1" cols="30"></textarea>
                    <textarea name="politica_text"  id="politica_text" class="form-control" rows="1" cols="30" style="display: none"></textarea>
                    <input type="hidden" name="politica_id"  id="politica_id" class="form-control">
                  </div>
                  <div class="col-lg-2">
                    <br><br>
                    <button type="button" class="btn btn-primary pull-right" id="btnAgregar"><i class="fa fa-plus"></i>Agregar</button>
                  </div>

                </div>

              </header>
							<section>
								<div class="table-responsive m-t">
									<table class="table table-condensed table-striped" id="inventable2">
									<thead class="thead-inverse">
										<tr>
										<th class='success col-md-11'>Descripci&oacute;n</th>
										<th class='success col-md-1'>Acción</th>
                    </tr>
									</thead>
									<tbody id="lista_poli">
                    <?php
                      if($cuenta > 0)
                      {
                        $lista = "";
                        while ($row_po = _fetch_array($sql_politicas))
                        {
                          $descripcion = $row_po["descripcion"];
                          $pol_id = $row_po["politica_id"];
                          $lista .= "<tr><input type='hidden' name='pol_id'  id='pol_id' value='".$pol_id."'>";
                          $lista .= "<td id='desc'>".$descripcion."</td>";
                          $lista .= "<td class='dele'><input  id='delpo' type='button' class='btn btn-danger fa pull-right'  value='&#xf1f8;'></td>";
                          $lista .= "</tr>";
                        }
                        echo $lista;
                      }
                    ?>
                  </tbody>

								</table>
                <input type='hidden' id="id_garantia"  value='<?php echo $id_garantia; ?>'>
					</section>

						</div>

					<button type="button" class="btn btn-primary" id="btnGua" data-dismiss="modal">Guardar</button>
					<!--button type="button" class="btn btn-danger" data-dismiss="modal">Finalizar</button-->

		</div>
	</div>


<?php

} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

function editar()
{
  $id_garantia = $_POST["id_garantia"];
  $cuantos = $_POST["cuantos"];
  $array_json=$_POST['json_arr'];
  $actualiza = false;
  if ($cuantos>0)
  {
    $tabla = "politicas_ga";
    $w = "id_garantia='".$id_garantia."'";
    $dele = _delete($tabla, $w);
    if($dele)
    {
      $array = json_decode($array_json, true);
      foreach ($array as $fila)
      {
        $politicas=$fila["desc"];
				$pol_id=$fila["pol_id"];
				if($pol_id == 0)
				{
					$tab = "politica";
					$lis_po = array(
						'descripcion' => $politicas,
					);
					$ins = _insert($tab, $lis_po);
					$pol_id = _insert_id();
				}
        $form_data = array(
            'descripcion' => $politicas,
            'id_garantia' => $id_garantia,
            'politica_id' => $pol_id,
          );
          $insert = _insert($tabla, $form_data);
          if($insert)
          {
            $actualiza = true;
          }
      }
    }
  }
  if($actualiza)
  {
    $xdatos['typeinfo']='Success';
    $xdatos['msg']='Politicas editadas con Exito !';
    $xdatos['process']='edit';
  }
  else
  {
    $xdatos['typeinfo']='Error';
    $xdatos['msg']='Error al actualizar politicas !';
  }
  echo json_encode($xdatos);
}
//functions to load
if(!isset($_REQUEST['process'])){
	initial();
}
//else {
if (isset($_REQUEST['process'])) {


	switch ($_REQUEST['process']) {
	case 'formEdit':
		initial();
		break;
  case 'edit_po':
		editar();
		break;
	}

 //}
}
?>
<script type="text/javascript">
  $(document).ready(function()
  {
    $("#politica").typeahead({
      source: function(query, process) {
        $.ajax({
          url: 'autocomplete_politica.php',
          type: 'POST',
          data: 'query=' + query,
          dataType: 'JSON',
          async: true,
          success: function(data) {
            process(data);
          }
        });
      },
      updater: function(selection) {
        var prod0 = selection;
        var prod = prod0.split("|");
        var id_prod = prod[0];
        var descrip = prod[1];
        $("#politica_text").val(descrip);
        $("#politica").css("display", "none");
        $("#politica_text").removeAttr("style");
        $("#politica_id").val(id_prod);
        //agregar_producto(id_prod);
      }
    });
  });
</script>
