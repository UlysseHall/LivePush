$(function() {
	function resize()
	{
		var totalHeight = $(window).outerHeight();
		var navHeight = $("nav").outerHeight();
		$(".home-container").outerHeight(totalHeight - navHeight);
	}
	
	resize();
	
	$(window).resize(function() {
		resize();
	});
});