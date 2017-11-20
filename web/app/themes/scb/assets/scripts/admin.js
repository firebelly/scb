/*
 * SCB admin - Firebelly
 */

// Good design for good reason for good namespace
var SCB_admin = (function($) {

  var _updateTimer,
  _submitDivHeight;

  function _init() {

    // Hack the update from bottom plugin to show it earlier
    _submitDivHeight = $('#submitdiv').height();
    $(window).scroll(function(){
      clearTimeout(_updateTimer);
      _updateTimer = setTimeout(function() {
        $('#updatefrombottom').toggle( $(window).scrollTop() > _submitDivHeight );
      }, 150);
    });

    // Behavior for custom Sort Projects admin page
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

  return {
    init: _init
  };

})(jQuery);

// Fire up the mothership
jQuery(document).ready(SCB_admin.init);
