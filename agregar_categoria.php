<?php
include_once "_core.php";
function initial() {
$id_categoria = -1

?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title">Detalle de Categoria</h4>
</div>
<div class="modal-body">

		<div class="row" id="row1">
			<div class="col-lg-12">
				  <form name="formulario" id="formulario">
	
				<div class="form-group has-info single-line"><label>Nombre</label> <input type="text" placeholder="Digite Nombre" class="form-control" id="nombre" name="nombre" value=" "></div>
				<div class="form-group has-info single-line"><label>Descripción</label> <input type="text" placeholder="Descripcion" class="form-control" id="descripcion" name="descripcion" value=""></div>


				  <input type="hidden" name="process" id="process" value="insert">
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

	function insert(){
		$id_categoria=$_POST["id_categoria"];
		$nombre=$_POST["nombre"];
			$descripcion=$_POST["descripcion"];

			$sql_result= _query("SELECT * FROM categoria WHERE nombre='$nombre'");
			$row_update=_fetch_array($sql_result);
			$id_update=$row_update["id_categoria"];
			$numrows=_num_rows($sql_result);


			$table = 'categoria';
			$form_data = array (
				'nombre' => $nombre,
				'descripcion' => $descripcion
			);

			if($numrows == 0 && trim($nombre)!=''){

			$insertar = _insert($table,$form_data);

			if($insertar){
				 $field='id_categoria';
				 $xdatos['typeinfo']='Success';
				 $xdatos['msg']='Record inserted successfully !';
				 $xdatos['process']='insert';
			}
			else{
				 $xdatos['typeinfo']='Error';
				 $xdatos['msg']='Record not inserted successfully !';
					$xdatos['process']='none';
			}
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
		case 'insert':
			insert();
			break;
		case 'formEdit' :
			initial();
			break;
		}
	}
	}
	?>
