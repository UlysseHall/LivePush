$(function() {
    
    function answering(com)
    {
        var action = $("form.comment-form").attr("action");
        var comFromId = com.attr("id");
        
        var input = "<form class='col-xs-11 col-xs-offset-1 comment-reply-form' method='POST' action='"+ action +"/"+ comFromId + "'> <textarea class='form-control' minlength='2' rows='2' placeholder='Commentez' name='comment' required></textarea> <button type='submit' class='btn btn-success pull-right btn-sm'>Répondre</button> </form>";
        
        com.after(input);
        com.addClass("pb-comments-comment-answering");
    }
    
    function unanswering(com)
    {
        com.next(".comment-reply-form").remove();
        com.removeClass("pb-comments-comment-answering");
    }
    
    $("button.comment-reply").click(function() {
        var com = $(this).closest(".pb-comments-comment");
        
        if(!com.hasClass("pb-comments-comment-answering"))
        {
            answering(com);
        }
        else
        {
            unanswering(com);
        }
    });
});