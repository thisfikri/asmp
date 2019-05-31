$(document).ready(function(){
	const APPLANG = [
		'in_ID',
		'en_US'
	];

	$('.button.lang').click(function(event) {
		var
		dropdownStat = $('.language-dropdown').css('display');

		if (dropdownStat == 'none') {
			$('.language-dropdown').css('display', 'block');
			$('.language-dropdown').css('transition', 'all 0.3s');
			$('.language-dropdown').css('height', '57px');
		} else if (dropdownStat == 'block') {
			$('.language-dropdown').css('transition', 'none');
			$('.language-dropdown').slideUp('300', function() {
				$('.language-dropdown').css('height', '0');
			});
		}
	});

	/*$('.language-dropdown ul li a').click(function(e){
		e.preventDefault();
		var
		avaibleLang = APPLANG.join('/');
		if (avaibleLang.search($(this).attr('id')) !== -1) {
			$.ajax({
                url: baseURL() + '/set_app_lang',
                type: 'POST',
                dataType: 'json',
                data: {
                    idiom: $(this).attr('id'),
                    vt: $.cookie('vt')
                }
            })
            .done(function() {})
            .fail(function() {})
            .always(function(result) {
                if (result.status == 'success') {
					$.ajax({
						url: baseURL() + 'get_pglang_txt',
						type: 'POST',
						dataType: 'json',
						data: {
							page_name: pageName,
							vt: $.cookie('vt')
						}
					})
					.done(function() {

					})
					.fail(function() {

					})
					.always(function(result) {
						changePgLangTxt(result.langText);
					});
				}
            });
		} else {
			console.log('Language Not Avaible!');
		}
	});*/
});