$(function()
{
	var upFiles = [];
    var imgType = ["jpg", "jpeg", "png", "gif"];
	
	function getInfos(file)
	{
		var reader = new FileReader();
        var fileType = file.name.split(".");
        fileType = fileType[fileType.length - 1].toLowerCase();
		
		reader.onload = function(e)
		{
            var name = file.name;
            var size = file.size;
            
            if(imgType.indexOf(fileType) != -1)
            {
                var content = e.target.result;
                var image = true;
            }
            else
            {
                var content = window.btoa(e.target.result);
                var image = false;
            }
            
            var fileObject = {"name": name, "content": content, "size": size, "image": image};

            upFiles.push(fileObject);
            var fileIndex = jQuery.inArray(fileObject, upFiles);
            $('.noms-fichiers').append("<div class='label-file-name' id='"+ fileIndex +"'>" + name + "</div>");
            $('.badge-max-fichiers').html("");

            delableFile(fileIndex);
		}
        
        if(imgType.indexOf(fileType) != -1)
        {
            reader.readAsDataURL(file);
        }
        else
        {
            reader.readAsText(file, "UTF-8");
        }
	}
	
	function delableFile(index)
	{
		$(".label-file-name#"+index).click(function()
		{
			upFiles[$(this).attr("id")] = "deleted";
			$(this).remove();
			$('.badge-nb-fichiers').html(parseInt($('.badge-nb-fichiers').html()) - 1);
            $('.badge-max-fichiers').html("");
		});
	}
	
    $('#files').change(function()
    {
        var files = $("#files")[0].files;
        var filesLength = files.length;
        var nbFiles = parseInt($('.badge-nb-fichiers').html());
        
        if(nbFiles + filesLength > 6)
        {
            $('.badge-max-fichiers').html("Maximum : 6 fichiers");
        }
        else
        {
            for(var i = 0; i < filesLength; i++)
            {	
                getInfos(files[i]);
                nbFiles++;
                $('.badge-nb-fichiers').html(nbFiles);
            }
        }
    });
	
	$(".problem-add-form").submit(function() {
		$(this).append("<input type='hidden' name='upFiles' value='"+ JSON.stringify(upFiles) +"'>");
	});
});