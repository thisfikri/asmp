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
            $('<label></label>').attr('for', 'pdf_layout').text('PDF Layout:')
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
                    class: 'add-om-submit save'
                }).html('<i class="fa fa-save"></i> Simpan').click(function () {
                    $.ajax({
                        type: "POST",
                        url: baseURL() + '/user/om_cmd_exec',
                        data: JSON.stringify({
                            data: {
                                action: 'save',
                                om_data: Formdata,
                                t: $.cookie('t')
                            }
                        }),
                        dataType: "json",
                    }).done(function () {

                    }).fail(function () {

                    }).always(function (result) {

                    });
                })
                .appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form');

            // button send
            $('<button></button>').attr({
                    type: 'submit',
                    name: 'send_om',
                    class: 'add-om-submit send'
                }).html('<i class="fa fa-paper-plane"></i> Kirim').click(function () {
                    $.ajax({
                        type: "POST",
                        url: baseURL() + '/user/om_action_exec',
                        data: JSON.stringify({
                            data: {
                                action: 'send',
                                om_data: formdata,
                                t: $.cookie('t')
                            }
                        }),
                        dataType: "json",
                    }).done(function () {

                    }).fail(function () {

                    }).always(function (result) {

                    });
                })
                .appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form');
        } else if (modal_type === 'edit') {
            $.ajax({
                type: "POST",
                url: baseURL() + '/user/om_action_exec',
                data: JSON.stringify({
                    data: {
                        action: 'load',
                        om_data: formdata,
                        t: $.cookie('t')
                    }
                }),
                dataType: "json",
            }).done(function () {

            }).fail(function () {

            }).always(function (result) {
                var omData = result.omData;
                // 2nd layer
                $('<div></div>').attr({
                    class: 'modal2ndlayer'
                }).appendTo(containerSelector);

                // modal form
                $('<form></form>').attr({
                    action: 'javascript:void(0)',
                    method: 'post',
                    class: 'mail-modal-form'
                }).appendTo(containerSelector + ' .modal2ndlayer');

                // pdf layout selection
                $('<label></label>').attr('for', 'pdf_layout').text('PDF Layout:')
                    .appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input');
                $('<select></select>').attr({
                    name: 'pdf_layout'
                }).appendTo(containerSelector + ' .modal2ndlayer');
                for (var i = 0; i < pdfLayouts.length; i++) {
                    $('<option></option>').attr({
                            value: pdfLayouts[i]
                        }).text(pdfLayouts[i].replace('_', ' ').toUpperCase())
                        .appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input select[name=pdf_layouts]');
                    if (omData.pdfLayout == pdfLayouts[i]) {
                        $(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input select[name=pdf_layouts] option').eq(i)
                            .prop('selected', 'selected');
                    }
                }

                // mail number
                $('<label></label>').attr('for', 'mail_number').text('Nomor Surat:')
                    .appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input');
                $('<input>').attr({
                    type: 'text',
                    name: 'mail_number',
                    value: omData.mailNumber,
                    placeholder: 'Ex: XII/M2/19'
                }).appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input');

                // mail subject
                $('<label></label>').attr('for', 'mail_subject').text('Perihal:')
                    .appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input');
                $('<input>').attr({
                    type: 'text',
                    name: 'mail_subject',
                    value: omData.mailSubject,
                    placeholder: 'Ex: Rapat Umum'
                }).appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input');

                // // mail contents
                // $('<label></label>').attr('for', 'mail_contents').text('Isi Surat:')
                //     .appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input');
                // $('<textarea></textarea>').attr({
                //     name: 'mail_contents',
                //     class: 'widgEditor',
                //     id: 'mailContentsEditor',
                //     value: omData.mailContents,
                //     style: 'display: none'
                // }).appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form');

                // // hidden input
                // $('<input>').attr({
                //     type: 'hidden',
                //     name: 'editor_data',
                //     value: 'no_data'
                // }).appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form');

                // button save
                $('<button></button>').attr({
                        type: 'submit',
                        name: 'save_om',
                        class: 'add-om-submit save'
                    }).html('<i class="fa fa-save"></i> Simpan')
                    .appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form');
            });
        }
        $(containerSelector).css('display', 'block');
    }

    $('#addOm').click(function () {
        omModalGerator('add', '.add-om-modal');
        $(this).attr('disabled', 'true');
    });

    $('.casual-theme.add-om-modal .modal2ndlayer .close-btn').click(function() {
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