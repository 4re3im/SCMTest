( function($) {

    var app = {

        init: function() {

            app.burgerMenu();
            app.notification();

            $(window).on("load resize", function() {
                app.resources();

            })
            app.accessibility();
            app.forms();
            app.passwordChecker();

          // GCAP-535 - gxbalila
          app.hideLoginButton();

        },

        burgerMenu: function() {

            $('.mobile-navigation').click(function(e){

                e.stopPropagation();

                // Burger menu icon to close icon
                $('.mobile-nav', this).toggleClass('open');

                // Check if burger menu has class 'open', change text from menu to close
                if($('.mobile-nav', this).hasClass('open')) {
                    $('.indicator').text('Close');
                } else {
                    $('.indicator').text('Menu');
                }

                // Add 'active' class to show navigation - for mobile view only
                if($(window).width() <= 992) {
                    $(this).next().toggleClass('active');
                }

                // If clicked outside the element, navigation menu will be hidden
                $(document).on("click", function(event){
                    var $trigger = $(".mobile-navigation");
                    if($trigger !== event.target && !$trigger.has(event.target).length){
                        $(".navigation").removeClass('active');
                        $('.mobile-nav', this).removeClass('open');
                        $('.indicator').text('Menu');
                    }
                });

            });
        },

        notification: function() {

            $(document).on('click', '.content-file-download', function (event) {
                event.preventDefault();
                closeModal();
                displayDownloadNotification();
                downloadContent(titlesURL + 'downloadFile/' + $(this).attr('href'));
                event.stopImmediatePropagation();
                return false;
            });

        },

        /* Show more/show less of subjects */
        resources: function() {

            var thumbnails = $(".thumbnail");
            var size_li = $(".thumbnail").hide().size();

            if($(window).width() > 768) {
                var take = 8;
            } else {
                var take = 4;
            }

            thumbnails.filter(':lt('+ take +')').show();

            $('.show-more').click(function(e) {
                e.preventDefault();
                var visible = $(".thumbnail:visible");
                count = visible.length;
                console.log(count)

                thumbnails.not(visible).show()

                if(count == size_li || count != size_li){
                    $('.show-more').hide();
                    $('.show-less').show();
                }
            });

            $('.show-less').click(function(e) {
                e.preventDefault();

                var visible = $(".thumbnail:visible"),
                    count = visible.length,
                    realTake = count === size_li ? size_li % take : take,
                    enoughShowing = (count - take > 0);

                console.log(visible.length)

                if( enoughShowing )
                    visible.slice(-realTake).hide()

                $('.show-less').hide();

                if(visible.length - take <= take){
                    $('.show-more').show();
                }
            });

        },

        /* Slick carousel for resources */
        resourcesCarousel: function() {

            $('.resource-carousel').not('.slick-initialized').slick({
                infinite: true,
                rows: 2,
                slidesToShow: 1,
                arrows: false,
                dots: false,
                variableWidth: true,
            });

            $(window).resize(function() {
                if($(window).width() > 767) {
                    $('.resource-carousel').slick('unslick');
                }
            })

        },

        /* Accessibility using tabs */
        accessibility: function() {

            $('.avatar-wrapper').focus(function() {
                $('.top-nav').show();

                $(document).on('keydown', function(e){
                    if(e.shiftKey && e.keyCode == 9 ) {
                        $('.top-nav').hide();
                    }
                });
            })

            $('.teacher-account').focus(function(e) {
                $('.top-nav').hide();
            })

        },

        /* Page forms */
        forms: function() {

            // Add .login-page class to the body if it's a login page
            if(window.location.href.indexOf('login') > -1) {
                $('body').addClass('login-page');
            }

            // Add .login-page class to the body if it's a registration page
            if(window.location.href.indexOf('teacher') > -1 || window.location.href.indexOf('student') > -1) {
                $('body').addClass('registration-page');
            }

          // Add .forget-password-page class to the body if it's a forgot-password page
          if(window.location.href.indexOf('forgot_password') > -1) {
            $('body').addClass('forget-password-page');
          }

            // Show / Hide password
            $('.toggle-password').css('opacity', 1);
            $(".toggle-password").click(function() {
                $(this).text( ($(this).text() == 'Hide' ? 'Show' : 'Hide'))
                    .toggleClass('hide-password');

                var input = $($(this).attr("toggle"));
                if (input.attr("type") == "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });

            var subscribe = $('.uCustomerCare');
            studentSubj = $('.student-subj');

            subscribe.change(function() {
                if(this.checked) {
                    studentSubj.show();
                } else {
                    studentSubj.hide();
                }
            });

        },

        // Password validator on keyup
        passwordChecker: function() {

            var password = document.querySelector('.password-field');

            var helperText = {
                charLength: document.querySelector('.password-req .length'),
                letter: document.querySelector('.password-req .letter'),
                number: document.querySelector('.password-req .number')
            };

            var pattern = {
                charLength: function() {
                    if( password.value.length >= 8 ) {
                        return true;
                    }
                },
                letter: function() {
                    var regex = /(?=.*?[A-Za-z]).+/; // atleast one letter pattern
                    if( regex.test(password.value) ) {
                        return true;
                    }
                },
                number: function() {
                    var regex = /(?=.*?[0-9])/; // atleast one number pattern

                    if( regex.test(password.value) ) {
                        return true;
                    }
                }
            };

            // ANZGO-3920 modified by jbernardez 20181122
            if (password !== null) {
                // Listen for keyup action on password field
                password.addEventListener('keyup', function () {
                    // Check that password is a minimum of 8 characters
                    patternTest(pattern.charLength(), helperText.charLength);

                    // Check that password contains at least one letter
                    patternTest(pattern.letter(), helperText.letter);

                    // Check that password contains at least one number
                    patternTest(pattern.number(), helperText.number);


                    // Check that all requirements are fulfilled
                    if (hasClass(helperText.charLength, 'valid') &&
                        hasClass(helperText.letter, 'valid') &&
                        hasClass(helperText.number, 'valid')
                    ) {
                        addClass(password.parentElement, 'valid');
                    } else {
                        removeClass(password.parentElement, 'valid');
                    }
                });
            }

            function patternTest(pattern, response) {
                if(pattern) {
                    addClass(response, 'valid');
                }
                else {
                    removeClass(response, 'valid');
                }
            }

            function addClass(el, className) {
                if (el.classList) {
                    el.classList.add(className);
                }
                else {
                    el.className += ' ' + className;
                }
            }

            function removeClass(el, className) {
                if (el.classList)
                    el.classList.remove(className);
                else
                    el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
            }

            function hasClass(el, className) {
                if (el.classList) {
                    // console.log(el.classList);
                    return el.classList.contains(className);
                }
                else {
                    new RegExp('(^| )' + className + '( |$)', 'gi').test(el.className);
                }
            }
        },

      hideLoginButton: function () {
        var currentUrl = window.location.href;
        var headerLinks = $('#navigation-wrapper').find('a');

        if (currentUrl.indexOf('/go/login/') > 0) {
          $('.login-btn').removeClass('show');
          $('.login-btn').hide();
        }
      }

    }

    $(document).ready(function(){
        app.init();

        // ANZGO-3947 myresources collapse added by mtanada 20181211
        $('.panel-heading').click( function() {
            $(this).toggleClass('active');
        })
    });

})( jQuery );