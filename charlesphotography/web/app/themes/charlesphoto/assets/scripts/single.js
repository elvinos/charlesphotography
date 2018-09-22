jQuery(document).ready(function ($) {


$(window).bind("load", function() {

  var singleImage = $(document.getElementById("singleImage"));
  imageHeight = singleImage.height();
  imageWidth = singleImage.width();
  imageMarginL = (singleImage.outerWidth(true) - imageWidth)/2;
  imageMarginR = (singleImage.outerWidth(true) - imageWidth)/2;
  hoverButtons(imageHeight, imageWidth, imageMarginL, imageMarginR);

    var $body = $('body');
  	var $item = $('.imgBox2');
  	var $img = $('.img');
  	var $info = $('.info');

  	$item.click(function() {
  		$(this).toggleClass('active');
  		$(this).siblings().removeClass('active');
  		$body.toggleClass('gallery-open');
  	});

});

function hoverButtons(imageHeight, imageWidth, imageMarginL, imageMarginR) {

/* Function to define the height and postion of the hover arrows on single Image*/
  $('.imgBoxOL').css({
  'position':"absolute",
  'top':'80px',
  'width':imageWidth/3,
  'height': imageHeight + 'px',

});

  $('.leftIB').css({
  'left':imageMarginL + 'px',
  });
  $('.rightIB').css({
  'right':imageMarginR + 'px',
  });
  var is_touch_device = 'ontouchstart' in document.documentElement;
  if(is_touch_device){
    $('.mfp-arrow').css({
      'opacity': 1,
      'filter':'alpha(opacity=100)',
    });
  }
  $('.mfp-arrow').css({
  'padding-top':imageHeight/2 - 35 +'px',
  'color':'#ffffff',
  });

  if ($('#site-wrapper').hasClass('show-nav')) {
    $('.imgBoxOL').css({
      'top':'0px'
    });
}
}



  $(function() {
      $('.toggle-nav').click(function() {
          // Calling a function in case you want to expand upon this.
      toggleNav();

      $('.button-plus').toggleClass('active-but');

      animateIt = animateIt ? false : true;

      loopAnimateForward('width');

      var SideMenu = document.getElementById('site-menu');
      // get the current value of the display property
      var displaySetting = SideMenu.style.display;


      if (displaySetting == 'block') {
        // visible. hide it
        SideMenu.style.display = 'none';
        // change button text
      }
      else {
        //  is hidden. show it
        SideMenu.style.display = 'block';
        // change button text
      }

      if ($('#site-wrapper').hasClass('show-nav')) {
        tabViewPush(0,0);

    }
      });
  });

  function toggleNav() {
  var sitewrapper = document.getElementById("site-wrapper");
      if ($(sitewrapper).hasClass('show-nav')) {
          // Do things on Nav Close
          $(sitewrapper).removeClass('show-nav');
          var sitecanvas = document.getElementById("main-canvas");
          sitecanvas.style.height = 'auto';
          sitewrapper.style.height = 'auto';

      } else {
          // Do things on Nav Open
          $('#site-wrapper').addClass('show-nav');
          tabViewPush(0,0);
          tabViewPush(0,0);
      }

  }
  $(document).keyup(function(e) {
      if (e.keyCode == 27) {
          if ($('#site-wrapper').hasClass('show-nav')) {
              // Assuming you used the function I made from the demo
              toggleNav();
          }
      }
  });

  // window.onresize = loopAnimateForward('width');

  /*========================================
  =            CUSTOM FUNCTIONS            =
  ========================================*/

  var imgToggled;
  var animateIt;

  var mq = window.matchMedia( "(min-width: 1000px)" );
  var mw = window.matchMedia( "(max-width: 999px)" );

  var button = document.getElementById('toggle-nav'); // Assumes element with id='button'
  var win = $(this);

  function loopAnimateForward(type) {

  var singleImage = $(document.getElementById("singleImage"));
  var mainCanvas = $(document.getElementById("main-canvas"));
    if (type == 'width' && animateIt && win.width() >= 1000) {
      $('.imgBox img').css({
      'max-width':'calc( 100% - 500px)',
      'float': 'right'
    });

    imageHeightNew = singleImage.height();
    imageWidthNew = singleImage.width();
    imageMarginL = (mainCanvas.outerWidth(true) - singleImage.outerWidth(true) - 45);
    imageMarginR = (singleImage.outerWidth(true) - imageWidthNew)/2 -15;

    hoverButtons(imageHeightNew, imageWidthNew, imageMarginL, imageMarginR);

   } else {
      $('.imgBox img').css({
      'width':"",
      'max-width':'100%',
      'float': ''
      });

      imageHeight = singleImage.height();
      imageWidth = singleImage.width();
      imageMarginL = (singleImage.outerWidth(true) - imageWidth)/2;
      imageMarginR = (singleImage.outerWidth(true) - imageWidth)/2;
      hoverButtons(imageHeight, imageWidth, imageMarginL, imageMarginR);
  }

  }

  $( window ).resize(function() {
    var img = $(document.getElementById('singleImage'));
    imageHeight = img.height();
    imageWidth = img.width();
    imageMarginL = (img.outerWidth(true) - imageWidth)/2;
    imageMarginR = (img.outerWidth(true) - imageWidth)/2;
    var win1 = $(this);
    var height = img.clientHeight;
    if ($('#site-wrapper').hasClass('show-nav') && win1.width() <= 1000) {
      $('#site-menu').css({
        'margin-top': '-240px',
      });
      $('.imgBox img').css({
             'width':"",
             'max-width':'100%',
             'float': ''
         });
      tabViewPush(0,0);
    }
    else if ($('#site-wrapper').hasClass('show-nav') && win1.width() > 1000){
    $('.imgBox img').css({
    'max-width':'calc( 100% - 500px)',
    'float': 'right'
    });
    $('#site-menu').css({
      'margin-top': '0px',
    });
    tabViewPush(0,0);
  imageMarginL = (img.outerWidth(true) - imageWidth)/2 + 500;
  }
    else if (win1.width() > 1000) {
        $('#site-menu').css({
          'margin-top': '0px',
        });
      }
    else if (win1.width() <= 1000 ) {
    $('.imgBox img').css({
           'width':"",
           'max-width':'100%',
           'float': ''
       });
     }
  else {
  }
  // tabViewPush(250,50);

  hoverButtons(imageHeight, imageWidth, imageMarginL, imageMarginR);
  });


var fadeInVar = 0;
var delayVar = 0;

function tabViewPush(fadeInVar, delayVar) {

   // Variables
   var singleImage = $(document.getElementById("singleImage"));
   imageHeight = singleImage.height();
 	var clickedTab = $(".tabs > .active");
 	var tabWrapper = $(".tab__content");
  var contentWrapper = $(".content__wrapper").height() + 50;
 	var activeTab = tabWrapper.find(".active");
  var sitecanvas = document.getElementById("main-canvas");
  var sitewrapper = document.getElementById("site-wrapper");

 	var activeTabHeight = activeTab.outerHeight();

  var siteHeight = $('#main-canvas').height();

 	// Show tab on page load
 	activeTab.show();

 	// Set height of wrapper on page load
 activeTabHeight= activeTab.height();
// console.log("contentWrapperHeight " + contentWrapper);
//  console.log("tabWrapper " + tabWrapper.height());
//  console.log("activeTabHeight " + activeTabHeight);
//  console.log("contentWrapperHeight " + contentWrapper);

  if ($(window).width() < 1000) {
    $('#site-menu').css({
      'margin-top': '-240px',
    });
    sitewrapper.style.height = activeTabHeight + imageHeight + 400 +'px';
    // console.log('siteHeight ' + sitewrapper.style.height);
  }
  else {
    $('#site-menu').css({
      'margin-top': '0px',
    });
    if((0.9 * $(window).height()) < activeTabHeight ){
    sitecanvas.style.height = activeTabHeight + 200 +'px';
    }
  else{
    sitecanvas.style.height = 'auto';
  }

  }


 	$(".tabs > li").on("click", function() {


 		// Remove class from active tab
 		$(".tabs > li").removeClass("active");

 		// Add class active to clicked tab
 		$(this).addClass("active");

 		// Update clickedTab variable
 		clickedTab = $(".tabs .active");

 		// fade out active tab
 		activeTab.fadeOut(fadeInVar, function() {

 			// Remove active class all tabs
 			$(".tab__content > li").removeClass("active");

 			// Get index of clicked tab
 			var clickedTabIndex = clickedTab.index();

 			// Add class active to corresponding tab
 			$(".tab__content > li").eq(clickedTabIndex).addClass("active");

 			// update new active tab
 			activeTab = $(".tab__content > .active");

 			// Update variable
 			activeTabHeight = activeTab.outerHeight();

      if ($(window).width() < 1000) {
        $('#site-menu').css({
          'margin-top': '-240px',
        });
        sitewrapper.style.height = activeTabHeight + imageHeight + 400 +'px';
      }
      else {
        if((0.9 * $(window).height()) < activeTabHeight ){
        sitecanvas.style.height = activeTabHeight + 200 + 'px';
        sitewrapper.style.height = 'auto';

      }
    else{
      sitecanvas.style.height = 'auto';
    }
        $('#site-menu').css({
          'margin-top': '0px',
        });
      }

 			// Animate height of wrapper to new tab height
 			tabWrapper.stop().delay(delayVar).animate({
 				height: activeTabHeight
 			}, 350, function() {

 				// Fade in active tab
 				activeTab.delay(delayVar).fadeIn(fadeInVar);

 			});
 		});
 	});
}
});
