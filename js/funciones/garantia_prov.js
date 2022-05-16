$(document).ready(function() {
var ruta=$('#ruta').val();
var ruta_archivo=$('#ruta_archivo').val()+'upload.php';
var dir_tabajo=$('#ruta_archivo').val()
var id_garantia=$('#id_garantia').val();

var ipserver=$('#ipserver').val();
ipserver=ipserver.trim();
if (ipserver=='::1'){
  var rutafinal='http://localhost/'+ruta_archivo;
}
else{
  var rutafinal='http://'+ipserver+ruta_archivo;
}

var img=ruta.split("/");
var filetype=ruta.split(".");
var ruta_imagen="http://"+ipserver+dir_tabajo+ruta;
var archivo='';
//alert(filetype[1])
if(filetype[1]=='pdf'||filetype[1]=='PDF'){
 archivo=ruta_imagen
 capt= {type: "pdf",  caption:img[1], url: ruta_imagen, downloadUrl: false}
}
else{
  //archivo="<img  class='kv-preview-data file-preview-image' src='"+ruta_imagen+"' class='file-preview-image' alt='Imagen'>";
  archivo=ruta_imagen
  capt={caption:img[1], url:ruta_imagen}
}
$("#producto-img").fileinput({
    uploadUrl: ruta_archivo,
    showUpload: false,
    showRemove: false,
    required: true,
    overwriteInitial:true,
    initialPreviewAsData: true, // identify if you are sending preview data only and not the raw markup
    initialPreviewFileType: 'image', // image is the default and can be overridden in config below
    initialPreview: [
    ruta_imagen,
    ],
    initialPreviewConfig: [
    capt,
    ],
    allowedFileExtensions: ["jpg", "png", "gif","pdf"],
    uploadExtraData: {
      id_garantia: $('#id_garantia').val()
    },
});



});
