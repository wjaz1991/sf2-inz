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
$(function() {
    //marking input as proper value
    //argument: desired input field
    /*function markSuccess(element) {
        if(element.parent().hasClass('has-error')) {
            element.parent().removeClass('has-error');
        }
        element.parent().find('.glyphicon').remove();
        element.parent().addClass('has-success has-feedback');
        var okIcon = '<span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>';
        element.parent().append(okIcon);
        if(element.parent().find('.text-danger').length) {
            element.parent().find('.text-danger').remove();
        }
    }
    
    //marking input error
    //argument: desired input field
    function markError(element, text) {
        if(element.parent().hasClass('has-success')) {
            element.parent().removeClass('has-success');
        }
        element.parent().find('.glyphicon').remove();
        element.parent().addClass('has-error has-feedback');
        var errorIcon = '<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>';
        element.parent().append(errorIcon);
        
        if(element.parent().find('.text-danger').length) {
            element.parent().find('.text-danger').remove();
        }
        
        if(typeof(text) !== 'undefined') {
            element.parent().append('<p class="text-danger">' + text + '</p>');
        }
    }
    
    //validating username field
    function validate_username(element) {
        var username = element.val();
        
        var request = $.ajax({
            url: Routing.generate('ajax_check_username'),
            type: 'post',
            data: { 'username': username },
            dataType: 'json'
        });
        
        request.success(function(msg) {
            if(msg.result === 1) {
                var message = 'Selected username is in use. Pick another one.';
                markError(element, message);
            } else {
                if(element.val().length >=3){
                    markSuccess(element);
                } else {
                    markError(element, 'Username must be at least 3 characters long.');
                }
            }
        });
    }
    
    //validating email field
    function validate_email(element) {
        var state = false;
        
        var email = element.val();
        
        var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        
        var result = regex.test(email);
        
        if(result) {
            markSuccess(element.parent());
            state = true;
        } else {
            markError(element.parent(), 'This email is not valid email address.');
            state = false;
        }
        
        return state;
    }
    
    //validating password fields
    function validate_first_password(element, secondElement) {
        var state = false;
        
        if(element.val().length < 3) {
            markError(element, 'Password must be at least 3 characters long.');
            state = false;
        } else {
            markSuccess(element);
            state = true;
        }
        
        if(element.val() !== secondElement.val()) {
            markError(secondElement, 'Both passwords must be the same.');
            state = false;
        } else {
            markSuccess(secondElement);
            state = true;
        }
        
        return state;
    }
    
    function validate_second_password(element, firstElement) {
        var state = false;
        
        if(element.val() !== firstElement.val()) {
            markError(element, 'Both passwords must be the same.');
            markError(firstElement);
            state = false;
        } else {
            markSuccess(element);
            markSuccess(firstElement);
            state = true;
        }
        
        return state;
    }
    
    //validating terms checkbox
    function validate_checkbox(element) {
        var state = false;
        
        if(element.is(':checked')) {
            element.parent().find('a').css('color', 'green');
            state = true;
        } else {
            element.parent().find('a').css('color', 'red');
            state = false;
        }
        
        return state;
    }
    
    $("#registration_user_username").on('input', function() {
        validate_username($(this));
    });
    
    $("#registration_user_email").on('input', function() {
        validate_email($(this));
    });
    
    $("#registration_user_password_first").on('input', function() {
        validate_first_password($(this), $("#registration_user_password_second"));
    });
    
    $("#registration_user_password_second").on('input', function() {
        validate_second_password($(this), $("#registration_user_password_first"));
    });
    
    $("#registration_terms").on('change', function() {
        validate_checkbox($(this));
    });
    
    //final checking before submit
    $("#register-form").on('submit', function() {
        var result = false;
        
        var username = false;
        //checking username
        if($("#registration_user_username").val().trim().length > 0
                && !$("#registration_user_username").parent().hasClass('has-error')) {
            username = true;
        } else {
            markError($("#registration_user_username"));
        }
        
        var email = validate_email($("#registration_user_email"));
        var password1 = validate_first_password($("#registration_user_password_first"), $("#registration_user_password_second"));
        var password2 = validate_second_password($("#registration_user_password_second"), $("#registration_user_password_first"));
        var terms = validate_checkbox($("#registration_terms"));
        
        result = username && email && password1 && password2 && terms;
        
        return result;
    });*/
});