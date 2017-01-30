/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can
 * always reference jQuery with $, even when in .noConflict() mode.
 * ======================================================================== */

(function($) {

  // Use this variable to set up the common and page specific functions. If you
  // rename this variable, you will also need to rename the namespace below.
  var Sage = {
    // All pages
    'common': {
      init: function() {

function MButton(el) {
  var _this = this;
  _this.el = el;
  _this.ripples = {};
  _this.rippleCount = 0;
  _this.rippleWrapper = _this.el.querySelector('ripples');
}

MButton.prototype.makeRipple = function(offset, size, id) {
  var _this = this;
  var ripple = document.createElement('ripple');


  function release() {
      this.addEventListener('transitionend', function() {
        _this.ripples[id].remove();
      });
  }
  setTimeout(function() {
    _this.el.addEventListener('mouseup', release);
    _this.el.addEventListener('mouseleave', release);
  }, 0);
  return ripple;
};

MButton.prototype.mouseDown = function(e) {
  var _this = this;
  var height = e.target.clientHeight;
  var width = e.target.clientWidth;
  var size = Math.max(height, width);
  var offset = {
    x: e.offsetX || e.layerX,
    y: e.offsetY || e.layerY
  };
  var sX = Math.abs(size / 2 - offset.x) * 2;
  var sY = Math.abs(size / 2 - offset.y) * 2;
  size += Math.max(sX, sY);
  var id = ++_this.rippleCount;
  _this.ripples[id] = _this.makeRipple(offset, size, id);
  _this.rippleWrapper.appendChild(_this.ripples[id]);
  _this.el.classList.toggle('selected');
};

var mButtons = document.querySelectorAll('m-button');
var forEach = [].forEach;

forEach.call(mButtons, function(el, i) {
  var mButton = new MButton(el);

  function mouseDown(e) {
    mButton.mouseDown(e);
  }
  el.addEventListener('mousedown', mouseDown);
});

        // JavaScript to be fired on all pages
        $('img').bind('contextmenu', function(e) {
          return false;
        });



        $(function() {
        $("img.lazy").lazyload();
      });

        setTimeout(function() {
          $('body').addClass('loaded');
          $('h1').css('color', '#222222');
        }, 1000);

        var $menu = $('.overlay');
        var hexNav = document.getElementById('hexNav');

        $("#menuBtn").click(function(e) {
          //animateMenu();
          var className = ' ' + hexNav.className + ' ';
          if (~className.indexOf(' active ')) {
            hexNav.className = className.replace(' active ', ' ');
            $menu.fadeOut();
            $('body').css({
              'overflow-y': 'scroll'
            });
          } else {
            hexNav.className += ' active';
            $menu.fadeIn();
            $('body').css({
              'overflow-y': 'hidden'
            });
          }
        });

        gallery();

        function gallery() {
          var e = $(window).width(),
            t = Math.floor(e / 3) * 3,
            n = Math.floor(t / 3),
            r = Math.floor(t / 3 * (9 / 16)),
            i = Math.floor(t / 2 * (9 / 16));
          $(".block").css("height", r + "px");
          if ($(window).width() < 640) {
            $(".block").attr("style", "");
            $(".block.two-thirds").css("height", r * 2 + "px");
            $(".block.full").css("height", r * 3 + "px");
            $(".block.one-third-double-height").css("height", r * 2 + "px");
            $(".block.two-thirds-double-height").css("height", r * 4 + "px");
            $(".block.full-double-height").css("height", r * 6 + "px");
            $(".block.one-third-push-down").css("height", r + "px").css("margin-top", r + "px");
            $(".block.one-third-bottom-left").css("height", r + "px").css("margin-top", "-" + r + "px").css("left", 0).css("z-index", "9").css("float", "left");
            $(".block.half").css("height", i + "px");
            $(".block.half-double-height").css("height", i * 2 + "px");
          } else if ($(window).width() >= 640 && $(window).width() < 1100) {
            $(".block").css("margin-top", "0px");
            $(".block.medium-two-thirds").css("height", r * 2 + "px");
            $(".block.medium-full ").css("height", r * 3 + "px");
            $(".block.medium-one-third-double-height").css("height", r * 2 + "px");
            $(".block.medium-two-thirds-double-height").css("height", r * 4 + "px");
            $(".block.medium-full-double-height").css("height", r * 6 + "px");
            $(".block.medium-one-third-push-down").css("height", r + "px").css("margin-top", r + "px");
            $(".block.medium-one-third-bottom-left").css("height", r + "px").css("margin-top", "-" + r + "px").css("left", 0).css("z-index", "9").css("float", "left");
            $(".block.medium-half").css("height", i + "px");
            $(".block.medium-half-double-height").css("height", i * 2 + "px");
          } else if ($(window).width() >= 1100) {
            $(".block.large-two-thirds").css("height", r * 2 + "px");
            $(".block.large-full").css("height", r * 3 + "px");
            $(".block.large-one-third-double-height").css("height", r * 2 + "px");
            $(".block.large-two-thirds-double-height").css("height", r * 4 + "px");
            $(".block.large-full-double-height").css("height", r * 6 + "px");
            $(".block.large-one-third-push-down").css("height", r + "px").css("margin-top", r + "px");
            $(".block.large-one-third-bottom-left").css("height", r + "px").css("margin-top", "-" + r + "px").css("left", 0).css("z-index", "9").css("float", "left");
            $(".block.large-half").css("height", i + "px");
            $(".block.large-half-double-height").css("height", i * 2 + "px");
          }
        }
        $(window).resize(function() {

          gallery();

        });

      },
      finalize: function() {
        // JavaScript to be fired on all pages, after page specific JS is fired
      }
    },
    // Home page
    'home': {
      init: function() {
        // JavaScript to be fired on the home page
      },
      finalize: function() {
        // JavaScript to be fired on the home page, after the init JS
      }
    },
    // About us page, note the change from about-us to about_us.
    'about_us': {
      init: function() {
        // JavaScript to be fired on the about us page

      }
    }
  };

  // The routing fires all common scripts, followed by the page specific scripts.
  // Add additional events for more control over timing e.g. a finalize event
  var UTIL = {
    fire: function(func, funcname, args) {
      var fire;
      var namespace = Sage;
      funcname = (funcname === undefined) ? 'init' : funcname;
      fire = func !== '';
      fire = fire && namespace[func];
      fire = fire && typeof namespace[func][funcname] === 'function';

      if (fire) {
        namespace[func][funcname](args);
      }
    },
    loadEvents: function() {
      // Fire common init JS
      UTIL.fire('common');

      // Fire page-specific init JS, and then finalize JS
      $.each(document.body.className.replace(/-/g, '_').split(/\s+/), function(i, classnm) {
        UTIL.fire(classnm);
        UTIL.fire(classnm, 'finalize');
      });

      // Fire common finalize JS
      UTIL.fire('common', 'finalize');
    }
  };

  // Load Events
  $(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.




/*
*   Turin (WordPress)
*   Copyright 2015, Limitless
*   www.limitless.company
*/
