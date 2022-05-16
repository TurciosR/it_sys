<?php
if(isset($_GET['estado'])){
	$estado=$_GET['estado'];
	if ($estado=='reiniciar'){
		system('/usr/bin/reboot');
		echo "<script>alert ('Reiniciando Sistema'); </script>";
	}
	else{
		system('/usr/bin/shutdown -h now');
		echo "<script>alert ('Apagando Sistema'); </script>";
	}
}
?>
