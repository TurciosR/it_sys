<?php
include ("_core.php");

function initial(){
		//permiso del script

	$id_sucursal=$_SESSION['id_sucursal'];
	//permiso del script
 	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

	?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Agregar Precio Producto Presentacion</h4>
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
echo "<input type='hidden' name='id_presentacion' id='id_presentacion' value='-1'>";
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
										echo "<tr><td>";
										echo "<tr><td>Descripcion:</td><td><input type='text' id='descripcion' name='descripcion' value=''  class='form-control decimal'></td></tr>";
										echo "<tr><td>Descripcion Corta:</td><td><input type='text' id='descrip_corta' name='descrip_corta' value=''  class='form-control decimal'></td></tr>";
										echo "<tr><td></td></tr>";

							?>
						</tbody>
						<tfoot>
						<td align='center'><button type="button" class="btn btn-primary" id="btnGuardarPresenta"><i class="fa fa-save"></i> Guardar</button> </td>
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

function agregar_presentacion(){

		$id_sucursal=$_SESSION["id_sucursal"];

		$id_presentacion=$_POST['id_presentacion'];
		$descripcion=$_POST['descripcion'];
    $descrip_corta=$_POST['descrip_corta'];


		$sql="SELECT *
	    FROM  presentacion
	    WHERE id_presentacion='$id_presentacion'
			AND descripcion='$descripcion';
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
	    $insertar = _update($table,$form_data, $where_clause );
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
				case 'agregar_presentacion' :
					agregar_presentacion();
					break;
		}
	}
}

?>
