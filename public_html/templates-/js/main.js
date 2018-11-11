
$(".btn_modal").fancybox({
    'padding'    : 0
});


$('.header__mobile_account').on('click touchstart', function(e) {
    e.preventDefault();
    $(this).toggleClass('open');
    $('.header__mobileNav').toggleClass('open');
});


$('.nav-toggle').on('click touchstart', function(e) {
    e.preventDefault();
    $(this).toggleClass('open');
    $('.topnav').toggleClass('open');
});


// SVG IE11 support
svg4everybody();


var reports = new Swiper('.reports_slider', {
    slidesPerView: 4,
    spaceBetween: 12,

    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev'
    },
    pagination: {
        el: '.swiper-pagination'
    },
    breakpoints: {

        1230: {
            slidesPerView: 3
        },
        992: {
            slidesPerView: 2
        },
        768: {
            slidesPerView: 1
        }
    }
});


var service = new Swiper('.service_slider', {
    slidesPerView: 1,
    spaceBetween: 20,

    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev'
    },
    pagination: {
        el: '.swiper-pagination'
    }
});

var reviews = new Swiper('.review_slider', {
    autoHeight: true,
    pagination: {
        el: '.swiper-pagination'
    },
});