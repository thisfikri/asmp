$(document).ready(function() {
    function setSelectionRange(input, selectionStart, selectionEnd) {
        if (input.setSelectionRange) {
            input.focus();
            input.setSelectionRange(selectionStart, selectionEnd);
        } else if (input.createTextRange) {
            var range = input.createTextRange();
            range.collapse(true);
            range.moveEnd('character', selectionEnd);
            range.moveStart('character', selectionStart);
            range.select();
        }
    }

    $(".sk-new-entry-form").submit(function() {
        var
			iframeData = $("#mailContentsEditorWidgIframe").contents().find('body').html(),
			blockElementTag = ['<p>', '<div<', '<ul>', '<ol>'],
			availableTags = ['<p>', '<b>', '<em>', '<u>', '<img>', '<div>', '<ul>', '<ol>', '<span>', '<style>', '<h1>', '<h2>', '<h3>', '<h4>', '<h5>', '<h6>'],
			noTagText = '',
			matchingPattern = [],
			matchingObject = null,
			tags = [];
			console.log(iframeData);
			iframeData = iframeData.replace(/<i(\s+|>)/g, "<em$1");
			iframeData = iframeData.replace(/<\/i(\s+|>)/g, "</em$1");
			console.log(iframeData);
			/** next is create special characher and sensitive word converter (check and convert), convert word or special character like
			 * span, style, coma(,) and another special character
			 * 
			/**
			 * tag pattern
			 * patt1 > no tag inside content of the body, just pure text. ex: just a plain text
			 * patt2 > single paragraph or single heading content with <p> or <h*> tag. ex: <p>Paragraph</p>
			 * patt3 > nesting tag. ex: <p><b>Nesting</b></p>
			 * patt4 > multiple nesting tag. ex: <p><b><i>Multiple Nesting Tag</i></b></p>
			 * patt5 > triple nesting tag. ex: <p><b><i><u>Triple Nesting Tag</u></i></b></p>
			 * patt6 > multiple block element. ex: p,div,ul,li,ol
			 */
			
			// patt1
			matchingPattern[0] = /([a-zA-Z0-9]+|<([a-zA-Z]+)>|<\/([a-zA-Z]+)>|<h([0-9]+)>|<\/h([0-9]+)>|(&nbsp;)|[!"#$%&'()*+,./:;<=>?@[\]^`{|}~])/g;
			matchingObject = iframeData.match(matchingPattern[0]);
			console.log(matchingObject);

			$("#mailContentsEditor").val(iframeData);
			var
			pDFDocAD = createPDFDocAD(matchingObject);
			pDFDocAD = pDFDocAD.join('|');
			$(".add-om-modal .modal2ndlayer .mail-modal-form input[name=editor_data]").val(iframeData);

			function createPDFDocAD(data) { //create a pdf document avaible data
				var i = 0, tag = [], plainTxt = [], spaceChar = 'nothing', itg = iptxt = ispc = 0, firstParent = 'nothing', endTagFirstParent = 'nothing',
				dataResult = '', contentOrderList = [], olindex = 0, firstText = '';
				
				var currTag = '', prevText = '';
				console.log(currTag);
				if (data[0] == '<p>' || data[0] == '<div>') {
					firstParent =  data[0];
				} else if (!checkAvaibleTags(data[0])) {
					firstText = data[0];
				} else if (checkAvaibleTags(data[0])) {
					dataResult += generateSDT(data[0]);
					currTag = data[0];
				}

				for (; i < data.length; i++) {
					if (checkAvaibleTags(data[i+1])) {
						tag[itg] = data[i+1];
						contentOrderList[olindex] = 'tag';

						if (currTag !== data[i+1]) {
							dataResult += generateSDT(data[i+1]);
							currTag = data[i+1];
						} else if (prevText) {
							currTag = '';
						}
						++olindex;
						++itg;
					} else if (data[i+1] !== '&nbsp;') {
						plainTxt[iptxt] = data[i+1];
						contentOrderList[olindex] = 'plainText';
						dataResult += data[i+1]+'`';
						prevText = data[i+1];
						++olindex;
						++iptxt;
					} else if (data[i+1] == '&nbsp;') {
						spaceChar[ispc] = data[i+1];
						contentOrderList[olindex] = 'space';
						dataResult += '&nbsp;`';
						++olindex;
						++ispc;
					}
				}

				if (plainTxt[plainTxt.length-1] == undefined) {
					plainTxt.pop();
					contentOrderList.pop();
				}
				
				if (tag || plainTxt || spaceChar || firstParent || contentOrderList || dataResult || firstText) {
					dataResult = dataResult.split('`');
					dataResult.pop();
					dataResult.pop();
					return [tag, plainTxt, spaceChar, firstParent, contentOrderList, dataResult, firstText];
				}
			}

			function checkAvaibleTags(tag) {
				if (availableTags.indexOf(tag) !== -1 && tag !== '&nbsp;') {
					return true;
				} else {
					return false;
				}
			}
			
			function generateSDT(tag) {
				switch(tag) {
					case '<b>':
						return '[style=bold]-';
						break;
					case '<em>':
						return '[style=italic]-';
						break;
					case '<u>':
						return '[style=underline]-';
						break;
					case '<p>':
						return '[newline=p]>';
						break;
					case '<div>':
						return '[newline=div]>';
						break;
					case '<style>':
						return '[attrstyle]->';
						break;
					default:
						return '';
				}
			}
    });

	$('.reply-form .reply-submit').submit(function(event) {
		var
		iframeData = $("#letterTextEditorWidgIframe").contents().find('body').html();

		$("#letterTextEditor").val(iframeData);
		$(".reply-form input[name=editor_data]").val(iframeData);
	});
	
});