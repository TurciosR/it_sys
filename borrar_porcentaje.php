<?php
include ("_core.php");
function initial()
{
	$id_porcentaje = $_REQUEST ['id_porcentaje'];
	$sql="SELECT * FROM porcentajes WHERE id_porcentaje='$id_porcentaje'";
	$result = _query($sql);
  $row = _fetch_array($result);
  $porcentaje = $row["porcentaje"];
  $estado = $row["estado"];

  if($estado == "1")
  {
    $tx = "Activo";
  }
  if($estado == "0")
  {
    $tx = "Inactivo";
  }

  $id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	?>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
		<h4 class="modal-title">Borrar Porcentaje</h4>
	</div>
	<div class="modal-body">
		<div class="wrapper wrapper-content  animated fadeInRight">
			<div class="row">
				<div class="col-lg-12">
					<?php if ($links!='NOT' || $admin=='1' ){ ?>
						<div class="row">
              <div class="col-md-12">
                <table class="table table-bordered table-striped" id="tableview">
    							<thead>
    								<tr>
    									<th class="col-lg-3">Porcentaje</th>
                      <th class="col-lg-9">Estado</th>
    								</tr>
    							</thead>
    							<tbody>
    								<?php
    								echo "<tr><td>".$porcentaje."</td><td>".$tx."</td></tr>";
    								?>
    							</tbody>
    						</table>
              </div>
            </div>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="id_porcentaje" id="id_porcentaje" value="<?php echo $id_porcentaje; ?>">
		<input type="hidden" name="porcess" id="process" value="borrar">
		<div class="modal-footer">
			<button type="button" class="btn btn-danger" id="btnDelete">Borrar</button>
			<button type="button" class="btn btn-default" data-dismiss="modal" id="cerrar">Cerrar</button>
		</div>
    <script type="text/javascript">
    $(document).ready(function()
    {
      $(".select").select2();
			$("#porcentaje").numeric({
				negative: false
			})
    })
    </script>
		<!--/modal-footer -->
		<?php
	} //permiso del script
	else {
		echo "<br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div></div></div></div></div>";
	}
}
function borrar()
{
	$id_porcentaje = $_POST["id_porcentaje"];

	$tabla = "porcentajes";
	$w = "id_porcentaje='".$id_porcentaje."'";
	$delete = _delete($tabla,$w);
	echo _error();
	if($delete)
	{
		$xdatos['typeinfo']='Success';
		$xdatos['msg']='Registro eliminado con exito!';
		$xdatos['process']='insert';
	}
	else
	{
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Registro no pudo ser eliminado !';
	}
	echo json_encode($xdatos);
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
				case 'borrar' :
				borrar();
				break;
			}
		}
	}
	?>
