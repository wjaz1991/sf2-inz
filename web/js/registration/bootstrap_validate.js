/* 
 * This is an jQuery extension to validate various form input fields
 */

(function($) {
    $.fn.bsValidateInput = function(options) {
        var lengthError = false;
        var emailError = false;
        var blankError = false;
        var identicalError = false;
        var checkboxError = false;
        var uniqueError = false;
        var message = '';
        //marking input as proper value
        //argument: desired input field
        function markSuccess(element) {
            var target = element.parent();
            
            if(element.parent().hasClass('input-group')) {
                target = element.parent().parent();
            }
            
            if(target.hasClass('has-error')) {
                target.removeClass('has-error');
            }
            target.find('.glyphicon').remove();
            target.addClass('has-success has-feedback');
            var okIcon = '<span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>';
            target.append(okIcon);
            if(target.find('.text-danger').length) {
                target.find('.text-danger').remove();
            }
        }
        
        //marking input error
        //argument: desired input field
        function markError(element, text) {
            var target = element.parent();
            
            if(element.parent().hasClass('input-group')) {
                target = element.parent().parent();
            }
            
            if(target.hasClass('has-success')) {
                target.removeClass('has-success');
            }
            target.find('.glyphicon').remove();
            target.addClass('has-error has-feedback');
            var errorIcon = '<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>';
            target.append(errorIcon);

            if(target.find('.text-danger').length) {
                target.find('.text-danger').remove();
            }

            if(typeof(text) !== 'undefined') {
                target.append('<p class="text-danger">' + text + '</p>');
            }
        }
        var settings = $.extend({
            'checkbox': false,
            'length': false,
            'blank': false,
            'email': false,
            'dbUnique': false,
            'dbUrl': false,
            'identical': false,
            'identicalTo': false,
            'formHandle': false
        }, options);
        
        if(settings.checkbox) {
            this.on('change', function() {
                if($(this).parent().find('.text-danger').length) {
                    $(this).parent().find('.text-danger').remove();
                }
                    if($(this).is(':checked')) {
                        //$(this).markSuccess();
                    } else {
                        checkboxError = true;
                        $(this).parent().parent().find('label').append('<p class="text-danger">This value must be checked</p>');
                        //markError($(this), 'This value must be checked');
                    }
                
                var formError = lengthError || blankError || emailError || identicalError || checkboxError;
        
                //console.log(formError);
                return formError;
            });
        } else {
            this.on('input', function() {
                if(settings.dbUnique && settings.dbUrl) {
                    var username = $(this).val();
                    
                    var element = $(this);
        
                    var request = $.ajax({
                        url: settings.dbUrl,
                        type: 'post',
                        data: { 'username': username },
                        dataType: 'json'
                    });

                    request.success(function(msg) {
                        if(msg.result == 1) {
                            message = 'Selected username is in use. Pick another one.';
                            markError(element, message);
                        }
                    });
                }
                
                if(settings.length) {
                    if($(this).val().length < settings.length) {
                        lengthError = true;
                        message =  'This field must be at least ' + settings.length + ' characters long.';
                    } else {
                        lengthError = false;
                    }
                }

                if(settings.email) {
                    var email = $(this).val();
                    var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    var result = regex.test(email);

                    if(result) {
                        emailError = false;
                    } else {
                        emailError = true;
                        message = 'This is not a valid email address.';
                    }
                }

                if(settings.blank) {
                    if($(this).val().trim().length == 0) {
                        blankError = true;
                        message = 'This field cannot be empty.';
                    } else {
                        blankError = false;
                    }
                }

                if(settings.identical && settings.identicalTo) {
                    var element = $(this);
                    $(settings.identicalTo).on('input', function() {
                        if($(settings.identicalTo).val().length != element.val().length) {
                            var message2 = "This value must be indentical to other value.";
                            markError(element, message2);
                        }
                        
                        if($(settings.identicalTo).val() == element.val()) {
                            markSuccess(element);
                        }
                    });
                    
                    if($(this).val() != $(settings.identicalTo).val()) {
                        identicalError = true;
                        message = "This value must be indentical to other value.";
                    } else {
                        identicalError = false;
                    }
                }
                
                var finalError = lengthError || blankError || emailError || identicalError || uniqueError;

                if(finalError) {
                    markError($(this), message);
                } else {
                    markSuccess($(this));
                }
            });
        }
        
        return this;
    };
}(jQuery));

$(function() {
        var username = $("#registration_user_username").bsValidateInput({
            length: 5,
            blank: true,
            dbUnique: true,
            dbUrl: Routing.generate('ajax_check_username')
        });

        var email = $("#registration_user_email").bsValidateInput({
            email: true,
            blank: true
        });

        var pass1 = $("#registration_user_password_first").bsValidateInput({
            blank: true,
            length: 6
        });

        var pass2 = $("#registration_user_password_second").bsValidateInput({
            identical: true,
            identicalTo: '#registration_user_password_first'
        });

        var terms = $("#registration_terms").bsValidateInput({
            checkbox: true
        });
});