var ADAMANTIUM = {};
var editor


var ADAMANTIUM = 
{

	init : function()
	{

		ADAMANTIUM.eventos();	

		 editor = CodeMirror.fromTextArea(document.getElementById("photoshopHtml"), {
		  lineNumbers: true,
		  mode: "text/html",
		  matchBrackets: true
		});


	},
	
	eventos : function()
	{

		$('#dale').click(function(event) {
			ADAMANTIUM.tratarHtml();
			return false;
		});

	},	

	tratarHtml : function()
	{
		// console.log(editor.doc.getValue());
		var url = "index_2.php";

		$.ajax({
			type: "POST",
			url: url,
			data: 'html_mkt='+editor.doc.getValue(),
			dataType: "json"
		}).done(function(data) {
	 		
	 		editor.doc.setValue(data);

	 	})
			
	}

}

ADAMANTIUM.init();	

