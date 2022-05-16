<?php
include("_core.php");
// Page setup
$_PAGE = array();
$title='Administrar Porcentajes';
$_PAGE ['title'] = $title;
$_PAGE ['links'] = null;
$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
include_once "header.php";
include_once "main_menu.php";

//permiso del script
$id_user=$_SESSION["id_usuario"];
$admin=$_SESSION["admin"];
$uri = $_SERVER['SCRIPT_NAME'];
$filename=get_name_script($uri);
$links=permission_usr($id_user, $filename);
$fechahoy=date("Y-m-d");
$fechaanterior=restar_dias($fechahoy, 30);

$id_sucursal=$_SESSION['id_sucursal'];


?>
<style media="screen">
span.select2-container {
  z-index:10050;
}
</style>

<div class="wrapper wrapper-content  animated fadeInRight">
  <div class="row" id="row1">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <?php if ($links!='NOT' || $admin=='1') {

          echo "<div class='ibox-title'>";
  				$filename='agregar_producto.php';
  				$link=permission_usr($id_user,$filename);
  				if ($link!='NOT' || $admin=='1' )
          {
  				   echo "<a data-toggle='modal' href='agregar_porcentaje.php' class='btn btn-primary' role='button' data-target='#addModal' data-refresh='true'><i class='fa fa-plus icon-large'></i> Agregar Porcentaje</a>";
          }
  				echo	"</div>";

             ?>
            <div class="ibox-content">
              <header>
    						<h4>Administrar Porcentaje</h4>
    					</header>
              <!--load datables estructure html-->
              <section>
                <table class="table table-striped table-bordered table-hover" id="editable2">
                  <thead>
                    <tr>
                      <th class="col-lg-1">Id</th>
                      <th class="col-lg-8">Porcentaje</th>
                      <th class="col-lg-2">Estado</th>
                      <th class="col-lg-1">Acci&oacute;n</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $sql_porcentaje = _query("SELECT * FROM porcentajes ORDER BY porcentaje ASC");
                      $cuenta = _num_rows($sql_porcentaje);
                      if($cuenta > 0)
                      {
                        $i = 1;
                        while ($row = _fetch_array($sql_porcentaje))
                        {
                          $id_porcentaje = $row["id_porcentaje"];
                          $porcentaje = $row["porcentaje"];
                          $estado = $row["estado"];

                          if($estado == 1)
                          {
                            $text_estado = "Activo";
                            $text_estado_2 = "Desactivar";
                            $icono = "fa fa-low-vision";
                          }
                          else {
                            $text_estado = "Inactivo";
                            $text_estado_2 = "Activar";
                            $icono = "fa fa-eye";
                          }

                          $menudrop="<div class='btn-group'>
              						<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
              						<ul class='dropdown-menu dropdown-primary'>";

                          $filename='editar_porcentaje.php';
                          $link=permission_usr($id_user, $filename);
                          if ($link!='NOT' || $admin=='1')
                          {
                              $menudrop.="<li><a data-toggle='modal' href='$filename?id_porcentaje=".$id_porcentaje."' data-target='#editModal' data-refresh='true'><i class='fa fa-pencil'></i> Editar</a></li>";
                          }

                          $filename='cambiar_estado_por.php';
                          $link=permission_usr($id_user, $filename);
                          if ($link!='NOT' || $admin=='1')
                          {
                              $menudrop.="<li><a data-toggle='modal' href='$filename?id_porcentaje=".$id_porcentaje."&estado=".$estado."' data-target='#stadoModal' data-refresh='true' ><i class='".$icono."'></i> ".$text_estado_2."</a></li>";
                          }

                          $filename='borrar_porcentaje.php';
                          $link=permission_usr($id_user, $filename);
                          if ($link!='NOT' || $admin=='1')
                          {
                              $menudrop.="<li><a data-toggle='modal' href='$filename?id_porcentaje=".$id_porcentaje."&estado=".$estado."' data-target='#deleteModal' data-refresh='true' ><i class='fa fa-eraser'></i> Borrar</a></li>";
                          }
                          $menudrop.="</ul>
              						</div>";

                          echo "<tr>";
                          echo "<td>".$i."</td>";
                          echo "<td>".$porcentaje."%</td>";
                          echo "<td>".$text_estado."</td>";
                          echo "<td>".$menudrop."</td>";
                          echo "</tr>";
                          $i += 1;
                        }
                      }
                    ?>
                  </tbody>
                </table>
                <input type="hidden" name="autosave" id="autosave" value="false-0">
              </section>
              <!--Show Modal Popups View & Delete -->
              <div class='modal fade' id='editModal' tabindex='-1' data-backdrop="static" data-keyboard="false" role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                <div class='modal-dialog modal-md'>
                  <div class='modal-content modal-sm'></div>
                  <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
              </div>
              <!-- /.modal -->
              <div class='modal fade' id='addModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                <div class='modal-dialog modal-sm'>
                  <div class='modal-content modal-sm'></div>
                  <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
              </div>
              <!-- /.modal -->
              <!--Show Modal Popups View & Delete -->
              <div class='modal fade' id='stadoModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                  <div class='modal-content modal-sm'></div>
                  <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
              </div>
              <!-- /.modal -->
              <!--Show Modal Popups View & Delete -->
              <div class='modal fade' id='deleteModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                  <div class='modal-content modal-sm'></div>
                  <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
              </div>
              <!-- /.modal -->

            </div>
            <!--div class='ibox-content'-->
          </div>
          <!--<div class='ibox float-e-margins' -->
        </div>
        <!--div class='col-lg-12'-->
      </div>
      <!--div class='row'-->
    </div>
    <!--div class='wrapper wrapper-content  animated fadeInRight'-->
    <?php


    include("footer.php");
    echo" <script type='text/javascript' src='js/funciones/funciones_porcentaje.js'></script>";
} //permiso del script
else {
  echo "<br><br>No tiene permiso para este modulo</div></div></div></div>";
  include "footer.php";
}


?>
