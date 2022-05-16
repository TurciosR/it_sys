<?php
include_once "_core.php";
include('num2letras.php');
include('facturacion_funcion_imprimir.php');
//include("escpos-php/Escpos.php");
function initial()
{
    $idtransace=$_REQUEST["idtransace"];
    //permiso del script
    $id_user=$_SESSION["id_usuario"];
    $admin=$_SESSION["admin"];
    $uri = $_SERVER['SCRIPT_NAME'];
    $filename=get_name_script($uri);
    $links=permission_usr($id_user, $filename);
    $fecha=date('d-m-Y');
    $id_sucursal=$_SESSION['id_sucursal'];
    $sql0="SELECT monto, numero_doc, saldo_pend, alias_tipodoc, fecha_vence
	FROM cxp
	WHERE idtransace='$idtransace'";
    $result = _query($sql0);
    $numrows= _num_rows($result);
    for ($i=0;$i<$numrows;$i++) {
        $row = _fetch_array($result);
        $total=$row['monto'];
        $numero_doc=$row['numero_doc'];
        $alias_tipodoc=$row['alias_tipodoc'];
        $saldo_pend=$row['saldo_pend'];
        $fecha_vence=$row['fecha_vence'];
    } ?>

<div class="modal-header">
	<h4 class="modal-title">Descontar Cuenta por Pagar</h4>
</div>

<div class="modal-body">
	<div class="row">
	<div class="form-group col-md-6">
		<label>Deuda Total&nbsp;</label>
		<input type="text"  class='form-control input_header_panel'  id="total_deuda" value='<?php echo $total; ?>' readOnly />
	</div>
	<div class="form-group col-md-6">
		<label>Saldo Actual&nbsp;</label>
		<input type="text"  class='form-control input_header_panel'  id="saldo_pendiente"  value='<?php echo $saldo_pend; ?>' readOnly />
	</div>
  <div class="form-group col-md-6">
		<label>Tipo de descuento&nbsp;</label>
		<select class="form-control select" id="descuento" name="descuento" style="width: 100%;">
			<option value="">Seleccione</option>
			<?php
          $sql_b = _query("SELECT * FROM tipo_descuento");
          while ($row_b = _fetch_array($sql_b)) {
              echo "<option value='".$row_b["id_tipo_descuento"]."'>".$row_b["tipo_descuento"]."</option>";
          }
      ?>
		</select>
	</div>

	<div class="form-group col-md-6">
		<label>Documento Numero</label>
		<input type="text"  class='form-control input_header_panel'  id="numero_doc">
	</div>
	<div class="form-group col-md-6">
		<label>Monto&nbsp;</label>
		<input type="text"  class='form-control input_header_panel'  id="monto">
	</div>

	<?php if ($saldo_pend!=0): ?>
		<div class="form-group col-md-12">
			<button class="btn btn-primary pull-right" id="abon"><i class="fa fa-plus"></i> Descontar</button>
		</div>

	<?php else: ?>
			<div class="alert col-md-12 alert-info">
	  <strong>Info!</strong> La cuenta se encuentra saldada.
			</div>
	<?php endif; ?>
	</div>


			<?php

                if ($links!='NOT' || $admin=='1') {
                    ?>

			<div class="row" id="row1">
				<div class="col-md-12">
				<input type='hidden' name='idtransace' id='idtransace' value='<?php echo $idtransace; ?>'>
				<input type='hidden' name='urlprocess' id='urlprocess'value="<?php echo $filename; ?>">
					<h4>Vence: &nbsp;<?php echo ED($fecha_vence); ?></h4>
					</header>
					<section>
						<div class="table-responsive m-t">
							<table class="table table2 table-fixed table-striped" id="inventable">
								<thead>
								<tr>
                 <th class="text-success">Fecha</th>
                 <th class="text-success">Hora</th>
								 <th class="text-success">Tipo de descuento</th>
                 <th class="text-success">Numero Documento</th>
								 <th class="text-success">Monto</th>
								</tr>
							</thead>
								<tbody id='cuerpo_tabla' class='tbody1 tbody2'>
								<?php
                    $sql = _query("SELECT tipo_descuento.tipo_descuento,descuento.numero_doc,descuento.fecha,descuento.hora, descuento.monto FROM descuento JOIN tipo_descuento ON tipo_descuento.id_tipo_descuento=descuento.id_tipo_descuento WHERE descuento.idtransace='$idtransace' ORDER BY descuento.id_descuento DESC");
                    $tot = 0;
                    while ($row = _fetch_array($sql)) {
                        $tot += $row["monto"];
                        echo "<tr>";
                        echo "<td>".$row["fecha"]."</td>";
                        echo "<td>".$row["hora"]."</td>";
                        echo "<td>".$row["tipo_descuento"]."</td>";
                        echo "<td>".$row["numero_doc"]."</td>";
                        echo "<td>".number_format($row["monto"], 2)."</td>";
                        echo "</tr>";
                    } ?>
								</tbody>
								<tfoot class='thead1'>
								<tr class='tr1'>
									<th class="text-success" >Total</th>
								 <th class="text-success"></th>
                 <th class="text-success"></th>
                 <th class="text-success"></th>
								 <th id="total_descuentos" class="text-success"><?php echo number_format($tot,2); ?></th>
								</tr>
							</tfoot>
						</table>
					</section>

				</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" id="closing" data-dismiss="modal">Salir</button>
			</div>
		</div>
	</div>

<script type="text/javascript">
	$(document).ready(function(){
    $('.select').select2({
      placeholder: {
        id: '', // the value of the option
        text: 'Seleccione'
      },
      allowClear: true
    });
		$("#monto").numeric({negative:false,decimalPlaces: 2});
	});
</script>
<?php
                } //permiso del script
    else {
        echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
    }
}


function descontar()
{
    $idtransace = $_POST["idtransace"];
    $numero_doc = $_POST["numero_doc"];
    $id_tipo_descuento = $_POST["tipo_descuento"];
    $monto = $_POST["monto"];
		$fecha=date("Y-m-d");
		$hora=date("H:i:s");

    $nuevosaldo=0;

    $sql=_query("SELECT cxp.idtransace,cxp.saldo_pend,cxp.monto FROM cxp WHERE idtransace=$idtransace");
    $row=_fetch_array($sql);
    $saldo_pend=$row['saldo_pend'];
    $monto_deuda=$row['monto'];

    if ($monto<=$saldo_pend) {
        $table = 'descuento';
        $form_data = array(
        'idtransace' => $idtransace,
        'numero_doc' => $numero_doc,
        'monto' => $monto,
        'id_tipo_descuento' => $id_tipo_descuento,
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
                $xdatos['msg']='Descuento realizado con exito!';
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
        $xdatos['msg']='El monto a descontar es superior al saldo pendiente!';
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
 function refresh()
{
  # code...
  $idtransace=$_REQUEST['idtransace'];
  $sql = _query("SELECT tipo_descuento.tipo_descuento,descuento.numero_doc,descuento.fecha,descuento.hora, descuento.monto FROM descuento JOIN tipo_descuento ON tipo_descuento.id_tipo_descuento=descuento.id_tipo_descuento WHERE descuento.idtransace='$idtransace' ORDER BY descuento.id_descuento DESC");
  $tot = 0;

  $opt = "";
  while ($row = _fetch_array($sql)) {
      $tot += $row["monto"];
      $opt .= "<tr>";
      $opt .= "<td>".$row["fecha"]."</td>";
      $opt .= "<td>".$row["hora"]."</td>";
      $opt .= "<td>".$row["tipo_descuento"]."</td>";
      $opt .= "<td>".$row["numero_doc"]."</td>";
      $opt .= "<td>".number_format($row["monto"], 2)."</td>";
      $opt .= "</tr>";
}

$sql0="SELECT saldo_pend
FROM cxp
WHERE idtransace='$idtransace'";
$result = _query($sql0);
$numrows= _num_rows($result);
for ($i=0;$i<$numrows;$i++) {
    $row = _fetch_array($result);
    $saldo_pend=$row['saldo_pend'];
}

$tot=number_format($tot,2);


$xdatos["typeinfo"] = "Success";
$xdatos["opt"] = $opt;
$xdatos["tot"] = $tot;
$xdatos["saldo_pend"] = $saldo_pend;
echo json_encode($xdatos);

}
//functions to load
if (!isset($_REQUEST['process'])) {
    initial();
}
//else {
if (isset($_REQUEST['process'])) {
    switch ($_REQUEST['process']) {
    case 'formEdit':
        initial();
        break;
    case 'val':
        cuentas_b();
        break;
    case 'descontar':
        descontar();
        break;
    case 'refresh':
        refresh();
        break;
    }

    //}
}
?>
