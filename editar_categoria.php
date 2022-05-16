<?php
include_once "_core.php";
function initial() {
$id_categoria = $_REQUEST['id_categoria'];
$sql="SELECT * FROM categoria WHERE id_categoria='$id_categoria'";
$result = _query( $sql);
$count = _num_rows( $result );

?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title">Detalle de Categoria</h4>
</div>
<div class="modal-body">

		<div class="row" id="row1">
			<div class="col-lg-12">
				  <form name="formulario" id="formulario">
				<?php
				if ($count>0){

				for($i=0;$i<$count;$i++){
					$row=_fetch_array($result);

					$nombre=$row['nombre'];
					$descripcion=$row['descripcion'];

				?>
				<div class="form-group has-info single-line"><label>Nombre</label> <input type="text" placeholder="Digite Nombre" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre;?> "></div>
				<div class="form-group has-info single-line"><label>Descripción</label> <input type="text" placeholder="Descripcion" class="form-control" id="descripcion" name="descripcion" value="<?php echo $descripcion;?> "></div>

				 <?php
							}
					}
				   ?>
				  <input type="hidden" name="process" id="process" value="edited">
					<input type="hidden" name="id_categoria" id="id_categoria" value="<?php echo $_REQUEST['id_categoria']?> ">
				  <div>
				  	<input type="submit" id="submit1" name="submit1" value="Guardar" class="btn btn-primary m-t-n-xs" />
 					</div>
				</form>
				</div>
			</div>

	</div>

	<?php
	echo "<script src='js/funciones/funciones_categoria.js'></script>";

	}

	function edited(){
		$id_categoria=$_POST["id_categoria"];
		$nombre=$_POST["nombre"];
	    $descripcion=$_POST["descripcion"];
	    $table = 'categoria';
	    $form_data = array (
	    	'nombre' => $nombre,
	    	'descripcion' => $descripcion
	    );

	   $where_clause = "id_categoria='" . $id_categoria . "'";
		$updates = _update ( $table, $form_data, $where_clause );
			if($updates){
				$xdatos['typeinfo']='Success';
				$xdatos['msg']='Record edited Successfully';
				$xdatos['process']='edited';
			}
			else{
				$xdatos['typeinfo']='Error';
				$xdatos['msg']='Record not Edited ';
			}
		echo json_encode($xdatos);
	}

	if(!isset($_REQUEST['process'])){
		initial();
	}
	else
	{
	if(isset($_REQUEST['process'])){
	switch ($_REQUEST['process']) {
		case 'edited':
			edited();
			break;
		case 'formEdit' :
			initial();
			break;
		}
	}
	}
	?>
