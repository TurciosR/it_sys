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

	$id_sucursal=$_SESSION['id_sucursal'];
	$id_contrato=$_REQUEST['id_contrato'];

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
	<h4 class="modal-title">Clausulas</h4>
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
                  <div class="col-md-12">
                    <label>Clausula</label>
                    <input type="text" name="clausula" id="clausula" class="form-control">
                    <input type="hidden" name="id_clausula" id="id_clausula" class="form-control">
                    <label id="text_clausula"></label>
                  </div>
                </div>

              </header>
							<section>
								<div class="table-responsive m-t">
									<table class="table table-condensed table-striped" id="inventable2">
									<thead class="thead-inverse">
										<tr>
										<th class='col-lg-3'>Titulo</th>
										<th class='col-lg-8'>Descripci&oacute;n</th>
                    <th class='col-lg-1'>Acci&oacute;n</th>
                    </tr>
									</thead>
									<tbody id="caja_clausula">
                    <?php
                      $sql_clausula = _query("SELECT * FROM contrato_clausulas WHERE id_contrato = '$id_contrato'");
                      $cuenta = _num_rows($sql_clausula);
                      if($cuenta > 0)
                      {
                        $lista = '';
                        while ($row = _fetch_array($sql_clausula))
                        {
                          $titulo = $row["titulo"];
                          $descripcion = $row["descripcion"];
                          $id_clausula = $row["id_clausula"];

                          $lista .= "<tr><td class='td_titulo'><input type='hidden' id='titulo' value='".$titulo."' class='titulo_td'>".$titulo."</td>";
                          $lista .= "<td class='td_descripcion'><input type='hidden' id='descripcion' class='descripcion_td' value='".$descripcion."'>".$descripcion."</td>";
                          $lista .= "<td class='borrar text-success'><input type='hidden' id='id_clausula' value='".$id_clausula."'><input  id='delprod' type='button' class='btn btn-danger fa pull-right'  value='&#xf1f8;'></td></tr>";
                        }
                        echo $lista;
                      }
                    ?>
                  </tbody>

								</table>
                <input type="hidden" name="id_con" class="id_con"  id="id_con" value="<?php echo $id_contrato;?>">
					</section>

						</div>

					<button type="button" class="btn btn-primary pull-right" id="btnGua" data-dismiss="modal">Guardar</button>
					<!--button type="button" class="btn btn-danger" data-dismiss="modal">Finalizar</button-->

		</div>
	</div>

<script type="text/javascript">
$("#clausula").typeahead({
  source: function(query, process) {
    $.ajax({
      url: 'autocomplete_clausula.php',
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
    var id_clausula = prod[0];
    var titulo = prod[1];
    var descrip = prod[2];

    add_calusula(id_clausula);
    //agregar_producto(id_prod);
  }
});
</script>
<?php

} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}
function clausula()
{
  $id_contrato = $_POST["id_contrato"];
  $cuantos = $_POST["cuantos"];
  $array_json1=$_POST['datos'];
  $tabla = "contrato_clausulas";
  $i = 0;
  $wpp = "id_contrato = '".$id_contrato."'";
  $dele = _delete("contrato_clausulas", $wpp);
  if ($dele)
  {
    // echo "Entra";
    $array1 = json_decode($array_json1, true);
    foreach ($array1 as $fila1)
    {
      $id_clausula=$fila1['id_clausula'];
      $titulo=$fila1['titulo'];
      $descripcion=$fila1['descripcion'];

      $table_fact_det= 'contrato_clausulas';
      $data_fact_det = array(
        'id_contrato' => $id_contrato,
        'id_clausula' => $id_clausula,
        'titulo' => $titulo,
        'descripcion' => $descripcion,
      );
      $insertar_fact_det = _insert($table_fact_det, $data_fact_det);
      echo _error();
      if(!$insertar_fact_det)
      {
        // $insertar_clausula = false;
      }
      else
      {
        $i += 1;
      }
    }
  }

  if($cuantos == $i)
  {
    $xdatos['typeinfo'] = "Success";
    $xdatos['msg'] = "Clausulas agregadas correctamente";
  }
  else
  {
    $xdatos['typeinfo'] = "Error";
    $xdatos['msg'] = "Fallo al insertar las clausulas";
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
  case 'agregar':
		clausula();
		break;
	}

 //}
}
?>
