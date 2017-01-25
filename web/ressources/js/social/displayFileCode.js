$(function()
{
	function displayCode(name, code)
	{
		$(".home-content").css("width", "50%");
		$(".home-problems").css("width", "50%");
		$(".home-problems").html("<pre><code class='display-code-tag'>");
		$(".home-problems .display-code-tag").text(code);
	}
	
	$(".page-file-icon").click(function() {
		var name = $(this).closest(".file-container").data("file-name");
		var code = $(this).closest(".file-container").data("file-code");
		displayCode(name, code);
	});
});