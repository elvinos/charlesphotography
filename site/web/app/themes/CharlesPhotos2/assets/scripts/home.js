var windowHeight;
var windowWidth;
var settings;
var retina = false;
var $j = jQuery.noConflict();

jQuery(document).ready(function($) {
   'use strict';

   	// Settings
   	settings = {
		enableAnimations: $j("body").attr("data-animated"),
	};
  $(function() {
    $('.footer').css({
      '-webkit-filter': 'invert(100%)',
      'border-top':'none'
    });
    $('#rightFoot a').css({
      'color': 'rgb(68, 68, 68)',
    });
});
   	// Basic
    var mediaQuery = '(-webkit-min-device-pixel-ratio: 1.5), (min--moz-device-pixel-ratio: 1.5), (-o-min-device-pixel-ratio: 3/2), (min-resolution: 1.5dppx)';
	var root = (typeof exports === 'undefined' ? window : exports);
    if (root.devicePixelRatio > 1) {
	    if (root.matchMedia && root.matchMedia(mediaQuery).matches) {
	        retina = true;
	    }
    }

	$j(".background.image").each(function() {
		var img = $j(this).attr("data-url");
		if(img===undefined) return false;
		$j(this).css("background-image", "url("+img+")");
	});

   	// Header
   	$j("header.header .menu").click(function(){
   		$j("header.header").toggleClass("active");
		playAnimations();
		$j("nav.navigation").toggleClass("active");
   		adjustSizes();
   	});

    $("#menuBtn").click(function(e) {
      animateMenu();
    });


});

jQuery(window).load(function($) {

	// Fixes
	adjustSizes();

	// Sliders
	playSlider();

//menu
animateMenu();

});


jQuery(window).resize(function($) {

	adjustSizes();

});

// MENU BUTTON

function animateMenu() {

  var className = ' ' + menuBtn.className + ' ';
  if (~className.indexOf(' animateMenuBtn ')) {
    menuBtn.className = className.replace(' animateMenuBtn ', ' ');
    menuBtn.className +=  ' menuBtnPos';
  } else {
    menuBtn.className = className.replace(' menuBtnPos ', ' ');
    menuBtn.className += ' animateMenuBtn';
  }

}



function adjustSizes() {

	windowHeight = $j(window).height();
	windowWidth = $j(window).width();

	//Fullscreen
	$j(".fullscreen").css('height', windowHeight);

	//Vertical Center
	$j(".vertical-center").each(function() {
		$j(this).css('margin-top', ($j(this).parent().height() - $j(this).height()) / 2);
	});



	// Adjust Page
	$j("section.page").each(function() {
		var content = $j(this).find(".content");
		var h = content.height() + 132;
		if(h<windowHeight) {
			content.css("padding-top", (windowHeight - content.height())/2);
		} else {
			content.css("padding-bottom", 66);
		}
	});



}


function playSlider() {

	var slider = $j(".slider");
	var slides = slider.find(".slides");
	var total = slides.find(".slide").length;

	if ( total > 0 ) {

		slider.append("<div class='controls'><ul></ul><div class='selected'></div></div>");
		slides.find(".slide").each(function() {
			var i = slides.find(".slide").index($j(this)) + 1;
			slider.find(".controls ul").append("<li><span>0"+i+"</span></li>");
		});

		slider.find(".controls li").click(function(){
			// var control = slider.find(".controls li").eq($j(this));
			var i = slider.find(".controls li").index($j(this));
			var slide = slides.find(".slide").eq(i);
			slide.addClass("current").siblings().removeClass("current");
			var control = slider.find(".controls li").eq(i);
			control.addClass("current").siblings().removeClass("current");
			var selectedPos = control.position();
			slider.find(".controls .selected").css("left", selectedPos.left);
		});

		if( slides.find(".current").length === 0 ) {
			var slide = slides.find(".slide").eq(0);
			slide.addClass("current");
			var control = slider.find(".controls li").eq(0);
			control.addClass("current").siblings().removeClass("current");
			var selectedPos = control.position();
			slider.find(".controls .selected").css("left", selectedPos.left);
		}

		setInterval(function(){
			var currentSlide = slides.find(".slide.current");
			var currentIndex = slides.find(".slide").index(currentSlide) + 1;
			var current = 0;
			if( currentIndex < total ) current = currentIndex;
			var slide = slides.find(".slide").eq(current);
			slide.addClass("current").siblings().removeClass("current");
			var control = slider.find(".controls li").eq(current);
			control.addClass("current").siblings().removeClass("current");
			var selectedPos = control.position();
			if( currentIndex < total ) slider.find(".controls .selected").addClass("expanded");
			slider.find(".controls .selected").css("left", selectedPos.left);
			setTimeout(function () {
		        slider.find(".controls .selected").removeClass("expanded");
		    }, 4000);

		}, 7000);
	}
}
