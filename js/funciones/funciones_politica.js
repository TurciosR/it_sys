$(document).ready(function() {
$(".select").select2();
	// Clean the modal form
	/*$(document).on('hidden.bs.modal', function(e) {
		var target = $(e.target);
		target.removeData('bs.modal').find(".modal-content").html('');
	});*/

});
$(document).on("click", "#btnGua", function(event) {
  senddata();
});
$(document).on("click", ".dele", function() {
  var parent = $(this).parents().get(0);
	console.log(parent);
  $(parent).remove();
});

$(document).on('hidden.bs.modal', function(e) {
  var target = $(e.target);
  target.removeData('bs.modal').find(".modal-content").html('');
});
$(document).on("keypress", "#politica", function(event) {
  if (event.keyCode == 13)
  {
    var pol=$("#politica").val();
    if (pol != "")
    {
      tr_add = '';
      tr_add += "<tr>";
      tr_add += "<td id='desc'>"+pol+"</td>";
      tr_add += "<td class='dele'><input  id='delpo' type='button' class='btn btn-danger fa pull-right'  value='&#xf1f8;'></td>";
      tr_add += '</tr>';
      $("#inventable2").append(tr_add);
      $("#politica").val("");
    }
  }
  console.log(pol);
});

function senddata()
{
  var k = 0;
	var array_json1 = new Array();
  $("#inventable2 tr").each(function() {
		var desc=$(this).find("#desc").text();
		if (desc!="") {
			var obj1 = new Object();
			obj1.desc = desc;
			text1 = JSON.stringify(obj1);
			array_json1.push(text1);
			k=k+1;
		}

	});

  json_arr1 = '[' + array_json1 + ']';
  var dataString = 'process=agregar' + '&datos=' + json_arr1+'&cuantos1=' + k;
  $.ajax({
    type: 'POST',
    url: "admin_politica.php",
    data: dataString,
    dataType: 'json',
    success: function(datax) {
      if (datax.typeinfo == "Success") {
				display_notify(datax.typeinfo, datax.msg);
				setInterval("reload1();", 1000);
      } else {
        display_notify(datax.typeinfo, datax.msg);
      }
    }
  });
}
