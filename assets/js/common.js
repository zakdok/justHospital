$(document).ready(function () {
    headerScrollonoff();
    headerHeightMargin();

    $(window).resize(function(){ 
        if (window.innerWidth <= 1024) {
            $(window).off('scroll');
            $(".header__top").slideDown();
        }else{
            headerScrollonoff();
        }
        headerHeightMargin();
    }).resize();
});


// 헤더 스크롤 이벤트
function headerScrollonoff() {
    $(window).on('scroll', function () {
        var scrollValue = $(document).scrollTop();
        if (scrollValue > 0) {
            $(".header__top").slideUp(function(){
                headerHeightMargin();
            });
        }else {
            $(".header__top").slideDown(function(){
                headerHeightMargin();
            });
        }
    });
}

// 헤더 마진 이벤트
var headerHeightMargin = function(){
    var headerHeight = $('#header').outerHeight();
    var $ele = $('.hd_ele');
    $ele.css({'height': headerHeight});
}

// pc : dep2 hover event
$('.dep2-con').hover(function() {
    $('.dep3-wrap').not($(this).children('ul').css({display: 'block'})).css({display: ''});
});

// moblie : dep1 click event
$('.m_dep1').click(function() {
    $(this).next().stop().slideToggle(300);
    $(".m_dep1").not(this).next().stop().slideUp(300);
    return false;
});

// moblie : dep2 click event
$('.m_dep2Plus').click(function() {
    $(this).next().stop().slideToggle(300);
    $(".m_dep2Plus").not(this).next().stop().slideUp(300);
    return false;
});

// 헤더 모바일 메뉴 on/off 이벤트
function hMenuOnOff(number, overflow, opacity) {
    $('.mobile__menu').css({'right': number+'px'});
    if (opacity === 1) {
        $('.mobile__opacity').fadeIn(500);
    }else{
        $('.mobile__opacity').fadeOut(500);
    }
    $('html, body').css({'overflow': overflow});
}
