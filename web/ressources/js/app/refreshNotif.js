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
            notifClear();
        });
        
    }
    
    function notifClicked() {
        $(".nav-notification-li").click(function() {
            var pathOpened = $(this).attr("data-path-clicked");
            $.post(pathOpened);
            
            window.location.href = $(this).find(".nav-notif-redirect-link").attr("data-redirect");
        });
    }
    
    function notifClear() {
        $(".nav-notif-clear-list").click(function() {
            var clearPath = $(this).attr("data-path");
            $.post(clearPath);
            $(".nav-notification-li").remove();
            notifRefresh();
        });
    }
    
    notifRefresh();
    setInterval(notifRefresh, 20000);
});