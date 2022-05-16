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

  $id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	?>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
		<h4 class="modal-title">Porcentaje</h4>
	</div>
	<div class="modal-body">
		<div class="wrapper wrapper-content  animated fadeInRight">
			<div class="row">
				<div class="col-lg-12">
					<?php if ($links!='NOT' || $admin=='1' ){ ?>
						<div class="row">
              <div class="col-md-12">
                <h3><?php
                if($estado == 1)
                {
                  echo "Desactivar ";
                }
                if($estado == 0)
                {
                  echo "Activar ";
                }
                ?>Porcentaje
              </h3>
              </div>
            </div>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="id_porcentaje" id="id_porcentaje" value="<?php echo $id_porcentaje; ?>">
		<input type="hidden" name="estado" id="estado" value="<?php echo $estado; ?>">
		<input type="hidden" name="porcess" id="process" value="cambiar">
		<div class="modal-footer">
			<button type="button" class="btn btn-warning" id="btnCan">Cambiar</button>
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
function cambiar()
{
	$id_porcentaje = $_POST["id_porcentaje"];
	$estado = $_POST["estado"];
  if($estado == 1)
  {
    $n_estado = 0;
  }
  if($estado == 0)
  {
    $n_estado = 1;
  }

	$tabla = "porcentajes";
	$form_por = array(
		'estado' => $n_estado,
	);
	$w = "id_porcentaje='".$id_porcentaje."'";
	$update = _update($tabla, $form_por, $w);
	echo _error();
	if($update)
	{
		$xdatos['typeinfo']='Success';
		$xdatos['msg']='Registro guardado con exito!';
		$xdatos['process']='insert';
	}
	else
	{
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Registro no pudo ser guardado !';
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
				case 'cambiar' :
				cambiar();
				break;
			}
		}
	}
	?>
