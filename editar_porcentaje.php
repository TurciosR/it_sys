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
		<h4 class="modal-title">Editar Porcentaje</h4>
	</div>
	<div class="modal-body">
		<div class="wrapper wrapper-content  animated fadeInRight">
			<div class="row">
				<div class="col-lg-12">
					<?php if ($links!='NOT' || $admin=='1' ){ ?>
						<div class="row">
              <div class="col-md-12">
                <label>Porcentaje</label>
                <input type="text" class="form-control" name="porcentaje" id="porcentaje" value="<?php echo $porcentaje; ?>">
              </div>
              <div class="col-md-12">
                <label>Estado</label>
                <select class="select for-control" style="width: 100%" name="estado" id="estado">
                  <option value="1"
                  <?php
                  if($estado == 1)
                  {
                    echo "selected";
                  }
                  ?>
                  >Activo</option>
                  <option value="0"
                  <?php
                  if($estado == 0)
                  {
                    echo "selected";
                  }
                  ?>
                  >Inactivo</option>
                </select>
              </div>
            </div>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="id_porcentaje" id="id_porcentaje" value="<?php echo $id_porcentaje; ?>">
		<input type="hidden" name="porcess" id="process" value="editar">
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" id="btnPor">Guardar</button>
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
function editar()
{
	$id_porcentaje = $_POST["id_porcentaje"];
	$porcentaje = $_POST["porcentaje"];
	$estado = $_POST["estado"];

	$sql_por = _query("SELECT * FROM porcentajes WHERE porcentaje = '$porcentaje' AND id_porcentaje != '$id_porcentaje'");
	$cuenta = _num_rows($sql_por);
	if($cuenta == 0)
	{
		$tabla = "porcentajes";
		$form_por = array(
			'porcentaje' => $porcentaje,
			'estado' => $estado,
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
	}
	else
	{
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Este porcentaje ya se encuentra registrado Verifique si se ecuentra activo !';
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
				case 'editar' :
				editar();
				break;
			}
		}
	}
	?>
