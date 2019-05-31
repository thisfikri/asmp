/**
 * Form Script
 * @param  {[type]} ) {               var        i [description]
 * @return {[type]}   [description]
 */
$(document).ready(function() {

    var
        i = 0,
        fInputLength,
        previousIndex = 0,
        inpChekerID = $('form').attr('id');

        if (inpChekerID == undefined) {
            inpChekerID = 'notFormToCheck';
        }
    
    if (inpChekerID.search('formToCheck') !== -1) {
        fInputLength = $('#formToCheck input[type=text], #formToCheck input[type=password], #formToCheck input[type=email]').length;
        for (; i < fInputLength; i++) {
            emptyInputChecker(i);
            notEmptyInputChecker(i);
        }
    }

    function emptyInputChecker(index) {
        $('#formToCheck input[type=text], #formToCheck input[type=password]').eq(index).focus(function(event) {
            if (index !== 0) {
                if (!$('#formToCheck input[type=text], #formToCheck input[type=password]').eq(index - 1).val()) {
                    previousIndex = index;
                    if (previousIndex !== 0) {
                        for (var j = 0; j < previousIndex; j++) {
                            if (!$('#formToCheck input[type=text], #formToCheck input[type=password], #formToCheck input[type=email]').eq(j).val()) {
                                $('.form-hint').eq(j).css('display', 'inline-block');
                                $('.form-hint').eq(j).text('* ' + $('#formToCheck input[type=text], #formToCheck input[type=password], #formToCheck input[type=email]').eq(j).attr('placeholder') + ' Harus Di Isi');
                            }
                        }
                    }
                }
            }
        });
    }

    var
        sysmbolLength = lowcharLength = uppercharLength = numLength = 0;

    function notEmptyInputChecker(index) {
        $('#formToCheck input[type=text], #formToCheck input[type=password], #formToCheck input[type=email]').eq(index).keyup(function(event) {
            if ($(this).val()) {
                $('.form-hint').eq(index).css('display', 'none');
                $('.form-hint').eq(index).text('');

                if ($(this).attr('type') == 'password') {
                    var
                        symbolMatches = $(this).val().match(/([!"#$%&'()*+,./:;<=>?@[\]^`{|}~]+)/),
                        lowcharMatches = $(this).val().match(/([a-z]+)/),
                        uppercharMatches = $(this).val().match(/([A-Z]+)/),
                        numMatches = $(this).val().match(/([0-9]+)/);
                    if (symbolMatches) {
                        sysmbolLength = symbolMatches[0].length;
                        
                    }

                    if (lowcharMatches) {
                        lowcharLength = lowcharMatches[0].length;
                        
                    }

                    if (uppercharMatches) {
                        uppercharLength = uppercharMatches[0].length;
                        
                    }

                    if (numMatches) {
                        numLength = numMatches[0].length;
                        
                    }
                    
                }
            } else {
                $('.form-hint').eq(index).css('display', 'inline-block');
                $('.form-hint').eq(index).text('* ' + $('#formToCheck input[type=text], #formToCheck input[type=password], #formToCheck input[type=email]').eq(index).attr('placeholder') + ' Harus Di Isi');
            }
        });
    }
});