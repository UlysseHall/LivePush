$(function()
{
    function displayImg(path)
    {
        var imgContainer = "<div class='img-display-container'><img src='"+ path +"' alt='pb-img'><span class='glyphicon glyphicon-remove undisplay-image-cross'></span></div>";
        $(".container-fluid").append(imgContainer);
        var cont = $('.img-display-container');
        var img = $('.img-display-container img');
        
        cont.css("opacity", 0).animate({"opacity": 1}, 150, "linear");
        img.css("opacity", 0).animate({"opacity": 1}, 150, "linear");
        
        cont.click(function(event) {
            if(event.target == this)
            {
                undisplayImg();
            }
        });
        
        $(".undisplay-image-cross").click(function() {
            undisplayImg();
        });
    }
    
    function undisplayImg()
    {
        var cont = $('.img-display-container');
        var img = $('.img-display-container img');
        
        cont.animate({"opacity": 0}, 150, "linear");
        img.animate({"opacity": 0}, 150, "linear", function() { cont.remove(); });
    }
    
    $(".page-file-icon, .page-file-name").click(function() {
        if($(this).closest(".file-container").data("file-image") == true)
        {
            var path = "" + $(this).closest(".file-container").data("file-code");
            displayImg(path);
        }
	});
});