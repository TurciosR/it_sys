<?php
include ("_core.php");
function initial(){
	$id_contrato = $_REQUEST ['id_contrato'];
	$sql="SELECT c.*, cl.nombre FROM contrato as c JOIN clientes as cl ON c.id_cliente = cl.id_cliente WHERE id_contrato='$id_contrato'";
	$result = _query( $sql );
	$count = _num_rows( $result );
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	//permiso del script

?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Detalles Contrato</h4>
</div>
<div class="modal-body">
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row" id="row1">
			<div class="col-lg-12">
				<?php
						//permiso del script
						if ($links!='NOT' || $admin=='1' ){
						?>
						<?php
						echo "<input type='hidden' class='id_contrato' nombre='id_contrato' id='id_contrato' value='$id_contrato'>";
						?>
				<table class="table bordered" id="tableview">
					<thead>
						<tr>
							<th class="col-md-3">Campo</th>
							<th class="col-md-9">Descripción</th>
						</tr>
					</thead>
					<tbody>
							<?php
                $row = _fetch_array($result);
                $numero_doc = $row["numero_doc"];
                $ex = explode("_", $numero_doc);
                $n_contrato = ltrim($ex[1], "0");
                $monto = $row["monto"];
                $iva = $row["iva"];

								echo "<tr><td>Contrato N°</th><td>".$n_contrato."</td></tr>";
								echo "<tr><td>Nombre</td><td>".$row['nombre']."</td></tr>";
								echo "<tr><td>Fecha Inicio</td><td>".ED($row['fecha'])."</td></tr>";
								echo "<tr><td>Fecha Fin</td><td>".ED($row['fecha_vence'])."</td></tr>";
								echo "</tr>";
							?>
						</tbody>
				</table>
        <hr>
        <h2>Cuotas</h2>
        <table class="table bordered" id="tableview">
					<thead>
						<tr>
							<th class="col-md-6">Descripción</th>
							<th class="col-md-2">Monto</th>
							<th class="col-md-3">Fecha Vence</th>
							<th class="col-md-1">Acción</th>
						</tr>
					</thead>
					<tbody id='cuotas'>
							<?php
                $sql_cuotas = _query("SELECT * FROM cuota_contrato WHERE id_contrato = '$id_contrato' AND cancelada != 1");
                $cuenta = _num_rows($sql_cuotas);
                if($cuenta > 0)
                {
                  $lista = "";
                  while ($row_cuota = _fetch_array($sql_cuotas))
                  {
                    $id_cuota = $row_cuota["id_cuota"];
                    $monto = number_format(round($row_cuota["monto"], 3),2);
                    $mes = $row_cuota["mes"];
                    $anhio = $row_cuota["anhio"];
                    $fecha_vence = $row_cuota["fecha_vence"];
                    $descripcion = "Cuota de ".ucwords(Minu(meses($mes)))." ".$anhio;
                    $lista .= "<tr>";
                    $lista .= "<td><input id='id_cuota' name='id_cuota' type='hidden' value='".$id_cuota."'>".$descripcion."</td>";
                    $lista .= "<td><input id='marcada' name='marcada' type='hidden' value='0'>".$monto."</td>";
                    $lista .= "<td>".ED($fecha_vence)."</td>";
                    $lista .= "<td><div class='checkbox i-checks'><label> <input id='pago_cuota' name='pago_cuota' type='checkbox' value='0'> <i></i> </label></div></td>";
                  }
                  echo $lista;
                }
							?>
						</tbody>
				</table>
			</div>
		</div>

		</div>

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-danger" id="btnPagar">Pagar</button>
	<button type="button" class="btn btn-primary" data-dismiss="modal">Salir</button>

</div>
<script type="text/javascript">
  $('.i-checks').iCheck({
    checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
  });
</script>
<!--/modal-footer -->

<?php
	} //permiso del script
	else
	{
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

if (! isset ( $_REQUEST ['process'] ))
{
	initial();
}
else
{
	if (isset ( $_REQUEST ['process'] ))
	{
		switch ($_REQUEST ['process'])
		{
			case 'formDelete' :
				initial();
				break;
			case 'deleted' :
				deleted();
				break;
		}
	}
}

?>
