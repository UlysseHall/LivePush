$(function() {
    
    var path = $(".nav-account-notification").attr("data-path");
    var previousData = "";
    
    function notifRefresh() {
        $.post(path, function(data) {
            
            if(previousData != data) {
                $(".nav-account-notification ul").html(data);
            }
            
            previousData = data;
            var nbNotifs = $(".nb-new-notifs-hidden").attr("data-nb");
            $(".nav-nb-notification-badge-replace").text(nbNotifs);
        });
        
    }
    
    notifRefresh();
    setInterval(notifRefresh, 10000);
});