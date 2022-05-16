<?php
include_once "_core.php";

function initial() {
	// Page setup
	$_PAGE = array ();
  $_PAGE ['title'] = 'Editar Servicios';
  $_PAGE ['links'] = null;
  $_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/jqGrid/ui.jqgrid.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
  $_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	$id_servicio= $_REQUEST['id'];

  $sql="SELECT * FROM servicios
     WHERE
     id_servicio='$id_servicio'";
     $result=_query($sql);
     $count=_num_rows($result);
?>

            <div class="row wrapper border-bottom white-bg page-heading">

                <div class="col-lg-2">

                </div>
            </div>
        <div class="wrapper wrapper-content  animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Registrar Servicios</h5>
                        </div>
                        <div class="ibox-content">
                          <?php
						                if ($count>0){

						                  for($i=0;$i<$count;$i++){
						                    $row=_fetch_array($result);
						                    $descripcion=$row['descripcion'];
						                    $id_servicio=$row['id_servicio'];
						                    $precio=$row['precio'];
						                    $costo=$row['costo'];
						                    $estado=$row['estado'];

						                    $id_categoria=$row['id_categoria'];

						                    }
						                  }
						               ?>

														<div class="row">
															<div class="col-md-12">
																<div class="form-group col-md-12">
																	<label>Descripción</label>
																	<input type="text" placeholder="Ingresa la descripción" class="form-control" id="descripcion" name="descripcion" value="<?php echo $descripcion ?> ">
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="form-group col-md-6">
																	<label>Costo</label>
																	<input type="text" placeholder="Ingrese costo" class="form-control numeric" id="costo" name="costo" value="<?php echo $costo ?>">
																</div>
	                              <div class="form-group col-md-6">
																	<label>Precio</label>
																	<input type="text" placeholder="Ingrese Precio " class="form-control numeric" id="precio" name="precio" value="<?php echo $precio ?>">
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="form-group col-md-6">
																	<label>Categoría Servicio</label>
																	<div class="input-group">
																		 <select name="cat_servicios" id="cat_servicios"  style="width:350px;">
																			 <option value=''>Select</option>
																			 <?php
																			 $qcat=_query("SELECT *FROM categoria ORDER BY nombre ");
																			 while($row_cat=_fetch_array($qcat))
																			 {
																				//$id_categoria=$row_cat["id_categoria"];
																				$nombre=$row_cat["nombre"];

																					if($id_categoria==$row_cat['id_categoria'])
																					{
																					echo "<option value='".$row_cat['id_categoria']."' selected>".$row_cat['nombre']."</option>";
																					}
																					else
																					{
																					echo "<option value='".$row_cat['id_categoria']."'>".$row_cat['nombre']."</option>";
																					}

																			 }
																			 ?>
																		</select>
																	</div>
															 	</div>
																<div class="form-group col-md-6">
																	 <div class="form-group"><label class="col-sm-2 control-label">Active </label>
																		 <div class="col-sm-10">
																			 <?php
																			 if($estado==1){
																				 echo "<div class=\"checkbox i-checks\"><label> <input type=\"checkbox\"  id=\"activo\" name=\"activo\" value=\"1\" checked> <i></i>  </label></div>";
																			 }
																			 else
																			 {
																				 echo "<div class=\"checkbox i-checks\"><label> <input type=\"checkbox\"  id=\"activo\" name=\"activo\" value=\"1\"> <i></i>  </label></div>";
																			 }
																			 ?>
																		 </div>
																	 </div>
																	 <input type="hidden" name="process" id="process" value="edited"><br>
															 </div>
															</div>
														</div>
														<input type="hidden" name="id_servicio" id="id_servicio" value="<?php echo $id_servicio;?> ">
														<a id="plus_item" name="plus_item" class="btn btn-primary m-t-n-xs pull-right"><i class="fa fa-plus"></i> Agregar ítem</a>
														<div class="row">
															<div class="col-md-12">
																<table class="table bordered">
																	<thead>
																		<th class="col-md-11">Descripción</th>
																		<th class="col-md-1">Acción</th>
																	</thead>
																	<tbody id="plus_detalle">
																		<?php
																		  	$sql_detalles = _query("SELECT * FROM servicio_detalle WHERE id_servicio = '$id_servicio'");
																				$cuenta = _num_rows($sql_detalles);
																				if($cuenta > 0)
																				{
																					$linea = "";
																					while ($row_deta = _fetch_array($sql_detalles))
																					{
																						$descripcion = $row_deta["descripcion"];
																						$linea .= "<tr>";
																						$linea .= "<td><input type='hidden' id='item' name='item' class='form-control item' value='".$descripcion."'>".$descripcion."</td>";
																						$linea .= '<td class="Delete"><input  id="delprod" type="button" class="btn btn-danger fa pull-right"  value="&#xf1f8;"></td>';
																						$linea .= "</tr>";
																					}
																					echo $linea;
																				}
																		?>
																	</tbody>
																</table>
															</div>
														</div>
                            <div class="row">
															<div class="col-md-12">
																<div class="col-md-12">
																	<input type="submit" id="submit1" name="submit1" value="Guardar" class="btn btn-primary m-t-n-xs pull-right" />
																</div>
															</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

<?php
include_once ("footer.php");
echo "<script src='js/funciones/funciones_servicios.js'></script>";

}


function edited(){
  $id_servicio=$_POST["id_servicio"];
  $id_categoria=$_POST["id_categoria"];
    $descripcion=$_POST["descripcion"];
    $costo=$_POST["costo"];
    $precio=$_POST["precio"];
    $estado=$_POST["estado"];
		$array_json=$_POST['array_data'];
    $table = 'servicios';
		echo $id_servicio;

    $form_data = array (
    'id_servicio' => $id_servicio,
    'id_categoria' => $id_categoria,
    'descripcion' => $descripcion,
    'costo' => $costo,
    'precio' => $precio,
    'estado' => $estado,
    'tipo_prod_servicio' => 'SERVICIO'
    );

   $where_clause = "id_servicio='" . $id_servicio . "'";
  $updates = _update ( $table, $form_data, $where_clause );
    if($updates)
		{
			$tabla_item = "servicio_detalle";
			$ww = "id_servicio = '".$id_servicio."'";
			$delete = _delete($tabla_item, $ww);
			if($delete)
			{
				$array = json_decode($array_json, true);
				foreach ($array as $fila)
				{
					$item = $fila["item"];
					$lista_item = array(
						'descripcion' => $item,
						'id_servicio' => $id_servicio,
					);
					$inser_item = _insert($tabla_item, $lista_item);
				}
			}
      $xdatos['typeinfo']='Success';
      $xdatos['msg']='Registro autualizado ';
      $xdatos['process']='edited';
    }
    else{
      $xdatos['typeinfo']='Error';
      $xdatos['msg']='Registro no editado';
    }
  echo json_encode($xdatos);
}



if(!isset($_POST['process'])){
	initial();
}
else
{
if(isset($_POST['process'])){
switch ($_POST['process']) {
	case 'edited':
		edited();
		break;

	}
}
}
?>
