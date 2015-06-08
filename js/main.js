var ENVIO = {};

var ENVIO = 
{

	init : function()
	{


	},
	
	eventos : function()
	{

		$('#enviar').click(function(event) {
			ENVIO.enviando();
		});

	},	

	enviando : function()
	{

		var data = $('.form-signin').serialize();

  		var url = "/action/upload_file.php";
  		
		$.ajax({
			type: "POST",
			url: url,
			data: request,
			dataType: "json"
		}).done(function(data) {
	 		
			console.log('ok');
	 	})
			
	}

}

ENVIO.init();	

