$(function()
{
	function displayCode(name, code)
	{
		$(".home-content, .home-problems").css("width", "50%");
		$(".home-problems").html("<pre class='display-code-tag prettyprint linenums'>");
		$(".home-problems .display-code-tag").text(code);
        PR.prettyPrint();
	}
	
	$(".page-file-icon").click(function() {
		var name = $(this).closest(".file-container").data("file-name");
		var code = $(this).closest(".file-container").data("file-code");
		displayCode(name, code);
	});
});