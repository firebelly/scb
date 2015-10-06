/*
 * SCB admin - Firebelly 2015
 */

// Good design for good reason for good namespace
var SCB_admin = (function($) {

  var _updateTimer;

  function _init() {
    // hack the update from bottom plugin to show it earlier
    $(window).scroll(function(){
      _updateTimer = setTimeout(function() {
        clearTimeout(_updateTimer);
        if($(window).scrollTop() > $('#submitdiv').height()) {
          $('#updatefrombottom').show();
        } else {
          $('#updatefrombottom').hide();
        }
      }, 250);
    });

  }
  // public functions
  return {
    init: _init
  };

})(jQuery);

// Fire up the mothership
jQuery(document).ready(SCB_admin.init);
