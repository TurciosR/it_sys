<?php
    $hostname = "localhost";
    $username = "mayorga";
    $password = "M@y0rga*18";
    $dbname = "mayorga";

	$conexion = mysqli_connect("$hostname","$username","$password","$dbname");
	if (mysqli_connect_errno()){
		echo "Error en conexión MySQL: " . mysqli_connect_error();
	}

function _query($sql_string){
	global $conexion;
  // Cambiar el set character a utf8
  mysqli_set_charset($conexion,"utf8");
	$query=mysqli_query($conexion,$sql_string);
	return $query;
}
// Begin functions queries
function _fetch_array($sql_string){
	global $conexion;
	$fetched = mysqli_fetch_array($sql_string,MYSQLI_ASSOC);
	return $fetched;
}

function _fetch_row($sql_string){
	global $conexion;
	$fetched = mysqli_fetch_row($sql_string);
	return $fetched;
}
function _fetch_assoc($sql_string){
	global $conexion;
	$fetched = mysqli_fetch_assoc($sql_string);
	return $fetched;
}

function _num_rows($sql_string){
	global $conexion;
	$rows = mysqli_num_rows($sql_string);
	return $rows;
}
function _insert_id(){
  //  mysqli_set_charset($conexion,"utf8");
	global $conexion;
	$value = mysqli_insert_id($conexion);
	return $value;
}
// End functions queries

//funcion real escape string
function _real_escape($sql_string){
	global $conexion;
	$query=mysqli_real_escape_string($conexion,$sql_string);
	return $query;
}

// funciones insertar
function _insert($table_name, $form_data){
    // retrieve the keys of the array (column titles)
	$form_data2=array();
	$variable='';
	// retrieve the keys of the array (column titles)
	$fields = array_keys ( $form_data );
	// join as string fields and variables to insert
	$fieldss = implode ( ',', $fields );
	//$variables = implode ( "','", $form_data ); U+0027
	foreach($form_data as $variable){
		$var1=preg_match('/\x{27}/u', $variable);
		$var2=preg_match('/\x{22}/u', $variable);
		if($var1==true || $var2==true){
		 $variable = addslashes($variable);
		}
		array_push($form_data2,$variable);
    }
    $variables = implode ( "','",$form_data2 );

    // build the query
    $sql = "INSERT INTO " . $table_name . "(" . $fieldss . ")";
    $sql .= "VALUES('" . $variables . "')";
    // run and return the query result resource
    return _query($sql);
}

function db_close(){
	global $conexion;
	mysqli_close($conexion);
}
// the where clause is left optional incase the user wants to delete every row!
function _delete($table_name, $where_clause='')
{
    // check for optional where clause
    $whereSQL = '';
    if(!empty($where_clause))
    {
        // check to see if the 'where' keyword exists
        if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
        {
            // not found, add keyword
            $whereSQL = " WHERE ".$where_clause;
        } else
        {
            $whereSQL = " ".trim($where_clause);
        }
    }
    // build the query
    $sql = "DELETE FROM ".$table_name.$whereSQL;
	return _query($sql);
}
// again where clause is left optional
function _update($table_name, $form_data, $where_clause='')
{
    // check for optional where clause
    $whereSQL = '';
    $form_data2=array();
	$variable='';
    if(!empty($where_clause))
    {
        // check to see if the 'where' keyword exists
        if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
        {
            // not found, add key word
            $whereSQL = " WHERE ".$where_clause;
        } else
        {
            $whereSQL = " ".trim($where_clause);
        }
    }
    // start the actual SQL statement
    $sql = "UPDATE ".$table_name." SET ";

    // loop and build the column /
    $sets = array();
    //begin modified
	foreach($form_data as $index=>$variable){
		$var1=preg_match('/\x{27}/u', $variable);
		$var2=preg_match('/\x{22}/u', $variable);
		if($var1==true || $var2==true){
		 $variable = addslashes($variable);
		}
		$form_data2[$index] = $variable;
    }
    foreach ( $form_data2 as $column => $value ) {
		$sets [] = $column . " = '" . $value . "'";
	}
    $sql .= implode(', ', $sets);

    // append the where statement
    $sql .= $whereSQL;
    // run and return the query result
    return _query($sql);
}

function max_id($field,$table)
{
    $max_id=_query("SELECT MAX($field) FROM $table");
    $row = _fetch_array($max_id);
    $max_record = $row[0];

    return $max_record;
}

//FUNCIONES PARA LOS PERMISOS DE USUARIO SEGUN ROLES
function get_name_script($url){
//metodo para obtener el nombre del file:
$nombre_archivo = $url;
//verificamos si en la ruta nos han indicado el directorio en el que se encuentra
if ( strpos($url, '/') !== FALSE ){
  $nombre_archivo_tmp = explode('/', $url);
}
//de ser asi, lo eliminamos, y solamente nos quedamos con el nombre y su extension
	$nombre_archivo= array_pop($nombre_archivo_tmp );
	return  $nombre_archivo;
}
function permission_usr($id_user,$filename){
	$sql1="SELECT menu.id_menu, menu.nombre as nombremenu, menu.prioridad,
			modulo.id_modulo,  modulo.nombre as nombremodulo, modulo.descripcion, modulo.filename,
			usuario_modulo.id_usuario,usuario.tipo_usuario as admin
			FROM menu, modulo, usuario_modulo, usuario
			WHERE usuario.id_usuario='$id_user'
			AND menu.id_menu=modulo.id_menu
			AND usuario.id_usuario=usuario_modulo.id_usuario
			AND usuario_modulo.id_modulo=modulo.id_modulo
			AND modulo.filename='$filename'
			";
	$result1=_query($sql1);
	$count1=_num_rows($result1);
	if($count1 >0){
		$row1=_fetch_array($result1);
		$admin=$row1['admin'];
		$nombremodulo=$row1['nombremodulo'];
		$filename=$row1['filename'];
		$name_link=$filename;
	}
	else{
    $name_link='NOT';
  }
  return $name_link;
}
//FUNCIONES PARA TRANSACTIONS SQL
function _begin(){
  global $conexion;
	mysqli_query($conexion, "START TRANSACTION");
}
function _commit(){
	global $conexion;
    mysqli_query($conexion,"COMMIT");
}
function _rollback(){
	global $conexion;
    mysqli_query($conexion,"ROLLBACK");
}
//FUNCIONES FECHAS
function check_date_ymd( $fecha ){
  list($y, $m, $d) = explode('-', $fecha);
  if(checkdate($m, $d, $y)){
      return true ;
  } else{
    return false ;
  }
}

function ED($fecha){
    $dia = substr($fecha,8,2);
    $mes = substr($fecha,5,2);
    $a = substr($fecha,0,4);
    $fecha = "$dia-$mes-$a";
    return $fecha;
}
function MD($fecha){
    $dia = substr($fecha,0,2);
    $mes = substr($fecha,3,2);
    $a = substr($fecha,6,4);
    $fecha = "$a-$mes-$dia";
    return $fecha;
}
//comparar 2 fechas y retornar la diferencia de dias
function compararFechas($separador,$primera, $segunda){
  $valoresPrimera = explode ($separador, $primera);
  $valoresSegunda = explode ($separador, $segunda);
  $diaPrimera    = $valoresPrimera[0];
  $mesPrimera  = $valoresPrimera[1];
  $anyoPrimera   = $valoresPrimera[2];
  $diaSegunda   = $valoresSegunda[0];
  $mesSegunda = $valoresSegunda[1];
  $anyoSegunda  = $valoresSegunda[2];

  $diasPrimeraJuliano = gregoriantojd($mesPrimera, $diaPrimera, $anyoPrimera);
  $diasSegundaJuliano = gregoriantojd($mesSegunda, $diaSegunda, $anyoSegunda);

  if(!checkdate($mesPrimera, $diaPrimera, $anyoPrimera)){
    // "La fecha ".$primera." no es valida";
    return 0;
  }elseif(!checkdate($mesSegunda, $diaSegunda, $anyoSegunda)){
    // "La fecha ".$segunda." no es valida";
    return 0;
  }else{
    return  $diasPrimeraJuliano - $diasSegundaJuliano;
  }
}

//sumar dias a una fecha dada
function sumar_dias($fecha,$dias){
	//formato date('Y-m-j');
	$nuevafecha = strtotime ('+'.$dias.' days' , strtotime ( $fecha ) ) ;
	$nuevafecha = date ( 'd-m-Y' , $nuevafecha );
	return 	$nuevafecha;
}

function sumar_dias_Ymd($date,$days){
    $date = strtotime("+".$days." days", strtotime($date));
    return  date("Y-m-d", $date);
}

//restar dias a una fecha dada
function restar_dias($fecha,$dias){
	//formato date('Y-m-j');
	$nuevafecha = strtotime ('-'.$dias.' day' , strtotime ( $fecha ) ) ;
	$nuevafecha = date ( 'Y-m-d' , $nuevafecha );
	return 	$nuevafecha;
}
//obtener el nombre segun numero de dia en spanish
function dialetras($fecha_ymd){
$dias = array('','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo');
$fecha = $dias[date('N', strtotime($fecha_ymd))];
return $fecha;
}
//obtener el dia en spanish segun el numero del dia entre 1 y 7
function dialetras2($numero){
$dias = array('','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo');
$dialetras = $dias[$numero];
return $dialetras;
}
//funcion que contiene un array de meses en spanish
function meses($n){
	$mes = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
	return $mes[$n-1];
}
//numero de meses transcurridos entre dos fechas
function nmeses($fechaini,$fechafin){
	$fechainicial = new DateTime($fechaini);

	$fechafinal = new DateTime($fechafin);
	$diferencia = $fechainicial->diff($fechafinal);
	$meses = ( $diferencia->y * 12 ) + $diferencia->m;
	return $meses;
}
//sumar meses a una fecha
function sumar_meses($fecha, $nmeses)
{
    $nuevafecha = strtotime ( '+'.$nmeses.' month' , strtotime ( $fecha ) ) ;
    $nuevafecha = date ( 'Y-m-d' , $nuevafecha );
    return $nuevafecha;
}
function restar_meses($fecha, $nmeses){
    $nuevafecha = strtotime ( '-'.$nmeses.' month' , strtotime ( $fecha ) ) ;
    $nuevafecha = date ( 'Y-m-d' , $nuevafecha );
    return $nuevafecha;
}
//funcion que devuelve un select con meses
function select_meses($nombre){
	$meses = array('SELECCIONE...','ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO',
               'AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE');
	$array = $meses;
	$txt= "<select class='select form-control' name='$nombre' id='$nombre'>";

	for ($i=0; $i<sizeof($array); $i++){
		$txt .= "<option value='$i'>". $array[$i] . '</option>';
	}
	$txt .= '</select>';
	return $txt;
}
//restar horas
function RestarHoras($horaini,$horafin)
{
	$horai=substr($horaini,0,2);
	$mini=substr($horaini,3,2);
	$segi=substr($horaini,6,2);

	$horaf=substr($horafin,0,2);
	$minf=substr($horafin,3,2);
	$segf=substr($horafin,6,2);

	$ini=((($horai*60)*60)+($mini*60)+$segi);
	$fin=((($horaf*60)*60)+($minf*60)+$segf);
	$dif=$fin-$ini;
	$difh=floor($dif/3600);
	$difm=floor(($dif-($difh*3600))/60);
	$difs=$dif-($difm*60)-($difh*3600);
	return date("H:i:s",mktime($difh,$difm,$difs));
}
function SumarHoras($horaini,$horafin)
{
	$horai=substr($horaini,0,2);
	$mini=substr($horaini,3,2);
	$segi=substr($horaini,6,2);

	$horaf=substr($horafin,0,2);
	$minf=substr($horafin,3,2);
	$segf=substr($horafin,6,2);

	$ini=((($horai*60)*60)+($mini*60)+$segi);
	$fin=((($horaf*60)*60)+($minf*60)+$segf);
	$dif=$fin+$ini;
	$difh=floor($dif/3600);
	$difm=floor(($dif-($difh*3600))/60);
	$difs=$dif-($difm*60)-($difh*3600);
	return date("H:i:s",mktime($difh,$difm,$difs));
}
//FUNCIONES  NUMEROS / CADENAS

//dividir una cadena en n lineas de x caracteres
function divtextlin( $text, $width = '80', $lines = '10', $break = '\n', $cut = 0 ) {
      $wrappedarr = array();
      $wrappedtext = wordwrap( $text, $width, $break , true );
       $wrappedtext = trim( $wrappedtext );
      $arr = explode( $break, $wrappedtext );
     return $arr;
}
//funcion mayusculas
function Mayu($cadena) {
$mayusculas = strtr(strtoupper(utf8_encode($cadena)),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
return $mayusculas;
}

//funcion para poner ceros en la cuenta, primero la cantidad de ceros y luego la palabra
function ceros_izquierda($cantidad,$cadena){
    $cadena_set = str_pad($cadena, $cantidad, "0",STR_PAD_LEFT);
    return $cadena_set;
}
function quitar_tildes($cadena){
    $no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹"," ");
    $permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E","_");
    $texto = str_replace($no_permitidas, $permitidas ,$cadena);
    return $texto;
}
function _error(){
  global $conexion;
    return mysqli_error($conexion);
}
function hora($hora)
{
  $hora_pre = date_create($hora);
  $hora_pos = date_format($hora_pre, 'g:i A');
  return $hora_pos;
}
function crear_select2($nombre,$array,$id_valor,$style){
  $txt='';
  //style='width:200px' <select id="select2-single-input-sm" class="form-control input-sm select2-single">
	$txt.= "<select class='select2 form-control input-sm select2-single' name='$nombre' id='$nombre' style='$style'>";

  foreach($array as $clave=>$valor)
	{
    if($id_valor==$clave){
		$txt .= "<option value='$clave' selected>". $valor . "</option>";
    }
    else {
      $txt .= "<option value='$clave'>". $valor . "</option>";
    }
	}
	$txt .= "</select>";
	return $txt;
}
 function zfill($string, $n){
	return str_pad($string,$n,"0",STR_PAD_LEFT);
}
?>
