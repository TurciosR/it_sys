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
	<h4 class="modal-title">Abonar Cuenta por Pagar</h4>
</div>

<div class="modal-body">
	<div class="row">
	<div class="form-group col-md-6">
		<label>Deuda Total&nbsp;</label>
		<input type="text"  class='form-control input_header_panel'  id="items" value='<?php echo $total; ?>' readOnly />
	</div>
	<div class="form-group col-md-6">
		<label>Saldo Actual&nbsp;</label>
		<input type="text"  class='form-control input_header_panel'  id="pares"  value='<?php echo $saldo_pend; ?>' readOnly />
	</div>
	<?php if ($saldo_pend!=0): ?>

	<?php else: ?>
			<div class="alert alert-info">
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
								 <th class="text-success">Banco</th>
								 <th class="text-success">Cuenta</th>
								 <th class="text-success">Cheque</th>
								 <th class="text-success">Monto</th>
								</tr>
							</thead>
								<tbody class='tbody1 tbody2'>
								<?php
                    $sql = _query("SELECT bancos.nombre,cuenta_bancos.nombre_cuenta,abono_cxp.cheque,abono_cxp.monto,abono_cxp.fecha,abono_cxp.hora FROM abono_cxp JOIN bancos ON bancos.id_banco=abono_cxp.id_banco JOIN cuenta_bancos ON cuenta_bancos.id_cuenta=abono_cxp.id_cuenta WHERE abono_cxp.idtransace='$idtransace'  ORDER BY abono_cxp.id_abono DESC");
                    $tot = 0;
                    while ($row = _fetch_array($sql)) {
                        $tot += $row["monto"];
                        echo "<tr>";
                        echo "<td>".$row["fecha"]."</td>";
                        echo "<td>".$row["hora"]."</td>";
                        echo "<td>".$row["nombre"]."</td>";
                        echo "<td>".$row["nombre_cuenta"]."</td>";
                        echo "<td>".$row["cheque"]."</td>";
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
								 <th class="text-success"></th>
								 <th class="text-success"><?php echo $tot; ?></th>
								</tr>
							</tfoot>
						</table>
					</section>
						<?php
                        //$total='1.0';
                        /*number_format($total,2,".","");
                        list($entero,$decimal)=explode('.',$total);
                        $enteros_txt=num2letras($entero);

                        if($entero==0)
                            $dolar=" Cero dolares";
                    if($entero>1)
                        $dolar=" dolares";
                    if($entero==1)
                        $dolar=" dolar";
                    $cadena_salida= "Son: <strong>".$enteros_txt.$dolar." con ".$decimal."/100 ctvs.</strong>";
                    echo "<div class='well m-t'  id='totaltexto'>".$cadena_salida." </div>";
                        */
                        ?>

				</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" id="clos" data-dismiss="modal">Salir</button>
			</div>
		</div>
	</div>

<script type="text/javascript">
	$(document).ready(function(){
		$(".select").select2();
		$("#monto").numeric({negative:false});
	});
</script>
<?php
                } //permiso del script
    else {
        echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
    }
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
    case 'abonar':
        abonar();
        break;
    }

    //}
}
?>
