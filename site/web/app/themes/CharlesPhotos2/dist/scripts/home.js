function animateMenu(){var e=" "+menuBtn.className+" ";~e.indexOf(" animateMenuBtn ")?(menuBtn.className=e.replace(" animateMenuBtn "," "),menuBtn.className+=" menuBtnPos"):(menuBtn.className=e.replace(" menuBtnPos "," "),menuBtn.className+=" animateMenuBtn")}function adjustSizes(){windowHeight=$j(window).height(),windowWidth=$j(window).width(),$j(".fullscreen").css("height",windowHeight),$j(".vertical-center").each(function(){$j(this).css("margin-top",($j(this).parent().height()-$j(this).height())/2)}),$j("section.page").each(function(){var e=$j(this).find(".content"),n=e.height()+132;windowHeight>n?e.css("padding-top",(windowHeight-e.height())/2):e.css("padding-bottom",66)})}function playSlider(){var e=$j(".slider"),n=e.find(".slides"),i=n.find(".slide").length;if(i>0){if(e.append("<div class='controls'><ul></ul><div class='selected'></div></div>"),n.find(".slide").each(function(){var i=n.find(".slide").index($j(this))+1;e.find(".controls ul").append("<li><span>0"+i+"</span></li>")}),e.find(".controls li").click(function(){var i=e.find(".controls li").index($j(this)),t=n.find(".slide").eq(i);t.addClass("current").siblings().removeClass("current");var s=e.find(".controls li").eq(i);s.addClass("current").siblings().removeClass("current");var a=s.position();e.find(".controls .selected").css("left",a.left)}),0===n.find(".current").length){var t=n.find(".slide").eq(0);t.addClass("current");var s=e.find(".controls li").eq(0);s.addClass("current").siblings().removeClass("current");var a=s.position();e.find(".controls .selected").css("left",a.left)}setInterval(function(){var t=n.find(".slide.current"),s=n.find(".slide").index(t)+1,a=0;i>s&&(a=s);var d=n.find(".slide").eq(a);d.addClass("current").siblings().removeClass("current");var o=e.find(".controls li").eq(a);o.addClass("current").siblings().removeClass("current");var r=o.position();i>s&&e.find(".controls .selected").addClass("expanded"),e.find(".controls .selected").css("left",r.left),setTimeout(function(){e.find(".controls .selected").removeClass("expanded")},4e3)},7e3)}}var windowHeight,windowWidth,settings,retina=!1,$j=jQuery.noConflict();jQuery(document).ready(function(e){"use strict";settings={enableAnimations:$j("body").attr("data-animated")},e(function(){e(".footer").css({"-webkit-filter":"invert(100%)","border-top":"none"}),e("#rightFoot a").css({color:"rgb(68, 68, 68)"})});var n="(-webkit-min-device-pixel-ratio: 1.5), (min--moz-device-pixel-ratio: 1.5), (-o-min-device-pixel-ratio: 3/2), (min-resolution: 1.5dppx)",i="undefined"==typeof exports?window:exports;i.devicePixelRatio>1&&i.matchMedia&&i.matchMedia(n).matches&&(retina=!0),$j(".background.image").each(function(){var e=$j(this).attr("data-url");return void 0===e?!1:void $j(this).css("background-image","url("+e+")")}),$j("header.header .menu").click(function(){$j("header.header").toggleClass("active"),playAnimations(),$j("nav.navigation").toggleClass("active"),adjustSizes()}),e("#menuBtn").click(function(e){animateMenu()})}),jQuery(window).load(function(e){adjustSizes(),playSlider(),animateMenu()}),jQuery(window).resize(function(e){adjustSizes()});
//# sourceMappingURL=home.js.map
