$(".btn_modal").fancybox({
    'padding': 0
});


$('.header__mobile_account').on('click touchstart', function (e) {
    e.preventDefault();
    $(this).toggleClass('open');
    $('.header__mobileNav').toggleClass('open');
});


$('.nav-toggle').on('click touchstart', function (e) {
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
    }
});


// Scrollbar

jQuery(document).ready(function () {
    jQuery('.scroll-wrap').scrollbar();
});


//Email Validation

var validations = {
    email: [/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/, 'Please enter a valid email address']
};
$(document).ready(function () {
    // Check all the input fields of type email. This function will handle all the email addresses validations
    $("input[type=email]").change(function () {
        // Set the regular expression to validate the email 
        validation = new RegExp(validations['email'][0]);
        // validate the email value against the regular expression
        if (!validation.test(this.value)) {
            // If the validation fails then we show the custom error message
            this.setCustomValidity(validations['email'][1]);
            return false;
        } else {
            // This is really important. If the validation is successful you need to reset the custom error message
            this.setCustomValidity('');
        }
    });
});

//Popup