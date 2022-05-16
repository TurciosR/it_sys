$(document).ready(function(){
	// load first tab content
  var url1=$("#tab-1").attr("data-url");
  var id_producto=$("#id_producto").val();
  $('.select2').select2();
// $.get(url1, { id_producto:id_producto},function(data){
  $.get(url1, function(data){
   $("#tab1").html(data);
 });
 //select different tab
 $('.nav a').click(function (e){
	    //e.preventDefault();
	    var url = $(this).attr("data-url");
	    var href = this.hash;
	    var pane = $(this);

	    // ajax load from data-url
	    $(href).load(url , function (result) {
	        pane.tab('show');
	    });
  });
});
