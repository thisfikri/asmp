$(".msg-box.hide").css("display", "none");
$(document).ready(function() {
    /*
     * Front Page
     */

    // -- Get Page Name --
    function get_page_name() {
        var
            curr_url = window.location.href,
            sburl = curr_url.split('/'),
            page_name = '';
        page_name = sburl[sburl.length - 1];
        //console.log(page_name);
        return page_name;
    }

    // -- Get Base URL --
    function baseURL() {
        var
            curr_url = window.location.href,
            sburl = curr_url.split('/'),
            base_url = '';
        base_url = sburl[0] + '//' + sburl[2] + '/' + sburl[3];
        return base_url;
    }

    function ASMPFormHandler(form) {
        var i = 0;
        Object.defineProperty(this, 'currForm', {
            value: form[0],
            configurable: true
        });

        Object.defineProperty(this, 'cFInpProp', {
            value: {},
            configurable: true
        });

        for (; i < this.currForm.length - 2; i++) {
            Object.defineProperty(this.cFInpProp, this.currForm[i].name, {
                value: this.currForm[i].value
            });
        }

        this.showFormProperties = function() {
            return Object.getOwnPropertyNames(this.cFInpProp);
        }

        this.getFormInputBy = function(dataType) {
            var
                propertyNames = this.showFormProperties(),
                finalResult = null;
            if (dataType == 'string') {
                finalResult = '';
                for (let i = 0; i < propertyNames.length - 2; i++) {
                    finalResult += propertyNames[i] + ': ' + this.cFInpProp[propertyNames[i]] + ', ';
                }

                return finalResult;
            } else if (dataType == 'pureObject') {
                finalResult = {};
                for (let i = 0; i < propertyNames.length; i++) {
                    Object.defineProperty(finalResult, propertyNames[i], {
                        value: this.cFInpProp[propertyNames[i]]
                    });
                }
                return finalResult;
            } else if (dataType == 'json') {
                finalResult = {};
                for (let i = 0; i < propertyNames.length; i++) {
                    Object.defineProperty(finalResult, propertyNames[i], {
                        value: this.cFInpProp[propertyNames[i]]
                    });
                }
                return JSON.stringify(finalResult);
            }
        }
    }

    // Top Action Help Button
    $('.top-action button.help').click(function() {
        let
            helpBoxStatus = $('.help-box').attr('class');
        if (helpBoxStatus.search('hide-element') !== -1) {
            $('.help-box').removeClass('hide-element');
            $('.dim-light').removeClass('hide-element');
        }
    });

    $('.top-action button.lang').click(function() {
        window.alert('Fitur Ini Belum Tersedia');
    });

    //Help Box Close Button
    $('.button.help-box-close-btn').click(function() {
        let
            helpBoxStatus = $('.help-box').attr('class');
        console.log(helpBoxStatus);
        if (helpBoxStatus.search('hide-element') === -1) {
            $('.help-box').addClass('hide-element');
            $('.dim-light').addClass('hide-element');
        }
    });

    // Pre-Register Submit Button
    $('.pre-register-btn').click(function() {
        var
            uName = $('.pre-register-form input[name=username]').val(),
            trName = $('.pre-register-form input[name=true_name]').val(),
            uPass = $('.pre-register-form input[name=password]').val(),
            uPassConfirm = $('.pre-register-form input[name=passconfirm]').val(),
            uEmail = $('.pre-register-form input[name=email').val(),
            fieldSection = $('.pre-register-form input[name=field_section]').val(),
            recoverID = $('.pre-register-form input[name=recovery_id]').val(),
            cvt = $.cookie('vt');
        $(this).html('<i class="fa fa-sync fa-spin"></i>');
        $(this).css('background-color', '#409da3');

        $.ajax({
                url: baseURL() + '/pre_user',
                type: 'POST',
                dataType: 'json',
                data: {
                    username: uName,
                    true_name: trName,
                    password: uPass,
                    passconfirm: uPassConfirm,
                    email: uEmail,
                    field_section_name: fieldSection,
                    recovery_id: recoverID,
                    vt: cvt
                },
            })
            .done(function() {
                console.log("success");
            })
            .fail(function() {
                console.log("error");
                $('.pre-register-btn').html('Buat <i class="fa fa-user-plus"></i>');
            })
            .always(function(result) {
                switch (result.status) {
                    case 'success':
                        window.location.replace(baseURL() + '/registrasi-awal');
                        break;
                    case 'failed':
                        window.location.replace(baseURL() + '/registrasi-awal');
                        break;
                    case 'error':
                        $('.pre-register-btn').html('Buat <i class="fa fa-user-plus"></i>');
                        $(".msg-box.hide").css("display", "block");
                        $(".msg-box.hide").removeClass('success');
                        $(".msg-box.hide").removeClass('warning');
                        $(".msg-box.hide").addClass('error');
                        if (result.message.search('|') !== -1) {
                            var
                                msglist = result.message.split('|'),
                                i = 0;
                            console.log(msglist);
                            for (; i < msglist.length - 1; i++) {
                                $(".msg-box.hide").append('<p class="msg-txt">' + msglist[i] + '</p>');
                                console.log(i);
                            }
                        } else {
                            $(".msg-box.hide").append('<p class="msg-txt">' + result.message + '</p>');
                        }
                        break;
                    case 'warning':
                        $('.pre-register-btn').html('Buat <i class="fa fa-user-plus"></i>');
                        $(".msg-box.hide").css("display", "block");
                        $(".msg-box.hide").removeClass('error');
                        $(".msg-box.hide").removeClass('success');
                        $(".msg-box.hide").addClass('warning');
                        if (result.message.search('|') !== -1) {
                            var
                                msglist = result.message.split('|'),
                                i = 0;
                            console.log(msglist);
                            for (; i < msglist.length - 1; i++) {
                                $(".msg-box.hide").append('<p class="msg-txt">' + msglist[i] + '</p>');
                                console.log(i);
                            }
                        } else {
                            $(".msg-box.hide").append('<p class="msg-txt">' + result.message + '</p>');
                        }
                        break;
                    default:
                        $('.pre-register-btn').html('Buat <i class="fa fa-user-plus"></i>');
                        if (result.message.search('|') !== -1) {
                            var
                                msglist = result.message.split('|'),
                                i = 0;
                            console.log(msglist);
                            for (; i < msglist.length - 1; i++) {
                                $(".msg-box.hide").append('<p class="msg-txt">' + msglist[i] + '</p>');
                                console.log(i);
                            }
                        } else {
                            $(".msg-box.hide").append('<p class="msg-txt">' + result.message + '</p>');
                        }
                }
            });
    });

    $('.msg-box.hide, .msg-box.php').click(function() {
        $(this).css('display', 'none');
        $('.msg-box.hide .msg-txt').remove();
    });

    // Login Submit Button
    $('.login-btn').click(function() {
        $(this).html('<i class="fa fa-sync fa-spin"></i>');
        $(this).css('background-color', '#409da3');

        var
            uName = $('.login-box form input[name=username]').val(),
            uPass = $('.login-box form input[name=password]').val();

        $.ajax({
                url: baseURL() + '/logauth',
                type: 'POST',
                dataType: 'json',
                data: {
                    username: uName,
                    password: uPass,
                    vt: $.cookie('vt')
                },
            })
            .done(function() {
                console.log("success");
            })
            .fail(function() {
                console.log("error");
                $('.login-btn').html('Masuk <i class="fa fa-sign-in-alt"></i>');
            })
            .always(function(result) {
                console.log("complete");
                switch (result.status) {
                    case 'success':
                        window.location.replace(result.message);
                        break;
                    case 'error':
                        $('.login-btn').html('Masuk <i class="fa fa-sign-in-alt"></i>');
                        $(".msg-box.hide").css("display", "block");
                        $(".msg-box.hide").removeClass('success');
                        $(".msg-box.hide").removeClass('warning');
                        $(".msg-box.hide").addClass('error');
                        // console.log(result.message.search('~'));
                        if (result.message.search('~') !== -1) {
                            console.log('Found');
                            var
                                msglist = result.message.split('~'),
                                i = 0;
                            // console.log(msglist);
                            for (; i < msglist.length - 1; i++) {
                                $(".msg-box.hide").append('<p class="msg-txt">' + msglist[i] + '</p>');
                                // console.log(i);
                            }
                        } else {
                            $(".msg-box.hide").append('<p class="msg-txt">' + result.message + '</p>');
                        }
                        break;
                    case 'warning':
                        $('.login-btn').html('Masuk <i class="fa fa-sign-in-alt"></i>');
                        $(".msg-box.hide").css("display", "block");
                        $(".msg-box.hide").removeClass('success');
                        $(".msg-box.hide").removeClass('error');
                        $(".msg-box.hide").addClass('warning');
                        console.log(result.message.search('~'));
                        if (result.message.search('~') !== -1) {
                            // console.log('Found');
                            var
                                msglist = result.message.split('~'),
                                i = 0;
                            // console.log(msglist);
                            for (; i < msglist.length - 1; i++) {
                                $(".msg-box.hide").append('<p class="msg-txt">' + msglist[i] + '</p>');
                                // console.log(i);
                            }
                        } else {
                            $(".msg-box.hide").append('<p class="msg-txt">' + result.message + '</p>');
                        }
                        break;
                    default:
                        $('.login-btn').html('Masuk <i class="fa fa-sign-in-alt"></i>');
                        if (result.message.search('|') !== -1) {
                            var
                                msglist = result.message.split('|'),
                                i = 0;
                            console.log(msglist);
                            for (; i < msglist.length - 1; i++) {
                                $(".msg-box.hide").append('<p class="msg-txt">' + msglist[i] + '</p>');
                                console.log(i);
                            }
                        } else {
                            $(".msg-box.hide").append('<p class="msg-txt">' + result.message + '</p>');
                        }
                }
            });
    });

    // Show/Hide Button
    $('.show-hide-btn').click(function() {
        let
            inpType = '';
        inpType = $('#recoveryID').attr('type');

        if (inpType === 'password') {
            $('#recoveryID').attr('type', 'text');
            $(this).html('<i class="fa fa-eye-slash"></i>');
        } else if (inpType === 'text') {
            $('#recoveryID').attr('type', 'password');
            $(this).html('<i class="fa fa-eye"></i>');
        }
    });

    $('.fpw-btn[name=fpwm-rid]').click(function() {
        $(this).html('<i class="fa fa-sync fa-spin"></i>');

        var
            uName = $('.fpw-box form input#uName').val(),
            uRID = $('.fpw-box form input#RID').val(),
            base_url = baseURL();

        $.ajax({
                url: base_url + '/verifyfpw',
                type: 'POST',
                dataType: 'json',
                data: {
                    username: uName,
                    recovery_id: uRID,
                    method: 'recovery_id',
                    vt: $.cookie('vt')
                }
            })
            .done(function() {
                console.log("success");
            })
            .fail(function() {
                console.log("error");
                $('.fpw-btn[name=fpwm-rid]').html('Proses <i class="fa fa-sync"></i>');
            })
            .always(function(result) {
                console.log("complete");
                switch (result.status) {
                    case 'success':
                        window.location.replace(result.message);
                        break;
                    case 'failed':
                        window.location.replace(result.message);
                        break;
                    case 'error':
                        $('.fpw-btn[name=fpwm-rid]').html('Proses <i class="fa fa-sync"></i>');
                        $(".msg-box.hide").css("display", "block");
                        $(".msg-box.hide").removeClass('success');
                        $(".msg-box.hide").removeClass('warning');
                        $(".msg-box.hide").addClass('error');
                        console.log(result.message.search('~'));
                        if (result.message.search('~') !== -1) {
                            console.log('Found');
                            var
                                msglist = result.message.split('~'),
                                i = 0;
                            console.log(msglist);
                            for (; i < msglist.length - 1; i++) {
                                $(".msg-box.hide").append('<p class="msg-txt">' + msglist[i] + '</p>');
                                console.log(i);
                            }
                        } else {
                            $(".msg-box.hide").append('<p class="msg-txt">' + result.message + '</p>');
                        }
                        break;
                    default:
                        $('.fpw-btn[name=fpwm-rid]').html('Proses <i class="fa fa-sync"></i>');
                        if (result.message.search('|') !== -1) {
                            var
                                msglist = result.message.split('|'),
                                i = 0;
                            console.log(msglist);
                            for (; i < msglist.length - 1; i++) {
                                $(".msg-box.hide").append('<p class="msg-txt">' + msglist[i] + '</p>');
                                console.log(i);
                            }
                        } else {
                            $(".msg-box.hide").append('<p class="msg-txt">' + result.message + '</p>');
                        }
                }
            });

    });

    $('.fpw-btn[name=respass-rid]').click(function() {
        $(this).html('<i class="fa fa-sync fa-spin"></i>');

        var
            uPass = $('.fpw-box form input#uPass').val(),
            uPassConfirm = $('.fpw-box form input#uPassConfirm').val(),
            base_url = baseURL();

        $.ajax({
                url: base_url + '/resetpw',
                type: 'POST',
                dataType: 'json',
                data: {
                    new_password: uPass,
                    new_password_confirm: uPassConfirm,
                    t: $.cookie('t')
                }
            })
            .done(function() {
                console.log("success");
            })
            .fail(function() {
                console.log("error");
                $('.fpw-btn[name=respass-rid]').html('Proses <i class="fa fa-sync"></i>');
            })
            .always(function(result) {
                console.log("complete");
                switch (result.status) {
                    case 'success':
                        window.location.replace(result.message);
                        break;
                    case 'failed':
                        window.location.replace(result.message);
                        break;
                    case 'error':
                        $('.fpw-btn[name=respass-rid]').html('Proses <i class="fa fa-sync"></i>');
                        $(".msg-box.hide").css("display", "block");
                        $(".msg-box.hide").removeClass('success');
                        $(".msg-box.hide").removeClass('warning');
                        $(".msg-box.hide").addClass('error');
                        console.log(result.message.search('~'));
                        if (result.message.search('~') !== -1) {
                            console.log('Found');
                            var
                                msglist = result.message.split('~'),
                                i = 0;
                            console.log(msglist);
                            for (; i < msglist.length - 1; i++) {
                                $(".msg-box.hide").append('<p class="msg-txt">' + msglist[i] + '</p>');
                                console.log(i);
                            }
                        } else {
                            $(".msg-box.hide").append('<p class="msg-txt">' + result.message + '</p>');
                        }
                        break;
                    default:
                        $('.fpw-btn[name=respass-rid]').html('Proses <i class="fa fa-sync"></i>');
                        if (result.message.search('|') !== -1) {
                            var
                                msglist = result.message.split('|'),
                                i = 0;
                            console.log(msglist);
                            for (; i < msglist.length - 1; i++) {
                                $(".msg-box.hide").append('<p class="msg-txt">' + msglist[i] + '</p>');
                                console.log(i);
                            }
                        } else {
                            $(".msg-box.hide").append('<p class="msg-txt">' + result.message + '</p>');
                        }
                }
            });

    });

    $('.fpw-btn[name=fpwm-email]').click(function() {
        $(this).html('<i class="fa fa-sync fa-spin"></i>');

        var
            uName = $('.fpw-box form input#uName').val(),
            uEmail = $('.fpw-box form input#uEmail').val(),
            base_url = baseURL();

        $.ajax({
                url: base_url + '/verifyfpw',
                type: 'POST',
                dataType: 'json',
                data: {
                    username: uName,
                    email: uEmail,
                    method: 'email',
                    vt: $.cookie('vt')
                }
            })
            .done(function() {
                console.log("success");
            })
            .fail(function() {
                console.log("error");
                $('.fpw-btn[name=fpwm-email]').html('Kirim <i class="fa fa-paper-plane"></i>');
            })
            .always(function(result) {
                console.log("complete");
                switch (result.status) {
                    case 'success':
                        window.location.replace(result.message);
                        break;
                    case 'failed':
                        window.location.replace(result.message);
                        break;
                    case 'error':
                        $('.fpw-btn[name=fpwm-email]').html('Kirim <i class="fa fa-paper-plane"></i>');
                        $(".msg-box.hide").css("display", "block");
                        $(".msg-box.hide").removeClass('success');
                        $(".msg-box.hide").removeClass('warning');
                        $(".msg-box.hide").addClass('error');
                        console.log(result.message.search('~'));
                        if (result.message.search('~') !== -1) {
                            console.log('Found');
                            var
                                msglist = result.message.split('~'),
                                i = 0;
                            console.log(msglist);
                            for (; i < msglist.length - 1; i++) {
                                $(".msg-box.hide").append('<p class="msg-txt">' + msglist[i] + '</p>');
                                console.log(i);
                            }
                        } else {
                            $(".msg-box.hide").append('<p class="msg-txt">' + result.message + '</p>');
                        }
                        break;
                    default:
                        $('.fpw-btn[name=fpwm-email]').html('Kirim <i class="fa fa-paper-plane"></i>');
                        if (result.message.search('|') !== -1) {
                            var
                                msglist = result.message.split('|'),
                                i = 0;
                            console.log(msglist);
                            for (; i < msglist.length - 1; i++) {
                                $(".msg-box.hide").append('<p class="msg-txt">' + msglist[i] + '</p>');
                                console.log(i);
                            }
                        } else {
                            $(".msg-box.hide").append('<p class="msg-txt">' + result.message + '</p>');
                        }
                }
            });

    });

    // Create New Account Submit Button
    $('.cna-btn').click(function() {
        $(this).html('<i class="fa fa-sync fa-spin"></i>');
        $(this).css('background-color', '#409da3');
        var
            cnaForm = new ASMPFormHandler($('.cna-box form')),
            formInput = cnaForm.getFormInputBy('pureObject');
        $.ajax({
                url: baseURL() + '/regnuser',
                type: 'POST',
                dataType: 'json',
                data: {
                    username: formInput['username'],
                    true_name: formInput['true_name'],
                    password: formInput['password'],
                    passconfirm: formInput['passconfirm'],
                    email: formInput['email'],
                    field_section: formInput['field_section'],
                    recovery_id: formInput['recovery_id'],
                    t: $.cookie('t')
                }

            })
            .done(function() {
                console.log("success");
            })
            .fail(function() {
                console.log("error");
                $('.cna-btn').html('Buat <i class="fa fa-user-plus"></i>');
            })
            .always(function(result) {
                console.log("complete");
                switch (result.status) {
                    case 'success':
                        window.location.replace(result.message);
                        break;
                    case 'failed':
                        window.location.replace(result.message);
                        break;
                    case 'error':
                        $('.cna-btn').html('Buat <i class="fa fa-user-plus"></i>');
                        $(".msg-box.hide").css("display", "block");
                        $(".msg-box.hide").removeClass('success');
                        $(".msg-box.hide").removeClass('warning');
                        $(".msg-box.hide").addClass('error');
                        console.log(result.message.search('~'));
                        if (result.message.search('~') !== -1) {
                            console.log('Found');
                            var
                                msglist = result.message.split('~'),
                                i = 0;
                            console.log(msglist);
                            for (; i < msglist.length - 1; i++) {
                                $(".msg-box.hide").append('<p class="msg-txt">' + msglist[i] + '</p>');
                                console.log(i);
                            }
                        } else {
                            $(".msg-box.hide").append('<p class="msg-txt">' + result.message + '</p>');
                        }
                        break;
                    default:
                        $('.cna-btn').html('Buat <i class="fa fa-user-plus"></i>');
                        if (result.message.search('~') !== -1) {
                            var
                                msglist = result.message.split('~'),
                                i = 0;
                            console.log(msglist);
                            for (; i < msglist.length - 1; i++) {
                                $(".msg-box.hide").append('<p class="msg-txt">' + msglist[i] + '</p>');
                                console.log(i);
                            }
                        } else {
                            $(".msg-box.hide").append('<p class="msg-txt">' + result.message + '</p>');
                        }
                }
            });
    });
});