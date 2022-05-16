<?php
include_once "_core.php";
function initial()
{
    $title = 'Editar Estante';
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

    $id_estante = $_REQUEST["id_estante"];
    $sql = _query("SELECT * FROM estante WHERE id_estante='$id_estante'");
    $datos = _fetch_array($sql);

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
                    <form name="formulario" id="formulario">
                      <div class="row">
                        <div class="col-lg-6">
                          <div class="form-group has-info single-line">
                            <label>Almacen</label>
                            <select class="col-md-12 select" id="select_almacen" name="select_almacen">
                              <option value="">Seleccione</option>
                              <?php
                                  $sqld = "SELECT * FROM almacen";
                                  $resultd=_query($sqld);
                                  while($almacen = _fetch_array($resultd))
                                  {
                                      echo "<option value='".$almacen['id_almacen']."'";
                                      if($almacen['id_almacen']==$datos['id_almacen'])
                                      {
                                        echo " selected";
                                      }
                                      echo">".$almacen['descripcion']."</option>";
                                  }
                              ?>
                            </select>
                          </div>

                        </div>
                          <div class="col-lg-6">
                            <div class="form-group has-info single-line">
                              <label>Estante</label>
                               <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?php echo $datos['descripcion']; ?>">
                            </div>
                          </div>
                      </div>
                        <input type="hidden" name="id_estante" id="id_estante" value="<?php echo $id_estante; ?>">
                        <input type="hidden" name="process" id="process" value="edited"><br>
                        <div>
                           <input type="submit" id="submit1" name="submit1" value="Guardar" class="btn btn-primary m-t-n-xs" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
        include_once ("footer.php");
        echo "<script src='js/funciones/funciones_estante.js'></script>";
	} //permiso del script
    else
    {
    		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
    }
}

function insertar()
{
  $id_almacen=$_POST["id_almacen"];
  $descripcion=$_POST["descripcion"];
  $id_estante=$_POST["id_estante"];
  $descripcion=strtoupper($descripcion);

  $sql_exis=_query("SELECT id_estante FROM estante WHERE id_almacen ='$id_almacen' AND descripcion='$descripcion' AND id_estante!='$id_estante'");
  $num_exis = _num_rows($sql_exis);
  if($num_exis > 0)
  {
      $xdatos['typeinfo']='Error';
      $xdatos['msg']='Este Estante ya fue registrado!';
  }
  else
  {
      $table = 'estante';
      $form_data = array(
      'id_almacen' => $id_almacen,
      'descripcion' => $descripcion,
      );
      $where_clause = "id_estante='" . $id_estante . "'";
    	$updates = _update ( $table, $form_data, $where_clause );
      if($updates)
      {
         $xdatos['typeinfo']='Success';
         $xdatos['msg']='Registro actualizado con exito!';
         $xdatos['process']='insert';
      }
      else
      {
         $xdatos['typeinfo']='Error';
         $xdatos['msg']='Registro no pudo ser guardado !'._error();
    }
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
        	case 'edited':
                insertar();
                break;
        }
    }
}
?>
