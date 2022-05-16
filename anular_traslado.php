<?php
include("_core.php");
function initial()
{
    $id_traslado = $_REQUEST ['id_traslado'];
    $id_sucursal=$_SESSION['id_sucursal'];
    $sql="SELECT traslado.*
	FROM traslado
	WHERE idtransace='$id_traslado' and id_sucursal_origen='$id_sucursal'
	and verificado=0";
    $result = _query($sql);
    $count = _num_rows($result); ?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title">Anular traslado</h4>
</div>
<div class="modal-body">
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row" id="row1">
			<div class="col-lg-12">
				<table class="table table-bordered table-striped" id="tableview">
					<thead>
						<tr>
							<th>Campo</th>
							<th>Descripcion</th>
						</tr>
					</thead>
					<tbody>
							<?php
                                if ($count > 0) {
                                    for ($i = 0; $i < $count; $i ++) {
                                        $row = _fetch_array($result, $i);
                                        //$cliente=$row['nombre']." ".$row['apellido'];
                                        echo "<tr><td>Id traslado</th><td>$id_traslado</td></tr>";
                                        echo "<tr><td>Items :</td><td>".$row['items']."</td>";
                                        echo "<tr><td>Pares :</td><td>".$row['pares']."</td>";
                                        echo "</tr>";
                                    }
                                } ?>
						</tbody>
				</table>
			</div>
		</div>
			<?php
            echo "<input type='hidden' nombre='id_traslado' id='id_traslado' value='$id_traslado'>"; ?>
		</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" id="btnAnular">Anular</button>
	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

</div>
<!--/modal-footer -->
<?php
}
function deleted()
{
    $id_traslado =$_POST['id_traslado'];
    $id_sucursal=$_SESSION['id_sucursal'];
    $id_usuario=$_SESSION["id_usuario"];
    $id_transac=$id_traslado;
    $fecha_movimiento=date('Y-m-d');
    _begin();
    $table = 'traslado';
    $where_clause = "idtransace='" . $id_traslado . "'";
    $form_data = array(
                    'anulado' =>1
                );
    $counter1=0;
    $counter2=0;
    $counter3=0;

    $anular=_update($table, $form_data, $where_clause);
    if ($anular) {
        # code...
    } else {
        $counter1=1;
    }

    $sql_result=_query("SELECT numero FROM correlativos WHERE id_sucursal='$id_sucursal' AND alias='ADT' ");
    $row=_fetch_array($sql_result);
    $correlativo=$row['numero']+1;

    $correlative=str_pad($correlativo, 15, '0', STR_PAD_LEFT);

    $table = 'correlativos';
    $form_data = array(
                'numero' => $correlativo
                );

    $where_clause = "id_sucursal ='".$id_sucursal."' AND alias='ADT'";
    $insertar = _update($table, $form_data, $where_clause);



    $sqla=_query("SELECT detalle_traslado.id_producto,detalle_traslado.cantidad FROM traslado INNER JOIN detalle_traslado ON detalle_traslado.idtransace=traslado.idtransace WHERE traslado.idtransace='$id_traslado' AND traslado.id_sucursal_origen='$id_sucursal' ");
    while ($rowa=_fetch_array($sqla)) {
        # code...
        $id_producto=$rowa['id_producto'];
        $cantidad=$rowa['cantidad'];

        $nuevo_stock=0;
        $sql_stock=_query("SELECT stock.existencias FROM stock WHERE stock.id_producto='$id_producto' AND stock.id_sucursal='$id_sucursal' ");
        $row_stock=_fetch_array($sql_stock);
        $stock_anterior=$row_stock['existencias'];

        $nuevo_stock=$stock_anterior+$cantidad;
        $tableS= 'stock';
        $form_dataS = array(
            'existencias'=> $nuevo_stock,
                        );

        $where_clauseS="WHERE id_producto='$id_producto' AND id_sucursal='$id_sucursal'";
        $update1 = _update($tableS, $form_dataS, $where_clauseS);
        if ($update1) {
            # code...
        } else {
            # code...
            $counter2=1;
        }



        $sqlkardex=_query("SELECT kardex.* FROM kardex WHERE kardex.almacen='$id_sucursal' AND kardex.id_sucursal_origen='$id_sucursal' AND kardex.id_transacc='$id_transac' AND kardex.alias_tipodoc='TRA' AND kardex.id_producto='$id_producto' ");
        $r=_fetch_array($sqlkardex);

        $table= 'kardex';
        $form_data = array(
                        'id_transacc'=>$r['id_transacc'],
                        'id_producto'=>$id_producto,
                        'id_empleado'=>$id_usuario,
                        'id_tipodoc'=>23,
                        'fechadoc'=>$fecha_movimiento,
                        'numero_doc'=>$correlative,
                        'cantidade'=>$cantidad,
                        'ultcosto'=>$r['ultcosto'],
                        'cantidads'=>0,
                        'costo'=>0.00,
                        'almacen'=>$id_sucursal,
                        'talla'=>$r['talla'],
                        'stock_anterior'=>$stock_anterior,
                        'stock_actual'=>$nuevo_stock,
                        'alias_tipodoc'=>"ADT",
                        );
        $insertar3 = _insert($table, $form_data);
        if ($insertar3) {
            # code...
        } else {
            # code...
            $counter3=1;
        }
    }

    if ($counter1==0 && $counter2==0 && $counter3==0) {
        _commit();
        $xdatos ['typeinfo'] = 'Success';
        $xdatos ['msg'] = 'Traslado Anulado!';
    } else {
        _rollback();
        $xdatos ['typeinfo'] = 'Error';
        $xdatos ['msg'] = 'Traslado no Anulado! ';
    }
    echo json_encode($xdatos);
}
if (! isset($_REQUEST ['process'])) {
    initial();
} else {
    if (isset($_REQUEST ['process'])) {
        switch ($_REQUEST ['process']) {
            case 'formDelete':
                initial();
                break;
            case 'anular':
                deleted();
                break;
        }
    }
}
?>
