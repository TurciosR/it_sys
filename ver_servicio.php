<?php
include ("_core.php");
$id_servicio = $_REQUEST['id'];
$sql="SELECT * FROM servicios WHERE id_servicio='$id_servicio'";
$result = _query( $sql);
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
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title">Detalle de servicios</h4>
</div>
<div class="modal-body">
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row" id="row1">
			<div class="col-lg-12">
					<table	class="table table-bordered table-striped" id="tableview">
						<thead>
							<tr>
								<th>Campo</th>
								<th>Detalle</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if ($count > 0) {
									for($i = 0; $i < $count; $i ++) {
										$row = _fetch_array ( $result, $i );
										$id_servicio=$row["id_servicio"];
										$descripcion=$row["descripcion"];
										$id_categoria=$row["id_categoria"];
										$costo=$row["costo"];
										$precio=$row["precio"];
										$estado=$row["estado"];
										 if($estado==1)
										 {
					                      $estado="Activo";
					                     }
					                     else
					                     {
					                      $estado="No Activo";
					                     }

										echo"<tr><td>Id servicio </td><td>".$id_servicio."</td></tr>";
										echo"<tr><td>Descripcion</td><td>".$descripcion."</td></tr>";
										echo"<tr><td>Costo</td><td>".$costo."</td></tr>";
										echo"<tr><td>Precio</td><td>".$precio."</td></tr>";
										echo"<tr><td>Estado</td><td>".$estado."</td></tr>
										</tr>";



									}
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<div class="modal-footer">


<?php
/*
	if($active=='t' && $admin!='t' ){
	$exist_module=false;
	foreach ($links as $linknombre){
		list($link,$filenombre,$descripcion,$nombremenu)=explode(',',$linknombre);
		if(trim($link)=='cliente.edit.php'){
			$exist_module=true;
		}
	}
	}
	if($exist_module==true || $admin=='t' ){
		echo"<a href='cliente.edit.php?id_cliente=".$id_cliente."&process=formEdit'"."class='btn btn-primary'><i class='fa fa-pencil'></i> Edit</a>";
	}
*/
	echo "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
	</div><!--/modal-footer -->";
/*
}
	else {
			echo "<div></div><br><br><div class='alert alert-warning'>You don't have permission to use this module.</div>";
		}
		*/
?>
