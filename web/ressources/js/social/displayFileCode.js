$(function()
{
    var headerNavHtml = "<span class='glyphicon glyphicon-remove file-undisplay'></span>";
    var headerNavCss = "<style>.file-undisplay { font-size: 1.3em; color: #000; } .home-problems { background-color: #ecf0f1; overflow: hidden; }</style>";
    
	function displayCode(name, code, fileElement)
	{
		$(".home-content, .home-problems").css("width", "50%");
		$(".home-problems").html(headerNavHtml + headerNavCss + "<pre class='display-code-tag prettyprint linenums'>");
		$(".home-problems .display-code-tag").text(code);
        PR.prettyPrint();
        $(".file-container").addClass("file-unfocus");
        $(fileElement).closest(".file-container").removeClass("file-unfocus");
	}
	
	$(".page-file-icon, .page-file-name").click(function() {
		var name = $(this).closest(".file-container").data("file-name");
		var code = $(this).closest(".file-container").data("file-code");
		displayCode(name, code, this);
	});
});