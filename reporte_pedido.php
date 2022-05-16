<?php
include_once "_core.php";

function initial() {
	$_PAGE = array ();
	$_PAGE ['title'] = 'Reporte de Existencias por Proveedor y Talla';
	$_PAGE ['links'] = null;
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
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	//permiso del script
	if ($links!='NOT' || $admin=='1' ){
?>
<style>
/* Center the loader */
.sect
{
  height: 400px;
}
#loader {
  position: absolute;
  left: 50%;
  top: 50%;
  z-index: 1;
  width: 150px;
  height: 150px;
  margin: -75px 0 0 -75px;
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 120px;
  height: 120px;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
}

@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

/* table {
        width: 100%;
    }

thead, tbody, tr, td, th { display: block; }

tr:after {
    content: ' ';
    display: block;
    visibility: hidden;
    clear: both;
}

thead th {
    height: 30px;

}

tbody {
    height: 120px;
    overflow-y: auto;
}

thead {
}


tbody td, thead th {
    width: 19.2%;
    float: left;
}
*/




@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Add animation to "page content" */
.animate-bottom {
  position: relative;
  -webkit-animation-name: animatebottom;
  -webkit-animation-duration: 1s;
  animation-name: animatebottom;
  animation-duration: 1s
}

@-webkit-keyframes animatebottom {
  from { bottom:-100px; opacity:0 }
  to { bottom:0px; opacity:1 }
}

@keyframes animatebottom {
  from{ bottom:-100px; opacity:0 }
  to{ bottom:0; opacity:1 }
}
</style>
            <div class="row wrapper border-bottom white-bg page-heading">

                <div class="col-lg-2">

                </div>
            </div>
        <div class="wrapper wrapper-content  animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Reporte de Existencias por Talla</h5>
                        </div>
                        <div class="ibox-content">
                              <form name="formulario" id="formulario" method='GET' action='reporte_aventas.php' target='_blank'>
                              <div class="row">
                                <div class="col-md-3">
                                  <div class="form-group has-info single-line">
                                    <label class="control-label">Proveedor</label>
                                    <select class="col-md-12 select" id="proveedor" name="proveedor">
                                        <option value="General">General</option>
                                        <?php
                                            $sqld = "SELECT * FROM proveedores";
                                            $resultd=_query($sqld);
                                            while($depto = _fetch_array($resultd))
                                            {
                                                echo "<option value='".$depto["id_proveedor"]."'";

                                                echo">".$depto["nombre"]."</option>";
                                            }
                                        ?>
                                    </select>
                                    </div>
                                  </div>
																	<div class="col-md-3">
	                                  <div class="form-group has-info single-line">
	                                    <label class="control-label">Unidades Minimas</label>
																			<input class="form-control" type="text" id="las" name="las" value="">
	                                    </div>
	                                  </div>
                              </div>
                               <div class="row">
                                <div class="col-lg-10">

                                </div>
                                <div class="col-lg-2">
                                   <a id="btn_ac" class="btn btn-primary"><i class="fa fa-search"></i> Ver</a>
                                  <button type="submit" id="print1" class="btn btn-primary"><i class="fa fa-print"></i> PDF</button>
                                </div>
                                </div>
                               </form>
                               <div class="row" id="res" hidden><br>
                                <div class="col-lg-12 table-responsive">
                                  <table class="table table-bordered table-striped table-hover ">
                                    <thead id="encabezado">
                                    </thead>
                                    <tbody id="resultado">
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                              <div class="row" id="no-data" hidden><br>
                                <div class="col-lg-12">
                                 <div class="alert alert-warning">
                                  No se encontraron resultados que coincidan con los criterios de busqueda
                                 </div>
                                </div>
                              </div>
                              <div class="row" style="display: none;" id="divh">
                              <div class="col-lg-12">
                                <div class="ibox float-e-margins">
                                  <div class="ibox-content">
                                    <section class="sect">
                                      <div id="loader">
                                      </div>
                                    </section>
                                  </div>
                                </div>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
<?php
include_once ("footer.php");
?>
   <script type="text/javascript">
      $(document).ready(function(){
        $("select").select2();
        $("#las").numeric({negative:false, decimal:false});
        reporte();
        $("#btn_ac").click(function(){
          reporte();
        });
        $("#proveedor").change(function(){
          reporte();
        });
        $(".input").blur(function(){
          reporte();
        });
      });
      function reporte()
      {
        $("#res").attr("style","display: none;");
        $("#divh").attr("style","display: block;");
        var proveedor = $("#proveedor").val();
        var l = $("#las").val();
        $.ajax({
          type: 'POST',
          url: 'reporte_pedido.php',
          data: 'process=reporte&l='+l+'&id_proveedor='+proveedor,
          dataType: 'JSON',
          success: function(datax)
          {
            if(datax.typeinfo == "Success")
            {
              $("#no-data").hide();
              $("#res").show();
              $("#resultado").html(datax.table);
              $("#res").attr("style","display: block;");
              $("#divh").attr("style","display: none;");

            }
            else
            {
              $("#res").hide();
              $("#no-data").show();
              $("#resultado").html("");
              $("#divh").attr("style","display: none;");

            }
          }
        });
      }
    </script>

<?php
	} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}
function reporte()
{
    $table = "<table class='table table-bordered'>";
    $sql_escala = _query("SELECT * FROM escala");
    $num_es = _num_rows($sql_escala);
		$num_es = $num_es+2;
    $table.= "<tr>";
    $table.= "<td rowspan='$num_es' style='vertical-align: middle' >N°</td>";
    $table.= "<td rowspan='$num_es' style='vertical-align: middle' >Descripcion</td>";
		$n=0;
		$id_proveedor=$_REQUEST['id_proveedor'];

		$j=1;

		$sql_suc = _query("SELECT * FROM sucursal");
		$numsuc = _num_rows($sql_suc);

    while($row_es = _fetch_array($sql_escala))
    {
			    $table.= "<tr>";
        $nombre = $row_es["nombre"];
        $table.="<td>$nombre</td>";
        $valores = explode(",",$row_es["valores"]);
        $a = 0;
        $n = count($valores);
				$nvals=$n;
        for($k = 0; $k<$nvals; $k++)
        {
            $table.="<td colspan='$numsuc'>".$valores[$k]."</td>";

            $a++;
        }
				    $table.= "</tr>";

    }

				$table.= "<tr>";
				$table.="<td>SUC</td>";
				for ($i=0; $i <$n ; $i++) {
					# code...
					$sql_suc = _query("SELECT * FROM sucursal");
					$numsuc = _num_rows($sql_suc);
					$s = 1;


					while ($dsuc = _fetch_array($sql_suc))
					{
							$table.="<td>S".$s."</td>";
							$s++;
					}

				}
				$table.= "</tr>";



		    $table.= "</tr>";

				$sql="";
				$and = "SELECT stock.id_sucursal,productos.descripcion, productos.id_color,stock.existencias,productos.id_producto, productos.escala, productos.talla, productos.estilo, colores.nombre FROM productos, stock, colores WHERE productos.id_color=colores.id_color AND productos.id_proveedor='$id_proveedor' AND productos.id_producto=stock.id_producto AND stock.existencias>0 AND productos.talla IS NOT NULL GROUP BY productos.descripcion,productos.id_color,productos.escala ORDER BY productos.descripcion ASC ";

				/*SELECT stock.id_sucursal, productos.id_color,stock.existencias, productos.descripcion, productos.escala, productos.talla, productos.estilo, colores.nombre FROM productos, stock, colores WHERE productos.id_color=colores.id_color AND productos.id_proveedor='3' AND productos.id_producto=stock.id_producto AND stock.existencias>0 AND productos.talla IS NOT NULL ORDER BY productos.descripcion*/
				$sql.=$and;
				$result = _query($sql);
				if(_num_rows($result)>0)
				{
						while($rowasw = _fetch_array($result))
						{
								$descripciona = $rowasw["descripcion"];
								$escala = $rowasw["escala"];
								$id_color = $rowasw["id_color"];
								$id_producto = $rowasw["id_producto"];

								$descripcion = utf8_decode($descripciona)."|".$rowasw["estilo"]."|".$rowasw["nombre"];

								$sql_escala = _query("SELECT * FROM escala WHERE id_escala='$escala'");
								$num_es = _num_rows($sql_escala);
								$hay = 0;
								$row_es = _fetch_array($sql_escala);
								$valores = explode(",",$row_es["valores"]);
								$nvals = count($valores);
								$escaa = $row_es["nombre"];
								$table.="<tr>";
								$table.="<td>".$j."</td>";
								$table.="<td>".$descripcion."</td>";
								$table.="<td>".$escaa."</td>";
								for($k = 0; $k<$nvals; $k++)
								{
												$sql_suc = _query("SELECT * FROM sucursal");
												$numsuc = _num_rows($sql_suc);
												while($dsuc = _fetch_array($sql_suc))
												{
													$id_sucursal = $dsuc["id_sucursal"];

													$sql_aux = _query("SELECT stock.existencias, stock.id_sucursal, productos.talla FROM productos, stock WHERE productos.descripcion='$descripciona' AND productos.id_producto=stock.id_producto AND productos.id_color=$id_color  AND productos.talla='".$valores[$k]."' AND stock.id_sucursal=$id_sucursal");
													$row = _fetch_array($sql_aux);
													$n = _num_rows($sql_aux);

													$existencias= 0;

													if ($n==0) {
														# code...
														$table.="<td>"."</td>";
													}
													else
													{
														$talla= $row["talla"];
														$existencias= $row["existencias"];
														$id_sucursal11= $row["id_sucursal"];


																	if($existencias > 0)
																	{
																			$table.="<td>".$existencias."</td>";
																	}
																	else
																	{
																		$table.="<td>"."</td>";
																	}
													}


												}

								}
								$table.="</tr>";
								$j++;
						}
				}


		$xdata["typeinfo"] = "Success";
		$xdata["table"] = $table;
    echo json_encode($xdata);
}
if(!isset($_POST['process'])){
	initial();
}
else
{
if(isset($_POST['process']))
{
    switch ($_POST['process'])
    {
      case 'reporte':
	    reporte();
	    break;
    }
  }
}
?>
