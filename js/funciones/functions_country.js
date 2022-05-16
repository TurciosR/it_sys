$(document).ready(function() {
$('#editable').dataTable({
	"pageLength": 50
	});


$('#formulario').validate({		
	    rules: {
                    name: {  
                    required: true,           
                     }, 
                 },
        submitHandler: function (form) { 
            senddata();
        }
    });
                    
    var config = {
             '.chosen-select'           : {},
             '.chosen-select-deselect'  : {allow_single_deselect:true},
             '.chosen-select-no-single' : {disable_search_threshold:10},
             '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
             '.chosen-select-width'     : {width:"95%"}
            }
    for (var country in config) {
		$('#country').chosen(config[country]);                
    }
   //flag for  configure autosave to form
	var getautosave="false-0";
	getautosave=$('#autosave').val();
	var valautosave = getautosave.split('-');
	
  
	if(valautosave[0]=='true'){
		setInterval(function () {autosave(valautosave)}, valautosave[1] * 1000);
	} 
});
$(function (){
	//binding event click for button in modal form
	$(document).on("click", "#btnDelete", function(event) {
		deleted();
	});
	// Clean the modal form
	$(document).on('hidden.bs.modal', function(e) {
		var target = $(e.target);
		target.removeData('bs.modal').find(".modal-content").html('');
	});
	
});	

function autosave(val){
	var name=$('#name').val(); 
	if (name==''|| name.length == 0){
		var	typeinfo="Info";
		var msg="The field name is required";
		display_notify(typeinfo,msg);
		$('#name').focus();
	}
	else{
		senddata();
	}	
}	

function senddata(){
	var name=$('#name').val();  
    var description=$('#description').val();
    var title=$('#title').val();
    var seo_url=$('#seo_url').val();
    var seo_description=$('#seo_description').val();
    var seo_keywords=$('#seo_keywords').val();
    var seo_title=$('#seo_title').val();
    var visible=$('#visible:checked').val();
    var active1=$('#active1:checked').val();
    //Get the value from form if edit or insert
	var process=$('#process').val();
	
        if( $('#visible').is(':checked') ) {
		visible=true;
	}
	else{
		visible=false;
	}
	
	if( $('#active1').is(':checked') ) {
		active1=true;
	 }
	 else{
		active1=false;
	 }
	  if(process=='insert'){
		var id_country=$('#country').val();  
		var urlprocess='country.add.php';
		var id_region=$('#region').val(); 
	 }
	 
	 if(process=='edited'){
		var id_country=$('#id_country').val();
		var id_region=$('#region').val();
		var urlprocess='country.edit.php';  
		var datetimeindex=$('#country').val();
	 }
	 
	 var dataString='process='+process+'&id_country='+id_country+'&id_region='+id_region+'&description='+description+'&title='+title+'&seo_url='+seo_url+'&seo_description='+seo_description+'&seo_keywords='+seo_keywords+'&seo_title='+seo_title+'&visible='+visible+'&active1='+active1+'&name='+name+'&datetimeindex='+datetimeindex;

			$.ajax({
				type:'POST',
				url:urlprocess,
				data: dataString,			
				dataType: 'json',
				success: function(datax){	
					process=datax.process;
					if(process=='insert'){
						var maxid=datax.max_id;
						display_notify(datax.typeinfo,datax.msg);					
						$("#submit1").click(function () {
							location.href = 'country.edit.php?id_country='+maxid;
						});	
					}
					else{
						var getautosave=$('#autosave').val();
						var valautosave = getautosave.split('-');
						if(valautosave[0]!='true'){						
							display_notify(datax.typeinfo,datax.msg);
						}
						/*
						$("#buttonsubmit").click(function () {
							display_notify(datax.typeinfo,datax.msg);
						});
						* */
					}	      	       
				}
			});          
}

function deleted() {
	var id_country = $('#id_country').val();
	var dataString = 'process=deleted' + '&id_country=' + id_country;
	$.ajax({
		type : "POST",
		url : "country.delete.php",
		data : dataString,
		dataType : 'json',
		success : function(datax) {
			display_notify(datax.typeinfo, datax.msg);
			setInterval("location.reload();", 3000);
			$('#deleteModal').hide(); 
		}
	});
}
