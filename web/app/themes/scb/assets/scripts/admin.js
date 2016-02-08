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

    if($('#sort-projects-form').length) {
      $('#sort-projects-form ul').sortable({
        'update' : function(e, ui) {
          $.post( ajaxurl, {
            action: 'update-menu-order',
            order: $('#sort-projects-form ul').sortable('serialize'),
          });
        }
      });
      $('select.filter-projects').on('change', function(e) {
        var slug = $(this).val();
        location.href = '/wp/wp-admin/edit.php?post_type=project&page=sort_projects&category_name='+slug;
      });

    }


  }
  // public functions
  return {
    init: _init
  };

})(jQuery);

// Fire up the mothership
jQuery(document).ready(SCB_admin.init);
