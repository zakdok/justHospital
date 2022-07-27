jQuery(function($){
    var m2Slide = new Swiper(".m2 .area .slide-list-area .slide-area", {
        pagination: {
            el: ".m2 .area .slide-list-area .slide-area .swiper-pagination",
        },
    });
    var mdocSlide = new Swiper(".mdoc .slide-area", {
        loop: true,
        slidesPerView: 5,
        spaceBetween: 0,
        centeredSlides: true,
        autoplay: {
          delay: 5000,
          disableOnInteraction: false,
        },
        breakpoints: {
          1620: {
            slidesPerView: 4.4,
          },
          1220: {
            slidesPerView: 3.3,
          },
          768: {
            slidesPerView: 2.5,
          },
          486: {
            slidesPerView: 2,
            centeredSlides: false,
          },
        },
    });
    $(window).load(function(){
        mdocSlide.slideTo(2);
    })
    var m5Slide = new Swiper(".m5 .area .slide-area", {
        loop: true,
        slidesPerView: 3,
        spaceBetween: 8,
        autoplay: {
          delay: 5000,
          disableOnInteraction: false,
        },
        breakpoints: {
          1220: {
            slidesPerView: 2.5,
          },
          768: {
            slidesPerView: 2.2,
          },
          486: {
            slidesPerView: 1,
            centeredSlides: false,
          },
        },
    });
    $('.m5 .area .top-title-area .arrow-area .btn.prev').on('click', function(){
        m5Slide.slidePrev();
    })
    $('.m5 .area .top-title-area .arrow-area .btn.next').on('click', function(){
        m5Slide.slideNext();
    })
})