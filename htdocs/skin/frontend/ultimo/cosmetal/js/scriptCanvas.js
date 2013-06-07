jQuery(function(){
	alert('caio');
	//console.log(json);
	jQuery("#inviaDati").bind("click",function (event) {
		var json = stage.toJSON();
		var shape = stage.get('#101')[0];
		alert(jQuery('#101'));
		//alert(shape.getId());
		jQuery.ajax({
			url : "dati.php",
			data : json,
			type: "POST",
			//dataType : 'json', //restituisce un oggetto JSON
			success : function (data,stato) {
			   // $("#risultati").html(data);
			   // $("#statoChiamata").text(stato);
			   alert(json);
			},
			error : function (richiesta,stato,errori) {
				alert("E' evvenuto un errore. "+stato);
			}
		});
	});
});
