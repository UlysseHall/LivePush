$(function(){
    var windowH = $(window).height();
    var wrapperH = $('.home-container').height();
    if(windowH > wrapperH) {                            
        $('.home-container').css({'height':($(window).height())+'px'});
    }                                                                               
    $(window).resize(function(){
        var windowH = $(window).height();
        var wrapperH = $('.home-container').height();
        var differenceH = windowH - wrapperH;
        var newH = wrapperH + differenceH;
        var truecontentH = $('.home-problems').height();
        if(windowH > truecontentH) {
            $('.home-container').css('height', (newH)+'px');
        }

    })          
});