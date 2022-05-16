<?php
include_once "_core.php";

function initial() {
	// Page setup
	$id_user=$_SESSION["id_usuario"];

	$_PAGE = array ();
	$_PAGE ['title'] = 'Agregar Servicios';
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
														<div class="row">
															<div class="col-md-12">
																<div class="form-group col-md-12">
																	<label>Descripción</label>
																	<input type="text" placeholder="Ingresa la descripción" class="form-control" id="descripcion" name="descripcion">
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="form-group col-md-6">
																	<label>Costo</label> <input type="text" placeholder="Ingrese costo" class="form-control numeric" id="costo" name="costo">
																</div>
																<div class="form-group col-md-6">
																	<label>Precio</label>
																	<input type="text" placeholder="Ingrese Precio " class="form-control numeric" id="precio" name="precio">
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="form-group col-md-6">
		                              <label>Categoría Servicio</label>
		                              <div class="input-group">
		                              	<select name="cat_servicios" id="cat_servicios"  style="width:490px;">
		                                <option value=''>Seleccione</option>
		                                 <?php
		                                 $qcat=_query("SELECT *FROM categoria ORDER BY nombre ");
		                                 while($row_cat=_fetch_array($qcat))
		                                 {
		                                     $id_categoria=$row_cat["id_categoria"];
		                                     $nombre=$row_cat["nombre"];
		                                     echo "
		                                 <option value='$id_categoria'>$nombre</option>
		                                 ";
		                                 }
		                                 ?>
		                            		</select>
		                              </div>
		                            </div>
																<div class="form-group col-md-6">
		                              <div class="form-group"><label class="col-sm-2 control-label">Activo </label>
		                                <div class="col-sm-10">
		                                    <div class="checkbox i-checks"><label> <input type="checkbox"  id="activo" name="activo" value="1"> <i></i>  </label></div>
		                                </div>
		                              </div>
		                              <input type="hidden" name="process" id="process" value="insert"><br>
		                            </div>
															</div>
														</div>
                    				<input type="hidden" name="process" id="process" value="insert">
														<a id="plus_item" name="plus_item" class="btn btn-primary m-t-n-xs pull-right"><i class="fa fa-plus"></i> Agregar ítem</a>
														<div class="row">
															<div class="col-md-12">
																<table class="table bordered">
																	<thead>
																		<th class="col-md-11">Descripción</th>
																		<th class="col-md-1">Acción</th>
																	</thead>
																	<tbody id="plus_detalle">

																	</tbody>
																</table>
															</div>
														</div>
                            <div class="row">
															<div class="col-md-12">
																<input type="submit" id="submit1" name="submit1" value="Guardar" class="btn btn-primary m-t-n-xs pull-right"/>
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


function insertar_servicio(){

	$id_servicio=$_POST["id_servicio"];
  $descripcion=$_POST["descripcion"];
	$id_categoria=$_POST["id_categoria"];
	$costo=$_POST["costo"];
  $precio=$_POST["precio"];
  $estado=$_POST["estado"];
	$array_json=$_POST['array_data'];


    $sql_result=_query("SELECT id_servicio,descripcion FROM servicios WHERE descripcion='$descripcion'");
    $row_update=_fetch_array($sql_result);
    $id_update=$row_update["id_servicio"];
    $numrows=_num_rows($sql_result);
    //$num=_num_rows($contar);


    $table = 'servicios';
    $form_data = array (
		'id_servicio' => $id_servicio,
    'id_categoria' => $id_categoria,
    'descripcion' => $descripcion,
    'costo' => $costo,
    'precio' => $precio,
    'estado' => $estado,
    'tipo_prod_servicio' => 'SERVICIO'
    );

    if($numrows == 0 && trim($descripcion)!=''){

    $insertar = _insert($table,$form_data);
    if($insertar)
		{
			$id_ser = _insert_id();
			$array = json_decode($array_json, true);
			foreach ($array as $fila)
			{
				$item = $fila["item"];
				$tabla_item = "servicio_detalle";
				$lista_item = array(
					'descripcion' => $item,
					'id_servicio' => $id_ser,
				);
				$inser_item = _insert($tabla_item, $lista_item);
			}
       //$field='id_country';
       //$max_country = max_id($field,$table);
       //$xdatos['max_id']=$max_country;
       $xdatos['typeinfo']='Success';
       $xdatos['msg']='Registro insertado correctamente !';
       $xdatos['process']='insert';
    }
    else{
       $xdatos['typeinfo']='Error';
       $xdatos['msg']='Registro no insertado !';
		}
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
	case 'insert':
		insertar_servicio();
		break;

	}
}
}
?>
