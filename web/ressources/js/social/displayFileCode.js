$(function()
{
    var homeProblemsHTML = $(".home-problems").html();
    
    function resizeCode()
    {
        var headerHeight = $("div.file-display-header").outerHeight();
        var containerHeight = $(".home-problems").outerHeight();
        $("pre.display-code-tag").outerHeight(containerHeight-headerHeight);
    }
    
    function undisplayCode()
    {
        $(".home-problems").css("width", "25%");
        $(".home-content").css("width", "75%");
        $(".home-problems").html(homeProblemsHTML);
        $(".file-container").removeClass("file-unfocus");
    }
    
	function displayCode(name, code, fileElement)
	{
        var headerNavHtml = "<div class='file-display-header row'><p class='pb-code-edit'>Corriger ce code</p><p>"+name+"</p><span class='glyphicon glyphicon-remove file-undisplay'></span></div>";
        
		$(".home-content, .home-problems").css("width", "50%");
		$(".home-problems").html(headerNavHtml+"<pre class='display-code-tag prettyprint linenums row'></pre>");
		$(".home-problems .display-code-tag").text(code);
        PR.prettyPrint();
        $(".file-container").addClass("file-unfocus");
        $(fileElement).closest(".file-container").removeClass("file-unfocus");
        resizeCode();
        
        $(".file-undisplay").click(function(){
            undisplayCode();
        });
        
        $("p.pb-code-edit").click(function() {
            $(".home-problems").html(headerNavHtml+"<pre class='display-code-tag prettyprint linenums row'></pre>");
		    $(".home-problems .display-code-tag").text(code);
            $("pre.display-code-tag").attr("contenteditable", "true").css({"transition": "border 0.1s", "border-top": "2px solid #2ecc71", "color": "white"});
            
            $(".file-undisplay").click(function(){
                undisplayCode();
            });
            
            $("p.pb-code-edit").text("Valider").click(function() {
                var codeEdited = $("pre.display-code-tag").text();
                undisplayCode();
                $(".form-confirmation").prepend("<div class='label label-success label-code-saved'>Correction enregistrée</div>");
            })
        });
	}
	
	$(".page-file-icon, .page-file-name").click(function() {
		var name = $(this).closest(".file-container").data("file-name");
		var code = $(this).closest(".file-container").data("file-code");
		displayCode(name, code, this);
	});
    
    $(window).resize(function() {
		resizeCode();
	});
});