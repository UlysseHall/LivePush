$(function() {
    
    var pathGet = $(".nav-account-notification").attr("data-path-get");
    var previousData = "";
    
    function notifRefresh() {
        $.post(pathGet, function(data) {
            
            if(previousData != data) {
                $(".nav-account-notification ul").html(data);
            }
            
            previousData = data;
            var nbNotifs = $(".nb-new-notifs-hidden").attr("data-nb");
            $(".nav-nb-notification-badge-replace").text(nbNotifs);
            
            notifClicked();
        });
        
    }
    
    function notifClicked() {
        $(".nav-notification-li").click(function() {
            var pathOpened = $(this).attr("data-path-clicked");
            $.post(pathOpened);
            
            window.location.href = $(this).find(".nav-notif-redirect-link").attr("data-redirect");
        });
    }
    
    notifRefresh();
    setInterval(notifRefresh, 10000);
});