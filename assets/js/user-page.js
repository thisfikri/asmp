$(document).ready(function() {
    var pagination = false;

    function xss_clean(str) {
        var filtered_str = xssFilters.inHTMLData(str);
        filtered_str = xssFilters.inSingleQuotedAttr(filtered_str);
        filtered_str = xssFilters.inDoubleQuotedAttr(filtered_str);
        //filtered_str = xssFilters.inUnQuotedAttr(filtered_str);
        return filtered_str;
    }

    // welcome msg


    //Navbar Menu Icon
    $('.navbar-icon').click(function() {
        let
            iconType = $(this).attr('class');
        if (iconType.search('arrow') !== -1) {
            $(this).removeClass('arrow');
            $(this).css('background-color', '#48c8d1');
            $('.casual-theme.activity-page.nav.sidebar').css('width', 0);
            $('section.casual-theme.activity-page.main-contents').css('margin-left', 0);
            $('footer.activity-page.footer').css('width', 0);
        } else {
            $(this).addClass('arrow');
            $(this).css('background-color', '#3da7af');
            $('.casual-theme.activity-page.nav.sidebar').css('width', '17%');
            $('section.casual-theme.activity-page.main-contents').css('margin-left', '232.22px');
            $('footer.activity-page.footer').css('width', '17%');
        }
    });

    // Options Button
    $('.button.options').click(function() {
        let
            dropdownS = $('.casual-theme.activity-page.options-dropdown').css('height');
        //console.log(dropdownS);
        if (dropdownS == '0px') {
            $(this).css('background-color', '#3da7af');
            $('.casual-theme.activity-page.options-dropdown').css('height', '62px');
        } else {
            $(this).css('background-color', '#48c8d1');
            $('.casual-theme.activity-page.options-dropdown').css('height', '0');
        }
    });

    // Notification Button
    $('.button.notification').click(function() {
        let
            displayStat = $('.casual-theme.activity-page.notification-box.outerdiv').css('display');
        if (displayStat == 'none') {
            $(this).html('<i class="fa fa-times fa-fw"></i>');
            $('.casual-theme.activity-page.notification-box.outerdiv').css('display', 'block');
            $('.casual-theme.activity-page.notification-box.outerdiv').css('width', '237.17px');
            $('.casual-theme.activity-page.notification-box.outerdiv').css('height', '144px');
        } else if (displayStat == 'block') {
            $(this).html('<i class="fa fa-bell fa-fw"></i>');
            $('.casual-theme.activity-page.notification-box.outerdiv').css('display', 'none');
            $('.casual-theme.activity-page.notification-box.outerdiv').css('width', '0');
            $('.casual-theme.activity-page.notification-box.outerdiv').css('height', '0');
        }
    });

    // Profile Edit Button
    $('.side-profile button.edit-profile').click(function() {
        let
            buttonHTML = $(this).html();
        //console.log(buttonHTML);
        if (buttonHTML == '<i class="fa fa-user-edit"></i> Edit') {
            $(this).html('<i class="fa fa-arrow-left"></i> Back');
            $('.side-profile .profile-editor-box-container').removeClass('hide');
            // $('.side-profile').addClass('edit');
            // $('.side-profile.edit .profile-img').addClass('edit');
            // $('.side-profile.edit .button.choose-btn').removeClass('hide');
            $('.side-profile.edit .photos-box').removeClass('hide');
            // $('.side-profile.edit .profile-info').addClass('edit');
            // $('.side-profile.edit .profile-info ul').addClass('hide');
            // $('.side-profile.edit .profile-info.edit .update-type').removeClass('hide');
        } else if (buttonHTML == '<i class="fa fa-arrow-left"></i> Back') {
            $(this).html('<i class="fa fa-user-edit"></i> Edit');
            $('.side-profile .profile-editor-box-container').addClass('hide');
            // $('.side-profile').removeClass('edit');
            // $('.side-profile .profile-img').removeClass('edit');
            // $('.side-profile .button.choose-btn').addClass('hide');
            $('.side-profile .photos-box').addClass('hide');
            // $('.side-profile .profile-info').removeClass('edit');
            // $('.side-profile .profile-info ul').removeClass('hide');
            // $('.side-profile .profile-info .update-type').addClass('hide');
        }
    });

    $('.logout-btn').click(function() {
        $.ajax({
                url: baseURL() + '/logout',
                type: 'POST',
                dataType: 'json',
                data: {
                    token: $(this).data('token')
                },
            })
            .done(function() {
                //console.log("success");
            })
            .fail(function() {
                //console.log("error");
            })
            .always(function(result) {
                //console.log("complete");
                switch (result.status) {
                    case 'success':
                        window.location.replace(result.message);
                        break;
                    case 'failed':
                        $(".msg-box.hide").css("display", "block");
                        $(".msg-box.hide").removeClass('success');
                        $(".msg-box.hide").removeClass('warning');
                        $(".msg-box.hide").addClass('error');
                        //console.log(result.message);
                        break;
                    default:
                        break;
                }
            });
    });

    // update profile
    $('.profile-editor-box form.update-type.01 button[name=update-type-01-submit]').click(function() {
        let
            form = $('.profile-editor-box form.update-type.01'),
            fData = {},
            i = 0;
        for (; i < form[0].length; i++) {
            fData[form[0][i].name] = form[0][i].value;
        }

        console.log(fData);
        $.ajax({
                url: baseURL() + '/update_profile',
                type: 'POST',
                dataType: 'json',
                data: {
                    requested_data: JSON.stringify({
                        token: $.cookie('t'),
                        change_type: 'name',
                        fdata: fData
                    })
                },
            })
            .done(function() {
                //console.log("success");
            })
            .fail(function() {
                //console.log("error");
            })
            .always(function(result) {
                console.log(result);
                if (result.status == 'success') {
                    $('.profile-editor-box form.update-type.01 input[name=password]').val('');
                    if (result.data['change_count'] == 1) {
                        // console.log($('.profile-info ul li').eq(0));
                        $('.profile-editor-box form.update-type.01 input[name=' + result.data['change_name'] + ']').val(result.data['change_value']);
                        $('.profile-info ul li').eq(0).text('Nama: ' + result.data['change_value']);
                    } else if (result.data['change_count'] == 2) {
                        $('.profile-editor-box form.update-type.01 input[name=' + result.data['change_name1'] + ']').val(result.data['change_value1']);
                        $('.profile-editor-box form.update-type.01 input[name=' + result.data['change_name2'] + ']').val(result.data['change_value2']);
                        $('.profile-info ul li').eq(0).text('Nama: ' + result.data['change_value1']);
                    }
                    // window.alert(result.message);
                    $('.profile-action-message').html('<p><i class="fa fa-check-circle"></i> ' + result.message + '</p>');
                    $('.profile-action-message').removeClass('error');
                    $('.profile-action-message').removeClass('failed');
                    $('.profile-action-message').removeClass('warning');
                    $('.profile-action-message').addClass('success');
                    $('.profile-action-message').removeClass('hide');
                    $('.profile-action-message').fadeOut({
                        duration: 3000,
                        complete: () => {
                            $('.profile-action-message').addClass('hide');
                            $('.profile-action-message').removeAttr('style');
                        }
                    });
                } else if (result.status == 'failed') {
                    // window.alert(result.message);
                    $('.profile-action-message').html('<p><i class="fa fa-exclamation-circle"></i> ' + result.message + '</p>');
                    $('.profile-action-message').removeClass('success');
                    $('.profile-action-message').removeClass('warning');
                    $('.profile-action-message').removeClass('error');
                    $('.profile-action-message').addClass('failed');
                    $('.profile-action-message').removeClass('hide');
                    $('.profile-action-message').fadeOut({
                        duration: 3000,
                        complete: () => {
                            $('.profile-action-message').addClass('hide');
                            $('.profile-action-message').removeAttr('style');
                        }
                    });
                } else if (result.status == 'error') {
                    // window.alert(result.message);
                    $('.profile-action-message').html('<p><i class="fa fa-exclamation-circle"></i> ' + result.message + '</p>');
                    $('.profile-action-message').removeClass('success');
                    $('.profile-action-message').removeClass('warning');
                    $('.profile-action-message').removeClass('failed');
                    $('.profile-action-message').addClass('error');
                    $('.profile-action-message').removeClass('hide');
                    $('.profile-action-message').fadeOut({
                        duration: 3000,
                        complete: () => {
                            $('.profile-action-message').addClass('hide');
                            $('.profile-action-message').removeAttr('style');
                        }
                    });
                } else if (result.status == 'warning') {
                    // window.alert(result.message);
                    $('.profile-action-message').html('<p><i class="fa fa-exclamation-triangle"></i> ' + result.message + '</p>');
                    $('.profile-action-message').removeClass('success');
                    $('.profile-action-message').removeClass('failed');
                    $('.profile-action-message').removeClass('error');
                    $('.profile-action-message').addClass('warning');
                    $('.profile-action-message').removeClass('hide');
                    $('.profile-action-message').fadeOut({
                        duration: 3000,
                        complete: () => {
                            $('.profile-action-message').addClass('hide');
                            $('.profile-action-message').removeAttr('style');
                        }
                    });
                }
            });
    });

    $('.profile-editor-box form.update-type.02 button[name=update-type-02-submit]').click(function() {
        let
            form = $('.profile-editor-box form.update-type.02'),
            fData = {},
            i = 0;
        for (; i < form[0].length; i++) {
            fData[form[0][i].name] = form[0][i].value;
        }

        console.log(fData);
        $.ajax({
                url: baseURL() + '/update_profile',
                type: 'POST',
                dataType: 'json',
                data: {
                    requested_data: JSON.stringify({
                        token: $.cookie('t'),
                        change_type: 'password',
                        fdata: fData
                    })
                },
            })
            .done(function() {
                //console.log("success");
            })
            .fail(function() {
                //console.log("error");
            })
            .always(function(result) {
                console.log(result);
                if (result.status == 'success') {
                    $('.profile-editor-box form.update-type.02 input[name=old_password]').val('');
                    $('.profile-editor-box form.update-type.02 input[name=new_password]').val('');
                    $('.profile-editor-box form.update-type.02 input[name=new_password_confirm]').val('');
                    // window.alert(result.message);
                    $('.profile-action-message').html('<p><i class="fa fa-check-circle"></i> ' + result.message + '</p>');
                    $('.profile-action-message').removeClass('error');
                    $('.profile-action-message').removeClass('failed');
                    $('.profile-action-message').removeClass('warning');
                    $('.profile-action-message').addClass('success');
                    $('.profile-action-message').removeClass('hide');
                    $('.profile-action-message').fadeOut({
                        duration: 3000,
                        complete: () => {
                            $('.profile-action-message').addClass('hide');
                            $('.profile-action-message').removeAttr('style');
                        }
                    });
                } else if (result.status == 'failed') {
                    // window.alert(result.message);
                    $('.profile-action-message').html('<p><i class="fa fa-exclamation-circle"></i> ' + result.message + '</p>');
                    $('.profile-action-message').removeClass('success');
                    $('.profile-action-message').removeClass('warning');
                    $('.profile-action-message').removeClass('error');
                    $('.profile-action-message').addClass('failed');
                    $('.profile-action-message').removeClass('hide');
                    $('.profile-action-message').fadeOut({
                        duration: 3000,
                        complete: () => {
                            $('.profile-action-message').addClass('hide');
                            $('.profile-action-message').removeAttr('style');
                        }
                    });
                } else if (result.status == 'error') {
                    // window.alert(result.message);
                    $('.profile-action-message').html('<p><i class="fa fa-exclamation-circle"></i> ' + result.message + '</p>');
                    $('.profile-action-message').removeClass('success');
                    $('.profile-action-message').removeClass('warning');
                    $('.profile-action-message').removeClass('failed');
                    $('.profile-action-message').addClass('error');
                    $('.profile-action-message').removeClass('hide');
                    $('.profile-action-message').fadeOut({
                        duration: 3000,
                        complete: () => {
                            $('.profile-action-message').addClass('hide');
                            $('.profile-action-message').removeAttr('style');
                        }
                    });
                } else if (result.status == 'warning') {
                    // window.alert(result.message);
                    $('.profile-action-message').html('<p><i class="fa fa-exclamation-tirangle"></i> ' + result.message + '</p>');
                    $('.profile-action-message').removeClass('success');
                    $('.profile-action-message').removeClass('failed');
                    $('.profile-action-message').removeClass('error');
                    $('.profile-action-message').addClass('warning');
                    $('.profile-action-message').removeClass('hide');
                    $('.profile-action-message').fadeOut({
                        duration: 3000,
                        complete: () => {
                            $('.profile-action-message').addClass('hide');
                            $('.profile-action-message').removeAttr('style');
                        }
                    });
                }
            });
    });

    $('.profile-editor-box form.update-type.03 button[name=update-type-03-submit]').click(function() {
        let
            form = $('.profile-editor-box form.update-type.03'),
            fData = {},
            i = 0;
        for (; i < form[0].length; i++) {
            fData[form[0][i].name] = form[0][i].value;
        }

        // console.log(fData);
        $.ajax({
                url: baseURL() + '/update_profile',
                type: 'POST',
                dataType: 'json',
                data: {
                    requested_data: JSON.stringify({
                        token: $.cookie('t'),
                        change_type: 'email',
                        fdata: fData
                    })
                },
            })
            .done(function() {
                //console.log("success");
            })
            .fail(function() {
                //console.log("error");
            })
            .always(function(result) {
                console.log(result);
                if (result.status == 'success') {
                    $('.profile-editor-box form.update-type.03 input[name=password]').val('');
                    // window.alert(result.message);
                    $('.profile-action-message').html('<p><i class="fa fa-check-circle"></i> ' + result.message + '</p>');
                    $('.profile-action-message').removeClass('error');
                    $('.profile-action-message').removeClass('failed');
                    $('.profile-action-message').removeClass('warning');
                    $('.profile-action-message').addClass('success');
                    $('.profile-action-message').removeClass('hide');
                    $('.profile-action-message').fadeOut({
                        duration: 3000,
                        complete: () => {
                            $('.profile-action-message').addClass('hide');
                            $('.profile-action-message').removeAttr('style');
                        }
                    });
                } else if (result.status == 'failed') {
                    // window.alert(result.message);
                    $('.profile-action-message').html('<p><i class="fa fa-exclamation-circle"></i> ' + result.message + '</p>');
                    $('.profile-action-message').removeClass('success');
                    $('.profile-action-message').removeClass('warning');
                    $('.profile-action-message').removeClass('error');
                    $('.profile-action-message').addClass('failed');
                    $('.profile-action-message').removeClass('hide');
                    $('.profile-action-message').fadeOut({
                        duration: 3000,
                        complete: () => {
                            $('.profile-action-message').addClass('hide');
                            $('.profile-action-message').removeAttr('style');
                        }
                    });
                } else if (result.status == 'error') {
                    // window.alert(result.message);
                    $('.profile-action-message').html('<p><i class="fa fa-exclamation-circle"></i> ' + result.message + '</p>');
                    $('.profile-action-message').removeClass('success');
                    $('.profile-action-message').removeClass('warning');
                    $('.profile-action-message').removeClass('failed');
                    $('.profile-action-message').addClass('error');
                    $('.profile-action-message').removeClass('hide');
                    $('.profile-action-message').fadeOut({
                        duration: 3000,
                        complete: () => {
                            $('.profile-action-message').addClass('hide');
                            $('.profile-action-message').removeAttr('style');
                        }
                    });
                } else if (result.status == 'warning') {
                    // window.alert(result.message);
                    $('.profile-action-message').html('<p><i class="fa fa-exclamation-triangle"></i> ' + result.message + '</p>');
                    $('.profile-action-message').removeClass('success');
                    $('.profile-action-message').removeClass('failed');
                    $('.profile-action-message').removeClass('error');
                    $('.profile-action-message').addClass('warning');
                    $('.profile-action-message').removeClass('hide');
                    $('.profile-action-message').fadeOut({
                        duration: 3000,
                        complete: () => {
                            $('.profile-action-message').addClass('hide');
                            $('.profile-action-message').removeAttr('style');
                        }
                    });
                }
            });
    });

    $('#checkAll').click(function() {
        var ma = new MultipleAction(baseURL() + '/remove_item');
        if ($(this).is(':checked') && $('.item td .checkbox').not('.item.hide td .checkbox').length !== 0) {
            var mailIds = $('.multiple-action').data('mail-ids'),
                i = 1,
                item;
            //console.log($('.item').attr('class'));
            checkedItemCount = $('.checkbox').not('.item.hide td .checkbox, .checkbox[disabled=disabled]').length;
            for (; i < checkedItemCount + 1; i++) {
                if (!$('.item.id' + i + ' td .checkbox').is(':disabled')) {
                    item = $('.item').eq(i - 1).attr('class');
                    item = item.split('id');
                    item = item[item.length - 1];
                    mailIds += item + ',';
                } else {
                    checkedItemCount += 1;
                }
            }
            $('.multiple-action').data('mail-ids', mailIds);
            //console.log('Checked Count: ' + checkedItemCount, 'Mail Ids: ' + $('.multiple-action').data('mail-ids'));

            if (checkedItemCount !== 0) {
                $('.checkbox').not('.item.hide td .checkbox, .checkbox[disabled=disabled]').prop('checked', true);
                $('.checkmark').not('.item.hide td .checkmark, .checkmark[disabled=disabled]').css('display', 'inline-block');
            }

            if (checkedItemCount > 1) {
                $('.multiple-action').removeClass('hide');
                $('.multiple-action').data('mail-ids', mailIds);
                ma.defineAction('multipleAction1');
                ma.defineAction('multipleAction2');
                ma.defineAction('multipleAction3');
                ma.defineAction('multipleAction4');
                ma.defineAction('multipleAction5');
                ma.defineAction('multipleAction6');
                ma.multipleAction1('remove', 'user_management', true);
                ma.multipleAction2('trash', 'incoming_mail', true);
                ma.multipleAction3('trash', 'outgoing_mail', true);
                ma.multipleAction4('remove', 'trash_can', true);
                ma.multipleAction4('recovery', 'trash_can', true);
                ma.multipleAction5('remove', 'field_section', true);
            } else if (checkedItemCount < 2) {
                $('.multiple-action').addClass('hide');
                ma.deleteAction('multipleAction1');
                ma.deleteAction('multipleAction2');
                ma.deleteAction('multipleAction3');
                ma.deleteAction('multipleAction4');
                ma.deleteAction('multipleAction5');
            }
        }

        $('.checkmark.all').click(function() {
            if (!$('#checkAll').is(':disabled')) {
                checkedItemCount = 0;
                $('.multiple-action').data('mail-ids', '');
                //console.log('Checked Count: ' + checkedItemCount, 'Mail Ids: ' + $('.multiple-action').data('mail-ids'));
                $('#checkAll').prop('checked', false);
                $('.checkbox').not('.item.hide td .checkbox, .checkbox[disabled=disabled]').prop('checked', false);
                $('.checkmark').not('.item.hide td .checkmark, .checkmark[disabled=disabled]').css('display', 'none');
                if (checkedItemCount > 1) {
                    $('.multiple-action').removeClass('hide');
                } else if (checkedItemCount < 2) {
                    $('.multiple-action').addClass('hide');
                }
            }
        });
    });

    $('.settings-checkbox input[type=checkbox]').click(function() {
        //console.log($(this).is(':checked'));
        if ($(this).is(':checked') == true) {
            $('.settings-checkbox .settings-checkmark').css('display', 'block');
        }
    });

    $('.settings-checkbox .settings-checkmark').click(function() {
        if ($('.settings-checkbox input[type=checkbox]').is(':checked') == true) {
            $('.settings-checkbox .settings-checkmark').css('display', 'none');
            $('.settings-checkbox input[type=checkbox]').prop('checked', false);
            //console.log($(this).is(':checked'));
        }
    });

    $('#settingsForm .save-settings-btn').click(function() {
        $(this).html('<i class="fa fa-sync fa-spin"></i>');
        var
            settingsData = {
                // company section
                companyName: $('#settingsForm .form-section.company #companyName').val(),
                companyAddress: $('#settingsForm .form-section.company #companyAddress').val(),
                companyContact: $('#settingsForm .form-section.company #companyContact').val(),
                // page section
                mulRemAct: $('#settingsForm .form-section.page #mulRemAct').val(),
                mulRecAct: $('#settingsForm .form-section.page #mulRecAct').val(),
                pagingItem: $('#settingsForm .form-section.page span.settings-checkbox #pagingItem').is(':checked'),
                pagingLimit: $('#settingsForm .form-section.page #pagingLimit').val(),
                cmd: 'save_user_settings',
                t: $.cookie('t')
            },
            currObj = $(this);
        if (settingsData.pagingItem == true) {
            settingsData.pagingItem = 1;
        } else {
            settingsData.pagingItem = 0;
        }
        $.ajax({
            type: "POST",
            url: baseURL() + '/settings',
            data: settingsData,
            dataType: "json",
        }).done(function() {

        }).fail(function() {

        }).always(function(result) {
            currObj.html('<i class="fa fa-save"></i> Simpan');
            if (result.status == 'success') {
                $('.action-msg-notification').html('<p>' + result.message + '</p>');
                $('.action-msg-notification').removeClass('error');
                $('.action-msg-notification').removeClass('failed');
                $('.action-msg-notification').addClass('success');
                $('.action-msg-notification').removeClass('hide');
                $('.action-msg-notification').fadeOut({
                    duration: 3000,
                    complete: () => {
                        $('.action-msg-notification').addClass('hide');
                        $('.action-msg-notification').removeAttr('style');
                    }
                });
            } else if (result.status == 'failed') {
                $('.action-msg-notification').html('<p>' + result.message + '</p>');
                $('.action-msg-notification').removeClass('success');
                $('.action-msg-notification').removeClass('error');
                $('.action-msg-notification').addClass('failed');
                $('.action-msg-notification').removeClass('hide');
                $('.action-msg-notification').fadeOut({
                    duration: 3000,
                    complete: () => {
                        $('.action-msg-notification').addClass('hide');
                        $('.action-msg-notification').removeAttr('style');
                    }
                });
            } else if (result.status == 'error') {
                $('.action-msg-notification').html('<p>' + result.message + '</p>');
                $('.action-msg-notification').removeClass('success');
                $('.action-msg-notification').removeClass('failed');
                $('.action-msg-notification').addClass('error');
                $('.action-msg-notification').removeClass('hide');
                $('.action-msg-notification').fadeOut({
                    duration: 3000,
                    complete: () => {
                        $('.action-msg-notification').addClass('hide');
                        $('.action-msg-notification').removeAttr('style');
                    }
                });
            }
        });
    });

    if (window.location.href == baseURL() + '/surat-keluar') {
        $('.table-container#outgoingMail .item-list').ready(function() {
            $.ajax({
                type: "POST",
                url: baseURL() + '/outgoing_mail/load',
                dataType: "json",
                data: {
                    t: $.cookie('t')
                }
            }).done(function() {

            }).fail(function() {

            }).always(function(result) {
                if ($.isArray(result.data)) {
                    var atrgr = new ActionTrigger(),
                        itemData;
                    //console.log(result.data.length);
                    for (var i = 1; i < result.data.length + 1; i++) {
                        console.log(result.data[i - 1].subject);
                        itemData = itemData = JSON.stringify(result.data[i - 1]);
                        $('.table-container#outgoingMail .item-list tbody').append('<tr class="item id' + i + '"><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
                        $('.table-container#outgoingMail .item-list tbody tr.item.id' + i).attr('data-itemdata', itemData);
                        $('.table-container#outgoingMail .item-list tbody tr.item.id' + i + ' td').eq(0).append('<input type="checkbox" class="checkbox item' + i + '"><span class="checkmark item' + i + '"><i class="fa fa-check"></i></span>');
                        $('.table-container#outgoingMail .item-list tbody tr.item.id' + i + ' td').eq(1).text(result.data[i - 1].mail_number);
                        $('.table-container#outgoingMail .item-list tbody tr.item.id' + i + ' td').eq(2).text(result.data[i - 1].subject);
                        $('.table-container#outgoingMail .item-list tbody tr.item.id' + i + ' td').eq(3).text(result.data[i - 1].sender);
                        $('.table-container#outgoingMail .item-list tbody tr.item.id' + i + ' td').eq(4).html(result.data[i - 1].status);
                        $('.table-container#outgoingMail .item-list tbody tr.item.id' + i + ' td').eq(5).text(result.data[i - 1].date);
                        // $('.table-container#outgoingMail .item-list tbody tr.item.id' + i + ' td').eq(6).append(
                        //     '<button class="button action-btn edit" id="item' + i + '"><i class="fa fa-edit"></i></button>'
                        // );
                        $('.table-container#outgoingMail .item-list tbody tr.item.id' + i + ' td').eq(6).append(
                            '<button class="button action-btn view" id="item' + i + '"><i class="fa fa-eye"></i></button>'
                        );
                        $('.table-container#outgoingMail .item-list tbody tr.item.id' + i + ' td').eq(6).append(
                            '<button class="button action-btn trash" id="item' + i + '"><i class="fa fa-trash"></i></button>'
                        );
                        atrgr.defineTrigger('checkboxAction' + i, 'checkbox_action');
                        atrgr.defineTrigger('viewMailOM' + i, 'view_mail');
                        atrgr.defineTrigger('throwMailToTrashOM' + i, 'throw_mail_tt');
                        atrgr['checkboxAction' + i](i);
                        atrgr['throwMailToTrashOM' + i](i, 'om', result.data[i - 1]);
                        atrgr['viewMailOM' + i](i);
                    }

                    if (Number.parseInt(result.paging.status) == 1) {
                        console.log('Entering...');
                        var pgnt = new Pagination(Number.parseInt(result.paging.limit), '.item', 'page-link', '.pagination');
                        pagination = pgnt.paginate();
                    }

                }
            });
        });

        $('.button.trash-can-btn').click(function() {
            window.location.replace(baseURL() + '/tong-sampah');
        });
    } else if (window.location.href == baseURL() + '/surat-masuk') {
        console.log('Surat Masuk');
        $('.table-container#incomingMail .item-list').ready(function() {
            console.log($.cookie('t'));
            $.ajax({
                type: "POST",
                url: baseURL() + '/incoming_mail/load',
                dataType: "json",
                data: {
                    t: $.cookie('t')
                }
            }).done(function() {

            }).fail(function() {

            }).always(function(result) {
                if ($.isArray(result.data)) {
                    var atrgr = new ActionTrigger(),
                        itemData;
                    //console.log(result.data.length);
                    for (var i = 1; i < result.data.length + 1; i++) {
                        console.log(result.data[i - 1].subject);
                        itemData = itemData = JSON.stringify(result.data[i - 1]);
                        $('.table-container#incomingMail .item-list tbody').append('<tr class="item id' + i + '"><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
                        $('.table-container#incomingMail .item-list tbody tr.item.id' + i).attr('data-itemdata', itemData);
                        $('.table-container#incomingMail .item-list tbody tr.item.id' + i + ' td').eq(0).append('<input type="checkbox" class="checkbox item' + i + '"><span class="checkmark item' + i + '"><i class="fa fa-check"></i></span>');
                        $('.table-container#incomingMail .item-list tbody tr.item.id' + i + ' td').eq(1).text(result.data[i - 1].mail_number);
                        $('.table-container#incomingMail .item-list tbody tr.item.id' + i + ' td').eq(2).text(result.data[i - 1].subject);
                        $('.table-container#incomingMail .item-list tbody tr.item.id' + i + ' td').eq(3).text(result.data[i - 1].sender);
                        $('.table-container#incomingMail .item-list tbody tr.item.id' + i + ' td').eq(4).html(result.data[i - 1].status);
                        $('.table-container#incomingMail .item-list tbody tr.item.id' + i + ' td').eq(5).text(result.data[i - 1].date);
                        // $('.table-container#outgoingMail .item-list tbody tr.item.id' + i + ' td').eq(6).append(
                        //     '<button class="button action-btn edit" id="item' + i + '"><i class="fa fa-edit"></i></button>'
                        // );
                        $('.table-container#incomingMail .item-list tbody tr.item.id' + i + ' td').eq(6).append(
                            '<button class="button action-btn view" id="item' + i + '"><i class="fa fa-eye"></i></button>'
                        );
                        $('.table-container#incomingMail .item-list tbody tr.item.id' + i + ' td').eq(6).append(
                            '<button class="button action-btn trash" id="item' + i + '"><i class="fa fa-trash"></i></button>'
                        );
                        atrgr.defineTrigger('checkboxAction' + i, 'checkbox_action');
                        atrgr.defineTrigger('viewMailOM' + i, 'view_mail');
                        atrgr.defineTrigger('throwMailToTrashOM' + i, 'throw_mail_tt');
                        atrgr['checkboxAction' + i](i);
                        atrgr['throwMailToTrashOM' + i](i, 'im', result.data[i - 1]);
                        atrgr['viewMailOM' + i](i);
                    }

                    if (Number.parseInt(result.paging.status) == 1) {
                        console.log('Entering...');
                        var pgnt = new Pagination(Number.parseInt(result.paging.limit), '.item', 'page-link', '.pagination');
                        pagination = pgnt.paginate();
                    }

                }
            });
        });

        $('.button.trash-can-btn').click(function() {
            window.location.replace(baseURL() + '/tong-sampah');
        });
    } else if (window.location.href == baseURL() + '/tong-sampah') {
        $('.table-container#trashCan .item-list').ready(function() {
            $.ajax({
                type: "POST",
                url: baseURL() + '/trash_can/load',
                dataType: "json",
                data: {
                    t: $.cookie('t')
                }
            }).done(function() {

            }).fail(function() {

            }).always(function(result) {
                if ($.isArray(result.data)) {
                    var atrgr = new ActionTrigger(),
                        itemData;
                    //console.log(result.data.length);
                    for (var i = 1; i < result.data.length + 1; i++) {
                        console.log(result.data[i - 1].subject);
                        itemData = itemData = JSON.stringify(result.data[i - 1]);
                        $('.table-container#trashCan .item-list tbody').append('<tr class="item id' + i + '"><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
                        $('.table-container#trashCan .item-list tbody tr.item.id' + i).attr('data-itemdata', itemData);
                        $('.table-container#trashCan .item-list tbody tr.item.id' + i + ' td').eq(0).append('<input type="checkbox" class="checkbox item' + i + '"><span class="checkmark item' + i + '"><i class="fa fa-check"></i></span>');
                        $('.table-container#trashCan .item-list tbody tr.item.id' + i + ' td').eq(1).text(result.data[i - 1].mail_number);
                        $('.table-container#trashCan .item-list tbody tr.item.id' + i + ' td').eq(2).text(result.data[i - 1].subject);
                        $('.table-container#trashCan .item-list tbody tr.item.id' + i + ' td').eq(3).text(result.data[i - 1].sender);
                        $('.table-container#trashCan .item-list tbody tr.item.id' + i + ' td').eq(4).html(result.data[i - 1].status);
                        $('.table-container#trashCan .item-list tbody tr.item.id' + i + ' td').eq(5).text(result.data[i - 1].date);

                        $('.table-container#trashCan .item-list tbody tr.item.id' + i + ' td').eq(6).append(
                            '<button class="button action-btn recovery" id="item' + i + '"><i class="fa fa-recycle"></i></button>'
                        );

                        $('.table-container#trashCan .item-list tbody tr.item.id' + i + ' td').eq(6).append(
                            '<button class="button action-btn remove" id="item' + i + '"><i class="fa fa-times"></i></button>'
                        );
                        atrgr.defineTrigger('checkboxAction' + i, 'checkbox_action');
                        atrgr.defineTrigger('removeTrash' + i, 'remove_trash');
                        atrgr.defineTrigger('recoverMail' + i, 'recover_mail');
                        atrgr['checkboxAction' + i](i);
                        atrgr['removeTrash' + i](i, result.data[i - 1].mail_number);
                        atrgr['recoverMail' + i](i, result.data[i - 1]);
                    }

                    if (Number.parseInt(result.paging.status) == 1) {
                        console.log('Entering...');
                        var pgnt = new Pagination(Number.parseInt(result.paging.limit), '.item', 'page-link', '.pagination');
                        pagination = pgnt.paginate();
                    }

                }
            });
        });
    }
    $('.casual-theme.mail-views .casual-theme.mail-view' + ' .close-btn').unbind('click');
    $('.casual-theme.mail-views .casual-theme.mail-view' + ' .close-btn').click(function() {
        $('.casual-theme.mail-views .casual-theme.mail-view').addClass('hide');
        $('.casual-theme.mail-views').addClass('hide');
        $('.casual-theme.mail-views .casual-theme.mail-view .modal2ndlayer button.mail-btn').remove();
        $('.table-container #mailAction .action-btn.view').removeAttr('disabled');

    });

});