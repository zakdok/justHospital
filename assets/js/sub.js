jQuery(function($){
    $('#snb .snb-area .snb-list-area .dep1-wrap .dep1').on('click', function(){
        $(this).parent().next().stop().slideToggle('fast').parent().toggleClass('active').siblings().removeClass('active').find('.dep2-area').stop().slideUp('fast');
    })
    // var snbSticky = function(){
    //     var snb = $('#snb');
    //     if(snb.length){
    //         var snbOffsetTop = $('#sv').outerHeight() + $('#sv').offset().top;
    //         var headerHeight = $('#header').outerHeight();
    //         var windowScrollTop = $(window).scrollTop();
    //         if(windowScrollTop >= snbOffsetTop - headerHeight){
    //             snb.addClass('sticky');
    //             snb.css({'top': headerHeight })
    //             $('#sv').css({'margin-bottom': snb.outerHeight() });
    //         } else {
    //             snb.removeClass('sticky');
    //             snb.css({'top': '0' })
    //             $('#sv').css({'margin-bottom': '0' });
    //         }
    //     }
    // }
    // $(document).ready(function(){
    //     snbSticky();
    // })
    // $(window).scroll(function(){
    //     snbSticky();
    // })
    // $(window).resize(function() {
    //     if(this.resizeTO) {
    //         clearTimeout(this.resizeTO);
    //     }
    //     this.resizeTO = setTimeout(function() {
    //         $(this).trigger('resizeEnd');
    //     }, 0);
    // });
    // $(window).on('resizeEnd', function() {
    //     snbSticky();
    // });
})