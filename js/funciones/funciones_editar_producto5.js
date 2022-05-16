$(document).ready(function(){
	var ruta=$('#ruta').val();
	var ruta_archivo=$('#ruta_archivo').val()+'upload.php';
	//alert('ruta:'+ruta+' ruta_archivo:'+ruta_archivo);
	var ipserver=$('#ipserver').val();
  ipserver=ipserver.trim();
	if (ipserver=='::1'){
	 var rutafinal='http://localhost/'+ruta_archivo;
	}
	else{
	 var rutafinal='http://'+ipserver+ruta_archivo;
	}
	var imgs=ruta.split(",");
 	var archivos='';
	imgs.forEach(function(item, index, array) {
	 archivos+="<img src='"+item+"' class='file-preview-image' alt='Imagen'>";
  });
	$("#producto-img").fileinput({
	'showUpload':true,
	'previewFileType':'image',
	'allowedFileExtensions' : ['jpg', 'png','gif'],
	'language': 'es',
  'uploadAsync': false,
  'uploadUrl': ruta_archivo,
  'allowedFileExtensions' : ['jpg', 'png','gif'],
  uploadExtraData: function() {
  return {
                idProd: $('#id_producto').val(),
                descrip: $('#descripcion').val(),
        };
    },
//  'initialPreview': ["<img src='"+imgs+"' class='file-preview-image' alt='Desert' title='Desert'>",],
  'initialPreview': [archivos,],
});

}); //end $(document).ready(function(){
