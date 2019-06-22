$(document).ready(function () {
    var pagination = false;
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

    // update profile
    $('.profile-info form.update-type.01 button[name=update-type-01-submit]').click(function(){
        let
        form = $('.profile-info form.update-type.01'),
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
        .done(function () {
            //console.log("success");
        })
        .fail(function () {
            //console.log("error");
        })
        .always(function (result) {
            console.log(result);
            if (result.status == 'success') {
                $('.profile-info form.update-type.01 input[name=password]').val('');
                if (result.data['change_count'] == 1) {
                    console.log($('.profile-info ul li').eq(0));
                    $('.profile-info form.update-type.01 input[name=' + result.data['change_name'] + ']').val(result.data['change_value']);
                    $('.profile-info ul li').eq(0).text('Nama: ' + result.data['change_value']);
                } else if (result.data['change_count'] == 2) {
                    $('.profile-info form.update-type.01 input[name=' + result.data['change_name1'] + ']').val(result.data['change_value1']);
                    $('.profile-info form.update-type.01 input[name=' + result.data['change_name2'] + ']').val(result.data['change_value2']);
                    $('.profile-info ul li').eq(0).text('Nama: ' + result.data['change_value1']);
                }
                window.alert(result.message);
                // $('.action-msg-notification').html('<p>' + result.message + '</p>');
                // $('.action-msg-notification').removeClass('error');
                // $('.action-msg-notification').removeClass('failed');
                // $('.action-msg-notification').removeClass('warning');
                // $('.action-msg-notification').addClass('success');
                // $('.action-msg-notification').removeClass('hide');
                // $('.action-msg-notification').fadeOut({
                //     duration: 3000,
                //     complete: () => {
                //         $('.action-msg-notification').addClass('hide');
                //         $('.action-msg-notification').removeAttr('style');
                //     }
                // });
            } else if (result.status == 'failed') {
                window.alert(result.message);
                // $('.action-msg-notification').html('<p>' + result.message + '</p>');
                // $('.action-msg-notification').removeClass('success');
                // $('.action-msg-notification').removeClass('warning');
                // $('.action-msg-notification').removeClass('error');
                // $('.action-msg-notification').addClass('failed');
                // $('.action-msg-notification').removeClass('hide');
                // $('.action-msg-notification').fadeOut({
                //     duration: 3000,
                //     complete: () => {
                //         $('.action-msg-notification').addClass('hide');
                //         $('.action-msg-notification').removeAttr('style');
                //     }
                // });
            } else if (result.status == 'error') {
                window.alert(result.message);
            //     $('.action-msg-notification').html('<p>' + result.message + '</p>');
            //     $('.action-msg-notification').removeClass('success');
            //     $('.action-msg-notification').removeClass('warning');
            //     $('.action-msg-notification').removeClass('failed');
            //     $('.action-msg-notification').addClass('error');
            //     $('.action-msg-notification').removeClass('hide');
            //     $('.action-msg-notification').fadeOut({
            //         duration: 3000,
            //         complete: () => {
            //             $('.action-msg-notification').addClass('hide');
            //             $('.action-msg-notification').removeAttr('style');
            //         }
            //     });
            } else if (result.status == 'warning') {
                window.alert(result.message);
                // $('.action-msg-notification').html('<p>' + result.message + '</p>');
                // $('.action-msg-notification').removeClass('success');
                // $('.action-msg-notification').removeClass('failed');
                // $('.action-msg-notification').removeClass('error');
                // $('.action-msg-notification').addClass('warning');
                // $('.action-msg-notification').removeClass('hide');
                // $('.action-msg-notification').fadeOut({
                //     duration: 3000,
                //     complete: () => {
                //         $('.action-msg-notification').addClass('hide');
                //         $('.action-msg-notification').removeAttr('style');
                //     }
                // });
            }
        });
    });

    $('.profile-info form.update-type.02 button[name=update-type-02-submit]').click(function(){
        let
        form = $('.profile-info form.update-type.02'),
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
        .done(function () {
            //console.log("success");
        })
        .fail(function () {
            //console.log("error");
        })
        .always(function (result) {
            console.log(result);
            if (result.status == 'success') {
                $('.profile-info form.update-type.02 input[name=old_password]').val('');
                $('.profile-info form.update-type.02 input[name=new_password]').val('');
                $('.profile-info form.update-type.02 input[name=new_password_confirm]').val('');
                window.alert(result.message);
                // $('.action-msg-notification').html('<p>' + result.message + '</p>');
                // $('.action-msg-notification').removeClass('error');
                // $('.action-msg-notification').removeClass('failed');
                // $('.action-msg-notification').removeClass('warning');
                // $('.action-msg-notification').addClass('success');
                // $('.action-msg-notification').removeClass('hide');
                // $('.action-msg-notification').fadeOut({
                //     duration: 3000,
                //     complete: () => {
                //         $('.action-msg-notification').addClass('hide');
                //         $('.action-msg-notification').removeAttr('style');
                //     }
                // });
            } else if (result.status == 'failed') {
                window.alert(result.message);
                // $('.action-msg-notification').html('<p>' + result.message + '</p>');
                // $('.action-msg-notification').removeClass('success');
                // $('.action-msg-notification').removeClass('warning');
                // $('.action-msg-notification').removeClass('error');
                // $('.action-msg-notification').addClass('failed');
                // $('.action-msg-notification').removeClass('hide');
                // $('.action-msg-notification').fadeOut({
                //     duration: 3000,
                //     complete: () => {
                //         $('.action-msg-notification').addClass('hide');
                //         $('.action-msg-notification').removeAttr('style');
                //     }
                // });
            } else if (result.status == 'error') {
                window.alert(result.message);
            //     $('.action-msg-notification').html('<p>' + result.message + '</p>');
            //     $('.action-msg-notification').removeClass('success');
            //     $('.action-msg-notification').removeClass('warning');
            //     $('.action-msg-notification').removeClass('failed');
            //     $('.action-msg-notification').addClass('error');
            //     $('.action-msg-notification').removeClass('hide');
            //     $('.action-msg-notification').fadeOut({
            //         duration: 3000,
            //         complete: () => {
            //             $('.action-msg-notification').addClass('hide');
            //             $('.action-msg-notification').removeAttr('style');
            //         }
            //     });
            } else if (result.status == 'warning') {
                window.alert(result.message);
                // $('.action-msg-notification').html('<p>' + result.message + '</p>');
                // $('.action-msg-notification').removeClass('success');
                // $('.action-msg-notification').removeClass('failed');
                // $('.action-msg-notification').removeClass('error');
                // $('.action-msg-notification').addClass('warning');
                // $('.action-msg-notification').removeClass('hide');
                // $('.action-msg-notification').fadeOut({
                //     duration: 3000,
                //     complete: () => {
                //         $('.action-msg-notification').addClass('hide');
                //         $('.action-msg-notification').removeAttr('style');
                //     }
                // });
            }
        });
    });
    // move this checkboxAction function to asmp-actionlib, and make defineAction method for this function
    // function checkboxAction(index) {
    //     var ma = new MultipleAction(baseURL() + '/remove_item');
    //     $('.checkbox.item'   + index).click(function () {
    //         var mailIds = $('.multiple-action').data('mail-ids');
    //         checkedItemCount += 1;
    //         mailIds += index + ',';
    //         $('.multiple-action').data('mail-ids', mailIds);
    //         console.log($(this).is(':checked'), index, 'Checked Count: ' + checkedItemCount, 'Mail Ids: ' + $('.multiple-action').data('mail-ids'));
    //         if ($(this).is(':checked')) {
    //             console.log('IndexCHECKED:' + index);
    //             $('.checkmark.item' + index).css('display', 'inline-block');
    //             if (checkedItemCount == 2) {
    //                 $('.multiple-action').removeClass('hide');
    //                 ma.defineAction('multipleAction1');
    //                 ma.defineAction('multipleAction2');
    //                 ma.defineAction('multipleAction3');
    //                 ma.multipleAction1('remove', 'user_management', false, true);
    //                 ma.multipleAction2('trash', 'incoming_mail', false, true);
    //                 ma.multipleAction3('remove', 'field_section', false, true); 
    //             } else if (checkedItemCount < 2) {
    //                 $('.multiple-action').addClass('hide');
    //             }
    //         }
    //     });

    //     $('.checkmark.item' + index).click(function () {
    //         var mailIds = $('.multiple-action').data('mail-ids');
    //         checkedItemCount -= 1;
    //         if (mailIds !== '1' && mailIds !== '') {
    //             mailIds = mailIds.replace(index + ',', '');
    //             mailIds = mailIds.split(',');
    //             mailIds = mailIds.slice(0, -1);
    //             console.log(mailIds);
    //             if (mailIds.length > 1) {
    //                 mailIds = mailIds.join(',') + ',';
    //             } else if (mailIds.length == 1) {
    //                 mailIds = mailIds.toString() + ',';
    //             }
    //         } else {
    //             mailIds = '';
    //         }
    //         $('.multiple-action').data('mail-ids', mailIds);
    //         console.log($(this).is(':checked'), index, 'Checked Count: ' + checkedItemCount, 'Mail Ids: ' + $('.multiple-action').data('mail-ids'));
    //         $('.checkbox.item' + index).prop('checked', false);
    //         $(this).css('display', 'none');
    //         if (checkedItemCount > 1) {
    //             $('.multiple-action').removeClass('hide');
    //         } else if (checkedItemCount < 2) {
    //             $('.multiple-action').addClass('hide');
    //         }
    //         console.log($('.checkbox.item' + index).is(':checked'), index, 'Checked Count: ' + checkedItemCount);
    //     });
    // }

    // function multipleAction(action, targetName, allItem = false) {
    //     var actionExecutor = new ASMPActionExecutor();
    //     if (action === 'remove') {
    //         if (targetName === 'user_management') {
    //             $('.table-container#userManagement .table-header .multiple-action .multiple-action-btn.trash').click(function () {
    //                 var itemIds = $('.multiple-action').data('mail-ids');
    //                 console.log(itemIds);
    //                 itemIds = itemIds.slice(0, -1);
    //                 itemIds = itemIds.split(',');
    //                 actionExecutor.actionName = 'Remove User';
    //                 actionExecutor.actionUrl = 'admin/remove_user';
    //                 actionExecutor.actionData = {
    //                     id: itemIds,
    //                     t: $.cookie('t'),
    //                     multiple: true,
    //                     all_item: allItem
    //                 };
    //                 actionExecutor.itemTarget = '.item';
    //                 actionExecutor.exec({
    //                     multipleAction: true,
    //                     haveID: true,
    //                     removeTarget: true
    //                 });
    //                 if (allItem === true) {
    //                     $('#checkAll').prop('checked', false);
    //                     $('.checkmark').css('display', 'none');
    //                 }
    //                 $('.multiple-action').data('mail-ids', '');
    //                 $('.table-container#userManagement .table-header .multiple-action').addClass('hide');
    //                 checkedItemCount = 0;
    //             });
    //         } else if (targetName === 'field_section') {
    //             $('.table-container#fieldSections .table-header .multiple-action .multiple-action-btn.trash').click(function () {
    //                 var
    //                     itemIds = $('.multiple-action').data('mail-ids'),
    //                     confirm = window.confirm('Apakah anda yakin ingin menghapusnya?'),
    //                     promptInp = false,
    //                     uPass = '';
    //                 itemIds = itemIds.slice(0, -1);
    //                 itemIds = itemIds.split(',');
    //                 if (confirm) {
    //                     if ($('.prompt-box form .prompt-submit').length === 0) {
    //                         $('<button></buton>').attr({
    //                             type: 'submit',
    //                             class: 'button prompt-submit',
    //                             id: 'rmAll'
    //                         }).text('Submit').appendTo('.prompt-box form');
    //                     }
    //                     $('.prompt-box').removeClass('hide');
    //                     $('.dim-light').removeClass('hide-elemet');
    //                     $('.prompt-box form input#uPass').focus();
    //                     $('.prompt-box form .prompt-submit#rmAll').click(function () {
    //                         promptInp = true;
    //                         uPass = $('.prompt-box form input#uPass').val();
    //                         console.log(uPass);
    //                         if (promptInp != false) {
    //                             actionExecutor.actionName = 'Remove Field/Section';
    //                             actionExecutor.actionUrl = baseURL() + '/remove_item';
    //                             actionExecutor.actionData = {
    //                                 id: itemIds,
    //                                 t: $.cookie('t'),
    //                                 item_type: 'field_sections',
    //                                 selected_item: false,
    //                                 multiple: true,
    //                                 all_item: allItem,
    //                                 item_data: {password: uPass}
    //                             };
    //                             actionExecutor.itemTarget = '.item';
    //                             actionExecutor.exec({
    //                                 multipleAction: true,
    //                                 haveID: true,
    //                                 removeTarget: true
    //                             });
    //                             if (allItem === true) {
    //                                 $('#checkAll').prop('checked', false);
    //                                 $('.checkmark').css('display', 'none');
    //                             }
    //                             $('.multiple-action').data('mail-ids', '');
    //                             console.log($('.multiple-action').data('mail-ids'));
    //                             $('.table-container#fieldSections .table-header .multiple-action').addClass('hide');
    //                             checkedItemCount = 0;
    //                             $('.prompt-box form input#uPass').val('');
    //                             $('.prompt-box').addClass('hide');
    //                             $('.dim-light').addClass('hide-elemet');
    //                             $('.prompt-box form .prompt-submit').remove();
    //                             uPass = '';
    //                             confirm = false;
    //                             promptInp = false;
    //                         }
    //                     });
    //                     $('.prompt-box .close-btn').click(function () {
    //                         console.log('prompt-box::before');
    //                         $('.prompt-box').addClass('hide');
    //                         $('.dim-light').addClass('hide-elemet');
    //                         confirm = false;
    //                     });
    //                 } else {
    //                     confirm = false;
    //                 }
    //             });

    //         }
    //     } else if (action === 'trash') {
    //         if (targetName === 'incoming_mail') {
    //             $('.table-container#incomingMail .table-header .multiple-action .multiple-action-btn.trash').click(function () {
    //                 var itemIds = $('.multiple-action').data('mail-ids');
    //                 console.log(itemIds);
    //                 itemIds = itemIds.slice(0, -1);
    //                 itemIds = itemIds.split(',');
    //                 actionExecutor.actionName = 'Throw All Mail to Trash';
    //                 actionExecutor.actionUrl = 'admin/throw_mail_to_trash';
    //                 actionExecutor.actionData = {
    //                     id: itemIds,
    //                     token: 'This is Token',
    //                     multiple: true,
    //                     all_item: allItem
    //                 };
    //                 actionExecutor.itemTarget = '.item';
    //                 actionExecutor.exec({
    //                     multipleAction: true,
    //                     haveID: true,
    //                     removeTarget: true
    //                 });
    //                 if (allItem === true) {
    //                     $('#checkAll').prop('checked', false);
    //                     $('.checkmark').css('display', 'none');
    //                 }
    //                 $('.multiple-action').data('mail-ids', '');
    //                 $('.table-container#incomingMail .table-header .multiple-action').addClass('hide');
    //                 checkedItemCount = 0;
    //             });
    //         }
    //     }
    // }

    $('#checkAll').click(function () {
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
                ma.multipleAction1('remove', 'user_management', true);
                ma.multipleAction2('trash', 'incoming_mail', true);
                ma.multipleAction3('remove', 'field_section', true);
            } else if (checkedItemCount < 2) {
                $('.multiple-action').addClass('hide');
                ma.deleteAction('multipleAction1');
                ma.deleteAction('multipleAction2');
                ma.deleteAction('multipleAction3');
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

    console.log(window.location.href, baseURL() + '/bidang-bagian');
    if (window.location.href == baseURL() + '/user-management') {
        // user management item loader
        $('.table-container#userManagement .item-list').ready(function () {
            $.ajax({
                type: "POST",
                url: baseURL() + '/user_management/load',
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
                        $('.table-container#userManagement .item-list tbody').append('<tr class="item id' + i + '"><td></td><td></td><td></td><td></td></tr>');
                        $('.table-container#userManagement .item-list tbody tr.item.id' + i + ' td').eq(0).append('<input type="checkbox" class="checkbox item' + i + '"><span class="checkmark item' + i + '"><i class="fa fa-check"></i></span>');
                        $('.table-container#userManagement .item-list tbody tr.item.id' + i + ' td').eq(1).text(result.data[i - 1].true_name);
                        $('.table-container#userManagement .item-list tbody tr.item.id' + i + ' td').eq(2).text(result.data[i - 1].position);
                        $('.table-container#userManagement .item-list tbody tr.item.id' + i + ' td').eq(3).append(
                            '<button class="button action-btn remove" id="item' + i + '"><i class="fa fa-times"></i></button>'
                        );
                        atrgr.defineTrigger('checkboxAction' + i, 'checkbox_action');
                        atrgr.defineTrigger('deleteUser' + i, 'delete_user');
                        atrgr['checkboxAction' + i](i);
                        atrgr['deleteUser' + i](i, result.data[i - 1].true_name);
                    }
                } else {
                    $('</p>').addClass('not-found-msg').html(result.data).appendTo('.table-container#userManagement .table-header');
                }
            });
        });
    } else if (window.location.href == baseURL() + '/bidang-bagian') {
        // field/sections item loader
        $('.table-container#fieldSections .item-list').ready(function () {
            $.ajax({
                type: "POST",
                url: baseURL() + '/field_sections/load',
                dataType: "json",
                data: {
                    t: $.cookie('t')
                }
            }).done(function () {

            }).fail(function () {

            }).always(function (result) {
                if ($.isArray(result.data)) {
                    var atrgr = new ActionTrigger();
                    for (var i = 1; i < result.data.length + 1; i++) {
                        $('.table-container#fieldSections .item-list tbody').append('<tr class="item id' + i + '"><td></td><td></td><td></td><td></td></tr>');
                        $('.table-container#fieldSections .item-list tbody tr.item.id' + i + ' td').eq(0).append('<input type="checkbox" class="checkbox item' + i + '"><span class="checkmark item' + i + '"><i class="fa fa-check"></i></span>');
                        $('.table-container#fieldSections .item-list tbody tr.item.id' + i + ' td').eq(1).text(result.data[i - 1].field_section_name);
                        $('.table-container#fieldSections .item-list tbody tr.item.id' + i + ' td').eq(2).text(result.data[i - 1].task);
                        $('.table-container#fieldSections .item-list tbody tr.item.id' + i + ' td').eq(3).append(
                            '<button class="button action-btn remove" id="item' + i + '"><i class="fa fa-times"></i></button>'
                        );

                        if (result.data[i - 1].disable_action) {
                            $('.table-container#fieldSections .item-list tbody tr.item.id' + i + ' td .checkbox.item' + i).attr('disabled', true);
                            $('.table-container#fieldSections .item-list tbody tr.item.id' + i + ' td .checkmark.item' + i).attr('disabled', true);
                            $('.table-container#fieldSections .item-list tbody tr.item.id' + i + ' td .action-btn.remove#item' + i).attr('disabled', true);
                        }
                        atrgr.defineTrigger('checkboxAction' + i, 'checkbox_action');
                        atrgr.defineTrigger('deleteFieldSection' + i, 'delete_field_section');
                        atrgr['checkboxAction' + i](i);
                        atrgr['deleteFieldSection' + i](i, result.data[i - 1].field_section_name);
                    }
                    //console.log(Number.parseInt(result.paging.limit));
                    if (Number.parseInt(result.paging.status) == 1) {
                        //console.log('Entering...');
                        var pgnt = new Pagination(Number.parseInt(result.paging.limit), '.item', 'page-link', '.pagination');
                        pagination = pgnt.paginate();
                    }
                } else {
                    $('</p>').addClass('not-found-msg').html(result.data).appendTo('.table-container#fieldSections .table-header');
                }
            });
        });
    } else if (window.location.href == baseURL() + '/surat-masuk') {
        console.log('SURAT MASUK');
        $('.table-container#incomingMail .item-list').ready(function () {
            $.ajax({
                type: "POST",
                url: baseURL() + '/incoming_mail/load',
                dataType: "json",
                data: {
                    t: $.cookie('t')
                }
            }).done(function () {

            }).fail(function () {

            }).always(function (result) {
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

        $('.button.trash-can-btn').click(function () {
            window.location.replace(baseURL() + '/tong-sampah');
        });
    } else if (window.location.href == baseURL() + '/tong-sampah') {
        $('.table-container#trashCan .item-list').ready(function () {
            $.ajax({
                type: "POST",
                url: baseURL() + '/trash_can/load',
                dataType: "json",
                data: {
                    t: $.cookie('t')
                }
            }).done(function () {

            }).fail(function () {

            }).always(function (result) {
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
    $('.casual-theme.mail-views .casual-theme.mail-view' + ' .close-btn').click(function () {
        $('.casual-theme.mail-views .casual-theme.mail-view').addClass('hide');
        $('.casual-theme.mail-views').addClass('hide');
        $('.casual-theme.mail-views .casual-theme.mail-view .modal2ndlayer button.mail-btn').remove();
        $('.table-container #mailAction .action-btn.view').removeAttr('disabled');

    });


    // add field section
    $('.button.add-item-btn').click(function () {
        $('.add-field-section-box').removeClass('hide');
        $(this).css('background-color', '#1da147');
        $(this).css('cursor', 'no-drop');
        $(this).attr('disabled', 'disabled');
    });

    $('.add-field-section-box .title .close-btn').click(function () {
        $('.add-field-section-box').addClass('hide');
        $('.button.add-item-btn').css('background-color', '#26C959');
        $('.button.add-item-btn').css('cursor', 'pointer');
        $('.button.add-item-btn').removeAttr('disabled');
    });

    $('.button[name=add_field_section]').click(function () {
        var
            fsName = $('.add-field-section-form #fieldSectionName').val(),
            fsTask = $('.add-field-section-form #fieldSectionTask').val(),
            itemLength = $('.table-container#fieldSections .item-list .item').length,
            fsTaskTxt = $('.add-field-section-form #fieldSectionTask option[value=' + fsTask + ']').text(),
            id = itemLength + 1;
        $('.add-field-section-form #fieldSectionName').val('');
        $('.add-field-section-form #fieldSectionTask').val('normal_accept_sending');
        $('.add-field-section-box').addClass('hide');
        $('.button.add-item-btn').css('background-color', '#26C959');
        $('.button.add-item-btn').css('cursor', 'pointer');
        $('.button.add-item-btn').removeAttr('disabled');
        $.ajax({
            type: "POST",
            url: baseURL() + '/add_field_section',
            dataType: "json",
            data: {
                t: $.cookie('t'),
                fs_name: fsName,
                fs_task: fsTask
            }
        }).done(function () {

        }).fail(function () {

        }).always(function (result) {
            //console.log(result);
            if (result.status == 'success') {
                var pgnt = new Pagination(4, '.item', 'page-link', '.pagination');
                var hideElement = '';
                var atrgr = new ActionTrigger();
                // tambahkan item lenght untuk fungsi pagniate
                if ($(pgnt.itemToPaginate).not(pgnt.itemToPaginate + '.hide').length == pgnt.itemLimit) {
                    hideElement = ' hide';
                }
                $('.table-container#fieldSections .item-list tbody').append('<tr class="item id' + id + hideElement + '"><td></td><td></td><td></td><td></td></tr>');
                $('.table-container#fieldSections .item-list tbody .item.id' + id + ' td').eq(0).html('<input type="checkbox" class="checkbox item' + id + '"><span class="checkmark item' + id + '"><i class="fa fa-check"></i></span>');
                $('.table-container#fieldSections .item-list tbody .item.id' + id + ' td').eq(1).text(fsName);
                $('.table-container#fieldSections .item-list tbody .item.id' + id + ' td').eq(2).text(fsTaskTxt);
                $('.table-container#fieldSections .item-list tbody .item.id' + id + ' td').eq(3).html('<button class="button action-btn remove" id="item' + id + '"><i class="fa fa-times"></i></button>');
                atrgr.defineTrigger('checkboxAction' + id, 'checkbox_action');
                atrgr.defineTrigger('deleteFieldSection' + id, 'delete_field_section');
                atrgr['checkboxAction' + id](id);
                atrgr['deleteFieldSection' + id](id, fsName);
                paginate = pgnt.updatePgNav();
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

    $('.settings-container .save-all-settings-btn').click(function () {
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
                pagingLimit: $('#settingsForm .form-section.page #pagingLimit').val()
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
            data: {
                data: JSON.stringify({
                    cmd: 'save_all_setting',
                    settings_data: settingsData,
                    t: $.cookie('t')
                })
            },
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

    // app settings
    $('#settingsForm .save-settings-btn[name=app_settings]').click(function () {
        $(this).html('<i class="fa fa-sync fa-spin"></i>');
        var
            settingsData = {
                // company section
                companyName: $('.form-section.company #settingsForm #companyName').val(),
                companyAddress: $('.form-section.company #settingsForm #companyAddress').val(),
                companyContact: $('.form-section.company #settingsForm #companyContact').val()
            },
            currObj = $(this);
        $.ajax({
            type: "POST",
            url: baseURL() + '/settings',
            data: {
                data: JSON.stringify({
                    cmd: 'save_app_settings',
                    settings_data: settingsData,
                    t: $.cookie('t')
                })
            },
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

    // page settings
    $('#settingsForm .save-settings-btn[name=page_settings]').click(function () {
        $(this).html('<i class="fa fa-sync fa-spin"></i>');
        var
            settingsData = {
                mulRemAct: $('.form-section.page #settingsForm #mulRemAct').val(),
                mulRecAct: $('.form-section.page #settingsForm #mulRecAct').val(),
                pagingItem: $('.form-section.page #settingsForm span.settings-checkbox #pagingItem').is(':checked'),
                pagingLimit: $('.form-section.page #settingsForm #pagingLimit').val()
            },
            currObj = $(this);
        $.ajax({
            type: "POST",
            url: baseURL() + '/settings',
            data: {
                data: JSON.stringify({
                    cmd: 'save_user_settings',
                    settings_data: settingsData,
                    t: $.cookie('t')
                })
            },
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

    function xss_clean(str) {
        var filtered_str = xssFilters.inHTMLData(str);
        filtered_str = xssFilters.inSingleQuotedAttr(filtered_str);
        filtered_str = xssFilters.inDoubleQuotedAttr(filtered_str);
        //filtered_str = xssFilters.inUnQuotedAttr(filtered_str);
        return filtered_str;
    }

    /**
     * generate a PDF Component Selection Box
     */
    function generatePDFComponentSB(container) {
        var
            formElementClass = 'component-form',
            componentsClass = 'component',
            lang = {
                indonesia: 'in_ID',
                english: 'en_US'
            },
            componentNames = [
                'idAndMailType',
                'docTitle',
                'docAddr',
                'docContact',
                'line',
                'docMailNum',
                'docDate',
                'docFor',
                'docSubject',
                'lineBreak',
                'leftMargin',
                'docContents',
                'docSignature'
            ],
            // human readable name
            componentNamesHR = {
                in_ID: {
                    idAndMailType: 'ID dan Jenis Surat',
                    docTitle: 'Header Surat > Nama Instansi Surat',
                    docAddr: 'Header Surat > Alamat Instansi Surat',
                    docContact: 'Header Surat > Kontak Instansi Surat',
                    line: 'Header Surat > Garis Bawah',
                    docMailNum: 'Nomor Surat',
                    docDate: 'Tanggal Surat',
                    docFor: 'Nama Penerima',
                    docSubject: 'Perihal',
                    lineBreak: 'Line Break',
                    leftMargin: 'Margin Kiri',
                    docContents: 'Isi Surat',
                    docSignature: 'Tanda Tangan'
                },
                en_US: {}
            }
        formSubmitClass = 'component-form-submit',
            i = 0;

        lang = lang[$.cookie('language')];
        //console.log($.cookie('language'));
        $('<button></button>').addClass('button close-btn').html('<i class="fa fa-times-circle"></i>').appendTo(container);
        $('<form></form>').attr({
            class: formElementClass,
            action: 'javascript:void(0)',
            method: 'post'
        }).appendTo(container);
        // Layout Name
        $('<label></label>')
            .attr('for', 'layout_name')
            .text('Nama Layout: ')
            .appendTo(container + ' form');
        $('<input>').attr({
            type: 'text',
            name: 'layout_name',
            id: 'layoutName'
        }).appendTo(container + ' form');
        // Layout Orientation
        $('<label></label>')
            .attr('for', 'layout_orientation')
            .text('Orientasi Layout: ')
            .appendTo(container + ' form');
        $('<select></select>').attr({
            name: 'layout_orientation',
            id: 'orientation'
        }).appendTo(container + ' form');
        $('<option></option>').attr('value', 'P').text('Potrait').appendTo(container + ' form #orientation');
        $('<option></option>').attr('value', 'L').text('Landscape').appendTo(container + ' form #orientation');
        // Layout Unit
        $('<label></label>')
            .attr('for', 'layout_unit')
            .text('Unit Layout: ')
            .appendTo(container + ' form');
        $('<select></select>').attr({
            name: 'layout_unit',
            id: 'unit'
        }).appendTo(container + ' form');
        $('<option></option>').attr('value', 'mm').text('mm').appendTo(container + ' form #unit');
        $('<option></option>').attr('value', 'cm').text('cm').appendTo(container + ' form #unit');
        // Layout Format
        $('<label></label>')
            .attr('for', 'layout_format')
            .text('Format Layout: ')
            .appendTo(container + ' form');
        $('<select></select>').attr({
            name: 'layout_format',
            id: 'format'
        }).appendTo(container + ' form');
        $('<option></option>').attr('value', 'A4').text('A4').appendTo(container + ' form #format');
        $('<option></option>').attr('value', 'A5').text('A5').appendTo(container + ' form #format');
        for (; i < componentNames.length; i++) {
            $(container + ' .' + formElementClass).append('<input type="checkbox" name="' + componentNames[i] +
                '" class="' + componentsClass +
                '" id="' + componentNames[i] +
                '"><span>' + componentNamesHR[lang][componentNames[i]] + '</span>');
        }
        $(container + ' .' + formElementClass + ' .' + componentsClass).prop('checked', 'checked');
        $('<button></button>').addClass('button ' + formSubmitClass).html('Ok <i class="fa fa-check-circle"></i>').appendTo('.' + formElementClass);
        $(container).css('display', 'block'); // show the box
        $(container + ' .close-btn').click(function () {
            $(container).css('display', 'none');
            $(container + ' .close-btn').unbind('click');
            $(container + ' .' + formSubmitClass).unbind('click');
            $(container + ' .close-btn').remove();
            $(container + ' form').remove();
            $('.pdf-layout-create-btn').removeAttr('disabled');
        });
        $('.' + formSubmitClass).unbind('click');
        $('.' + formSubmitClass).click(function () {
            var
                choicedCD = [], // choiced component data
                pageSetup = {
                    orientation: $('.component-form #orientation').val(),
                    unit: $('.component-form #unit').val(),
                    format: $('.component-form #format').val()
                }, // pdf page setup
                pdfLayoutBox = $('.pdf-layout-box'),
                pdfLayoutBoxNextID = pdfLayoutBox.length + 1,
                layoutName = xss_clean($('.component-form #layoutName').val());
            //console.log(pageSetup);
            for (var i = 0; i < componentNames.length; i++) {
                if ($('.component-form #' + componentNames[i]).is(':checked')) {
                    choicedCD.push(componentNames[i]);
                }
            }
            // add xss_clean (escape all special characters)
            $.ajax({
                type: "POST",
                url: baseURL() + '/add_pdf_layout',
                data: {
                    data: JSON.stringify({
                        t: $.cookie('t'),
                        choiced_cd: choicedCD,
                        layout_name: layoutName,
                        page_setup: pageSetup
                    })
                },
                dataType: "json",
            }).done(function () {

            }).fail(function () {

            }).always(function (result) {
                if (result.status == 'success') {
                    $('.action-msg-notification').html('<p><i class="fa fa-check-circle"></i> ' + result.message + '</p>');
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
                    $(container).css('display', 'none');
                    $(container + ' .close-btn').unbind('click');
                    $(container + ' .' + formSubmitClass).unbind('click');
                    $(container + ' .close-btn').remove();
                    $(container + ' form').remove();
                    $('.pdf-layout-create-btn').removeAttr('disabled');
                    $('<div></div>').attr({
                        class: 'pdf-layout-box',
                        id: 'layout' + pdfLayoutBoxNextID
                    }).appendTo('.pdf-layout-list-container');
                    $('<i></i>').addClass('fa fa-file-pdf fa-inverse pdf-icon').appendTo('#layout' + pdfLayoutBoxNextID);
                    $('<div></div>').addClass('pdf-layout-action-container').appendTo('#layout' + pdfLayoutBoxNextID);
                    $('<p></p>').addClass('pdf-layout-name').text(layoutName).appendTo('#layout' + pdfLayoutBoxNextID);
                    $('<button></button>').addClass('button pdf-layout-action edit').html('<i class="fa fa-edit fa-fw fa-lg"></i>').appendTo('#layout' + pdfLayoutBoxNextID + ' .pdf-layout-action-container');
                    $('<button></button>').addClass('button pdf-layout-action view').html('<i class="fa fa-eye fa-fw fa-lg"></i>').appendTo('#layout' + pdfLayoutBoxNextID + ' .pdf-layout-action-container');
                    $('<button></button>').addClass('button pdf-layout-action active-non-active').html('<i class="fa fa-toggle-off fa-fw fa-lg"></i>').appendTo('#layout' + pdfLayoutBoxNextID + ' .pdf-layout-action-container');
                    $('<button></button>').addClass('button pdf-layout-action remove').html('<i class="fa fa-trash-alt fa-fw fa-lg"></i>').appendTo('#layout' + pdfLayoutBoxNextID + ' .pdf-layout-action-container');
                } else if (result.status === 'error') {
                    $('.action-msg-notification').html('<p><i class="fa fa-exclamation-circle"></p> ' + result.message + '</p>');
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
                    $(container).css('display', 'none');
                    $(container + ' .close-btn').unbind('click');
                    $(container + ' .' + formSubmitClass).unbind('click');
                    $(container + ' .close-btn').remove();
                    $(container + ' form').remove();
                    $('.pdf-layout-create-btn').removeAttr('disabled');
                } else if (result.status === 'failed') {
                    $('.action-msg-notification').html('<p><i class="fa fa-exclamation-circle"></p> ' + result.message + '</p>');
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
                    $(container).css('display', 'none');
                    $(container + ' .close-btn').unbind('click');
                    $(container + ' .' + formSubmitClass).unbind('click');
                    $(container + ' .close-btn').remove();
                    $(container + ' form').remove();
                    $('.pdf-layout-create-btn').removeAttr('disabled');
                }
            });
        });
    }

    $('.pdf-layout-create-btn').unbind('click');
    $('.pdf-layout-create-btn').click(function () {
        generatePDFComponentSB('.pdf-components-sb');
        $(this).prop('disabled', 'disabled');
    });

    function pdfLayoutActionEdit(id) {
        //console.log('Edit' + id);

        $('#layout' + id + ' .pdf-layout-action.edit').click(function () {
            //console.log('Editing....');
            var
                layoutName = $('#layout' + id + ' .pdf-layout-name').text(),
                t = $.cookie('t');
            window.open(baseURL() + '/pdf-editor/' + layoutName + '/' + t);
        });
    }

    function pdfLayoutActionView(id) {
        $('#layout' + id + ' .pdf-layout-action.view').unbind('click');
        $('#layout' + id + ' .pdf-layout-action.view').click(function () {
            var
                layoutName = $('#layout' + id + ' .pdf-layout-name').text(),
                t = $.cookie('t');
            window.open(baseURL() + '/pdf-layout/view/' + layoutName + '/im/' + encodeURIComponent("XII&sol;23") + '/' + t);
        });
    }

    function pdfLayoutActionToggleActive(id) {
        $('#layout' + id + ' .pdf-layout-action.active-non-active').unbind('click');
        $('#layout' + id + ' .pdf-layout-action.active-non-active').click(function () {
            var
                layoutName = $('#layout' + id + ' .pdf-layout-name').text(),
                toggleStat = 'nonactive',
                toggleClass = $(this);
            //console.log($('#layout' + id + ' .pdf-layout-action.active-non-active i').attr('class').search('fa-toggle-off'));
            if ($(this).html() == "<i class=\"fa fa-toggle-off fa-fw fa-lg\"></i>") {
                toggleStat = 'active';
                //$('#layout' + id + ' .pdf-layout-action.active-non-active i').toggleClass('fa-toggle-on');
            } else if ($(this).html() == "<i class=\"fa fa-toggle-on fa-fw fa-lg\"></i>") {
                toggleStat = 'nonactive'
                //$('#layout' + id + ' .pdf-layout-action.active-non-active i').toggleClass('fa-toggle-off');
            }
            $.ajax({
                type: "POST",
                url: baseURL() + '/plsc', // pdf layout status changer
                data: {
                    data: JSON.stringify({
                        t: $.cookie('t'),
                        layout_name: layoutName,
                        pdf_status: toggleStat
                    })
                },
                dataType: "json",
            }).done(function () {

            }).fail(function () {

            }).always(function (result) {
                if (result.status == 'success') {
                    //console.log('Status Changed');
                    if (toggleStat == 'active') {
                        toggleClass.html("<i class=\"fa fa-toggle-on fa-fw fa-lg\"></i>");
                    } else if (toggleStat == 'nonactive') {
                        toggleClass.html("<i class=\"fa fa-toggle-off fa-fw fa-lg\"></i>");
                    }
                } else {
                    //console.log('Status Not Changed');
                }
            });
        });
    }

    function pdfLayoutActionRemove(id) {
        //console.log('Remove' + id);
        $('#layout' + id + ' .pdf-layout-action.remove').unbind('click');
        $('#layout' + id + ' .pdf-layout-action.remove').click(function () {
            var layoutName = $('#layout' + id + ' .pdf-layout-name').text();

            $.ajax({
                type: "POST",
                url: baseURL() + '/rempl', // remove pdf layout
                data: {
                    data: JSON.stringify({
                        t: $.cookie('t'),
                        layout_name: layoutName
                    })
                },
                dataType: "json",
            }).done(function () {

            }).fail(function () {

            }).always(function (result) {
                if (result.status == 'success') {
                    $('#layout' + id).unbind('click');
                    $('#layout' + id).remove();
                    $('.action-msg-notification').html('<p><i class="fa fa-check-circle"></i> ' + result.message + '</p>');
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
                } else if (result.status === 'error') {
                    $('.action-msg-notification').html('<p><i class="fa fa-exclamation-circle"></i> ' + result.message + '</p>');
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
                } else if (result.status === 'failed') {
                    $('.action-msg-notification').html('<p><i class="fa fa-exclamation-circle"></i> ' + result.message + '</p>');
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
                }
            });
        });
    }
    var
        i = 1,
        itemLength = $('.checkbox').length,
        atrgr = new ActionTrigger();
    for (; i < itemLength + 1; i++) {
        atrgr.defineTrigger('chekboxAction' + i, 'checkbox_action');
        atrgr.defineTrigger('throwMailToTrash' + i, 'throw_mail_tt');
        atrgr.defineTrigger('viewMail' + i, 'view_mail');
        atrgr.execTrigger('chekboxAction' + i, i);
        atrgr.execTrigger('throwMailToTrash' + i, i);
        atrgr.execTrigger('viewMail' + i, i);
        //console.log(i);
    }

    if (window.location.pathname.split('/')[3] == 'pdf-layouts') {
        //console.log('pdf-layouts');
        var
            i = 1;
        itemLength = $('.pdf-layout-box').length;
        for (; i < itemLength + 1; i++) {
            pdfLayoutActionEdit(i);
            pdfLayoutActionView(i);
            pdfLayoutActionToggleActive(i);
            pdfLayoutActionRemove(i);
            //console.log(i);
        }
    }

    if (window.location.pathname.split('/')[3] == 'pdf-editor') {
        var
            viewerClass = '.pdf-editor-container .pdf-editor-box.viewer',
            configsClass = '.pdf-editor-container .pdf-editor-box.configs',
            usefulAPI = new ASMPUsefulAPI(),
            urlParams = usefulAPI.getFullURL(),
            lang = {
                indonesia: 'in_ID',
                english: 'en_US'
            },
            componentNames = [
                'idAndMailType',
                'docTitle',
                'docAddr',
                'docContact',
                'line',
                'docMailNum',
                'docDate',
                'docFor',
                'docSubject',
                'docContents',
                'docSignature'
            ],
            subComponents = [],
            componentNamesHR = {
                in_ID: {
                    idAndMailType: 'ID dan Jenis Surat',
                    docTitle: 'Header Surat > Nama Instansi Surat',
                    docAddr: 'Header Surat > Alamat Instansi Surat',
                    docContact: 'Header Surat > Kontak Instansi Surat',
                    line: 'Header Surat > Garis Bawah',
                    docMailNum: 'Nomor Surat',
                    docDate: 'Tanggal Surat',
                    docFor: 'Nama Penerima',
                    docSubject: 'Perihal',
                    docContents: 'Isi Surat',
                    docSignature: 'Tanda Tangan'
                },
                en_US: {}
            },
            editorData = null,
            pdfPageSetup = null,
            i = 0;

        lang = lang[$.cookie('language')];
        $.ajax({
            type: "POST",
            url: baseURL() + '/gedtrdta', // get editor data
            data: {
                data: JSON.stringify({
                    t: $.cookie('t'),
                    layout_name: urlParams[2]
                })
            },
            dataType: "json",
        }).done(function () {

        }).fail(function () {

        }).always(function (result) {
            if (result.status == 'success') {
                editorData = result.data;
                pdfPageSetup = result.page_setup;

                if (editorData !== null && pdfPageSetup !== null) {
                    $('<iframe></iframe>').attr({
                        src: baseURL() + '/pdf-editor/view/' + urlParams[2] + '/' + $.cookie('t'),
                        class: 'pdf-editor-viewer'
                    }).appendTo(viewerClass);

                    $('.pdf-editor-viewer').contents().find('body').append('<a></a>');
                    $('<form></form>').attr({
                        action: 'javascript:void(0)',
                        class: 'pdf-editor-form-input'
                    }).appendTo(configsClass);

                    // Layout Name
                    $('<label></label>')
                        .attr('for', 'layout_name')
                        .text('Nama Layout: ')
                        .appendTo(configsClass + ' form');
                    $('<input>').attr({
                        type: 'text',
                        name: 'layout_name',
                        id: 'layoutName',
                        value: urlParams[2]
                    }).appendTo(configsClass + ' form');
                    // Layout Orientation
                    $('<label></label>')
                        .attr('for', 'layout_orientation')
                        .text('Orientasi Layout: ')
                        .appendTo(configsClass + ' form');
                    $('<select></select>').attr({
                        name: 'layout_orientation',
                        id: 'orientation'
                    }).change(function () {
                        pdfPageSetup['orientation'] = $(configsClass + ' form #orientation').val();
                        $.ajax({
                            type: "POST",
                            url: baseURL() + '/upedtrdta', // update editor data
                            data: {
                                data: JSON.stringify({
                                    t: $.cookie('t'),
                                    layout_name: urlParams[2],
                                    data_name: 'pdf_page_setup',
                                    new_data: pdfPageSetup,
                                    save_data: false
                                })
                            },
                            dataType: "json",
                        }).done(function () {

                        }).fail(function () {

                        }).always(function (result) {
                            //console.log(result);
                            $('.pdf-editor-viewer').attr('src', baseURL() + '/pdf-editor/view/' + urlParams[2] + '/' + $.cookie('t'));
                        });
                    }).appendTo(configsClass + ' form');
                    $('<option></option>').attr('value', 'P').text('Potrait').appendTo(configsClass + ' form #orientation');
                    if (pdfPageSetup['orientation'] == 'P') {
                        $(configsClass + ' form #orientation option[value=P]').prop('selected', 'selected');
                    }
                    $('<option></option>').attr('value', 'L').text('Landscape').appendTo(configsClass + ' form #orientation');
                    if (pdfPageSetup['orientation'] == 'L') {
                        $(configsClass + ' form #orientation option[value=L]').prop('selected', 'selected');
                    }
                    // Layout Unit
                    $('<label></label>')
                        .attr('for', 'layout_unit')
                        .text('Unit Layout: ')
                        .appendTo(configsClass + ' form');
                    $('<select></select>').attr({
                        name: 'layout_unit',
                        id: 'unit',
                        value: pdfPageSetup.unit
                    }).change(function () {
                        pdfPageSetup['unit'] = $(configsClass + ' form #unit').val();
                        $.ajax({
                            type: "POST",
                            url: baseURL() + '/upedtrdta', // update editor data
                            data: {
                                data: JSON.stringify({
                                    t: $.cookie('t'),
                                    layout_name: urlParams[2],
                                    data_name: 'pdf_page_setup',
                                    new_data: pdfPageSetup,
                                    save_data: false
                                })
                            },
                            dataType: "json",
                        }).done(function () {

                        }).fail(function () {

                        }).always(function (result) {
                            $('.pdf-editor-viewer').attr('src', baseURL() + '/pdf-editor/view/' + urlParams[2] + '/' + $.cookie('t'));
                        });
                    }).appendTo(configsClass + ' form');
                    $('<option></option>').attr('value', 'mm').text('mm').appendTo(configsClass + ' form #unit');
                    if (pdfPageSetup['unit'] == 'L') {
                        $(configsClass + ' form #unit option[value=mm]').prop('selected', 'selected');
                    }
                    $('<option></option>').attr('value', 'cm').text('cm').appendTo(configsClass + ' form #unit');
                    if (pdfPageSetup['unit'] == 'cm') {
                        $(configsClass + ' form #unit option[value=cm]').prop('selected', 'selected');
                    }

                    // Layout Format
                    $('<label></label>')
                        .attr('for', 'layout_format')
                        .text('Format Layout: ')
                        .appendTo(configsClass + ' form');
                    $('<select></select>').attr({
                        name: 'layout_format',
                        id: 'format',
                        value: pdfPageSetup.format
                    }).change(function () {
                        pdfPageSetup['format'] = $(configsClass + ' form #format').val();
                        $.ajax({
                            type: "POST",
                            url: baseURL() + '/upedtrdta', // update editor data
                            data: {
                                data: JSON.stringify({
                                    t: $.cookie('t'),
                                    layout_name: urlParams[2],
                                    data_name: 'pdf_page_setup',
                                    new_data: pdfPageSetup,
                                    save_data: false
                                })
                            },
                            dataType: "json",
                        }).done(function () {

                        }).fail(function () {

                        }).always(function (result) {
                            $('.pdf-editor-viewer').attr('src', baseURL() + '/pdf-editor/view/' + urlParams[2] + '/' + $.cookie('t'));
                        });
                    }).appendTo(configsClass + ' form');
                    $('<option></option>').attr('value', 'A4').text('A4').appendTo(configsClass + ' form #format');
                    if (pdfPageSetup['format'] == 'A4') {
                        $(configsClass + ' form #format option[value=A4]').prop('selected', 'selected');
                    }
                    $('<option></option>').attr('value', 'A5').text('A5').appendTo(configsClass + ' form #format');
                    if (pdfPageSetup['format'] == 'A5') {
                        $(configsClass + ' form #format option[value=A5]').prop('selected', 'selected');
                    }

                    for (; i < componentNames.length; i++) {
                        $('<div></div>').attr('id', componentNames[i]).appendTo(configsClass + ' form');
                        $('<label></label>').attr({
                            for: componentNames[i]
                        }).text(componentNamesHR[lang][componentNames[i]]).appendTo(configsClass + ' form #' + componentNames[i]);

                        subComponents = Object.getOwnPropertyNames(editorData[componentNames[i]]);

                        for (var j = 0; j < subComponents.length; j++) {
                            if (subComponents[j] == 'font_family' || subComponents[j].search('font_style') !== -1) {
                                $('<input>').attr({
                                    type: 'text',
                                    name: subComponents[j],
                                    class: 'configs-input',
                                    placeholder: subComponents[j],
                                    value: editorData[componentNames[i]][subComponents[j]],
                                }).change(function () {
                                    editorData[$(this).parent().attr('id')][$(this).attr('name')] = $(this).val();
                                    $.ajax({
                                        type: "POST",
                                        url: baseURL() + '/upedtrdta', // update editor data
                                        data: {
                                            data: JSON.stringify({
                                                t: $.cookie('t'),
                                                layout_name: urlParams[2],
                                                data_name: 'layout_data',
                                                new_data: editorData,
                                                save_data: false
                                            })
                                        },
                                        dataType: "json",
                                    }).done(function () {

                                    }).fail(function () {

                                    }).always(function (result) {
                                        $('.pdf-editor-viewer').attr('src', baseURL() + '/pdf-editor/view/' + urlParams[2] + '/' + $.cookie('t'));
                                    });
                                }).appendTo(configsClass + ' form #' + componentNames[i]);
                            } else {
                                $('<input>').attr({
                                    type: 'number',
                                    name: subComponents[j],
                                    class: 'configs-input',
                                    placeholder: subComponents[j],
                                    value: editorData[componentNames[i]][subComponents[j]],
                                }).change(function () {
                                    editorData[$(this).parent().attr('id')][$(this).attr('name')] = $(this).val();
                                    //console.log(editorData[$(this).parent().attr('id')][$(this).attr('name')]);
                                    $.ajax({
                                        type: "POST",
                                        url: baseURL() + '/upedtrdta', // update editor data
                                        data: {
                                            data: JSON.stringify({
                                                t: $.cookie('t'),
                                                layout_name: urlParams[2],
                                                data_name: 'layout_data',
                                                new_data: editorData,
                                                save_data: false
                                            })
                                        },
                                        dataType: "json",
                                    }).done(function () {

                                    }).fail(function () {

                                    }).always(function (result) {
                                        $('.pdf-editor-viewer').attr('src', baseURL() + '/pdf-editor/view/' + urlParams[2] + '/' + $.cookie('t'));
                                    });
                                }).appendTo(configsClass + ' form #' + componentNames[i]);
                            }
                        }
                    }

                    $('<button></button>').attr({
                        class: 'button configs-save-btn',
                        name: 'configs_save'
                    }).html('<i class="fa fa-save"></i> Simpan').appendTo(configsClass + ' form');

                    $('.configs-save-btn').click(function () {
                        var newLayoutName = $(configsClass + ' form #layoutName').val();
                        if (newLayoutName === urlParams[2]) {
                            newLayoutName = false;
                        }
                        $.ajax({
                            type: "POST",
                            url: baseURL() + '/upedtrdta', // update editor data
                            data: {
                                data: JSON.stringify({
                                    t: $.cookie('t'),
                                    layout_name: urlParams[2],
                                    data_name: 'pdf_layout_data',
                                    new_data: editorData,
                                    page_setup: pdfPageSetup,
                                    new_layname: newLayoutName,
                                    save_data: true
                                })
                            },
                            dataType: "json",
                        }).done(function () {

                        }).fail(function () {

                        }).always(function (result) {
                            if (result.status == 'success') {
                                $('.action-msg-notification').html('<p><i class="fa fa-check-circle"></i> ' + result.message + '</p>');
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
                                $('.pdf-editor-viewer').attr('src', baseURL() + '/pdf-editor/view/' + urlParams[2] + '/' + $.cookie('t'));
                            } else if (result.status === 'error') {
                                $('.action-msg-notification').html('<p><i class="fa fa-exclamation-circle"></i> ' + result.message + '</p>');
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
                            } else if (result.status === 'failed') {
                                $('.action-msg-notification').html('<p><i class="fa fa-exclamation-circle"></i> ' + result.message + '</p>');
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
                            }
                        });
                    });
                }
            }
        });
    }

    // check for update
    $('.check-for-update').click(function(event) {
        $('.check-for-update .fa').toggleClass('fa-circle-notch fa-spin');
        $('.check-for-update').css('background', '#8f8f8f');
        var
            csrf_token = $.cookie('t');
        $.ajax({
                url: baseURL() + '/check_for_update/' + csrf_token,
            })
            .done(function() {
                //console.log("success");
            })
            .fail(function() {
                //console.log("error");
            })
            .always(function(result) {
                //console.log("complete");
                result = result.split('-');
                if (result[0] === 'true') {
                    $('.check-for-update .fa').toggleClass('fa-circle-notch fa-spin');
                    $('.check-for-update').css('background', 'linear-gradient(90deg, #26C959, #23B650)');
                    var
                        confirm_todown = confirm('Pembaharuan Ditemukan ->' + result[1] + ' | ' + result[2] + ',  apakah anda ingin memperbaharui aplikasi?'),
                        app_ver = result[1].split(':');
                    app_ver = app_ver[1];
                    if (confirm_todown === true) {
                        $('.update-dialogue-box').css('display', 'block');
                        $('.update-dialogue-box .process-log .refresh').before("<p><i class=\"fa fa-chevron-right\"></i> Downloading......</p>");
                        $.ajax({
                                url: baseURL() + '/download_new_update/' + csrf_token + '/' + app_ver,
                                type: 'POST',
                                data: {
                                    file_size: result[2]
                                }
                            })
                            .done(function() {
                                //console.log("success");
                            })
                            .fail(function() {
                                //console.log("error");
                            })
                            .always(function(result) {
                                if (result.search('-') !== -1) {
                                    result = result.split('-');
                                    var msg = result[1];
                                    result = result[0];
                                }
                                //console.log("complete");
                                if (result == 'complete') {
                                    $('.update-dialogue-box .process-log .refresh').before("<p><i class=\"fa fa-check-circle\"></i> Download Complete</p>");
                                    $('.update-dialogue-box .process-log .refresh').before("<p><i class=\"fa fa-chevron-right\"></i> Extracting......</p>");
                                    $.ajax({
                                            url: baseURL() + '/extract_new_update/' + csrf_token + '/' + app_ver + '/asmp-test',
                                        })
                                        .done(function() {
                                            //console.log("success");
                                        })
                                        .fail(function() {
                                            //console.log("error");
                                        })
                                        .always(function(result) {
                                            //console.log("complete");
                                            if (result.search('-') !== -1) {
                                                result = result.split('-');
                                                var msg = result[1];
                                                result = result[0];
                                            }

                                            if (result == 'complete') {
                                                $('.update-dialogue-box .process-log .refresh').before("<p><i class=\"fa fa-check-circle\"></i> Extract Complete</p>");
                                                $('.update-dialogue-box .process-log .refresh').before("<p><i class=\"fa fa-chevron-right\"></i> Memvalidasi Versi Terbaru......</p>");
                                                $.ajax({
                                                        url: baseURL() + '/validate_new_version/' + csrf_token + '/' + app_ver,
                                                    })
                                                    .done(function() {
                                                        //console.log("success");
                                                    })
                                                    .fail(function() {
                                                        //console.log("error");
                                                    })
                                                    .always(function(result) {
                                                        //console.log("complete");
                                                        if (result == 'complete') {
                                                            $('.update-dialogue-box .process-log .refresh').before("<p><i class=\"fa fa-check-circle\"></i> Update Complete</p>");
                                                            $('.update-dialogue-box .process-log .refresh').css('display', 'block');
                                                        } else if (result == 'failed') {
                                                            $('.update-dialogue-box .process-log .refresh').before("<p><i class=\"fa fa-times-circle\"></i> Validasi Gagal, Versi Bukan Versi Terbaru</p>");
                                                            $('.update-dialogue-box .process-log .refresh').css('display', 'block');
                                                        }
                                                    });

                                            } else if (result == 'failed') {
                                                $('.update-dialogue-box .process-log .refresh').before("<p><i class=\"fa fa-times-circle\"></i> Extracting Failed</p>");
                                            } else if (result == 'error') {
                                                $('.update-dialogue-box .process-log .refresh').before("<p><i class=\"fa fa-times-circle\"></i> Error: " + msg + "</p>");
                                                $('.update-dialogue-box .process-log .refresh').css('display', 'block');
                                            }
                                        });

                                } else if (result == 'failed') {
                                    if (!msg) {
                                        $('.update-dialogue-box .process-log .refresh').before("<p><i class=\"fa fa-times-circle\"></i> Downloading Failed</p>");
                                    } else {
                                        $('.update-dialogue-box .process-log .refresh').before("<p><i class=\"fa fa-times-circle\"></i> " + msg + "</p>");
                                    }
                                }
                            });

                    }
                } else if (result[0] === 'false') {
                    $('.check-for-update .fa').toggleClass('fa-circle-notch fa-spin');
                    $('.check-for-update').css('background', 'linear-gradient(90deg, #26C959, #23B650)');
                    alert('Versi Aplikasi Anda Adalah Versi Terbaru');
                } else {
                    $('.check-for-update .fa').toggleClass('fa-circle-notch fa-spin');
                    $('.check-for-update').css('background', 'linear-gradient(90deg, #26C959, #23B650)');
                    alert('Tidak Ada Hasil');
                }
            });

    });

    $('.changelogs-container-close-btn').click(function(event) {
        $('.changelogs-container').css('display', 'none');
        var
            csrf_token = $('.check-for-update').data('token');
        $.ajax({
                url: baseURL() + '/close_changelogs/' + csrf_token,
            })
            .done(function() {
                console.log("success");
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });

    });
});