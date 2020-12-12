/*global $, document, Chart, LINECHART, data, options, window*/
$(document).ready(function () {

    'use strict';

    // ------------------------------------------------------- //
    // full screen button
    // ------------------------------------------------------ //

    function toggleFullscreen(elem) {
        elem = elem || document.documentElement;
        if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.msRequestFullscreen) {
                    elem.msRequestFullscreen();
                } else if (elem.mozRequestFullScreen) {
                    elem.mozRequestFullScreen();
                } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                }
            } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
        }
    }
    if(('#btnFullscreen').length > 0) {
        document.getElementById('btnFullscreen').addEventListener('click', function() {
            toggleFullscreen();
        });
    }

    //Custom select
    $('select').selectpicker();

    // Main Template Color
    var brandPrimary = '#33b35a';

    // ------------------------------------------------------- //
    // Custom Scrollbar
    // ------------------------------------------------------ //

    if ($(window).outerWidth() > 992) {
        $("nav.side-navbar,.table-container,.transaction-list").mCustomScrollbar({
            theme: "light",
            scrollInertia: 200
        });
    }

    $(document).scroll(function() {
        var y = $(this).scrollTop();
        if (y > 65) {
            $('nav.side-navbar').css("top","0");
        } else {
            $('nav.side-navbar').css("top","63px");
        }
    });


    // ------------------------------------------------------- //
    // Side Navbar Functionality
    // ------------------------------------------------------ //
    
    $('#toggle-btn').on('click', function (e) {

        e.preventDefault();

        if ($(window).outerWidth() > 1199) {
            $('nav.side-navbar').toggleClass('shrink');
            $('.page').toggleClass('active');
        } else {
            $('nav.side-navbar').toggleClass('shrink');
            $('.page').toggleClass('active-sm');
        }
    });


    // ------------------------------------------------------- //
    // Login  form validation
    // ------------------------------------------------------ //
    $('#login-form').validate({
        messages: {
            loginUsername: 'please enter your username',
            loginPassword: 'please enter your password'
        }
    });

    // ------------------------------------------------------- //
    // Register form validation
    // ------------------------------------------------------ //
    $('#register-form').validate({
        messages: {
            registerUsername: 'please enter your first name',
            registerEmail: 'please enter a vaild Email Address',
            registerPassword: 'please enter your password'
        }
    });

    // ------------------------------------------------------- //
    // Jquery Progress Circle
    // ------------------------------------------------------ //
    var progress_circle = $("#progress-circle").gmpc({
        color: brandPrimary,
        line_width: 5,
        percent: 80
    });
    progress_circle.gmpc('animate', 80, 3000);

    // ------------------------------------------------------- //
    // External links to new window
    // ------------------------------------------------------ //

    $('.external').on('click', function (e) {

        e.preventDefault();
        window.open($(this).attr("href"));
    });

    // ------------------------------------------------------ //
    // For demo purposes, can be deleted
    // ------------------------------------------------------ //

    var stylesheet = $('link#theme-stylesheet');
    $( "<link id='new-stylesheet' rel='stylesheet'>" ).insertAfter(stylesheet);
    var alternateColour = $('link#new-stylesheet');

    if ($.cookie("theme_csspath")) {
        alternateColour.attr("href", $.cookie("theme_csspath"));
    }

    $("#colour").change(function () {

        if ($(this).val() !== '') {

            var theme_csspath = 'css/style.' + $(this).val() + '.css';

            alternateColour.attr("href", theme_csspath);

            $.cookie("theme_csspath", theme_csspath, { expires: 365, path: document.URL.substr(0, document.URL.lastIndexOf('/')) });

        }

        return false;
    });

});
