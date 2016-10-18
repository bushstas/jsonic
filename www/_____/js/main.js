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

	
	// Навигация
	var logo = $('header .logo');
	var menuLinks = $('header nav a');
	logo.click(function(e) {
		menuLinks.removeClass('active');
		$("html, body").animate({ scrollTop: 0}, 500);
	});
	menuLinks.click(function(e) {
		e.preventDefault();
		menuLinks.removeClass('active');
		var target = $(e.target);
		target.addClass('active');
		var hash = target.attr('href').replace(/\#/, '');
		var block = $('#' + hash);
		if (block[0]) {
			$("html, body").animate({ scrollTop: block.offset().top - 100}, 500);
		}
	});
	
	// Слайдер
	var slider = $('.slider-outer');
	var slides = slider.find('.slide');
	var sliderArea = slider.find('.slider-inner');
	var slideWidth = slides.width();
	var slidesCount = slides.length;
	var buttons = slider.find('.slider-button');
	var buttonsArr = buttons.toArray();
	var prevButton = slider.find('.prev');
	var nextButton = slider.find('.next');
	var currentSlide = 0;
	
	var changeSlide = function(shift, idx) {
		buttons.removeClass('active');
		if (shift) {
			var tempSlide = currentSlide + shift;
			if (tempSlide >= slidesCount) {
				tempSlide = 0;
			} else if (tempSlide < 0) {
				tempSlide = slidesCount - 1;
			}
			currentSlide = tempSlide;
		} else {
			currentSlide = idx;
		}
		$(buttons[currentSlide]).addClass('active');
		var x = slideWidth * -currentSlide;
		sliderArea.css('left', x + 'px');
	};
	prevButton.click(function() {
		changeSlide(-1);
	});
	nextButton.click(function() {
		changeSlide(1);
	});
	buttons.click(function(e) {
		var idx = buttonsArr.indexOf(e.target);
		if (idx != currentSlide) {
			changeSlide(null, idx);
		}
	});
});