$(function() {
    
    function clickOnList() {
        $(".pb-display-select-list p").click(function() {
            var loader = $(".pb-select-list-container").attr("data-loader");
            var pbListPath = $(this).attr("data-link");
            $(".pb-select-list-container").addClass("list-pb-loading").html("<img src='"+ loader +"'>");
            
            $.get(pbListPath, function(data) {
                $(".pb-select-list-container").removeClass("list-pb-loading").html(data);
                clickOnList();
            });
        });
    }
    
    clickOnList();
    
});