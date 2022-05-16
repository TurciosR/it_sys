<?php
include_once "_core.php";
function initial()
{
    $title = 'Pago a Proveedores';
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
  $_PAGE ['links'] .= '<link href="js/plugins/bootstrap-duallistbox-master/src/bootstrap-duallistbox.css" rel="stylesheet">';
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

  //crear array proveedores
  $sql0="SELECT * FROM proveedores";
  $result0=_query($sql0);
  $count0=_num_rows($result0);
  $array0 =array(-1=>"Seleccione");
  for ($x=0;$x<$count0;$x++) {
      $row0=_fetch_array($result0);
      $id0=$row0['id_proveedor'];
      $description=$row0['nombre'];
      $array0[$id0] = $description;
  }

?>
<style media="screen">
  select
  {
    border: 1px solid #FFFFFF !important;

  }
</style>
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
                        <div class="col-md-12">
                          <div class="form-group"> <label>Proveedores &nbsp;</label></div>
                          <?php
                          $nombre_select0="select_proveedores";
                          $idd0=-1;
                          //$style='width:400px';
                          $style='';
                          $select0=crear_select2($nombre_select0, $array0, $idd0, $style);
                          echo $select0; ?>
                        </div>

                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <br>
                          <label>Seleccion de facturas pendientes de pago</label>
                          <select multiple="multiple" size="6" name="duallistbox_demo2" class="demo2">
                          </select>
                        </div>
                      </div>
                      <br>
                      <div class="row">
                        <div class="col-md-12">
                          <table class="table table-striped table-bordered table-hover" id="tabla">
                            <tr>
                              <td class="col-lg-1">FECHA</td>
                              <td class="col-lg-2">NUMERO</td>
                              <td class="col-lg-1">CARGO</td>
                              <td class="col-lg-1">% DESC</td>
                              <td class="col-lg-1">DESCUENTO</td>
                              <td class="col-lg-1">DEVOLUCION</td>
                              <td class="col-lg-1">BONIFICACION</td>
                              <td class="col-lg-1">RETENCION</td>
                              <td class="col-lg-1">VIÑETA</td>
                              <td class="col-lg-1">SALDO</td>
                              <td class="col-lg-1">ACCIÓN</td>
                            </tr>
                          </table>
                        </div>

                      </div>
                        <input type="hidden" name="process" id="process" value="inser"><br>
                        <div>
                           <input type="button" id="submit1" name="submit1" value="Guardar" class="btn btn-primary m-t-n-xs" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
        include_once ("footer.php");
        echo "<script src='js/funciones/funciones_pago_proveedores.js'></script>";
        echo "<script src='js/plugins/bootstrap-duallistbox-master/src/jquery.bootstrap-duallistbox.js'></script>";
	} //permiso del script
    else
    {
    		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
    }
}

function insertar()
{
    $nombre=$_POST["nombre"];
    $codigo=$_POST["codigo"];

    $sql_exis=_query("SELECT id_color FROM colores WHERE nombre ='$nombre' AND codicolor='$codigo'");
    $num_exis = _num_rows($sql_exis);
    if($num_exis > 0)
    {
        $xdatos['typeinfo']='Error';
        $xdatos['msg']='Este colore ya fue registrado!';
    }
    else
    {
        $table = 'colores';
        $form_data = array(
        'nombre' => $nombre,
        'codicolor' => $codigo,
        );
        $insertar = _insert($table,$form_data );
        if($insertar)
        {
           $xdatos['typeinfo']='Success';
           $xdatos['msg']='Registro guardado con exito!';
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

function genera_select()
{
    $id_proveedor=$_POST['id'];
    $id_sucursal =$_SESSION['id_sucursal'];

    $sql="SELECT cxp.numero_doc, cxp.fecha,cxp.idtransace,cxp.saldo_pend FROM cxp WHERE cxp.id_sucursal='$id_sucursal' AND cxp.id_proveedor=$id_proveedor AND cxp.saldo_pend!=0  ORDER BY cxp.fecha_vence ASC";
    $result=_query($sql);
    $count=_num_rows($result);
    if ($count>0) {
        for ($y=0;$y<$count;$y++) {
            $row=_fetch_array($result);
            $id1=$row['idtransace'];
            $description="".$row['fecha']."|".$row['numero_doc']."| $ ".$row['saldo_pend'];
            echo '<option value="'.$id1.'">'.$description.'</option>';
        }
    } else {
        echo '<option value="">NO SE ENCONTRARON  FACTURAS</option>';
    }
}

function addFactura()
{
  # code...
  $idtransace=$_POST['idtransace'];
  $sql="SELECT cxp.numero_doc, cxp.fecha,cxp.idtransace,cxp.saldo_pend FROM cxp WHERE cxp.idtransace='$idtransace' AND cxp.saldo_pend!=0 ";
  $result=_query($sql);

  while ($row=_fetch_array($result)) {
    # code...
    $fact="<tr class='$row[idtransace]' id='$row[idtransace]'>
          <td>$row[fecha]</td>
          <td>$row[numero_doc] <input type='hidden' id='fact_idtransace' name='fact_idtransace' value='$row[idtransace]'></td>
          <td>$row[saldo_pend]</td>
          <td class='nm'></td>
          <td class=''></td>
          <td class='ed'></td>
          <td class='ed'></td>
          <td class='ed'></td>
          <td class='ed'></td>
          <td class='$row[idtransace]'>$row[saldo_pend]</td>
          <td class='text-center'></td>
        </tr>";
      $xdatos['fact']=$fact;
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
          case 'genera_select':
              genera_select();
              break;
          case 'addFactura':
              addFactura();
              break;
        }
    }
}
?>
