$(function() {
    function logoResize()
    {
        var liHeight = $(".list-pb-display li").height();
        var liWidth = $(".list-pb-display li").width();
        
        $(".pb-display-logo img").height(liHeight);
        $(".pb-display-logo img").width(liHeight);
        $(".pb-display-logo").width(liHeight);
    }
    
    logoResize();
});