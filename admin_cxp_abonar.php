<?php
include_once "_core.php";
function initial()
{
    $title = 'Abonar a Cuentas por Pagar';
    $_PAGE = array();
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
    $id_sucursal=$_SESSION["id_sucursal"];
    $uri = $_SERVER['SCRIPT_NAME'];
    $id_proveedor=$_REQUEST['id_proveedor'];
    $filename=get_name_script($uri);
    $links=permission_usr($id_user, $filename);
    $sql_p=_query("SELECT proveedores.nombre FROM proveedores WHERE id_proveedor=$id_proveedor");
    $row_p=_fetch_array($sql_p);
    $n_proveedor=$row_p['nombre'];

    $sql_d =_query("SELECT SUM(cxp.saldo_pend) as deuda FROM cxp JOIN proveedores ON proveedores.id_proveedor=cxp.id_proveedor WHERE cxp.id_sucursal=$id_sucursal AND proveedores.id_proveedor=$id_proveedor GROUP BY cxp.id_proveedor");
    $row_d =_fetch_array($sql_d);
    $deuda =$row_d['deuda'];

    $sqlp = _query("SELECT COUNT(*) as c FROM cxp as c WHERE c.saldo_pend!=0 AND c.id_proveedor=$id_proveedor");
    $con =_fetch_array($sqlp);
    $facp=$con['c'];

?>

  <div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
      <div class="col-lg-12">
        <div class="ibox">
          <?php
                  //permiso del script
                    if ($links!='NOT' || $admin=='1') {
                        ?>
            <div class="ibox-title">
              <h5><?php echo $title;
                        echo": $n_proveedor"; ?></h5>
            </div>
            <div class="ibox-title">
              <h5><?php echo "Deuda total: $";
                        echo number_format($deuda,2); ?></h5>

            </div>
            <div class="ibox-title">
              <h5><?php echo "  Facturas pendientes de pago: ";
                        echo $facp ?></h5>
            </div>

            <div class="ibox-content">
              <form name="formulario" id="formulario">
                <input type="hidden" id="id_proveedor" name="id_proveedor" value="<?php echo $id_proveedor ?>">
                <input type="hidden" id="total_deuda" name="total_deuda" value="<?php echo round($deuda,2); ?>">
                <div class="row ">
                  <div class="col-lg-12 has-info single-line">
                    <div class="form-group ">
                      <div class="row">


                      <div class="form-group col-md-6">
                        <label>Banco&nbsp;</label>
                        <select class="form-control select" id="banco" style="width: 100%;">
                          <option value="">Seleccione</option>
                          <?php
                              $sql_b = _query("SELECT * FROM bancos");
                              while ($row_b = _fetch_array($sql_b)) {
                                  echo "<option value='".$row_b["id_banco"]."'>".$row_b["nombre"]."</option>";
                              }
                          ?>
                        </select>
                      </div>
                      <div class="form-group col-md-6">
                        <label>Cuenta&nbsp;</label>
                        <select class="form-control select" id="cuenta" style="width: 100%;">
                          <option value="">Seleccione</option>
                        </select>
                      </div>
                      </div>
                      <div class="row">


                      <div class="form-group col-md-6">
                        <label>Saldo Cuenta&nbsp;</label>
                        <input class="form-control" disabled type="text" id="saldo" name="saldo" value="">
                      </div>
                      <div class="form-group col-md-6">
                        <label>Fecha Cheque &nbsp;</label>
                        <input class="form-control datepick" disabled type="text" id="fecha" name="fecha" value="">
                      </div>
                      </div>

                      <div class="row">
                      <div class="form-group col-md-6">
                        <label>Cheque&nbsp;</label>
                        <input disabled type="text"  class='form-control input_header_panel'  id="cheque">
                      </div>
                      <div class="form-group col-md-6">
                        <label>Monto&nbsp;</label>
                        <input disabled type="text" hola="hola" class='form-control input_header_panel'  id="monto" name="monto">
                      </div>
                      </div>
                      <div class="col-lg-12">
                        <button disabled class="btn btn-primary m-t-n-xs" type="button" id="agregar" name="agregar" value="Agregar cheque"><i class="fa fa-plus"> Agregar cheque</i></button>
                      </div>

                    </div>

                  </div>
                </div>
                <br>
                <table class="table table-striped table-bordered table-hover" id="tabla">
                </table>
                <input type="hidden" name="process" id="process" value="insert"><br>
                <div>
                  <input type="submit" disabled id="submit1" name="submit1" value="Guardar" class="btn btn-primary m-t-n-xs" />
                </div>
              </form>
            </div>
        </div>
      </div>
    </div>
  </div>
  <?php
        include_once("footer.php");
                        echo "<script src='js/funciones/funciones_cxp_p.js'></script>";
                    } //permiso del script
    else {
        echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
    }
}

function insertar()
{
  $verMcB=0;
  $verAcxp=0;
  $verCxp=0;

  $id_sucursal=$_SESSION["id_sucursal"];
  $id_usuario=$_SESSION["id_usuario"];
  $sqlNe=_query("SELECT empleados.nombre FROM usuario JOIN empleados ON empleados.id_empleado=usuario.id_empleado WHERE usuario.id_usuario=$id_usuario ");
  $rowNe=_fetch_array($sqlNe);
  $responsable=$rowNe['nombre'];

  $json_arr = $_POST["json_arr"];
  $json_arr2 = $_POST["json_arr2"];
  $idtransace = 0;
  $banco = 0;
  $cuenta = 0;
  $cheque = 0;
  $monto = 0;

  $sql_result=_query("SELECT numero FROM correlativos WHERE id_sucursal='$id_sucursal' AND alias='VOC' ");
  $row=_fetch_array($sql_result);
  $correlativo=$row['numero']+1;

  $correlative=str_pad($correlativo, 15, '0', STR_PAD_LEFT);

  $id_cuenta=0;
  $numero_doc=0;
  $salida=0;

  $fecha=date("Y-m-d");
  $hora=date("H:i:s");

  _begin();
  $array2 = json_decode($json_arr2, true);
  foreach ($array2 as $fila2) {
    $id_cuenta=$fila2['id_cuenta'];
    $tipo="Egreso";
    $alias_tipodoc="CHQ";
    $numero_doc=$fila2['numero_doc'];
    $salida=$fila2['salida'];
    $concepto='Pago de facturas';

    $sql = _query("SELECT mov_cta_banco.id_movimiento,mov_cta_banco.id_cuenta,mov_cta_banco.saldo FROM mov_cta_banco WHERE mov_cta_banco.id_cuenta=$id_cuenta AND id_movimiento=(SELECT MAX(mov_cta_banco.id_movimiento) AS ultm FROM mov_cta_banco WHERE mov_cta_banco.id_cuenta=$id_cuenta)");
    $row = _fetch_array($sql);
    $saldo = $row['saldo'];
    $saldo=round(($saldo-$salida),2);

    $table = 'mov_cta_banco';
    $form_data = array(
    'id_cuenta' => $id_cuenta,
    'tipo' => $tipo,
    'alias_tipodoc' => $alias_tipodoc,
    'numero_doc' => $numero_doc,
    'salida' => $salida,
    'saldo' => $saldo,
    'fecha' => $fecha,
    'responsable' => $responsable,
    'concepto' => $concepto,
    );
    $insertar = _insert($table, $form_data);
    if ($insertar) {
      # code...
    }
    else {
      # code...
      $verMcB=$verMcB+1;
    }
  }


  $array = json_decode($json_arr, true);
  foreach ($array as $fila) {
    $idtransace = $fila['idtransace'];
    $banco = $fila['id_banco'];
    $cuenta = $fila['id_cuenta'];
    $cheque = $fila['cheque'];
    $monto = $fila['monto'];
    $fecha_cheque=$fila['fecha'];
    $monto_cheque=$fila['montocheque'];

    $nuevosaldo=0;

    $sql=_query("SELECT cxp.idtransace,cxp.saldo_pend,cxp.monto FROM cxp WHERE idtransace=$idtransace");
    $row=_fetch_array($sql);
    $saldo_pend=$row['saldo_pend'];
    $monto_deuda=$row['monto'];

    $table = 'abono_cxp';
    $form_data = array(
    'idtransace' => $idtransace,
    'id_banco' => $banco,
    'id_cuenta' => $cuenta,
    'cheque' => $cheque,
    'monto' => $monto,
    'fecha' => $fecha,
    'hora' => $hora,
    'fecha_cheque' => $fecha_cheque,
    'monto_cheque' => $monto_cheque,
    'numero_doc'=>$correlative,
    );
    $insertar = _insert($table, $form_data);
    if ($insertar) {
        $nuevosaldo=round(($saldo_pend-$monto), 2);
        $table = 'cxp';
        $form_data = array(
        'saldo_pend' => $nuevosaldo,
        );
        $where_clause = "idtransace='" . $idtransace . "'";
        $updates = _update($table, $form_data, $where_clause);
        if ($updates) {

          $table = 'correlativos';
          $form_data = array(
          'numero' => $correlativo
          );

          $where_clause = "id_sucursal ='".$id_sucursal."' AND alias='VOC'";
          $insertar = _update($table, $form_data, $where_clause);

        } else {
          $verCxp=$verCxp+1;
        }
    } else {
      $verAcxp=$verAcxp+1;

    }

  }

  if ($verMcB==0&&$verAcxp==0&&$verCxp==0) {
      _commit();
      $xdatos['typeinfo']='Success';
      $xdatos['msg']='Abono realizado con exito!';

  } else {
      _rollback();
      $xdatos['typeinfo']='Error';
      $xdatos['msg']='Registro no pudo ser guardado !'.$verMcB.$verAcxp.$verCxp;
  }

  echo json_encode($xdatos);
}
function abonar()
{
    $idtransace = $_POST["idtransace"];
    $banco = $_POST["banco"];
    $cuenta = $_POST["cuenta"];
    $cheque = $_POST["cheque"];
    $monto = $_POST["monto"];
    $fecha=date("Y-m-d");
    $hora=date("H:i:s");

    $nuevosaldo=0;

    $sql=_query("SELECT cxp.idtransace,cxp.saldo_pend,cxp.monto FROM cxp WHERE idtransace=$idtransace");
    $row=_fetch_array($sql);
    $saldo_pend=$row['saldo_pend'];
    $monto_deuda=$row['monto'];

    if ($monto<=$saldo_pend) {
        $table = 'abono_cxp';
        $form_data = array(
        'idtransace' => $idtransace,
        'id_banco' => $banco,
        'id_cuenta' => $cuenta,
        'cheque' => $cheque,
        'monto' => $monto,
                'fecha' => $fecha,
                'hora' => $hora,
        );
        $insertar = _insert($table, $form_data);
        if ($insertar) {
            $nuevosaldo=round(($saldo_pend-$monto), 2);
            $table = 'cxp';
            $form_data = array(
            'saldo_pend' => $nuevosaldo,
            );
            $where_clause = "idtransace='" . $idtransace . "'";
            $updates = _update($table, $form_data, $where_clause);
            if ($updates) {
                $xdatos['typeinfo']='Success';
                $xdatos['msg']='Abono realizado con exito!';
            } else {
                $xdatos['typeinfo']='Error';
                $xdatos['msg']='Registro no pudo ser guardado !';
            }
        } else {
            $xdatos['typeinfo']='Error';
            $xdatos['msg']='Registro no pudo ser guardado !';
        }
    } else {
        $xdatos['typeinfo']='Error';
        $xdatos['msg']='El monto a abonar es superior al saldo pendiente!';
    }

    echo json_encode($xdatos);
}
function cuentas_b()
{
    $id_banco = $_POST["id_banco"];
    $sql = _query("SELECT * FROM cuenta_bancos WHERE id_banco='$id_banco'");
    $opt = "<option value=''>Seleccione</option>";
    while ($row = _fetch_array($sql)) {
        $opt .="<option value='".$row["id_cuenta"]."'>".$row["nombre_cuenta"]."</option>";
    }
    $xdatos["typeinfo"] = "Success";
    $xdatos["opt"] = $opt;
    echo json_encode($xdatos);
}

function addCheque()
{
    $id_proveedor = $_POST["id_proveedor"];
    $id_banco = $_POST["id_banco"];
    $id_cuenta = $_POST["id_cuenta"];
    $cheque = $_POST["cheque"];
    $monto = $_POST["monto"];
    $fecha = $_POST["fecha"];
    $id_sucursal =$_SESSION['id_sucursal'];

    $opt="<tr id='a$cheque'>";
    $opt.="<td class='col-lg-4'>Cheque</td>";
    $opt.="<td class='col-lg-8'>Facturas</td>";
    $opt.="</tr>";
    $opt.="<tr id='b$cheque'>";
    $opt.="<td class='col-lg-4'>";
    $opt.="<div class='row'>";
    $opt.="<div class='col-lg-6'>";
    $opt.="<label>Cheque</label>";
    $opt.="<input class='form-control' id='chequeabono' name='chequeabono' monto='$monto' banco='$id_banco' fecha='$fecha' cuenta='$id_cuenta' readOnly type='text' value='$cheque'>";
    $opt.="</div>";
    $opt.="<div class='col-lg-6'>";
    $opt.="<label>Monto</label>";
    $opt.="<input class='form-control' readOnly  type='text' value='$monto'>";
    $opt.="</div>";
    $opt.="<div class='col-lg-6'>";
    $opt.="<label>Borrar cheque</label>";
    $opt.="<button style='width:100%' onclick='remover($cheque)' class='col col-lg-12 btn btn-danger' type='button' name='button'><i class='fa fa-trash'> Borrar</i></button>";
    $opt.="</div>";
    $opt.="<div id='c$cheque' class='col-lg-6'>";
    $opt.="<label>Estado</label>";
    $opt.="<button style='width:100%'  id='d$cheque' onclick='revise()' class='col col-lg-12 btn btn-warning' type='button' name='button'><i id='e$cheque' class='fa fa-exclamation-circle'> Revise abonos</i></button>";
    $opt.="</div>";
    $opt.="</div>";
    $opt.="</td>";
    $opt.="<td class='col-lg-8'>";
    $opt.="<div class='widget-content pre-scrollable' >";
    $opt.="<div class=''>";
    $opt.="<table class='col-lg-12 table table-striped'>";
    $opt.="<tr>";
    $opt.="<td class='col-lg-3'>Factura</td>";
    $opt.="<td class='col-lg-3'>Fecha Vencimiento</td>";
    $opt.="<td class='col-lg-3'>Saldo Pendiente</td>";
    $opt.="<td class='col-lg-3'>Abono</td>";
    $opt.="</tr>";

    $sql=_query("SELECT cxp.numero_doc, cxp.fecha_vence,cxp.idtransace,cxp.saldo_pend FROM cxp WHERE cxp.id_sucursal='$id_sucursal' AND cxp.id_proveedor=$id_proveedor AND cxp.saldo_pend!=0  ORDER BY cxp.fecha_vence ASC");
    while ($row=_fetch_array($sql)) {
      # code...
      $opt.="<tr><td>$row[numero_doc]</td>";
      $opt.="<td>$row[fecha_vence]</td>";
      $opt.="<td>$row[saldo_pend]</td>";
      $opt.="<td> ";
      $opt.="<input class='form-control decimal' numeroDoc='$row[numero_doc]' saldoPend='$row[saldo_pend]' cheque='$cheque' idtransace='$row[idtransace]' monto='$monto' banco='$id_banco' fecha='$fecha' cuenta='$id_cuenta' type='text' id='abono' name='abono' value=''> ";
      $opt.="</td>";
      $opt.="</tr>";
    }
    $opt.="</table></div></div></td></tr>";


    $xdatos["typeinfo"] = "Success";
    $xdatos["opt"] = $opt;
    echo json_encode($xdatos);
}

function saldoBanco()
{
    $id_cuenta = $_POST["id_cuenta"];
    $sql = _query("SELECT mov_cta_banco.id_movimiento,mov_cta_banco.id_cuenta,mov_cta_banco.saldo FROM mov_cta_banco WHERE mov_cta_banco.id_cuenta=$id_cuenta AND id_movimiento=(SELECT MAX(mov_cta_banco.id_movimiento) AS ultm FROM mov_cta_banco WHERE mov_cta_banco.id_cuenta=$id_cuenta)");
    $row = _fetch_array($sql);
    $saldo = $row['saldo'];
    $xdatos["typeinfo"] = "Success";
    $xdatos["saldo"] = $saldo;
    echo json_encode($xdatos);
}
if (!isset($_POST['process'])) {
    initial();
} else {
    if (isset($_POST['process'])) {
        switch ($_POST['process']) {
            case 'insert':
                insertar();
                break;
            case 'val':
                cuentas_b();
                break;
            case 'saldo':
                saldoBanco();
                break;
            case 'agregarCheque':
                addCheque();
                break;
            case 'abonar':
                abonar();
                break;
        }
    }
}
?>
