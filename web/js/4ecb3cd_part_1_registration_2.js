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