$(document).ready(function() {
	
	// Модальное окно
	var dialog = $('.dialog.send-form');
	var mask = $('.dialog-mask.send-form-mask');
	$('.show-form-dialog').click(function() {
		dialog.show();
		mask.show();
	});
	mask.click(function() {
		dialog.hide();
		mask.hide();
	});

	var isFixed = false;
	var fixOn = 300;
	var body = $('body');
	$(document).scroll(function(){
		var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
		if (!isFixed && scrollTop >= fixOn) {
			isFixed = true;
			body.addClass('fixed-header');
		} else if (isFixed && scrollTop < fixOn) {
			isFixed = false;
			body.removeClass('fixed-header');
		}
	});

});