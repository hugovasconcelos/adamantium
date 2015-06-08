var UPLOAD = {};

var UPLOAD = 
{

	init : function()
	{

		UPLOAD.eventos();	

	},
	
	eventos : function()
	{

		$('#comentar').click(function(event) {
			$('#jira').slideToggle(400);
		});

		// $('#enviar').click(function(event) {
		// 	UPLOAD.enviar();
		// 	return false
		// });		

	},	

	enviar : function()
	{

		// var data = $('.form-signin').serialize();
		// var url = "action/upload_file.php";
  		
		// $.ajax({
		// 	type: "POST",
		// 	url: url,
		// 	data: data
		// }).done(function(data) {
	 		
		// 	console.log(data);
	 // 	})


	},	



}

UPLOAD.init();	

