<?php
include "_core.php";
include ('num2letras.php');
//include ('facturacion_funcion_imprimir.php');
//include("escpos-php/Escpos.php");
function initial() {
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


?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
	<h4 class="modal-title">Politicas</h4>
</div>

<div class="modal-body">
		<div class="row" id="row1">
				<?php
					if ($links!='NOT' || $admin=='1' ){
				?>
						<div>
							<!--load datables estructure html-->
              <header>
                <div class="row">
                  <div class="col-lg-12">
                    <label for="">Descripción</label>
                    <textarea name="name"  id="politica" class="form-control" rows="1" cols="30"></textarea>
                  </div>

                </div>

              </header>
							<section>
								<div class="table-responsive m-t">
									<table class="table table-condensed table-striped" id="inventable2">
									<thead class="thead-inverse">
										<tr>
										<th class='success col-lg-11'>Descripci&oacute;n</th>
                    <th class='success col-lg-1'>Acci&oacute;n</th>
                    </tr>
									</thead>
									<tbody id="Agregar">

                  </tbody>

								</table>

					</section>

						</div>

					<button type="button" class="btn btn-primary pull-right" id="btnGua" data-dismiss="modal">Guardar</button>
					<!--button type="button" class="btn btn-danger" data-dismiss="modal">Finalizar</button-->

		</div>
	</div>


<?php

} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
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
	}

 //}
}
?>
