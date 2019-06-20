$(document).ready(function () {

    // -- Get Base URL --
    function baseURL() {
        var
            baseURL = window.location.origin + '/' + window.location.pathname.split('/')[1];
        return baseURL;
    }

    console.log(baseURL());

    /**
     * Modal for outgoing mail
     */
    function omModalGerator(modal_type = 'add', containerSelector) {
        var
            itemID = $(containerSelector).attr('id'),
            pdfLayouts = $(containerSelector).data('pdflayouts').split(',');
        $(containerSelector).css('display', 'block');
        if (modal_type === 'add') {
            // 2nd layer
            // $('<div></div>').attr({
            //     class: 'modal2ndlayer'
            // }).appendTo(containerSelector);

            // modal form
            // $('<form></form>').attr({
            //     action: 'javascript:void(0)',
            //     method: 'post',
            //     class: 'mail-modal-form'
            // }).appendTo(containerSelector + ' .modal2ndlayer');

            // pdf layout selection
            $('<label></label>').attr('for', 'pdf_layouts').text('PDF Layout:')
                .appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input');
            $('<select></select>').attr({
                name: 'pdf_layouts'
            }).appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input');
            for (var i = 0; i < pdfLayouts.length; i++) {
                console.log(pdfLayouts[i].replace('_', ' ').toUpperCase());
                $('<option></option>').attr({
                        value: pdfLayouts[i]
                    }).text(pdfLayouts[i].replace('_', ' ').toUpperCase())
                    .appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input select[name=pdf_layouts]');
            }

            // mail number
            $('<label></label>').attr('for', 'mail_number').text('Nomor Surat:')
                .appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input');
            $('<input>').attr({
                type: 'text',
                name: 'mail_number',
                placeholder: 'Ex: XII/M2/19'
            }).appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input');

            // mail subject
            $('<label></label>').attr('for', 'mail_subject').text('Perihal:')
                .appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input');
            $('<input>').attr({
                type: 'text',
                name: 'mail_subject',
                placeholder: 'Ex: Rapat Umum'
            }).appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input');

            // // mail contents
            // $('<label></label>').attr('for', 'mail_contents').text('Isi Surat:')
            //     .appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input');
            // $('<textarea></textarea>').attr({
            //     name: 'mail_contents',
            //     class: 'widgEditor',
            //     id: 'mailContentsEditor',
            //     style: 'display: none'
            // }).appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input');

            // // hidden input
            // $('<input>').attr({
            //     type: 'hidden',
            //     name: 'editor_data',
            //     value: 'no_data'
            // }).appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input');

            // button save
            $('<button></button>').attr({
                    type: 'submit',
                    name: 'save_om',
                    class: 'button add-om-submit save'
                }).html('<i class="fa fa-save"></i> Simpan').click(function () {
                    let
                        form = $(containerSelector + ' .modal2ndlayer .mail-modal-form'),
                        fdata = {},
                        i = 0,
                        iframeData = $("#mailContentsEditorWidgIframe").contents().find('body').html();

                    $("#mailContentsEditor").val(iframeData);
                    $(containerSelector + ' .modal2ndlayer .mail-modal-form input[name=editor_data]').val(iframeData);
                    for (; i < form[0].length; i++) {
                        fdata[form[0][i].name] = form[0][i].value;
                    }

                    $.ajax({
                        type: "POST",
                        url: baseURL() + '/user/om_action_exec',
                        data: {
                            request_data: JSON.stringify({
                                action: 'save',
                                om_data: fdata,
                                t: $.cookie('t')
                            })
                        },
                        dataType: "json",
                    }).done(function () {

                    }).fail(function () {

                    }).always(function (result) {
                        if (result.status == 'success') {
                            var pgnt = new Pagination(result.data.user_setting['row_limit'], '.item', 'page-link', '.pagination');
                            var hideElement = '';
                            var atrgr = new ActionTrigger();
                            var id = 0;
                            var itemData = JSON.stringify(result.data);
                            id = $('.table-container#outgoingMail .item-list tbody .item').length + 1;
                            // tambahkan item lenght untuk fungsi pagniate
                            if ($(pgnt.itemToPaginate).not(pgnt.itemToPaginate + '.hide').length == pgnt.itemLimit) {
                                hideElement = ' hide';
                            }
                            $('.table-container#outgoingMail .item-list tbody').append('<tr class="item id' + id + hideElement + '"><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
                            $('.table-container#outgoingMail .item-list tbody tr.item.id' + id).attr('data-itemdata', itemData);
                            $('.table-container#outgoingMail .item-list tbody .item.id' + id + ' td').eq(0).html('<input type="checkbox" class="checkbox item' + id + '"><span class="checkmark item' + id + '"><i class="fa fa-check"></i></span>');
                            $('.table-container#outgoingMail .item-list tbody .item.id' + id + ' td').eq(1).text(result.data['mail_number']);
                            $('.table-container#outgoingMail .item-list tbody .item.id' + id + ' td').eq(2).text(result.data['subject']);
                            $('.table-container#outgoingMail .item-list tbody .item.id' + id + ' td').eq(3).text(result.data['sender']);
                            $('.table-container#outgoingMail .item-list tbody .item.id' + id + ' td').eq(4).html(result.data['status']);
                            $('.table-container#outgoingMail .item-list tbody .item.id' + id + ' td').eq(5).text(result.data['date']);
                            $('.table-container#outgoingMail .item-list tbody tr.item.id' + id + ' td').eq(6).append(
                                '<button class="button action-btn view" id="item' + id + '"><i class="fa fa-eye"></i></button>'
                            );
                            $('.table-container#outgoingMail .item-list tbody tr.item.id' + id + ' td').eq(6).append(
                                '<button class="button action-btn trash" id="item' + id + '"><i class="fa fa-trash"></i></button>'
                            );
                            atrgr.defineTrigger('checkboxAction' + id, 'checkbox_action');
                            atrgr.defineTrigger('viewMail' + id, 'view_mail');
                            atrgr.defineTrigger('throwMailToTrashOM' + id, 'throw_mail_tt');
                            atrgr['checkboxAction' + id](id);
                            atrgr['throwMailToTrashOM' + id](id, 'om', result.data);
                            atrgr['viewMail' + id](id);
                            if (result.data.user_setting['paging_status'] !== 1) {
                                paginate = pgnt.updatePgNav();
                            }

                            $('.add-om-modal').css('display', 'none');
                            $('#addOm').removeAttr('disabled');
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input input').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input select').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input label').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .mail-modal-form  button').remove();

                            $('.action-msg-notification').html('<p>' + result.message + '</p>');
                            $('.action-msg-notification').removeClass('error');
                            $('.action-msg-notification').removeClass('failed');
                            $('.action-msg-notification').removeClass('warning');
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
                            $('.add-om-modal').css('display', 'none');
                            $('#addOm').removeAttr('disabled');
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input input').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input select').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input label').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .mail-modal-form  button').remove();

                            $('.action-msg-notification').html('<p>' + result.message + '</p>');
                            $('.action-msg-notification').removeClass('success');
                            $('.action-msg-notification').removeClass('warning');
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
                            $('.add-om-modal').css('display', 'none');
                            $('#addOm').removeAttr('disabled');
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input input').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input select').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input label').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .mail-modal-form  button').remove();

                            $('.action-msg-notification').html('<p>' + result.message + '</p>');
                            $('.action-msg-notification').removeClass('success');
                            $('.action-msg-notification').removeClass('warning');
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
                        } else if (result.status == 'warning') {
                            $('.add-om-modal').css('display', 'none');
                            $('#addOm').removeAttr('disabled');
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input input').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input select').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input label').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .mail-modal-form  button').remove();

                            $('.action-msg-notification').html('<p>' + result.message + '</p>');
                            $('.action-msg-notification').removeClass('success');
                            $('.action-msg-notification').removeClass('failed');
                            $('.action-msg-notification').removeClass('error');
                            $('.action-msg-notification').addClass('warning');
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
                })
                .appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form');

            // button send
            $('<button></button>').attr({
                    type: 'submit',
                    name: 'send_om',
                    class: 'button add-om-submit send'
                }).html('<i class="fa fa-paper-plane"></i> Kirim').click(function () {
                    let
                        form = $(containerSelector + ' .modal2ndlayer .mail-modal-form'),
                        fdata = {},
                        i = 0,
                        iframeData = $("#mailContentsEditorWidgIframe").contents().find('body').html();

                    $("#mailContentsEditor").val(iframeData);
                    $(containerSelector + ' .modal2ndlayer .mail-modal-form input[name=editor_data]').val(iframeData);
                    for (; i < form[0].length; i++) {
                        fdata[form[0][i].name] = form[0][i].value;
                    }
                    $.ajax({
                        type: "POST",
                        url: baseURL() + '/user/om_action_exec',
                        data: {
                            request_data: JSON.stringify({
                                action: 'send',
                                om_data: fdata,
                                t: $.cookie('t')
                            })
                        },
                        dataType: "json",
                    }).done(function () {

                    }).fail(function () {

                    }).always(function (result) {
                        if (result.status == 'success') {
                            var pgnt = new Pagination(result.data.user_setting['row_limit'], '.item', 'page-link', '.pagination');
                            var hideElement = '';
                            var atrgr = new ActionTrigger();
                            var id = 0;
                            var itemData = JSON.stringify(result.data);
                            id = $('.table-container#outgoingMail .item-list tbody .item').length + 1;
                            // tambahkan item lenght untuk fungsi pagniate
                            if ($(pgnt.itemToPaginate).not(pgnt.itemToPaginate + '.hide').length == pgnt.itemLimit) {
                                hideElement = ' hide';
                            }
                            $('.table-container#outgoingMail .item-list tbody').append('<tr class="item id' + id + hideElement + '"><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
                            $('.table-container#outgoingMail .item-list tbody tr.item.id' + id).attr('data-itemdata', itemData);
                            $('.table-container#outgoingMail .item-list tbody .item.id' + id + ' td').eq(0).html('<input type="checkbox" class="checkbox item' + id + '"><span class="checkmark item' + id + '"><i class="fa fa-check"></i></span>');
                            $('.table-container#outgoingMail .item-list tbody .item.id' + id + ' td').eq(1).text(result.data['mail_number']);
                            $('.table-container#outgoingMail .item-list tbody .item.id' + id + ' td').eq(2).text(result.data['subject']);
                            $('.table-container#outgoingMail .item-list tbody .item.id' + id + ' td').eq(3).text(result.data['sender']);
                            $('.table-container#outgoingMail .item-list tbody .item.id' + id + ' td').eq(4).html(result.data['status']);
                            $('.table-container#outgoingMail .item-list tbody .item.id' + id + ' td').eq(5).text(result.data['date']);
                            $('.table-container#outgoingMail .item-list tbody tr.item.id' + id + ' td').eq(6).append(
                                '<button class="button action-btn view" id="item' + id + '"><i class="fa fa-eye"></i></button>'
                            );
                            $('.table-container#outgoingMail .item-list tbody tr.item.id' + id + ' td').eq(6).append(
                                '<button class="button action-btn trash" id="item' + id + '"><i class="fa fa-trash"></i></button>'
                            );
                            atrgr.defineTrigger('checkboxAction' + id, 'checkbox_action');
                            atrgr.defineTrigger('viewMail' + id, 'view_mail');
                            atrgr.defineTrigger('throwMailToTrashOM' + id, 'throw_mail_tt');
                            atrgr['checkboxAction' + id](id);
                            atrgr['throwMailToTrashOM' + id](id, 'om', result.data);
                            atrgr['viewMail' + id](id);
                            if (result.data.user_setting['paging_status'] !== 1) {
                                paginate = pgnt.updatePgNav();
                            }

                            $('.add-om-modal').css('display', 'none');
                            $('#addOm').removeAttr('disabled');
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input input').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input select').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input label').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .mail-modal-form  button').remove();

                            $('.action-msg-notification').html('<p>' + result.message + '</p>');
                            $('.action-msg-notification').removeClass('error');
                            $('.action-msg-notification').removeClass('failed');
                            $('.action-msg-notification').removeClass('warning');
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
                            $('.add-om-modal').css('display', 'none');
                            $('#addOm').removeAttr('disabled');
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input input').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input select').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input label').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .mail-modal-form  button').remove();

                            $('.action-msg-notification').html('<p>' + result.message + '</p>');
                            $('.action-msg-notification').removeClass('success');
                            $('.action-msg-notification').removeClass('warning');
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
                            $('.add-om-modal').css('display', 'none');
                            $('#addOm').removeAttr('disabled');
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input input').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input select').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input label').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .mail-modal-form  button').remove();

                            $('.action-msg-notification').html('<p>' + result.message + '</p>');
                            $('.action-msg-notification').removeClass('success');
                            $('.action-msg-notification').removeClass('warning');
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
                        } else if (result.status == 'warning') {
                            $('.add-om-modal').css('display', 'none');
                            $('#addOm').removeAttr('disabled');
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input input').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input select').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .form-input label').remove();
                            $('.casual-theme.add-om-modal .modal2ndlayer .mail-modal-form  button').remove();

                            $('.action-msg-notification').html('<p>' + result.message + '</p>');
                            $('.action-msg-notification').removeClass('success');
                            $('.action-msg-notification').removeClass('failed');
                            $('.action-msg-notification').removeClass('error');
                            $('.action-msg-notification').addClass('warning');
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
                })
                .appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form');
        } 
        $(containerSelector).css('display', 'block');
    }

    $('#addOm').click(function () {
        omModalGerator('add', '.add-om-modal');
        $(this).attr('disabled', 'true');
    });

    $('.casual-theme.add-om-modal .modal2ndlayer .close-btn').click(function () {
        $('.add-om-modal').css('display', 'none');
        $('#addOm').removeAttr('disabled');
        $('.casual-theme.add-om-modal .modal2ndlayer .form-input input').remove();
        $('.casual-theme.add-om-modal .modal2ndlayer .form-input select').remove();
        $('.casual-theme.add-om-modal .modal2ndlayer .form-input label').remove();
        $('.casual-theme.add-om-modal .modal2ndlayer .mail-modal-form    button').remove();

    });

    /**
     * Modal for incoming mail
     */
    function imModalGenerator(containerSelector) {

    }
});