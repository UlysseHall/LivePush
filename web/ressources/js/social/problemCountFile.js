$(function()
{
	var upFiles = [];
	
	function getInfos(file)
	{
		var reader = new FileReader();
		
		reader.onload = function(e)
		{
			var content = window.btoa(e.target.result);
			var name = file.name;
			var size = file.size;
			var fileObject = {"name": name, "content": content, "size": size};
			
			upFiles.push(fileObject);
			var fileIndex = jQuery.inArray(fileObject, upFiles);
			$('.noms-fichiers').append("<div class='label-file-name' id='"+ fileIndex +"'>" + name + "</div>");
			
			delableFile(fileIndex);
		}
		reader.readAsText(file, "UTF-8");
	}
	
	function delableFile(index)
	{
		$(".label-file-name#"+index).click(function()
		{
			upFiles[$(this).attr("id")] = "deleted";
			$(this).remove();
			$('.badge-nb-fichiers').html(parseInt($('.badge-nb-fichiers').html()) - 1);
		});
	}
	
    $('#files').change(function()
    {
        var files = $("#files")[0].files;
        var filesLength = files.length;
		
        $('.badge-nb-fichiers').html(filesLength);
        $('.noms-fichiers').html("");
        
        if(filesLength > 6)
        {
            $('.badge-nb-fichiers').html("Maximum : 6 fichiers");
            $('#files').val("");
        }
        
        for(var i = 0; i < filesLength; i++)
        {	
			getInfos(files[i]);
        }
    });
	
	$(".problem-add-form").submit(function() {
		$(this).append("<input type='hidden' name='upFiles' value='"+ JSON.stringify(upFiles) +"'>");
	});
});