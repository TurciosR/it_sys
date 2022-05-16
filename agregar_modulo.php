<?php
include_once "_core.php";
function initial()
{
    $title = 'Agregar Modulos';
	$_PAGE = array ();
	$_PAGE ['title'] = $title;
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
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
    $_PAGE ['links'] .= '<link href="css/plugins/fileinput/fileinput.css" media="all" rel="stylesheet" type="text/css"/>';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';

	include_once "header.php";
	include_once "main_menu.php";
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-2">
    </div>
</div>
<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
    			<?php
    	   		  //permiso del script
        			if ($links!='NOT' || $admin=='1' ){
    			?>
                <div class="ibox-title">
                    <h5><?php echo $title; ?></h5>
                </div>
                <div class="ibox-content">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group has-info single-line">
                          <label>Nombre  <span style="color:red;">*</span></label>
                          <input type="text" placeholder="Nombre del modulo" class="form-control" id="nombre" name="nombre">
                      </div>
                  </div>
                  </div>
                    <div class="row">
											<div class="col-lg-6">
												<h4><b>Items</b></h4>
											</div>
											<div class="col-lg-6">
												<a id="plus_item" name="plus_item" class="btn btn-primary m-t-n-xs pull-right"><i class="fa fa-plus"></i> Agregar ítem</a>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<table class="table table-striperd table-hover table-bordered">
													<thead>
														<th class="col-md-11">Descripción</th>
														<th class="col-md-1">Acción</th>
													</thead>
													<tbody id="plus_detalle">

													</tbody>
												</table>
											</div>
										</div>
                    <input type="hidden" name="process" id="process" value="insert"><br>
                    <div>
                       <input type="submit" id="btn_confirmar" name="btn_confirmar" value="Guardar" class="btn btn-primary m-t-n-xs" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
        include_once ("footer.php");
        echo "<script src='js/funciones/funciones_modulo.js'></script>";
        echo " <script src='js/plugins/fileinput/fileinput.js'></script>";
	} //permiso del script
    else
    {
    		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
    }
}

function insertar()
{
  $nombre=$_POST["nombre"];
	$array_json=$_POST['items'];

  $sql_modulo = _query("SELECT * FROM modulos_sistema WHERE nombre LIKE '%$nombre%'");
  $cuenta = _num_rows($sql_modulo);
  if($cuenta == 0)
  {
    $tabla = "modulos_sistema";
    $from_data = array(
      'nombre' => $nombre,
    );

    $insert = _insert($tabla, $from_data);
    if($insert)
    {
      $id_modulo = _insert_id();
      $array1 = json_decode($array_json, true);
      foreach ($array1 as $fila1)
      {
        $item = $fila1["item"];
        $tabla_item = "modulos_sistema_detalle";
        $lista_item = array(
          'nombre' => $item,
          'id_modulo_sistema' => $id_modulo,
        );
        $inser_item = _insert($tabla_item, $lista_item);
      }

      $xdatos['typeinfo']='Success';
      $xdatos['msg']='Registro guardado con exito!';
      $xdatos['process']='insert';
    }
    else{
      $xdatos['typeinfo']='Error';
      $xdatos['msg']='Registro no guardado ';
    }
  }
  else{
    $xdatos['typeinfo']='Error';
    $xdatos['msg']='Ya existe un modulo con estas caracteristicas!!';
  }
	echo json_encode($xdatos);
}
if(!isset($_POST['process']))
{
	initial();
}
else
{
    if(isset($_POST['process']))
    {
        switch ($_POST['process'])
        {
        	case 'insert':
                insertar();
                break;
        }
    }
}
?>
