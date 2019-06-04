$(document).ready(function () {
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
    $('.navbar-icon').click(function () {
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
    $('.button.options').click(function () {
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
    $('.button.notification').click(function () {
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
    $('.side-profile button.edit-profile').click(function () {
        let
            buttonHTML = $(this).html();
        //console.log(buttonHTML);
        if (buttonHTML == '<i class="fa fa-user-edit"></i> Edit') {
            $(this).html('<i class="fa fa-arrow-left"></i> Back');
            $('.side-profile').addClass('edit');
            $('.side-profile.edit .profile-img').addClass('edit');
            $('.side-profile.edit .button.choose-btn').removeClass('hide');
            $('.side-profile.edit .photos-box').removeClass('hide');
            $('.side-profile.edit .profile-info').addClass('edit');
            $('.side-profile.edit .profile-info ul').addClass('hide');
            $('.side-profile.edit .profile-info.edit .update-type').removeClass('hide');
        } else if (buttonHTML == '<i class="fa fa-arrow-left"></i> Back') {
            $(this).html('<i class="fa fa-user-edit"></i> Edit');
            $('.side-profile').removeClass('edit');
            $('.side-profile .profile-img').removeClass('edit');
            $('.side-profile .button.choose-btn').addClass('hide');
            $('.side-profile .photos-box').addClass('hide');
            $('.side-profile .profile-info').removeClass('edit');
            $('.side-profile .profile-info ul').removeClass('hide');
            $('.side-profile .profile-info .update-type').addClass('hide');
        }
    });

    $('.logout-btn').click(function () {
        $.ajax({
                url: baseURL() + '/logout',
                type: 'POST',
                dataType: 'json',
                data: {
                    token: $(this).data('token')
                },
            })
            .done(function () {
                //console.log("success");
            })
            .fail(function () {
                //console.log("error");
            })
            .always(function (result) {
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

    $('#checkAll').click(function () {
        var ma = new MultipleAction(baseURL() + '/user/remove_item');
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
                ma.multipleAction1('remove', 'user_management', true);
                ma.multipleAction2('trash', 'incoming_mail', true);
                ma.multipleAction3('trash', 'outgoing_mail', true);
                ma.multipleAction4('remove', 'field_section', true);
            } else if (checkedItemCount < 2) {
                $('.multiple-action').addClass('hide');
                ma.deleteAction('multipleAction1');
                ma.deleteAction('multipleAction2');
                ma.deleteAction('multipleAction3');
                ma.deleteAction('multipleAction4');
            }
        }

        $('.checkmark.all').click(function () {
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

    $('.settings-checkbox input[type=checkbox]').click(function () {
        //console.log($(this).is(':checked'));
        if ($(this).is(':checked') == true) {
            $('.settings-checkbox .settings-checkmark').css('display', 'block');
        }
    });

    $('.settings-checkbox .settings-checkmark').click(function () {
        if ($('.settings-checkbox input[type=checkbox]').is(':checked') == true) {
            $('.settings-checkbox .settings-checkmark').css('display', 'none');
            $('.settings-checkbox input[type=checkbox]').prop('checked', false);
            //console.log($(this).is(':checked'));
        }
    });

    $('#settingsForm .save-settings-btn').click(function () {
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
            url: baseURL() + '/user/settings',
            data: settingsData,
            dataType: "json",
        }).done(function () {

        }).fail(function () {

        }).always(function (result) {
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

    $('.table-container#outgoingMail .item-list').ready(function () {
        $.ajax({
            type: "POST",
            url: baseURL() + '/outgoing_mail/load',
            dataType: "json",
            data: {
                t: $.cookie('t')
            }
        }).done(function () {

        }).fail(function () {

        }).always(function (result) {
            if ($.isArray(result.data)) {
                var atrgr = new ActionTrigger();
                //console.log(result.data.length);
                for (var i = 1; i < result.data.length + 1; i++) {
                    console.log(result.data[i - 1].subject);
                    $('.table-container#outgoingMail .item-list tbody').append('<tr class="item id' + i + '"><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
                    $('.table-container#outgoingMail .item-list tbody tr.item.id' + i + ' td').eq(0).append('<input type="checkbox" class="checkbox item' + i + '"><span class="checkmark item' + i + '"><i class="fa fa-check"></i></span>');
                    $('.table-container#outgoingMail .item-list tbody tr.item.id' + i + ' td').eq(1).text(result.data[i - 1].mail_number);
                    $('.table-container#outgoingMail .item-list tbody tr.item.id' + i + ' td').eq(2).text(result.data[i - 1].subject);
                    $('.table-container#outgoingMail .item-list tbody tr.item.id' + i + ' td').eq(3).text(result.data[i - 1].sender);
                    $('.table-container#outgoingMail .item-list tbody tr.item.id' + i + ' td').eq(4).text(result.data[i - 1].status);
                    $('.table-container#outgoingMail .item-list tbody tr.item.id' + i + ' td').eq(5).text(result.data[i - 1].date);
                    $('.table-container#outgoingMail .item-list tbody tr.item.id' + i + ' td').eq(6).append(
                        '<button class="button action-btn edit" id="item' + i + '"><i class="fa fa-edit"></i></button>'
                    );
                    $('.table-container#outgoingMail .item-list tbody tr.item.id' + i + ' td').eq(6).append(
                        '<button class="button action-btn view" id="item' + i + '"><i class="fa fa-eye"></i></button>'
                    );
                    $('.table-container#outgoingMail .item-list tbody tr.item.id' + i + ' td').eq(6).append(
                        '<button class="button action-btn trash" id="item' + i + '"><i class="fa fa-times"></i></button>'
                    );
                    atrgr.defineTrigger('checkboxAction' + i, 'checkbox_action');
                    atrgr.defineTrigger('viewMail' + i, 'view_mail');
                    atrgr.defineTrigger('throwMailToTrash' + i, 'throw_mail_tt');
                    atrgr['checkboxAction' + i](i);
                    atrgr['throwMailToTrash' + i](i, result.data[i - 1].true_name);
                }

                if (Number.parseInt(result.paging.status) == 1) {
                    console.log('Entering...');
                    var pgnt = new Pagination(Number.parseInt(result.paging.limit), '.item', 'page-link', '.pagination');
                    pagination = pgnt.paginate();
                }

            } else {
                $('</p>').addClass('not-found-msg').html(result.data).appendTo('.table-container#outgoingMail .table-header');
            }
        });
    });

    // var
    //     i = 1,
    //     itemLength = $('.checkbox').length,
    //     atrgr = new ActionTrigger();
    // for (; i < itemLength + 1; i++) {
    //     console.log(i);
    //     atrgr.defineTrigger('chekboxAction' + i, 'checkbox_action');
    //     atrgr.defineTrigger('throwMailToTrash' + i, 'throw_mail_tt');
    //     atrgr.defineTrigger('viewMail' + i, 'view_mail');
    //     atrgr.execTrigger('chekboxAction' + i, i);
    //     atrgr.execTrigger('throwMailToTrash' + i, i);
    //     atrgr.execTrigger('viewMail' + i, i);
    // }
});