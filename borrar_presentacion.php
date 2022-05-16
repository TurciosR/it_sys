<?php
include ("_core.php");

function initial(){
	$id_presentacion= $_REQUEST['id_presentacion'];
		//permiso del script

	$id_sucursal=$_SESSION['id_sucursal'];
	//permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

	$sql="SELECT *
		FROM  presentacion
		WHERE id_presentacion='$id_presentacion'
		";

		$result=_query($sql);
		$count=_num_rows($result);

	?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Borrar Presentacion</h4>
</div>
<div class="modal-body">
	<!--div class="wrapper wrapper-content  animated fadeInRight"-->
		<div class="row" id="row1">
			<!--div class="col-lg-12"-->
				<?php
				$fecha_hoy=date("Y-m-d");
						//permiso del script
						if ($links!='NOT' || $admin=='1' ){


						echo "<input type='hidden' name='id_suc' id='id_suc' value='$id_sucursal'>";
						echo "<input type='hidden' name='id_presentacion' id='id_presentacion' value='$id_presentacion'>";

					?>

				<table class="table table-bordered table-striped" id="tableview">
					<thead>
						<tr>
							<th>Campo</th>
							<th>Descripcion</th>
						</tr>
					</thead>
					<tbody>
							<?php
							if ($count>0) {

								for($i = 0; $i < $count; $i ++) {
									$row = _fetch_array ($result);
									$descripcion=$row['descripcion'];
									$descrip_corta=$row['descrip_corta'];

										echo "<tr><td>Descripcion:</td><td><input type='text' id='descripcion' name='descripcion' value='$descripcion'  class='form-control' readonly></td></tr>";
										echo "<tr><td>Descripcion Corta:</td><td><input type='text' id='descrip_corta' name='descrip_corta' value='$descrip_corta'  class='form-control' readonly></td></tr>";
										echo "<tr><td></td></tr>";
									}
								}
							?>
						</tbody>
						<tfoot>
						<td align='center'><button type="button" class="btn btn-primary" id="btnAnularPresenta"><i class="fa fa-minus"></i> Borrar</button> </td>
						<td align='center'><button type="button"  class="btn btn-danger" id="btnEsc" data-dismiss="modal"><i class="fa fa-stop"></i> Salir</button>	 </td>
						</tfoot>
				</table>
			</div>
</div>


<?php

} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

function borrar_presentacion(){

		$id_sucursal=$_SESSION["id_sucursal"];

		$id_presentacion=$_POST['id_presentacion'];
		$descripcion=$_POST['descripcion'];
    $descrip_corta=$_POST['descrip_corta'];


		$sql="SELECT *
	    FROM  presentacion
	    WHERE id_presentacion='$id_presentacion'
	  ";

	    $result=_query($sql);
	    $count=_num_rows($result);
	    $table= 'presentacion';
			$form_data = array(
				'descripcion'=>$descripcion,
				'descrip_corta'=>$descrip_corta,
			);
	    if ($count>0){

			$where_clause= "id_presentacion='".$id_presentacion."'";
	    $insertar = _delete($table,$where_clause );
		}
		else{
			$insertar = _insert($table,$form_data);
		}

		if($insertar){
				$xdatos['typeinfo']='Info';
				$xdatos['msg']='Registro guardado con exito!';
				$xdatos['process']='edited';
				$xdatos['id_producto']=$id_producto;
			}
			echo json_encode($xdatos);

	}


if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'formDelete' :
				initial();
				break;
			case 'editar_presentacion' :
				editar_presentacion();
				break;
				case 'anular_presentacion' :
					borrar_presentacion();
					break;
		}
	}
}

?>
