<?php
include_once "_core.php";

function initial() {
	$_PAGE = array ();
	$_PAGE ['links'] = null;
  $_PAGE ['title'] = "Realizar Despacho";
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
	$id_factura = $_REQUEST ['id_factura'];
	//	$id_sucursal=$_REQUEST['id_sucursal'];
			//permiso del script
	 	$id_user=$_SESSION["id_usuario"];
		$admin=$_SESSION["admin"];
		$uri = $_SERVER['SCRIPT_NAME'];
		$filename=get_name_script($uri);
		$links=permission_usr($id_user,$filename);

		//$id_sucursal=$_SESSION['id_sucursal'];

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

		$sql_fact="SELECT factura.*, clientes.nombre,clientes.id_cliente FROM factura JOIN clientes
		ON factura.id_cliente=clientes.id_cliente
		WHERE factura.id_factura='$id_factura and factura.entregado=0 '
		";
		/*and factura.id_sucursal='$id_sucursal*/
		$result_fact = _query( $sql_fact);
		$row = _fetch_array ( $result_fact);
		date_default_timezone_set('America/El_Salvador');
		$cliente=$row['nombre'];
		$id_cliente=$row['id_cliente'];
		$factnum=$row['num_fact_impresa'];
		$alias_tipodoc=$row['tipo_documento'];
		$total=$row['total'];

		$fecha=ED($row['fecha']);	//permiso del script
?>


        <div class="wrapper wrapper-content  animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox">
							<?php
						//permiso del script
						if ($links!='NOT' || $admin=='1' ){
					?>

					<div>
						<!--load datables estructure html-->
						<!--button type="button"  data-toggle="modal" class="btn btn-primary pull-right" id="btnPol" data-target="#viewModal" data-refresh="true"><i class="fa fa-plus"></i>Agregar Politicas</button-->
						<a data-toggle="modal" href="politicas.php" class="btn btn-primary pull-right" id="btnPol" data-target="#viewModal" data-refresh="false"><i class="fa fa-plus"></i>Agregar Politicas</a>

						<header><h4 class="text-danger">Factura No: &nbsp;<?php echo $alias_tipodoc." ".$factnum;  ?> </h4>
						<h4  class='text-navy'>Fecha:<?php echo $fecha;  ?>&nbsp;
						Cliente:<?php echo $cliente; ?></h4>
						</header>
						<section>
							<div class="table-responsive m-t">
								<table class="table table-condensed table-striped" id="inventable">
								<thead class="thead-inverse">
									<tr>
									<th class='success text-left'>Cantidad</th>
									<th class='success text-left'>Descripci&oacute;n</th>
									<th class='success text-center'>Entregado</th>
									<th class='success text-center'>Serie</th>
									<th class='success text-center' colspan="2">Tiempo Garantia</th>
									</tr>
								</thead>
								<tbody>
								<?php
									$sql_det = "SELECT p.descripcion,p.tiene_garantia,p.tipo_tiempo,p.id_producto,p.dias_garantia, df.cantidad, df.precio_venta, df.subtotal
									FROM productos as p, factura_detalle as df
									 WHERE p.id_producto=df.id_prod_serv AND df.id_factura='$id_factura'";

									$result_det=_query($sql_det);
									$filas=1;
									while($row2 = _fetch_array($result_det))
									{
										echo "<tr>";
										echo "<td>".$row2["cantidad"]."</td>";
										echo "<td>".$row2["descripcion"]."<input type='hidden'  id='id_p' name='id_p' value='".$row2["id_producto"]."'></td>";
										echo "<td id='pv' class='text-center'><input type='checkbox' id='activar' name='activar' class='checkbox i-checks cort' >
										<input type='hidden'  id='cortesia' name='cortesia' value='0'>
										<input type='hidden'  id='idco' name='idco' value='".$filas."'></td>";
										$input="";
										$input1="";
										$input2="";
										$dia="";
										$mes="";
										$año="";
										if ($row2["tipo_tiempo"]==0) {
												$dia="selected";
										}elseif ($row2["tipo_tiempo"]==1) {
											$mes="selected";
										}elseif ($row2["tipo_tiempo"]==2) {
											$año="selected";
										}
										if ($row2["tiene_garantia"]==1) {
											$input.="<input type='text'  id='serie' name='serie' style='width:100px;' ><input type='hidden'  id='ti_ga' name='ti_ga' value='".$row2["tiene_garantia"]."'><input type='hidden'  id='cant' name='cant' value='".$row2["cantidad"]."'><input type='hidden'  id='precio_v' name='precio_v' value='".$row2["precio_venta"]."'>";
											$input1.="<input type='text'  id='dias' name='dias' value='".$row2["dias_garantia"]."' style='width:60px;'><input type='hidden'  id='ti_ga' name='ti_ga' value='".$row2["tiene_garantia"]."'>";
											$input2.="<select  class='select' name='tipo_periodo' id='tipo_periodo' style='width:70%;'>";
											$input2.="<option value='0' $dia>Dia</option>";
											$input2.="<option value='1' $mes>Mes</option>";
											$input2.="<option value='2' $año>Año</option>";
											$input2.="<input type='hidden'  id='ti_ga' name='ti_ga' value='".$row2["tiene_garantia"]."'></select> ";
										}else {
											$input.="---";
											$input1.="---";

										}
										echo "<td class='text-center'>".$input."</td>";
										echo "<td class='text-right'>".$input1."</td>";
										echo "<td class='text-letf'>".$input2."</td>";

										echo "</tr>";
										$filas++;
									}
								?>

								</tbody>
							</table>
					</div>
						<input type="hidden" name="alias_tipodoc" id="alias_tipodoc" value="<?php echo $alias_tipodoc; ?>">
						<input type="hidden" name="id_factura" id="id_factura" value="<?php echo $id_factura; ?>">
						<input type="hidden" name="id_cliente" id="id_cliente" value="<?php echo $id_cliente; ?>">
						<button type="button" class="btn btn-primary pull-right" id="btnDesp">Finalizar</button>
				<!--button type="button" class="btn btn-danger" data-dismiss="modal">Finalizar</button-->


				<!--Show Modal Popups View & Delete -->
				<div class='modal fade' id='viewModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop="static" data-keyboard="false">
					<div class='modal-dialog'>
						<div class='modal-content modal-md'></div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->

                        </div>
                    </div>
                </div>
            </div>

        </div>

<?php
include_once ("footer.php");
echo" <script type='text/javascript' src='js/funciones/admin_despacho1.js'></script>";
		} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}
function despacho() {
	date_default_timezone_set('America/El_Salvador');
	$fecha = date('Y-m-d');
	$hora = date('H:i:s');
	$id_factura = $_POST["id_factura"];
	$responsable = $_SESSION["id_usuario"];
	$id_sucursal = $_SESSION["id_sucursal"];
	$cuantos = $_POST["cuantos"];
	$cuantos1 = $_POST["cuantos1"];
	$array_json=$_POST['json_arr'];
	$array_json1=$_POST['json_arr1'];
	$id_cliente = $_POST["id_cliente"];
	$sql_result=_query("SELECT id_factura,tipo_documento,num_fact_impresa FROM factura WHERE  id_factura='$id_factura'");
	$numrows=_num_rows($sql_result);
	$row=_fetch_array($sql_result);
	$doc=$row["tipo_documento"];
	$num_fat=$row["num_fact_impresa"];
	$where_clause = "id_factura ='".$id_factura."'";
	if($numrows != 0)
	{
		$sql="select * from correlativo WHERE id_sucursal=$id_sucursal";
		$result= _query($sql);
		$rows=_fetch_array($result);
		$nrows=_num_rows($result);
		$ult_ga=$rows['ga']+1;

		$list_ga = array('ga' => $ult_ga,);
		$wg = "id_sucursal = 1";
		$up_ga = _update("correlativo", $list_ga, $wg);

		$a=1;
		$b=1;
		$c=1;
		$d=1;
		$e=1;
		$table1 = 'despacho';
		$form_data1 = array (
		'id_factura' => $id_factura,
		'fecha_des' => $fecha,
		'hora_des' => $hora,
		'responsable' => $responsable,
		);
		$insertar1 = _insert($table1,$form_data1);
		echo _error();
		$id_despacho=_insert_id();
		if (!$insertar1) {
			$a=0;
		}else {

		$table = 'factura';
		$form_data = array (
		'entregado' => 1
		);
		$insertar = _update($table,$form_data, $where_clause);
		if (!$insertar) {
			$b=0;
		}
		if ($cuantos>0) {
			$table2= 'garantia_cliente';
			$form_data2 = array(
				'id_cliente' => $id_cliente,
				'fecha' => $fecha,
				'numero_doc' => $num_fat,
				'alias_tipodoc' => $doc,
				'id_sucursal' => $id_sucursal,
				'id_empleado' => $responsable,
				'correlativo' => $ult_ga,
			);

			$insertar2 = _insert($table2,$form_data2 );
			echo _error();
			$id_garantia= _insert_id();
			if (!$insertar2) {
				$c=0;

			}
			$array = json_decode($array_json, true);
			foreach ($array as $fila)
			{
				$id_producto=$fila['id_p'];
				$serie=$fila['serie'];
				$dias=$fila['dias'];
				$cantidad=$fila['cantidad'];
				$tipo_pe=$fila['tipo_periodo'];
				$precio=$fila['precio'];
				$fecha_fin=MD(sumar_dias(date("d-m-Y"),$dias));


				$table3= 'garantia_cte_det';
				$form_data3 = array(
					'id_garantia' => $id_garantia,
					'id_producto' => $id_producto,
					'serie' => $serie,
					'fecha_ini' => $fecha,
					'fecha_fin' => $fecha_fin,
					'dias_garantia' => $dias,
					'tipo_periodo' => $tipo_pe,
					'cantidad' => $cantidad,
					'precio' => $precio,
				);
				$insertar3 = _insert($table3,$form_data3 );
				if (!$insertar3) {
					# code...
					$d=0;
				}

			}
		if ($cuantos1>0) {
			$array1 = json_decode($array_json1, true);
			foreach ($array1 as $fila1)
			{
				$politicas=$fila1["desc"];
				$pol_id=$fila1["pol_id"];
				if($pol_id == 0)
				{
					$tab = "politica";
					$lis_po = array(
						'descripcion' => $politicas,
					);
					$ins = _insert($tab, $lis_po);
					$pol_id = _insert_id();
				}

				$table4= 'politicas_ga';
				$form_data4 = array(
					'id_garantia' => $id_garantia,
					'descripcion' => $politicas,
					'politica_id' => $pol_id,
				);
				$insertar4 = _insert($table4,$form_data4 );
				if (!$insertar4) {
					# code...
					$e=0;
				}

			}
		}
		}

		}
			if($a&&$b&&$c&&$d&&$e)
			{
				_commit(); // transaction is committed
				 $xdatos['typeinfo']='Success';
				 $xdatos['msg']='Despacho realizado correctamente!';
				 $xdatos['process']='insert';
			}
			else
			{
				_rollback(); // transaction rolls back
				$xdatos['typeinfo']='Error';
				$xdatos['msg']='Despacho no pudo ser realizado!'.$a."-".$b."-".$c."-".$d;
		}

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
	case 'insert':
		despacho();
		break;
	}

 //}
}
