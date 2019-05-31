$(document).ready(function () {
    'use strict';

    // -- Get Base URL --
    function baseURL() {
        var
            curr_url = window.location.href,
            sburl = curr_url.split('/'),
            base_url = '';
        base_url = sburl[0] + '//' + sburl[2] + '/' + sburl[3];
        return base_url;
    }

    function fileSizeConvert(fileSize, sizeType) {
        var
            dataSizeList = {
                B: 1,
                KB: 1024,
                MB: 1024 * 1024,
                GB: 1024 * 1024 * 1024,
                TB: 1024 * 1024 * 1024 * 1024
            }

        switch (sizeType.toUpperCase()) {
            case 'B':
                return Math.ceil(fileSize / dataSizeList.B);
                break;
            case 'KB':
                return Math.ceil(fileSize / dataSizeList.KB);
                break;
            case 'MB':
                return Math.ceil(fileSize / dataSizeList.MB);
                break;
            case 'GB':
                return Math.ceil(fileSize / dataSizeList.GB);
                break;
            case 'TB':
                return Math.ceil(fileSize / dataSizeList.TB);
                break;
            default:
                return 'unknown size type';
                break;
        }
    }

    function getRandomInt(min, max) {
        min = Math.ceil(min);
        max = Math.floor(max);
        return Math.floor(Math.random() * (max - min)) + min; //The maximum is exclusive and the minimum is inclusive
    }

    function getRandomString() {
        var
            characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            charactersLenght = characters.length,
            randomString = '',
            i = 0;
        for (; i < 32; i++) {
            randomString += characters[getRandomInt(0, charactersLenght - 1)];
        }
        return randomString;
    }

    // Photos Box Open Btn
    $('.button.choose-btn').click(function () {
        $('.photos-box-container').css('display', 'block');
    });

    //Photos Box Close Btn
    $('.photos-box-close-btn').click(function () {
        $('.photos-box-container').css('display', 'none');
        $('.uploaded-images .image-container').css('display', 'none');
        $('.image-list .image-container').css('display', 'none');
    });

    //Photos Box Tabs
    $('.option-tab#upload').click(function () {
        $('.option-tab#gallery').removeClass('current');
        $(this).addClass('current');
        $('.upload-container').removeClass('hide');
        $('.gallery-container').addClass('hide');
        $('.image-list .image-container').removeClass('checked');
        $('.image-list .image-container').addClass('unchecked');
        $('.image-list .image-container .checklist').remove();
        $('.image-list .image-container').remove();
        $('.select-all-btn').data('tab', 'upld');
        $('.choose-image-btn').data('checkedimg', 0);
        if ($('.choose-image-btn').data('checkedimg') == 0) {
            $('.choose-image-btn').attr('disabled', 'true');
        }
    });

    $('.option-tab#gallery').click(function () {
        var
        usefulapi = new ASMPUsefulAPI(),
        fullUrl = usefulapi.getFullURL();
        $('.option-tab#upload').removeClass('current');
        $(this).addClass('current');
        $('.upload-container').addClass('hide');
        $('.gallery-container').removeClass('hide');
        $('.uploaded-images .image-container').removeClass('checked');
        $('.uploaded-images .image-container').addClass('unchecked');
        $('.uploaded-images .image-container .checklist').remove();
        $('.select-all-btn').data('tab', 'glry');
        $('.choose-image-btn').data('checkedimg', 0);
        if ($('.choose-image-btn').data('checkedimg') == 0) {
            $('.choose-image-btn').attr('disabled', 'true');
        }
        $.ajax({
                url: baseURL() + '/' + fullUrl[0] + '/image_upload',
                type: 'GET',
                dataType: 'json',
            })
            .done(function () {
                console.log("success");
            })
            .fail(function () {
                console.log("error");
            })
            .always(function (result) {
                console.log("complete");
                if (result !== null) {
                    $.each(result.files, function (index, image) {
                        image.name = image.name.split('_');
                        image.name = image.name[1];
                        $('<div/>').attr({
                            class: 'image-container unchecked',
                            id: 'glry' + index
                        }).appendTo('.gallery-container .image-list');
                        $('<img/>').attr({
                            src: image.url,
                            alt: image.name,
                            class: 'image'
                        }).appendTo('.gallery-container .image-list .image-container#glry' + index);
                        glryActionChecklist(index, image.url);
                    });
                }
            });
    });

    //Upload Button
    $('#fileUpload').change(function (e) {
        console.log(e.target.files);
        var
            file = [],
            i = 0,
            fileCount = e.target.files.length;
        if (fileCount > 1) {
            for (; i < fileCount; i++) {
                file[i] = e.target.files[i].name;
            }
            console.log(file);
            $('.file-name-txt').text(file.join(', '));
            $(this).attr('title', $('.file-name-txt').text());
        } else if (fileCount == 1) {
            $('.file-name-txt').text(e.target.files[0].name);
        }
    });

    //Image Delete Action
    var
        hasChackedUpldImg = '',
        hasChackedGlryImg = '',
        hasDelUrlUpldImg = '',
        hasDelUrlGlryImg = '';

    $('.delete-image-btn').click(function () {
        var
            usefulapi = new ASMPUsefulAPI(),
            fullUrl = usefulapi.getFullURL(),
            i = 0,
            t = $.cookie('t');
        if (hasChackedUpldImg.search(',') !== -1) {
            hasChackedUpldImg = hasChackedUpldImg.split(',');
            hasDelUrlUpldImg = hasDelUrlUpldImg.split(',');
            if (hasChackedUpldImg.length > 0) {
                for (; i < hasChackedUpldImg.length - 1; i++) {
                    $('.image-container#upld' + hasChackedUpldImg[i]).remove();
                    $.ajax({
                            url: baseURL() + '/' + fullUrl[0] + '/image_upload/?file=' + hasDelUrlUpldImg[i] + '&t=' + t,
                            type: 'DELETE',
                            dataType: 'json',

                        })
                        .done(function () {})
                        .fail(function (result) {})
                        .always(function (result) {
                            if (result.status == 'warning') {
                                alert(result.message);
                            } else if (result[hasDelUrlUpldImg[i]]) {
                                console.log(hasDelUrlUpldImg[i] + ' Has Deleted');
                            }
                        });
                }
                hasChackedUpldImg = '';
                hasDelUrlUpldImg = '';
                i = 0;
            }
        }

        if (hasChackedGlryImg.search(',') !== -1) {
            hasChackedGlryImg = hasChackedGlryImg.split(',');
            hasDelUrlGlryImg = hasDelUrlGlryImg.split(',');
            if (hasChackedGlryImg.length > 0) {
                for (; i < hasChackedGlryImg.length - 1; i++) {
                    $('.image-container#glry' + hasChackedGlryImg[i]).remove();
                    $.ajax({
                            url: baseURL() + '/' + fullUrl[0] + '/image_upload/?file=' + hasDelUrlGlryImg[i] + '&t=' + t,
                            type: 'DELETE',
                            dataType: 'json',

                        })
                        .done(function () {})
                        .fail(function (result) {})
                        .always(function (result) {
                            if (result.status == 'warning') {
                                alert(result.message);
                            } else if (result[hasDelUrlGlryImg[i]]) {
                                console.log(hasDelUrlGlryImg[i] + ' Has Deleted');
                            }
                        });
                }
                hasChackedGlryImg = '';
                hasDelUrlGlryImg = '';
                i = 0;
            }
        }
    });

    //Uploaded Photos Action Checklist
    function upldActionChecklist(index, deleteUrl) {
        $('.uploaded-images .image-container#upld' + index).click(function () {
            if ($(this).attr('class').search('unchecked') !== -1) {
                $(this).append('<div class="checklist"><i class="fa fa-check-circle"></i></div>');
                $(this).removeClass('unchecked');
                $(this).addClass('checked');
                hasChackedUpldImg += index + ',';
                deleteUrl = deleteUrl.split('/');
                deleteUrl = deleteUrl[deleteUrl.length - 1];
                hasDelUrlUpldImg += deleteUrl + ',';
                var
                    checkedImg = $('.choose-image-btn').data('checkedimg');
                $('.choose-image-btn').data('checkedimg', checkedImg + 1);
                checkedImg = $('.choose-image-btn').data('checkedimg');
                if (checkedImg == 1) {
                    $('.choose-image-btn').removeAttr('disabled');
                } else if (checkedImg == 0 || checkedImg > 1) {
                    $('.choose-image-btn').attr('disabled', 'true');
                }
            } else if ($(this).attr('class').search('checked') !== -1) {
                $('.uploaded-images .image-container#upld' + index + ' .checklist').remove();
                $(this).addClass('unchecked');
                $(this).removeClass('checked');
                var
                    checkedImg = $('.choose-image-btn').data('checkedimg');
                $('.choose-image-btn').data('checkedimg', checkedImg - 1);
                checkedImg = $('.choose-image-btn').data('checkedimg');
                if (checkedImg == 1) {
                    $('.choose-image-btn').removeAttr('disabled');
                } else if (checkedImg == 0 || checkedImg > 1) {
                    $('.choose-image-btn').attr('disabled', 'true');
                }
            }
            console.log($('.choose-image-btn').data('checkedimg'));
        });
    }

    //Gallery Photos Action Checklist
    function glryActionChecklist(index, deleteUrl) {
        $('.image-list .image-container#glry' + index).click(function () {
            if ($(this).attr('class').search('unchecked') !== -1) {
                $(this).append('<div class="checklist"><i class="fa fa-check-circle"></i></div>');
                $(this).removeClass('unchecked');
                $(this).addClass('checked');
                hasChackedGlryImg += index + ',';
                deleteUrl = deleteUrl.split('/');
                deleteUrl = deleteUrl[deleteUrl.length - 1];
                hasDelUrlGlryImg += deleteUrl + ',';
                var
                    checkedImg = $('.choose-image-btn').data('checkedimg');
                $('.choose-image-btn').data('checkedimg', checkedImg + 1);
                checkedImg = $('.choose-image-btn').data('checkedimg');
                if (checkedImg == 1) {
                    $('.choose-image-btn').removeAttr('disabled');
                } else if (checkedImg == 0 || checkedImg > 1) {
                    $('.choose-image-btn').attr('disabled', 'true');
                }
            } else if ($(this).attr('class').search('checked') !== -1) {
                $('.image-list .image-container#glry' + index + ' .checklist').remove();
                $(this).addClass('unchecked');
                $(this).removeClass('checked');
                var
                    checkedImg = $('.choose-image-btn').data('checkedimg');
                $('.choose-image-btn').data('checkedimg', checkedImg - 1);
                checkedImg = $('.choose-image-btn').data('checkedimg');
                if (checkedImg == 1) {
                    $('.choose-image-btn').removeAttr('disabled');
                } else if (checkedImg == 0 || checkedImg > 1) {
                    $('.choose-image-btn').attr('disabled', 'true');
                }
            }
        });
    }

    $('.choose-image-btn').click(function () {
        var
            usefulapi = new ASMPUsefulAPI(),
            fullUrl = usefulapi.getFullURL(),
            upldImage,
            glryImage,
            imageUrl,
            imageName;

        if (hasChackedUpldImg.search(',') !== -1) {
            upldImage = hasChackedUpldImg.split(',');
            if (upldImage.length <= 2 && upldImage.length !== 0) {
                imageUrl = $('.uploaded-images .image-container#upld' + upldImage[0] + ' .image').attr('src');
                imageName = $('.uploaded-images .image-container#upld' + upldImage[0] + ' .image').attr('alt');
                $('.profile-img img').attr('src', imageUrl);
                $.ajax({
                    type: "POST",
                    url: baseURL() + '/' + fullUrl[0] + '/update_profile_picture',
                    dataType: "json",
                    data: {
                        imageData: {
                            url: imageUrl,
                            name: imageName
                        },
                        t: $.cookie('t')
                    }
                }).done(function () {

                }).fail(function () {

                }).always(function (result) {
                    console.log(result);
                });
            }
        } else if (hasChackedGlryImg.search(',') !== -1) {
            glryImage = hasChackedGlryImg.split(',');
            if (glryImage.length <= 2 && glryImage.length !== 0) {
                imageUrl = $('.image-list .image-container#glry' + glryImage[0] + ' .image').attr('src');
                imageName = $('.image-list .image-container#glry' + glryImage[0] + ' .image').attr('alt');
                $('.profile-img img').attr('src', imageUrl);
                $.ajax({
                    type: "POST",
                    url: baseURL() + '/' + fullUrl[0] + '/update_profile_picture',
                    dataType: "json",
                    data: {
                        imageData: {
                            url: imageUrl,
                            name: imageName
                        },
                        t: $.cookie('t')
                    }
                }).done(function () {

                }).fail(function () {

                }).always(function (result) {
                    console.log(result);
                });
            }
        }

        hasChackedUpldImg = '';
        hasChackedGlryImg = '';
    });

    $('.select-all-btn').click(function () {
        var
            imageCount = 0,
            deleteUrl,
            i = 0;
        if ($(this).data('tab') == 'upld') {
            imageCount = $('.uploaded-images .image-container').length;
            for (; i < imageCount; i++) {
                if ($('.uploaded-images .image-container#upld' + i).children('div.checklist').attr('class') == 'checklist') {
                    $('.uploaded-images .image-container#upld' + i + ' .checklist').remove();
                    $('.uploaded-images .image-container#upld' + i).addClass('unchecked');
                    $('.uploaded-images .image-container#upld' + i).removeClass('checked');
                    hasChackedUpldImg = '';
                    hasDelUrlUpldImg = '';
                    var
                        checkedImg = $('.choose-image-btn').data('checkedimg');
                    $('.choose-image-btn').data('checkedimg', checkedImg - 1);
                    checkedImg = $('.choose-image-btn').data('checkedimg');
                    if (checkedImg == 1) {
                        $('.choose-image-btn').removeAttr('disabled');
                    } else if (checkedImg == 0 || checkedImg > 1) {
                        $('.choose-image-btn').attr('disabled', 'true');
                    }
                } else if ($('.uploaded-images .image-container#upld' + i).children('div.checklist').attr('class') == undefined) {
                    $('.uploaded-images .image-container#upld' + i).append('<div class="checklist"><i class="fa fa-check-circle"></i></div>');
                    $('.uploaded-images .image-container#upld' + i).removeClass('unchecked');
                    $('.uploaded-images .image-container#upld' + i).addClass('checked');
                    hasChackedUpldImg += i + ',';
                    deleteUrl = $('.uploaded-images .image-container#upld' + i + ' .image').attr('src').split('/');
                    deleteUrl = deleteUrl[deleteUrl.length - 1];
                    hasDelUrlUpldImg += deleteUrl + ',';
                    var
                        checkedImg = $('.choose-image-btn').data('checkedimg');
                    $('.choose-image-btn').data('checkedimg', checkedImg + 1);
                    checkedImg = $('.choose-image-btn').data('checkedimg');
                    if (checkedImg == 1) {
                        $('.choose-image-btn').removeAttr('disabled');
                    } else if (checkedImg == 0 || checkedImg > 1) {
                        $('.choose-image-btn').attr('disabled', 'true');
                    }
                }
            }
            console.log($('.choose-image-btn').data('checkedimg'));
        } else if ($(this).data('tab') == 'glry') {
            imageCount = $('.image-list .image-container').length;
            for (; i < imageCount; i++) {
                if ($('.image-list .image-container#glry' + i).children('div.checklist').attr('class') == 'checklist') {
                    $('.image-list .image-container#glry' + i + ' .checklist').remove();
                    $('.image-list .image-container#glry' + i).addClass('unchecked');
                    $('.image-list .image-container#glry' + i).removeClass('checked');
                    hasChackedGlryImg = '';
                    hasDelUrlGlryImg = '';
                    var
                        checkedImg = $('.choose-image-btn').data('checkedimg');
                    $('.choose-image-btn').data('checkedimg', checkedImg - 1);
                    checkedImg = $('.choose-image-btn').data('checkedimg');
                    if (checkedImg == 1) {
                        $('.choose-image-btn').removeAttr('disabled');
                    } else if (checkedImg == 0 || checkedImg > 1) {
                        $('.choose-image-btn').attr('disabled', 'true');
                    }
                } else if ($('.image-list .image-container#glry' + i).children('div.checklist').attr('class') == undefined) {
                    $('.image-list .image-container#glry' + i).append('<div class="checklist"><i class="fa fa-check-circle"></i></div>');
                    $('.image-list .image-container#glry' + i).removeClass('unchecked');
                    $('.image-list .image-container#glry' + i).addClass('checked');
                    hasChackedGlryImg += i + ',';
                    deleteUrl = $('.image-list .image-container#glry' + i + ' .image').attr('src').split('/');
                    deleteUrl = deleteUrl[deleteUrl.length - 1];
                    hasDelUrlGlryImg += deleteUrl + ',';
                    var
                        checkedImg = $('.choose-image-btn').data('checkedimg');
                    $('.choose-image-btn').data('checkedimg', checkedImg + 1);
                    checkedImg = $('.choose-image-btn').data('checkedimg');
                    if (checkedImg == 1) {
                        $('.choose-image-btn').removeAttr('disabled');
                    } else if (checkedImg == 0 || checkedImg > 1) {
                        $('.choose-image-btn').attr('disabled', 'true');
                    }
                }
            }
        }
    });

    // uploadhandler url
    var
        usefulapi = new ASMPUsefulAPI(),
        fullUrl = usefulapi.getFullURL(),
        url = baseURL() + '/' + fullUrl[0] + '/image_upload',
        eID = 0;
    $('#fileUpload').fileupload({
            url: url,
            dataType: 'json',
            singleFileUploads: false,
            add: function (e, data) {
                if ($('.upload-buttons .upload-btn').length === 1) {
                    $('.upload-buttons .upload-btn').eq(0).remove();
                }
                data.context = $('<button class="upload-btn"><i class="fa fa-upload"></i> Upload</button>').appendTo('.upload-buttons')
                    .click(function () {
                        console.log(data.files.length);
                        if (data.files.length > 1) {
                            eID = $('.uploaded-images .image-container').length;
                            $.each(data.files, function (index, file) {
                                index = eID + index;
                                if (file.name.search('_') !== -1) {
                                    file.name = file.name.replace(/(_)/g, '-');
                                }
                                file.uploadName = Math.abs(window.crypto.getRandomValues(new Int32Array(1))) + '_' + file.name;
                                $('<div/>').attr({
                                    class: 'image-container unchecked',
                                    id: 'upld' + index
                                }).appendTo('.uploaded-images');
                                $('<img/>').attr({
                                    src: baseURL() + '/assets/images/default-profile.png',
                                    alt: file.name,
                                    class: 'image'
                                }).appendTo('.uploaded-images .image-container#upld' + index);
                                $('<div/>').addClass('upload-progress').html('<div class="progress">0%</div>')
                                    .appendTo('.uploaded-images .image-container#upld' + index);
                            });
                        } else if (data.files.length == 1) {
                            var file = data.files[0],
                                fileName = file.name;
                                if (file.name.search('_') !== -1) {
                                    fileName = fileName.replace(/(_)/g, '-');
                                }
                                file.uploadName = Math.abs(window.crypto.getRandomValues(new Int32Array(1))) + '_' + fileName;
                            if ($('.uploaded-images .image-container').length !== 0) {
                                eID = $('.uploaded-images .image-container').length + 1;
                            }

                            console.log($('.uploaded-images .image-container').length);

                            $('<div/>').attr({
                                class: 'image-container unchecked',
                                id: 'upld' + eID
                            }).appendTo('.uploaded-images');
                            $('<img/>').attr({
                                src: baseURL() + '/assets/images/default-profile.png',
                                alt: file.name,
                                class: 'image'
                            }).appendTo('.uploaded-images .image-container#upld' + eID);
                            $('<div/>').addClass('upload-progress').html('<div class="progress">0%</div>')
                                .appendTo('.uploaded-images .image-container#upld' + eID);
                        }

                        data.submit();
                    });
            },
            done: function (e, data) {
                var imageUrl = [];
                console.log(data.result.files);
                if (data.result.files.length > 1) {
                    $.each(data.result.files, function (index, file) {
                        index = eID + index;
                        file.name = file.name.split('_');
                        file.name = file.name[1];
                        $('.uploaded-images .image-container#upld' + index + ' .image').attr('src', file.url);
                        $('.uploaded-images .image-container#upld' + index + ' .upload-progress').remove();
                        $('<p/>').addClass('image-caption').text(file.name).appendTo('.uploaded-images .image-container#upld' + index);
                        upldActionChecklist(index, file.url);
                        imageUrl[index] = file.url;
                    });
                } else if (data.result.files.length == 1) {
                    let index = 0,
                        file = data.result.files[0];
                        file.name = file.name.split('_');
                        file.name = file.name[1];
                    $('.uploaded-images .image-container#upld' + eID + ' .image').attr('src', file.url);
                    $('.uploaded-images .image-container#upld' + eID + ' .upload-progress').remove();
                    $('<p/>').addClass('image-caption').text(file.name).appendTo('.uploaded-images .image-container#upld' + eID);
                    upldActionChecklist(eID, file.url);
                    imageUrl = file.url;
                }
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);

                $('.upload-progress .progress').css('width', progress + '%');
                $('.upload-progress .progress').text(progress + '%');
            }
        }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
});