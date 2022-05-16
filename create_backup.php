<?php
  require("_conexion.php");
  $orden=$_GET["orden"];

if($orden=="crear"){

  if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
 
    $target_path = getcwd();
    $now = str_replace(":", "", date("Y-m-d H:i"));
    $outputfilename = $dbname . '-' . $now . '.sql';
    $outputfilename = str_replace(" ", "-", $outputfilename);
    $save_path = $target_path . '\\'.$outputfilename;
            
    $command = "C:\\xampp\\mysql\\bin\\mysqldump --user=$username $dbname > $outputfilename";
    shell_exec($command);
              
    //Para forzar la descarga del navegador
    header('Content-Type: application/octet-stream');
    header("Content-Transfer-Encoding: Binary"); 
    header('Content-Disposition: attachment; filename='.basename($outputfilename));
    header('Content-Transfer-Encoding: binary');
    header("Content-Type: application/download");
    header("Content-Description: File Transfer"); 
    header("Content-Length: ".filesize($outputfilename));
    readfile($save_path);
    $exe="rm $save_path";
    shell_exec('del ' . $save_path); 
  }
  else
  {
    $target_path = getcwd();
    $now = str_replace(":", "", date("Y-m-d H:i"));
    $outputfilename = $dbname . '-' . $now . '.sql';
    $outputfilename = str_replace(" ", "-", $outputfilename);
    $save_path = $target_path . '/'.$outputfilename;
            
    $command = "mysqldump --user=$username --password=$password $dbname > $outputfilename";
    shell_exec($command);
              
    //Para forzar la descarga del navegador
    header('Content-Type: application/octet-stream');
    header("Content-Transfer-Encoding: Binary"); 
    header('Content-Disposition: attachment; filename='.basename($outputfilename));
    header('Content-Transfer-Encoding: binary');
    header("Content-Type: application/download");
    header("Content-Description: File Transfer"); 
    header("Content-Length: ".filesize($outputfilename));
    readfile($save_path);
           
    //Eliminar el archivo del servidor
    shell_exec('rm ' . $save_path); 

  }
}	



?>