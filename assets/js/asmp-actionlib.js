/**
 * Useful API for ASMP application
 */

var checkedItemCount = 0;

// -- Get Base URL --
function baseURL() {
    var
        baseURL = window.location.origin + '/' + window.location.pathname.split('/')[1];
    return baseURL;
}

function ASMPUsefulAPI() {
    /**
     * check data type of value, if value is empty function will return false
     * @param dataType A data type to check
     * @param value A value for data type checking
     */
    this.dataTypeOf = function (dataType, value) {
        switch (dataType) {
            case 'string':
                if (typeof (value) == 'string') {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'number':
                if (typeof (value) == 'number') {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'integer':
                return Number.isInteger(value);
                break;
            case 'float':
                if (typeof (value) == 'number') {
                    if (Number.isSafeInteger(value) === false) {
                        return true
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
                break;
            case 'array':
                if (Array.isArray(value) == 'array') {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'object':
                if (typeof (value) == 'object') {
                    return true;
                } else {
                    return false;
                }
                break;
            default:
                'no data type found!';
        }
    }

    this.getFullURL = function () {
        var
            url = window.location.pathname,
            urlParams = url.split('/');
        return urlParams.slice(2, urlParams.length);
    }
}

/**
 * A object for execute ASMP Action
 */
function ASMPActionExecutor() {
    ASMPUsefulAPI.call(this);
    Object.defineProperties(this, {
        actionName: {
            set: function (val) {
                if (this.dataTypeOf('string', val)) {
                    this.actionNameVal = val;
                } else {
                    //console.log('action name must be a string!');
                }
            },
            get: function () {
                return this.actionNameVal;
            }
        },
        actionUrl: {
            set: function (val) {
                if (this.dataTypeOf('string', val)) {
                    this.actionUrlVal = val;
                } else {
                    //console.log('action name must be a string!');
                }
            },
            get: function () {
                return this.actionUrlVal;
            }
        },
        actionData: {
            set: function (val) {
                if (this.dataTypeOf('object', val)) {
                    this.actionDataVal = val;
                } else {
                    //console.log('action name must be a object!');
                }
            },
            get: function () {
                return this.actionDataVal;
            }
        },
        resultContainer: {
            set: function (val) {
                if (this.dataTypeOf('string', val)) {
                    this.resContainer = val;
                } else {
                    //console.log('action name must be a string!');
                }
            },
            get: function () {
                return this.resContainer;
            }
        },
        itemTarget: {
            set: function (val) {
                if (this.dataTypeOf('string', val)) {
                    this.itarget = val;
                } else {
                    //console.log('action name must be a string!');
                }
            },
            get: function () {
                return this.itarget;
            }
        }
    });

    this.getActionInfo = function () {
        //console.log('Action Name:' + this.actionName, ', Action Url:' + this.actionUrl, ', Action Data:' + JSON.stringify(this.actionData),
        //     ', Action Item Target:' + this.itemTarget
        // );
    }

    this.exec = function (settings = {
        multipleAction: false,
        haveID: false,
        removeTarget: false,
        mailFunction: {
            view: false,
            tableContainer: undefined,
            mailContainer: undefined
        }
    }) {
        //console.log('MultipleAction:', settings.multipleAction, 'HaveID:', settings.haveID, 'RemoveTarget:', settings.removeTarget);
        if (settings.multipleAction === true && settings.haveID === true && settings.removeTarget === true) {
            var itemID = this.actionData.id,
                itemTarget = this.itemTarget,
                elementMembers = $(itemTarget).not(itemTarget + '.hide'),
                isNN = [];
            $.ajax({
                type: "POST",
                url: this.actionUrl,
                data: this.actionData,
                dataType: "json",
            }).done(function () {

            }).fail(function () {

            }).always(function (result) {
                if (result.status !== 'error' && result.status !== 'failed') {
                    var
                        i = 0,
                        itemLength = itemID.length;
                    //console.log(itemID[0]);
                    for (; i < itemLength; i++) {
                        $(itemTarget + '.id' + itemID[i]).remove();
                        isNN[i] = Number.parseInt(itemID[i]);
                    }
                    //console.log(isNN, typeof (itemID[0]));
                    //console.log(itemID);
                    var pgnt = new Pagination(4, '.item', 'page-link', '.pagination');
                    if ($(pgnt.itemToPaginate).not(pgnt.itemToPaginate + '.hide').length !== 0) {
                        pgnt.itemSlideToUp(isNN, elementMembers, true, true);
                    }
                    pgnt.updatePgNav();
                    checkedItemCount = 0;
                    $('.multiple-action').data('mail-ids', '');
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
                } else if (result.status === 'error') {
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
                } else if (result.status === 'failed') {
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
                }
            });
        } else if (settings.haveID === true && settings.removeTarget === true) {
            var
                ID = this.actionData.id,
                iTarget = this.itemTarget,
                elementMembers = $(iTarget).not(iTarget + '.hide');
            $.ajax({
                type: "POST",
                url: this.actionUrl,
                data: this.actionData,
                dataType: "json",
            }).done(function () {

            }).fail(function () {

            }).always(function (result) {
                if (result.status !== 'error' && result.status !== 'failed') {
                    $(iTarget + '.id' + ID).remove();
                    //console.log(ID);
                    var pgnt = new Pagination(4, '.item', 'page-link', '.pagination');
                    if ($(pgnt.itemToPaginate).not(pgnt.itemToPaginate + '.hide').length !== 0) {
                        pgnt.itemSlideToUp(ID, elementMembers, true);
                    }
                    pgnt.updatePgNav(true);
                    checkedItemCount = 0;
                    $('.multiple-action').data('mail-ids', '');
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
                }
            });
        } else if (settings.multipleAction !== true && settings.mailFunction.view === true && settings.haveID === true) {
            let
                itemID = this.actionData.id,
                itemTarget = this.itemTarget,
                mailData = JSON.parse(this.actionData.mailData),
                mailType = window.location.href,
                mtFirstChar = '',
                i = 0,
                atrgr = new ActionTrigger();
            mailType = mailType.split('/');
            mailType = mailType[mailType.length - 1];
            mailType = mailType.split('-');
            for (; i < mailType.length; i++) {
                mtFirstChar = mailType[i].charAt(0).toUpperCase();
                mailType[i] = mailType[i].replace(mailType[i].charAt(0), mtFirstChar);
            }
            mailType = mailType.join(' ');
            if (settings.mailFunction.tableContainer !== undefined && settings.mailFunction.mailContainer !== undefined) {
                //$(settings.mailFunction.tableContainer).addClass('hide');

                $(settings.mailFunction.mailContainer).removeClass('hide');
                // mail top number
                $(settings.mailFunction.mailContainer + ' ' + itemTarget + ' .modal2ndlayer .mail-top-number')
                    .html('<p><b>' + mailData.id + '.' + mailType + '</b></p>');

                $(settings.mailFunction.mailContainer + ' ' + itemTarget + ' .modal2ndlayer .mail-information')
                    .html(
                        '<p class="info-txt mail-number"><b>Nomor Surat:</b> ' + mailData.mail_number + '</p>' +
                        '<p class="info-txt mail-subject"><b>Perihal:</b> ' + mailData.subject + '</p>' +
                        '<p class="info-txt sender"><b>Dari:</b> ' + mailData.sender + '</p>' +
                        '<p class="info-txt receiver"><b>Kepada:</b> ' + mailData.receiver + '</p>' +
                        '<p class="info-txt date"><b>Tanggal:</b> ' + mailData.date + '</p>'
                    );

                $(settings.mailFunction.mailContainer + ' ' + itemTarget + ' .modal2ndlayer .mail-contents')
                    .html(mailData.contents);

                $('<button></button>').attr({
                        class: 'button mail-btn print',
                        id: itemID
                    })
                    .html('<i class="fa fa-print"></i>')
                    .appendTo(settings.mailFunction.mailContainer + ' ' + itemTarget + ' .modal2ndlayer');

                if (mailData.hasOwnProperty('mail_send')) {
                    if (mailData.mail_send === false) {
                        $('<button></button>').attr({
                                class: 'button mail-btn edit',
                                id: itemID
                            })
                            .html('<i class="fa fa-edit"></i>')
                            .appendTo(settings.mailFunction.mailContainer + ' ' + itemTarget + ' .modal2ndlayer');

                        $('<button></button>').attr({
                                class: 'button mail-btn send',
                                id: itemID
                            })
                            .html('<i class="fa fa-paper-plane"></i>')
                            .appendTo(settings.mailFunction.mailContainer + ' ' + itemTarget + ' .modal2ndlayer');

                        atrgr.defineTrigger('editMail' + itemID, 'edit_mail');
                        atrgr['editMail' + itemID](itemID, '.edit-om-modal', mailData, [settings.mailFunction.mailContainer, itemTarget]);
                    }
                }

                $(settings.mailFunction.mailContainer + ' ' + itemTarget).removeClass('hide');
            } else {
                $('.action-msg-notification').html('<p><i class="fa fa-exclamation-circle"></i> Tidak Ada Container</p>');
                $('.action-msg-notification').addClass('error');
                $('.action-msg-notification').removeClass('hide');
            }
        } else if (result.status === 'error') {
            $('.action-msg-notification').html('<p>' + result.message + '</p>');
            $('.action-msg-notification').addClass('error');
            $('.action-msg-notification').removeClass('hide');
        } else if (result.status === 'failed') {
            $('.action-msg-notification').html('<p>' + result.message + '</p>');
            $('.action-msg-notification').addClass('failed');
            $('.action-msg-notification').removeClass('hide');
        }
    }
}

/**
 * this object is to create pagination in webpage to be asynchronous
 * 
 * @param limit A integer parameter contain item limit
 * @param itemToPaginate A string paraneter contain class,id, or tag item to paginate
 * @param pgNavClass A string parameter contain paginate navigation class
 * @param pgNavContainer A string contain paginate navigation container class
 */
function Pagination(limit = 5, itemToPaginate = '.item', pgNavClass = 'page-link', pgNavContainer = '.pagination') {
    ASMPUsefulAPI.call(this);
    Object.defineProperties(this, {
        itemLimit: {
            value: limit
        },
        itemToPaginate: {
            value: itemToPaginate
        },
        paginateNavClass: {
            value: pgNavClass
        },
        paginateNavContainer: {
            value: pgNavContainer
        }
    });
    var
        itemLength = $(this.itemToPaginate).length,
        targetIncrement = this.itemLimit,
        prevTarget = 1,
        pageNum = 1,
        pageCount = 0,
        //pageUrl = window.location.href,
        pageAElement = '';

    this.paginate = function () {
        // if (pageUrl.search('#page') !== -1 && pageUrl.search('#page1') === -1) {
        //     pageUrl = pageUrl.split('#');
        //     pageNum = pageUrl[pageUrl.length - 1];
        //     pageNum = pageNum.match(/page([0-9])/);
        //     pageNum = pageNum[1];
        //     console.log(pageNum);
        //     if (pageNum == 2) {
        //         prevTarget = this.itemLimit + 1;
        //         targetIncrement = this.itemLimit * pageNum;
        //     } else if (pageNum > 2) {
        //         prevTarget = this.itemLimit * (pageNum - 1) + 1;
        //         targetIncrement = this.itemLimit * pageNum;
        //     }
        //     console.log(prevTarget);
        //     $(this.itemToPaginate).addClass('hide');
        //     for (; prevTarget < targetIncrement + 1; prevTarget++) {
        //         $(this.itemToPaginate + '.id' + prevTarget).removeClass('hide');
        //     }

        //     pageCount = itemLength / this.itemLimit;

        //     if (Number.isSafeInteger(pageCount) === false) {
        //         pageCount = Number.parseInt(pageCount) + 1;
        //     }

        //     console.log(pageNum);

        //     for (var i = 1; i < pageCount + 1; i++) {
        //         if (i == pageNum) {
        //             pageAElement = '<a href="#page' + i + '" class="' + this.paginateNavClass + ' pg' + i + ' current">' + i + '</a>';
        //             $(this.paginateNavContainer).append(pageAElement);
        //             pageNext(i, this.paginateNavClass);
        //         } else {
        //             pageAElement = '<a href="#page' + i + '" class="' + this.paginateNavClass + ' pg' + i + '">' + i + '</a>';
        //             $(this.paginateNavContainer).append(pageAElement);
        //             pageNext(i, this.paginateNavClass);
        //         }
        //         console.log($('.page-link'), '.' + this.paginateNavClass + '.pg' + i);
        //         $('.' + this.paginateNavClass + '.pg' + i).click(function (e) {
        //             e.preventDefault();
        //             pageNum = i;
        //             if (pageNum == 2) {
        //                 prevTarget = this.itemLimit + 1;
        //                 targetIncrement = this.itemLimit * pageNum;
        //             } else if (pageNum > 2) {
        //                 prevTarget = this.itemLimit * (pageNum - 1) + 1;
        //                 targetIncrement = this.itemLimit * pageNum;
        //             }
        //             console.log(prevTarget);
        //             $(this.itemToPaginate).addClass('hide');
        //             for (; prevTarget < targetIncrement + 1; prevTarget++) {
        //                 $(this.itemToPaginate + '.id' + prevTarget).removeClass('hide');
        //             }
        //         });
        //     }
        // } else {
        pageCount = itemLength / this.itemLimit;

        if (Number.isSafeInteger(pageCount) === false) {
            pageCount = Number.parseInt(pageCount) + 1;
        }

        $(this.itemToPaginate).addClass('hide');
        for (; prevTarget < targetIncrement + 1; prevTarget++) {
            //console.log(prevTarget)
            $(this.itemToPaginate + '.id' + prevTarget).removeClass('hide');
        }

        //console.log(pageCount);
        var tlimit = this.itemLimit,
            itemToPgnt = this.itemToPaginate,
            prevIndex = 1;
        $(this.paginateNavContainer + ' .' + this.paginateNavClass).remove();
        if (itemLength > tlimit) {
            //console.log(itemLength, tlimit);
            for (var i = 1; i < pageCount + 1; i++) {
                //console.log(i);
                if (i == 1) {
                    pageAElement = '<a href="#page' + i + '" class="' + this.paginateNavClass + ' pg' + i + ' current">' + i + '</a>';
                    $(this.paginateNavContainer).append(pageAElement);
                    pageNext(i, this.paginateNavClass);
                } else {
                    pageAElement = '<a href="#page' + i + '" class="' + this.paginateNavClass + ' pg' + i + '">' + i + '</a>';
                    $(this.paginateNavContainer).append(pageAElement);
                    pageNext(i, this.paginateNavClass);
                }
            }
            return true;
        } else {
            return false;
        }

        function pageNext(index, pnc) {
            $('.' + pnc + '.pg' + index).click(function (e) {
                //e.preventDefault();
                //console.log(index);
                pageNum = index;
                if (pageNum == 1) {
                    prevTarget = 1;
                    targetIncrement = tlimit;
                } else if (pageNum == 2) {
                    //console.log('Loger');
                    prevTarget = tlimit + 1;
                    //console.log(prevTarget);
                    targetIncrement = tlimit * pageNum;
                } else if (pageNum > 2) {
                    prevTarget = tlimit * (pageNum - 1) + 1;
                    targetIncrement = tlimit * pageNum;
                }
                $(itemToPgnt).addClass('hide');
                for (; prevTarget < targetIncrement + 1; prevTarget++) {
                    $(itemToPgnt + '.id' + prevTarget).removeClass('hide');
                }
                $('.' + pnc + '.pg' + prevIndex).removeClass('current');
                $(this).addClass('current');
                prevIndex = index;
            });
        }
        // }
    }

    this.sortItemID = function (idType = 'class') {
        if (idType == 'class') {
            var itemCount = $(this.itemToPaginate).length,
                itemID, // item id to sort
                i = 1,
                atrgr = new ActionTrigger(),
                onlyID = 0,
                mailITemData;
            for (; i < itemCount + 1; i++) {
                itemID = $(this.itemToPaginate).eq(i - 1).attr('class').match(/id([0-9])/g);
                //console.log($(this.itemToPaginate + '.' + itemID[0] + ' .action-btn.remove'), this.itemToPaginate + '.' + itemID[0] + ' .action-btn.remove');
                $(this.itemToPaginate + '.' + itemID[0] + ' .action-btn.remove').removeAttr('id');
                $(this.itemToPaginate + '.' + itemID[0] + ' .action-btn.trash').removeAttr('id');
                $(this.itemToPaginate + '.' + itemID[0] + ' .action-btn.view').removeAttr('id');
                $(this.itemToPaginate + '.' + itemID[0] + ' .checkbox').removeClass('item' + itemID[0].split('id')[1]);
                $(this.itemToPaginate + '.' + itemID[0] + ' .checkmark').removeClass('item' + itemID[0].split('id')[1]);
                $(this.itemToPaginate + '.' + itemID[0] + ' .action-btn.remove').attr('id', 'item' + i);
                $(this.itemToPaginate + '.' + itemID[0] + ' .action-btn.trash').attr('id', 'item' + i);
                $(this.itemToPaginate + '.' + itemID[0] + ' .action-btn.view').attr('id', 'item' + i);
                $(this.itemToPaginate + '.' + itemID[0] + ' .checkbox').addClass('item' + i);
                $(this.itemToPaginate + '.' + itemID[0] + ' .checkmark').addClass('item' + i);
                onlyID = itemID[0].split('id');
                //console.log(onlyID);
                atrgr.deleteTrigger('deleteFieldSection' + onlyID[1]);
                atrgr.deleteTrigger('deleteUser' + onlyID[1]);
                atrgr.deleteTrigger('throwMailToTrash' + onlyID[1]);
                atrgr.deleteTrigger('viewMail' + onlyID[1]);
                atrgr.deleteTrigger('checkboxAction' + onlyID[1]);
                atrgr.defineTrigger('deleteFieldSection' + i, 'delete_field_section');
                atrgr.defineTrigger('deleteUser' + i, 'delete_user');
                atrgr.defineTrigger('throwMailToTrashOM' + i, 'throw_mail_tt');

                atrgr.defineTrigger('viewMail' + i, 'view_mail');
                atrgr.defineTrigger('checkboxAction' + i, 'checkbox_action');
                $(this.itemToPaginate).eq(i - 1).removeClass(itemID[0]);
                $(this.itemToPaginate).eq(i - 1).addClass('id' + i);
                mailITemData = $(this.itemToPaginate + '.id' + i).data('itemdata');
                mailITemData.id = i;
                mailITemData = JSON.stringify(mailITemData);
                $(this.itemToPaginate + '.id' + i).data('itemdata', mailITemData);
                mailITemData = JSON.parse(mailITemData);
                //console.log($(this.itemToPaginate + '.id' + i + ' td').eq(1).text());
                atrgr['deleteFieldSection' + i](i, $(this.itemToPaginate + '.id' + i + ' td').eq(1).text());
                atrgr['deleteUser' + i](i, $(this.itemToPaginate + '.id' + i + ' td').eq(1).text());
                atrgr['throwMailToTrashOM' + i](i, 'om', mailITemData);

                atrgr['viewMail' + i](i);
                atrgr['checkboxAction' + i](i);
            }
            //console.log(Object.getOwnPropertyNames(atrgr));
        } else if (idType == 'id') {

        } else {
            //console.log('id type not found!');
        }
    }

    /**
     * Slide item to up after deleted item
     * @param prevItem The previous item class which deleted
     * @param elementMembers The element members of not hidden item
     * @param  noHiddenItem No hidden item in list
     * @param selection slide up element with selection
     */
    this.itemSlideToUp = function (prevItemID = null, elementMembers, noHiddenItem = true, selection = false) {
        var
            itemCount = $(this.itemToPaginate).length,
            pageCount = Math.round(itemCount / this.itemLimit),
            currPage = 1,
            pageUrl = window.location.href,
            elementMemCount = elementMembers.length,
            currElementID = 0; // current element id for loop

        if (pageUrl.search('#page') !== -1) {
            pageUrl = pageUrl.split('#');
            currPage = pageUrl[pageUrl.length - 1];
            currPage = currPage.match(/page([0-9])/);
            currPage = Number.parseInt(currPage[1]);
        }
        //console.log(currPage, pageCount, noHiddenItem);
        if (currPage <= pageCount && noHiddenItem === true) {
            if (selection === false) {
                if (prevItemID == null) {
                    //console.log('no selected id is undefined!');
                } else {
                    if (this.dataTypeOf('integer', prevItemID)) {
                        for (let i = 0; i < elementMemCount; i++) {
                            currElementID = elementMembers.eq(i).attr('class').match(/id([0-9])/g);
                            currElementID = currElementID[0].split('id');
                            currElementID = currElementID[1];
                            if (currElementID == prevItemID) {
                                //console.log(currElementID, prevItemID, elementMemCount, i);
                                if (elementMemCount != 1 && i == 0) {
                                    //console.log('FIRST ELEMENT');
                                    prevItemID = prevItemID + this.itemLimit;
                                    break;
                                } else if (elementMemCount == 1) {
                                    prevItemID += 1;
                                    break;
                                } else {
                                    prevItemID = prevItemID + this.itemLimit - i;
                                }
                            }
                        }
                        $(this.itemToPaginate + '.id' + prevItemID).removeClass('hide');
                    } else {
                        //console.log('previous item id must be a integer');
                    }
                }
            } else if (selection === true) {
                if (prevItemID == null) {
                    //console.log('no selected id is undefined!');
                } else {
                    //console.log(prevItemID);
                    if (!$.isArray(prevItemID)) {
                        //console.log('selected id must be an array!');
                    } else {
                        //console.log(elementMembers);
                        for (let i = 0; i < prevItemID.length; i++) {
                            currElementID = elementMembers.eq(i).attr('class').match(/id([0-9])/g);
                            currElementID = currElementID[0].split('id');
                            currElementID = currElementID[1];
                            prevItemID[i] = Number.parseInt(prevItemID[i]);
                            //console.log('SLIDEID:', prevItemID[i]);
                            if (prevItemID[i] == currElementID) {
                                prevItemID[i] += this.itemLimit;
                            } else {
                                prevItemID[i] = prevItemID[i] + this.itemLimit - prevItemID.length;
                            }
                            //console.log('SLIDEID_SUM:', prevItemID[i], this.itemLimit, prevItemID.length);
                            $(this.itemToPaginate + '.id' + prevItemID[i]).removeClass('hide');
                        }
                    }
                }
            }
            this.sortItemID();
        }
    }

    this.updatePgNav = function (refresh = false) {
        itemLength = $(this.itemToPaginate).length;
        pageCount = itemLength / this.itemLimit;

        if (Number.isSafeInteger(pageCount) === false) {
            pageCount = Number.parseInt(pageCount) + 1;
        }

        var tlimit = this.itemLimit,
            itemToPgnt = this.itemToPaginate,
            prevIndex = 1,
            pageUrl = window.location.href,
            pageNum = 0,
            i = 1,
            targetCurr = 1;
        if (pageUrl.search('#page') !== -1) {
            pageUrl = pageUrl.split('#');
            pageNum = pageUrl[pageUrl.length - 1];
            pageNum = pageNum.match(/page([0-9])/);
            pageNum = pageNum[1];
        }

        if (pageNum !== 0) {
            targetCurr = pageNum;
        }

        $(this.paginateNavContainer + ' .' + this.paginateNavClass).remove();
        if (itemLength > tlimit) {
            for (; i < pageCount + 1; i++) {
                //console.log(i);
                if (i == targetCurr) {
                    pageAElement = '<a href="#page' + i + '" class="' + this.paginateNavClass + ' pg' + i + ' current">' + i + '</a>';
                    $(this.paginateNavContainer).append(pageAElement);
                    pageNext(i, this.paginateNavClass);
                } else {
                    pageAElement = '<a href="#page' + i + '" class="' + this.paginateNavClass + ' pg' + i + '">' + i + '</a>';
                    $(this.paginateNavContainer).append(pageAElement);
                    pageNext(i, this.paginateNavClass);
                }
            }
            return true;
        } else {
            if (refresh === true) {
                if ($(this.itemToPaginate).not(this.itemToPaginate + '.hide').length === 0) {
                    var currUrl = window.location.href;
                    currUrl = currUrl.split('#');
                    //window.location.replace(currUrl[0]);
                }
            }
            return false;
        }

        function pageNext(index, pnc) {
            $('.' + pnc + '.pg' + index).click(function (e) {
                //e.preventDefault();
                pageUrl = window.location.href;
                if (pageUrl.search('#page') !== -1) {
                    pageUrl = pageUrl.split('#');
                    pageNum = pageUrl[pageUrl.length - 1];
                    pageNum = pageNum.match(/page([0-9])/);
                    pageNum = pageNum[1];
                    prevIndex = pageNum;
                } else {
                    prevIndex = 1;
                }
                //console.log(prevIndex);

                pageNum = index;
                if (pageNum == 1) {
                    prevTarget = 1;
                    targetIncrement = tlimit;
                } else if (pageNum == 2) {
                    //console.log('Loger');
                    prevTarget = tlimit + 1;
                    //console.log(prevTarget);
                    targetIncrement = tlimit * pageNum;
                } else if (pageNum > 2) {
                    prevTarget = tlimit * (pageNum - 1) + 1;
                    targetIncrement = tlimit * pageNum;
                }
                $(itemToPgnt).addClass('hide');
                for (; prevTarget < targetIncrement + 1; prevTarget++) {
                    $(itemToPgnt + '.id' + prevTarget).removeClass('hide');
                }
                $('.' + pnc + '.pg' + prevIndex).removeClass('current');
                $(this).addClass('current');
                prevIndex = index;
            });
        }
    }

    this.getPaginateNav = function () {
        return pageAElement;
    }
}

function MultipleAction(url) {
    ASMPUsefulAPI.call(this);
    this.defineAction = function (actionName) {
        var functToInsert = function (action, targetName, allItem = false, seltdItem = false) {
            var actionExecutor = new ASMPActionExecutor();
            if (action === 'remove') {
                if (targetName === 'user_management') {
                    $('.confirm-box#userManagement').remove();
                    $('.action-msg-notification').after('<div class="confirm-box hide" id="userManagement"></div>');
                    $('.confirm-box#userManagement').append('<p>Apakah Anda Yakin Ingin Menghapusnya?</p>');
                    $('.confirm-box#userManagement').append('<button class="button yes-btn">Ya</button>');
                    $('.confirm-box#userManagement').append('<button class="button no-btn">Tidak</button>');
                    $('.table-container#userManagement .table-header .multiple-action .multiple-action-btn.trash').unbind('click');
                    $('.table-container#userManagement .table-header .multiple-action .multiple-action-btn.trash').click(function () {
                        //console.log(Object.getOwnPropertyNames(new MultipleAction));
                        var
                            itemIds = $('.multiple-action').data('mail-ids'),
                            promptInp = false,
                            uPass = '',
                            names = [];
                        itemIds = itemIds.slice(0, -1);
                        itemIds = itemIds.split(',');
                        if ($.isArray(itemIds)) {
                            for (var i = 0; i < itemIds.length; i++) {
                                names[i] = $('.item-list .item.id' + itemIds[i]).children('td').eq(1).text();
                            }
                            //console.log(names);
                        }
                        $('#checkAll').attr('disabled', 'disabled');
                        $('.checkmark.all').attr('disabled', 'disabled');
                        $('.checkbox.item').attr('disabled', 'disabled');
                        $('.confirm-box#userManagement').removeClass('hide');
                        $('.confirm-box#userManagement .yes-btn').click(function () {
                            $('.confirm-box#userManagement').addClass('hide');
                            $('#checkAll').removeAttr('disabled');
                            $('.checkmark.all').removeAttr('disabled');
                            $('.checkbox.item').removeAttr('disabled');
                            if ($('.prompt-box form .prompt-submit').length === 0) {
                                $('<button></buton>').attr({
                                    type: 'submit',
                                    class: 'button prompt-submit',
                                    id: 'rmAll'
                                }).text('Submit').appendTo('.prompt-box form');
                            }
                            $('.prompt-box').removeClass('hide');
                            $('.dim-light').removeClass('hide-elemet');
                            $('.prompt-box form input#uPass').focus();
                            $('.prompt-box form .prompt-submit#rmAll').click(function () {
                                promptInp = true;
                                uPass = $('.prompt-box form input#uPass').val();
                                //console.log(uPass);
                                if (promptInp != false) {
                                    actionExecutor.actionName = 'Remove User';
                                    actionExecutor.actionUrl = url;
                                    actionExecutor.actionData = {
                                        id: itemIds,
                                        t: $.cookie('t'),
                                        item_type: 'user_management',
                                        selected_item: seltdItem,
                                        multiple: true,
                                        all_item: allItem,
                                        item_data: {
                                            names: names,
                                            password: uPass,
                                        }
                                    };
                                    actionExecutor.itemTarget = '.item';
                                    actionExecutor.exec({
                                        multipleAction: true,
                                        haveID: true,
                                        removeTarget: true
                                    });
                                    if (allItem === true) {
                                        $('#checkAll').prop('checked', false);
                                        $('.checkmark').css('display', 'none');
                                    }
                                    $('.multiple-action').data('mail-ids', '');
                                    //console.log($('.multiple-action').data('mail-ids'));
                                    $('.table-container#userManagement .table-header .multiple-action').addClass('hide');
                                    checkedItemCount = 0;
                                    $('.prompt-box form input#uPass').val('');
                                    $('.prompt-box').addClass('hide');
                                    $('.dim-light').addClass('hide-elemet');
                                    $('.prompt-box form .prompt-submit').remove();
                                    uPass = '';
                                    promptInp = false;
                                }
                            });
                            $('.prompt-box .close-btn').click(function () {
                                //console.log('prompt-box::before');
                                $('.prompt-box').addClass('hide');
                                $('.dim-light').addClass('hide-elemet');
                            });
                        });
                        $('.confirm-box#userManagement .no-btn').click(function () {
                            $('.confirm-box#userManagement').addClass('hide');
                            $('#checkAll').removeAttr('disabled');
                            $('.checkmark.all').removeAttr('disabled');
                            $('.checkbox.item').removeAttr('disabled');
                        });
                    });
                } else if (targetName === 'field_section') {
                    $('.confirm-box#fieldSections').remove();
                    $('.action-msg-notification').after('<div class="confirm-box hide" id="fieldSections"></div>');
                    $('.confirm-box#fieldSections').append('<p>Apakah Anda Yakin Ingin Menghapusnya?</p>');
                    $('.confirm-box#fieldSections').append('<button class="button yes-btn">Ya</button>');
                    $('.confirm-box#fieldSections').append('<button class="button no-btn">Tidak</button>');
                    $('.table-container#fieldSections .table-header .multiple-action .multiple-action-btn.trash').unbind('click');
                    $('.table-container#fieldSections .table-header .multiple-action .multiple-action-btn.trash').click(function () {
                        //console.log(Object.getOwnPropertyNames(new MultipleAction));
                        var
                            itemIds = $('.multiple-action').data('mail-ids'),
                            promptInp = false,
                            uPass = '',
                            fieldSections = [];
                        itemIds = itemIds.slice(0, -1);
                        itemIds = itemIds.split(',');
                        if ($.isArray(itemIds)) {
                            for (var i = 0; i < itemIds.length; i++) {
                                fieldSections[i] = $('.item-list .item.id' + itemIds[i]).children('td').eq(1).text();
                            }
                            //console.log(fieldSections);
                        }
                        $('#checkAll').attr('disabled', 'disabled');
                        $('.checkmark.all').attr('disabled', 'disabled');
                        $('.checkbox.item').attr('disabled', 'disabled');
                        $('.confirm-box#fieldSections').removeClass('hide');
                        $('.confirm-box#fieldSections .yes-btn').click(function () {
                            $('.confirm-box#fieldSections').addClass('hide');
                            $('#checkAll').removeAttr('disabled');
                            $('.checkmark.all').removeAttr('disabled');
                            $('.checkbox.item').removeAttr('disabled');
                            if ($('.prompt-box form .prompt-submit').length === 0) {
                                $('<button></buton>').attr({
                                    type: 'submit',
                                    class: 'button prompt-submit',
                                    id: 'rmAll'
                                }).text('Submit').appendTo('.prompt-box form');
                            }
                            $('.prompt-box').removeClass('hide');
                            $('.dim-light').removeClass('hide-elemet');
                            $('.prompt-box form input#uPass').focus();
                            $('.prompt-box form .prompt-submit#rmAll').click(function () {
                                promptInp = true;
                                uPass = $('.prompt-box form input#uPass').val();
                                //console.log(uPass);
                                if (promptInp != false) {
                                    actionExecutor.actionName = 'Remove Field/Section';
                                    actionExecutor.actionUrl = url;
                                    actionExecutor.actionData = {
                                        id: itemIds,
                                        t: $.cookie('t'),
                                        item_type: 'field_sections',
                                        selected_item: seltdItem,
                                        multiple: true,
                                        all_item: allItem,
                                        item_data: {
                                            field_sections: fieldSections,
                                            password: uPass,
                                        }
                                    };
                                    actionExecutor.itemTarget = '.item';
                                    actionExecutor.exec({
                                        multipleAction: true,
                                        haveID: true,
                                        removeTarget: true
                                    });
                                    if (allItem === true) {
                                        $('#checkAll').prop('checked', false);
                                        $('.checkmark').css('display', 'none');
                                    }
                                    $('.multiple-action').data('mail-ids', '');
                                    //console.log($('.multiple-action').data('mail-ids'));
                                    $('.table-container#fieldSections .table-header .multiple-action').addClass('hide');
                                    checkedItemCount = 0;
                                    $('.prompt-box form input#uPass').val('');
                                    $('.prompt-box').addClass('hide');
                                    $('.dim-light').addClass('hide-elemet');
                                    $('.prompt-box form .prompt-submit').remove();
                                    uPass = '';
                                    promptInp = false;
                                }
                            });
                            $('.prompt-box .close-btn').click(function () {
                                //console.log('prompt-box::before');
                                $('.prompt-box').addClass('hide');
                                $('.dim-light').addClass('hide-elemet');
                            });
                        });
                        $('.confirm-box#fieldSections .no-btn').click(function () {
                            $('.confirm-box#fieldSections').addClass('hide');
                            $('#checkAll').removeAttr('disabled');
                            $('.checkmark.all').removeAttr('disabled');
                            $('.checkbox.item').removeAttr('disabled');
                        });
                    });

                }
            } else if (action === 'trash') {
                if (targetName === 'incoming_mail') {
                    $('.table-container#incomingMail .table-header .multiple-action .multiple-action-btn.trash').unbind('click');
                    $('.table-container#incomingMail .table-header .multiple-action .multiple-action-btn.trash').click(function () {
                        var itemIds = $('.multiple-action').data('mail-ids');
                        //console.log(itemIds);
                        itemIds = itemIds.slice(0, -1);
                        itemIds = itemIds.split(',');
                        actionExecutor.actionName = 'Throw All Mail to Trash';
                        actionExecutor.actionUrl = url;
                        actionExecutor.actionData = {
                            id: itemIds,
                            token: 'This is Token',
                            multiple: true,
                            all_item: allItem,
                            selected_item: seltdItem
                        };
                        actionExecutor.itemTarget = '.item';
                        actionExecutor.exec({
                            multipleAction: true,
                            haveID: true,
                            removeTarget: true
                        });
                        if (allItem === true) {
                            $('#checkAll').prop('checked', false);
                            $('.checkmark').css('display', 'none');
                        }
                        $('.multiple-action').data('mail-ids', '');
                        $('.table-container#incomingMail .table-header .multiple-action').addClass('hide');
                        checkedItemCount = 0;
                    });
                } else if (targetName === 'outgoing_mail') {
                    $('.table-container#outgoingMail .table-header .multiple-action .multiple-action-btn.trash').unbind('click');
                    $('.table-container#outgoingMail .table-header .multiple-action .multiple-action-btn.trash').click(function () {
                        var itemIds = $('.multiple-action').data('mail-ids'),
                            mailNumbers = [];
                        //console.log(itemIds);
                        itemIds = itemIds.slice(0, -1);
                        itemIds = itemIds.split(',');
                        if ($.isArray(itemIds)) {
                            for (var i = 0; i < itemIds.length; i++) {
                                mailNumbers[i] = $('.item-list .item.id' + itemIds[i]).children('td').eq(1).text();
                            }
                            //console.log(fieldSections);
                        }
                        actionExecutor.actionName = 'Throw All Mail to Trash';
                        actionExecutor.actionUrl = url;
                        actionExecutor.actionData = {
                            id: itemIds,
                            t: $.cookie('t'),
                            item_type: targetName,
                            item_data: {
                                mail_numbers: mailNumbers
                            },
                            multiple: true,
                            all_item: allItem,
                            selected_item: seltdItem
                        };
                        actionExecutor.itemTarget = '.item';
                        actionExecutor.exec({
                            multipleAction: true,
                            haveID: true,
                            removeTarget: true
                        });
                        if (allItem === true) {
                            $('#checkAll').prop('checked', false);
                            $('.checkmark').css('display', 'none');
                        }
                        $('.multiple-action').data('mail-ids', '');
                        $('.table-container#outgoingMail .table-header .multiple-action').addClass('hide');
                        checkedItemCount = 0;
                    });
                }
            }
        }
        if (!this.hasOwnProperty(actionName)) {
            Object.defineProperty(this, actionName, {
                value: functToInsert,
                configurable: true
            });
        } else {
            this.deleteAction(actionName);
            Object.defineProperty(this, actionName, {
                value: functToInsert,
                configurable: true
            });
        }
    }

    this.deleteAction = function (actionName) {
        delete this[actionName];
    }
}

function ActionTrigger() {
    this.defineTrigger = function (actionName, triggerName) {
        var
            usefullapi = new ASMPUsefulAPI(),
            userRole = usefullapi.getFullURL()[0],
            deleteUser = function (index, uTrueName) {
                var actionExecutor = new ASMPActionExecutor();
                $('.confirm-box#userManagement').remove();
                $('.action-msg-notification').after('<div class="confirm-box hide" id="userManagement"></div>');
                $('.confirm-box#userManagement').append('<p>Apakah Anda Yakin Ingin Menghapusnya?</p>');
                $('.confirm-box#userManagement').append('<button class="button yes-btn">Ya</button>');
                $('.confirm-box#userManagement').append('<button class="button no-btn">Tidak</button>');
                //console.log('NEW INDEX: ' + index);
                $('.table-container#userManagement .action-btn.remove#item' + index).unbind('click');
                $('.table-container#userManagement .action-btn.remove#item' + index).click(function () {
                    //console.log(index);
                    var
                        promptInp = false,
                        uPass = '';
                    $('#checkAll').attr('disabled', 'disabled');
                    $('.checkmark.all').attr('disabled', 'disabled');
                    $('.checkbox.item').attr('disabled', 'disabled');
                    $('.confirm-box#userManagement').removeClass('hide');
                    $('.confirm-box#userManagement .yes-btn').click(function () {
                        $('.confirm-box#userManagement').addClass('hide');
                        $('#checkAll').removeAttr('disabled');
                        $('.checkmark.all').removeAttr('disabled');
                        $('.checkbox.item').removeAttr('disabled');
                        $('.prompt-box form .prompt-submit').remove();
                        if ($('.prompt-box form .prompt-submit').length === 0) {
                            $('<button></buton>').attr({
                                type: 'submit',
                                class: 'button prompt-submit',
                                id: 'id' + index
                            }).text('Submit').appendTo('.prompt-box form');
                        }
                        $('.prompt-box').removeClass('hide');
                        $('.dim-light').removeClass('hide-elemet');
                        $('.prompt-box form input#uPass').focus();
                        $('.prompt-box form .prompt-submit#id' + index).click(function () {
                            promptInp = true;
                            uPass = $('.prompt-box form input#uPass').val();
                            if (promptInp != false) {
                                //console.log(uPass, index);
                                actionExecutor.actionName = 'Delete User';
                                actionExecutor.actionUrl = baseURL() + '/' + userRole + '/remove_item';
                                actionExecutor.actionData = {
                                    id: index,
                                    item_type: 'user_management',
                                    selected_item: false,
                                    all_item: false,
                                    item_data: {
                                        name: uTrueName,
                                        password: uPass
                                    },
                                    t: $.cookie('t')
                                };
                                actionExecutor.itemTarget = '.item';
                                actionExecutor.exec({
                                    haveID: true,
                                    removeTarget: true
                                });
                                $('.prompt-box form input#uPass').val('');
                                $('.prompt-box').addClass('hide');
                                $('.dim-light').addClass('hide-elemet');
                                $('.prompt-box form .prompt-submit').remove();
                                uPass = '';
                                confirm = false;
                                promptInp = false;
                            }
                        });
                        $('.prompt-box .close-btn').click(function () {
                            //console.log('prompt-box::before');
                            $('.prompt-box').addClass('hide');
                            $('.dim-light').addClass('hide-elemet');
                        });
                    });
                    $('.confirm-box#userManagement .no-btn').click(function () {
                        $('.confirm-box#userManagement').addClass('hide');
                        $('#checkAll').removeAttr('disabled');
                        $('.checkmark.all').removeAttr('disabled');
                        $('.checkbox.item').removeAttr('disabled');
                    });
                });
            }, // add unbind to item
            throwMailToTrash = function (index, mailType, mailData) {
                var actionExecutor = new ASMPActionExecutor();
                $('.table-container #mailAction .action-btn.trash#item' + index).unbind('click');
                $('.table-container #mailAction .action-btn.trash#item' + index).click(function () {
                    console.log(index);
                    actionExecutor.actionName = 'Throw Mail to Trash';
                    actionExecutor.actionUrl = baseURL() + '/' + mailType + '_action_exec';
                    actionExecutor.actionData = {
                        id: index,
                        token: $.cookie('t'),
                        request_data: JSON.stringify({
                            action: 'throw',
                            mail_data: mailData
                        }),
                        multiple: false
                    };
                    actionExecutor.itemTarget = '.item';
                    actionExecutor.exec({
                        multipleAction: false,
                        haveID: true,
                        removeTarget: true
                    });
                });
            },
            deleteFieldSection = function (index, fsName) {
                var actionExecutor = new ASMPActionExecutor();
                $('.confirm-box#fieldSections').remove();
                $('.action-msg-notification').after('<div class="confirm-box hide" id="fieldSections"></div>');
                $('.confirm-box#fieldSections').append('<p>Apakah Anda Yakin Ingin Menghapusnya?</p>');
                $('.confirm-box#fieldSections').append('<button class="button yes-btn">Ya</button>');
                $('.confirm-box#fieldSections').append('<button class="button no-btn">Tidak</button>');
                //console.log('NEW INDEX: ' + index);
                $('.table-container#fieldSections .item.id' + index + ' .action-btn.remove#item' + index).unbind('click');
                $('.table-container#fieldSections .item.id' + index + ' .action-btn.remove#item' + index).click(function () {
                    //console.log(index);
                    var
                        promptInp = false,
                        uPass = '';
                    $('#checkAll').attr('disabled', 'disabled');
                    $('.checkmark.all').attr('disabled', 'disabled');
                    $('.checkbox.item').attr('disabled', 'disabled');
                    $('.confirm-box#fieldSections').removeClass('hide');
                    $('.confirm-box#fieldSections .yes-btn').click(function () {
                        $('.confirm-box#fieldSections').addClass('hide');
                        $('#checkAll').removeAttr('disabled');
                        $('.checkmark.all').removeAttr('disabled');
                        $('.checkbox.item').removeAttr('disabled');
                        $('.prompt-box form .prompt-submit').remove();
                        if ($('.prompt-box form .prompt-submit').length === 0) {
                            $('<button></buton>').attr({
                                type: 'submit',
                                class: 'button prompt-submit',
                                id: 'id' + index
                            }).text('Submit').appendTo('.prompt-box form');
                        }
                        $('.prompt-box').removeClass('hide');
                        $('.dim-light').removeClass('hide-elemet');
                        $('.prompt-box form input#uPass').focus();
                        $('.prompt-box form .prompt-submit#id' + index).click(function () {
                            promptInp = true;
                            uPass = $('.prompt-box form input#uPass').val();
                            if (promptInp != false) {
                                //console.log(uPass, index);
                                actionExecutor.actionName = 'Delete field section';
                                actionExecutor.actionUrl = baseURL() + '/' + userRole + '/remove_item';
                                actionExecutor.actionData = {
                                    id: index,
                                    item_type: 'field_sections',
                                    selected_item: false,
                                    all_item: false,
                                    item_data: {
                                        field_section: fsName,
                                        password: uPass
                                    },
                                    t: $.cookie('t')
                                };
                                actionExecutor.itemTarget = '.item';
                                actionExecutor.exec({
                                    haveID: true,
                                    removeTarget: true
                                });
                                $('.prompt-box form input#uPass').val('');
                                $('.prompt-box').addClass('hide');
                                $('.dim-light').addClass('hide-elemet');
                                $('.prompt-box form .prompt-submit').remove();
                                uPass = '';
                                confirm = false;
                                promptInp = false;
                            }
                        });
                        $('.prompt-box .close-btn').click(function () {
                            //console.log('prompt-box::before');
                            $('.prompt-box').addClass('hide');
                            $('.dim-light').addClass('hide-elemet');
                        });
                    });
                    $('.confirm-box#fieldSections .no-btn').click(function () {
                        $('.confirm-box#fieldSections').addClass('hide');
                        $('#checkAll').removeAttr('disabled');
                        $('.checkmark.all').removeAttr('disabled');
                        $('.checkbox.item').removeAttr('disabled');
                    });
                });
            },
            checkboxAction = function (index) {
                var ma = new MultipleAction(baseURL() + '/remove_item');
                $('.checkbox.item' + index).unbind('click');
                $('.checkbox.item' + index).click(function () {
                    var mailIds = $('.multiple-action').data('mail-ids');
                    checkedItemCount += 1;
                    mailIds += index + ',';
                    $('.multiple-action').data('mail-ids', mailIds);
                    //console.log($(this).is(':checked'), index, 'Checked Count: ' + checkedItemCount, 'Mail Ids: ' + $('.multiple-action').data('mail-ids'));
                    if ($(this).is(':checked')) {
                        //console.log('IndexCHECKED:' + index);
                        $('.checkmark.item' + index).css('display', 'inline-block');
                        if (checkedItemCount == 2) {
                            $('.multiple-action').removeClass('hide');
                            ma.defineAction('multipleAction1');
                            ma.defineAction('multipleAction2');
                            ma.defineAction('multipleAction3');
                            ma.defineAction('multipleAction4');
                            ma.multipleAction1('remove', 'user_management', false, true);
                            ma.multipleAction2('trash', 'incoming_mail', false, true);
                            ma.multipleAction3('trash', 'outgoing_mail', false, true);
                            ma.multipleAction4('remove', 'field_section', false, true);
                        } else if (checkedItemCount < 2) {
                            $('.multiple-action').addClass('hide');
                        }
                    }
                });

                $('.checkmark.item' + index).unbind('click');
                $('.checkmark.item' + index).click(function () {
                    var mailIds = $('.multiple-action').data('mail-ids');
                    checkedItemCount -= 1;
                    if (mailIds !== '1' && mailIds !== '') {
                        mailIds = mailIds.replace(index + ',', '');
                        mailIds = mailIds.split(',');
                        mailIds = mailIds.slice(0, -1);
                        //console.log(mailIds);
                        if (mailIds.length > 1) {
                            mailIds = mailIds.join(',') + ',';
                        } else if (mailIds.length == 1) {
                            mailIds = mailIds.toString() + ',';
                        }
                    } else {
                        mailIds = '';
                    }
                    $('.multiple-action').data('mail-ids', mailIds);
                    //console.log($(this).is(':checked'), index, 'Checked Count: ' + checkedItemCount, 'Mail Ids: ' + $('.multiple-action').data('mail-ids'));
                    $('.checkbox.item' + index).prop('checked', false);
                    $(this).css('display', 'none');
                    if (checkedItemCount > 1) {
                        $('.multiple-action').removeClass('hide');
                    } else if (checkedItemCount < 2) {
                        $('.multiple-action').addClass('hide');
                    }
                    //console.log($('.checkbox.item' + index).is(':checked'), index, 'Checked Count: ' + checkedItemCount);
                });
            },
            viewMail = function (index) {
                console.log('View...');
                var actionExecutor = new ASMPActionExecutor();
                $('.table-container #mailAction .action-btn.view#item' + index).unbind('click');
                $('.table-container #mailAction .action-btn.view#item' + index).click(function () {
                    //console.log('View' + index);.table-container #mailAction .action-btn.view
                    $('.table-container #mailAction .action-btn.view').attr('disabled', 'disabled');
                    actionExecutor.actionName = actionName;
                    actionExecutor.actionUrl = baseURL() + '/view_im';
                    actionExecutor.actionData = {
                        id: index,
                        token: $.cookie('t'),
                        mailData: $('.table-container .item-list tbody tr.item.id' + index).attr('data-itemdata')
                    };
                    actionExecutor.itemTarget = '.mail-view';
                    actionExecutor.exec({
                        haveID: true,
                        mailFunction: {
                            view: true,
                            tableContainer: '.table-container',
                            mailContainer: '.mail-views'
                        }
                    });
                });

                $('.incoming-mails-container .incoming-mail.id' + index + ' .im-btn.back#mail' + index).unbind('click');
                $('.incoming-mails-container .incoming-mail.id' + index + ' .im-btn.back#mail' + index).click(function () {
                    $('.incoming-mails-container').addClass('hide');
                    $('.incoming-mails-container .incoming-mail.id' + index).addClass('hide');
                    $('#incomingMail').removeClass('hide');
                });
            },
            editMail = function (index, containerSelector, mailData, currWindowToClose) {
                $('.mail-views .mail-view .modal2ndlayer button.mail-btn.edit').unbind('click');
                $('.mail-views .mail-view .modal2ndlayer button.mail-btn.edit').click(function () {
                    var
                        itemID = index,
                        pdfLayouts = $(containerSelector).data('pdflayouts').split(',');

                    if ($.isArray(currWindowToClose)) {
                        let i = 0;
                        for (; i < currWindowToClose.length; i++) {
                            $(currWindowToClose[i]).addClass('hide');
                        }
                    } else {
                        $(currWindowToClose).addClass('hide');
                    }

                    $(containerSelector).css('display', 'block');

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
                    $(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input select[name=pdf_layouts]').val(mailData.pdf_layout.toLowerCase());

                    // mail number
                    $('<label></label>').attr('for', 'mail_number').text('Nomor Surat:')
                        .appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input');
                    $('<input>').attr({
                        type: 'text',
                        name: 'mail_number',
                        placeholder: 'Ex: XII/M2/19',
                        value: mailData.mail_number
                    }).appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input');

                    // mail subject
                    $('<label></label>').attr('for', 'mail_subject').text('Perihal:')
                        .appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input');
                    $('<input>').attr({
                        type: 'text',
                        name: 'mail_subject',
                        placeholder: 'Ex: Rapat Umum',
                        value: mailData.subject
                    }).appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form .form-input');

                    $("#mailContentsEditorEditingWidgIframe").contents().find('body').html(mailData.contents);

                    // button save
                    $('<button></button>').attr({
                            type: 'submit',
                            name: 'update_om',
                            class: 'add-om-submit save'
                        }).html('<i class="fa fa-save"></i> Simpan').click(function () {
                            let
                                form = $(containerSelector + ' .modal2ndlayer .mail-modal-form'),
                                fdata = {},
                                i = 0,
                                iframeData = $("#mailContentsEditorEditingWidgIframe").contents().find('body').html();

                            $("#mailContentsEditorEditing").val(iframeData);
                            $(containerSelector + ' .modal2ndlayer .mail-modal-form input[name=editor_data]').val(iframeData);
                            for (; i < form[0].length; i++) {
                                fdata[form[0][i].name] = form[0][i].value;
                            }

                            fdata['prev_mailnum'] = mailData.mail_number;

                            $.ajax({
                                type: "POST",
                                url: baseURL() + '/om_action_exec',
                                data: {
                                    request_data: JSON.stringify({
                                        action: 'update',
                                        om_data: fdata,
                                        t: $.cookie('t')
                                    })
                                },
                                dataType: "json",
                            }).done(function () {

                            }).fail(function () {

                            }).always(function (result) {
                                $('.edit-om-modal').css('display', 'none');
                                $('#addOm').removeAttr('disabled');
                                $('.casual-theme.edit-om-modal .modal2ndlayer .form-input input').remove();
                                $('.casual-theme.edit-om-modal .modal2ndlayer .form-input select').remove();
                                $('.casual-theme.edit-om-modal .modal2ndlayer .form-input label').remove();
                                $('.casual-theme.edit-om-modal .modal2ndlayer .mail-modal-form  button').remove();
                                $('.table-container #mailAction .action-btn.view').removeAttr('disabled');
                                if (result.status !== 'error' && result.status !== 'failed') {
                                    
                                    $('.table-container .item-list tbody tr.item.id' + index).attr('data-itemdata', JSON.stringify(result.data));
                                    $('.table-container .item-list tbody .item.id' + index + ' td').eq(1).text(result.data['mail_number']);
                                    $('.table-container .item-list tbody .item.id' + index + ' td').eq(2).text(result.data['subject']);
                                    $('.table-container .item-list tbody .item.id' + index + ' td').eq(5).text(result.data['date']);
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
                                }
                            });
                        })
                        .appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form');

                    // button send
                    $('<button></button>').attr({
                            type: 'submit',
                            name: 'send_om',
                            class: 'add-om-submit send'
                        }).html('<i class="fa fa-paper-plane"></i> Kirim').click(function () {
                            let
                                form = $(containerSelector + ' .modal2ndlayer .mail-modal-form'),
                                fdata = {},
                                i = 0,
                                iframeData = $("#mailContentsEditorEditingWidgIframe").contents().find('body').html();

                            $("#mailContentsEditorEditing").val(iframeData);
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

                            });
                        })
                        .appendTo(containerSelector + ' .modal2ndlayer .mail-modal-form');
                    $('.casual-theme.edit-om-modal .modal2ndlayer .close-btn').unbind('click');
                    $('.casual-theme.edit-om-modal .modal2ndlayer .close-btn').click(function () {
                        $('.edit-om-modal').css('display', 'none');
                        $('#addOm').removeAttr('disabled');
                        $('.casual-theme.edit-om-modal .modal2ndlayer .form-input input').remove();
                        $('.casual-theme.edit-om-modal .modal2ndlayer .form-input select').remove();
                        $('.casual-theme.edit-om-modal .modal2ndlayer .form-input label').remove();
                        $('.casual-theme.edit-om-modal .modal2ndlayer .mail-modal-form    button').remove();

                        $('.casual-theme.mail-views .casual-theme.mail-view').addClass('hide');
                        $('.casual-theme.mail-views').addClass('hide');
                        $('.casual-theme.mail-views .casual-theme.mail-view .modal2ndlayer button.mail-btn').remove();
                        $('.table-container #mailAction .action-btn.view').removeAttr('disabled');

                    });
                });
            },
            printMail = function(index, mailType, mailNumber, pdfLayout) {
                mailNumber = mailNumber.replace(/\//g, '&sol;');
                window.open(baseURL() + '/pdf-layout/view/' + pdfLayout + '/' + mailType + '/' + encodeURIComponent(mailNumber) + '/' + $.cookie('t'));
            },
            currTrigger = '';

        switch (triggerName) {
            case 'delete_user':
                currTrigger = deleteUser;
                break;
            case 'throw_mail_tt':
                currTrigger = throwMailToTrash;
                break;
            case 'delete_field_section':
                currTrigger = deleteFieldSection;
                break;
            case 'checkbox_action':
                currTrigger = checkboxAction;
                break;
            case 'view_mail':
                currTrigger = viewMail;
                break;
            case 'edit_mail':
                currTrigger = editMail;
                break;
            default:
                console.log('No Trigger Found!');
        }

        if (this.hasOwnProperty(actionName)) {
            this.deleteTrigger(actionName);
        }

        Object.defineProperty(this, actionName, {
            value: currTrigger,
            configurable: true,
        });

        if (this.hasOwnProperty(actionName)) {
            console.log(actionName + ' has defined!');
        }
    }

    this.execTrigger = function (actionName, params) {
        this[actionName](params);
    }

    this.deleteTrigger = function (actionName) {
        delete this[actionName];
        if (this.hasOwnProperty(actionName) == false) {
            console.log(actionName + ' has deleted!');
        }
    }
}

// inherit ASMPUsefulAPI
ASMPActionExecutor.prototype = Object.create(ASMPUsefulAPI.prototype);
ASMPActionExecutor.prototype.constructor = ASMPActionExecutor;
Pagination.prototype = Object.create(ASMPUsefulAPI.prototype);
Pagination.prototype.constructor = Pagination;
MultipleAction.prototype = Object.create(ASMPUsefulAPI.prototype);
MultipleAction.prototype.constructor = MultipleAction;
ActionTrigger.prototype = Object.create(ASMPUsefulAPI.prototype);
ActionTrigger.prototype.constructor = ActionTrigger;