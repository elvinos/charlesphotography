jQuery(document).ready(function ($) {
console.log("Category");
  $(function() {
    if ($(window).width() <= 768) {
      // menu.style.right = "-250px";
      document.getElementById("menu-toggle").className = "";
    }
});
/****************New Category Page ***********************/
$("#menu-toggle").click(function(e) {
  e.preventDefault();
  $("#wrapper").toggleClass("toggled");
  if ($('#wrapper').hasClass('toggled')) {
    // Do things on Nav Close
    (function() {
      var wrapper = document.getElementById("wrapper");
      var menu = document.getElementById("menu-id");
      wrapper.style.height = "auto";
      if ($(window).width() <= 768) {
        // menu.style.right = "-250px";
        $("#menu-toggle").css("right", -220 + "px");
      }
    })();
  } else {
    // Do things on Nav Open
    (function() {
      var wrapper = document.getElementById("wrapper");
      var height = document.documentElement.scrollHeight;
      // wrapper.style.height = height + 50 + "px";
      if ($(window).width() <= 768) {
        // menu.style.right = "-250px";
        $("#menu-toggle").css("right", 30 + "px");
      }
    })();
  }

});
/**************** ***********************/

});
