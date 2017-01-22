$(function()
{
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
            $('.noms-fichiers').append("<span class='label label-form label-default'>" + files[i].name + "</span>");
        }
    });
});