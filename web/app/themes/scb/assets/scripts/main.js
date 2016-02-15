// SCB - Firebelly 2015
/*jshint latedef:false*/

// Good Design for Good Reason for Good Namespace
var SCB = (function($) {

  var screen_width = 0,
      breakpoint_small = false,
      breakpoint_medium = false,
      breakpoint_large = false,
      breakpoint_array = [480,1000,1200],
      $document,
      $sidebar,
      $collection,
      $modal,
      loadingTimer,
      page_at;

  function _init() {
    // touch-friendly fast clicks
    FastClick.attach(document.body);

    // Cache some common DOM queries
    $document = $(document);
    $collection = $('.collection.mini');
    $modal = $('.global-modal');
    $('body').addClass('loaded');

    // Set screen size vars
    _resize();

    // Fit them vids!
    $('main').fitVids();

    // Homepage (pre _initMasonry)
    if ($('.home.page').length) {
      page_at = 'homepage';
      $('.project-categories').on('click', 'a', function(e) {
        e.preventDefault();
        var thisUrl =  $(this).attr('href'),
            $li = $(this).parent('li'),
            $activeSiblings = $li.siblings('.active');
        // match height of absolutely-positing children lists
        var childHeight = $li.find('ul.children:first').outerHeight();
        $('.project-categories').outerHeight(childHeight + 20);
        $li.find('ul.children:first').addClass('active');

        if (!$li.hasClass('active')) {
          $li.addClass('active');
        } else {
          $li.removeClass('active');
          $li.find('ul.children').removeClass('active');
        }

        // Activate/deactivate the parent ul
        var $parentUl = $li.parent('ul');
        if (!$parentUl.hasClass('children')) {
          if (!$parentUl.is('.active')) {
            $li.parent('ul').addClass('active');
          } else if ($parentUl.hasClass('active') && !$activeSiblings.length) {
            $parentUl.removeClass('active');
          }
        }

        // If there are active siblings, deactivate them and their children
        if ($activeSiblings.length) {
          $activeSiblings.find('li.active').removeClass('active');
          $activeSiblings.find('ul').removeClass('active');
          $activeSiblings.removeClass('active');
        }

        var project_categories = [];
        $('.project-categories li.active>a').each(function() {
          project_categories.push($(this).text());
        });
        var parentCategory = (!$li.parents('li').length ? $(this) : $li.parents('li').last().children('a'));
        var parentUrl = parentCategory.attr('href');
        parentCategory = parentCategory.text();
        $.ajax({
            url: wp_ajax_url,
            method: 'post',
            data: {
                action: 'load_more_posts',
                post_type: 'project',
                page: 1,
                per_page: 6,
                project_category: project_categories.join(',')
            },
            success: function(data) {
              var $data = $(data);
              if (loadingTimer) { clearTimeout(loadingTimer); }
              $('section.projects .initial-section').html($data).removeClass('loading');
              $('body').attr('data-pageClass', (parentUrl.replace(/\bprojects\b|\//g,'')));
              window.history.pushState({}, parentCategory, thisUrl);
              $('.load-more').attr('data-category', project_categories[0].toLowerCase());
              $('.load-more-container').empty();
            }
        });
      });
    }

    // Toggle Categories filter
    $(document).on('click', '.categories-toggle', function(e) {
      $('.project-categories').toggleClass('expanded');
    });

    if ($('.project-categories').length) {
      $(window).on('scroll', function() {
        if($(window).scrollTop() >= $('.main .projects').offset().top - $('.site-header').outerHeight()) {
          $('.project-categories').addClass('fixed');
          if ($('.categories-toggle').is('.expanded') && !$('.project-categories').is('.expanded')) {
            $('.categories-toggle').removeClass('expanded');
          }
        } else if ($(window).scrollTop() <= $('.main').offset().top + $('.site-header').outerHeight()) {
          $('.project-categories').removeClass('fixed explanded');
          $('.categories-toggle').addClass('expanded');
        }
      });
    }

    // Show/hide mini collection in nav
    $(document).on('click', '.show-collection', function(e) {
      e.preventDefault();
      if ($collection.hasClass('active')) {
        _hideCollection();
      } else {
        _showCollection();
      }
    });
    $(document).on('click', '.hide-collection', function(e) {
      e.preventDefault();
      if ($collection.hasClass('active')) {
        _hideCollection();
      }
    });

    // Show/hide glabal modal in nav
    $(document).on('click', '.show-modal', function(e) {
      e.preventDefault();
      if ($modal.hasClass('active')) {
        _hideModal();
      } else {
        _showModal();
      }
    });
    $(document).on('click', '.hide-modal', function(e) {
      e.preventDefault();
      if ($modal.hasClass('active')) {
        _hideModal();
        _hidePageOverlay();
      }
    });

    // Add/Remove from collection links
    $(document).on('click', '.collection-action', function(e) {
      e.preventDefault();
      var $link = $(this);
      var id = $link.data('id') || '';
      var collection_id = $link.parents('section.collection:first').data('id');
      var action = $link.data('action');

      // Add action class to article for styling perposes
      if ($link.parents('section.collection').length) {
        $(this).closest('article').addClass(action);
      }

      $.ajax({
        url: wp_ajax_url,
        method: 'post',
        dataType: 'json',
        data: {
          action: 'collection_action',
          do: action,
          post_id: id,
          collection_id: collection_id
        }
      }).done(function(response) {
        // if add/remove, repopulate collection & reinit behavior
        if (action.match(/add|remove/)) {
          _updatePostCollectionLinks(id,action);
          // repopulate all collections
          $('section.collection').html(response.data.collection_html);
          _initCollectionBehavior();
          _showCollection();
          _collectionMessage(action);
        } else if (action.match(/pdf/)) {
          var buttonText = $(e.target).text();
          if (response.success) {
            if (buttonText.match('print')) {
              // Make tmp iframe with PDF and trigger print()
              $('<iframe id="pdf-print"></iframe>').appendTo('body')
                .attr('src', response.data.pdf.url)
                .hide()
                .load(function(){
                  this.focus();
                  this.contentWindow.print();
                 });
            } else {
              // Make tmp link to trigger download of PDF (from http://stackoverflow.com/a/27563953/1001675)
              var link = document.createElement('a');
              link.href = response.data.pdf.url;
              link.download = response.data.pdf.name;
              link.click();
              // Perhaps this is needed for <=IE10?
              // window.location = response.data.pdf.url;
            }
          } else {
            alert(response.data.message);
          }
        }
      });
    });

    // Hide page overlay when clicked
    $(document).on('click', '#page-overlay', function() {
      _hidePageOverlay();
      _hideCollection();
      _hideModal();
      _hideMobileNav();
    });

    // Esc handlers
    $(document).keyup(function(e) {
      if (e.keyCode === 27) {
        _hideSearch();
        _hideCollection();
        _hideModal();
        _hideMobileNav();
        _hidePageOverlay();
        _hideEmailForm();
      }
    });

    // Smoothscroll links
    $('a.smoothscroll').click(function(e) {
      e.preventDefault();
      var href = $(this).attr('href');
      _scrollBody($(href));
    });

    // Scroll down to hash afer page load
    $(window).load(function() {
      if (window.location.hash) {
        _scrollBody($(window.location.hash)); 
      }
    });

    _initNav();
    // _initSearch();
    // _initMasonry();
    _initLoadMore();
    _initPostModals();
    _initBigClicky();

    // AJAX form submissions
    _initApplicationForms();

    // Drag-sorting of Collections
    _initCollectionBehavior();

    // Init SVG Injection
    _injectSvgSprite();

    _plusButtons();
    _shrinkHeader();

  } // end init()

  function _showEmailForm() {
    $('#email-collection-form').addClass('active');
    _scrollBody($('.collection .collection-actions'), 250);
  }
  function _hideEmailForm() {
    $('#email-collection-form').removeClass('active');
  }
  // AJAX Application form submissions
  function _initApplicationForms() {
    // only AJAXify if browser supports FormData (necessary for file uploads via AJAX, <IE10 = no go)
    if( window.FormData !== undefined ) {
      $('.application-form').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var formData = new FormData(this);
        formData.append('action', 'application_submission');
        $.ajax({
          url: wp_ajax_url,
          method: 'POST',
          // data: data + '&action=application_submission',
          data: formData,
          dataType: 'json',
          mimeType: 'multipart/form-data',
          processData: false,
          contentType: false,
          cache: false,
          success: function(response) {
            $form[0].reset();
            alert(response.data);
          },
          error: function(response) {
            alert(response.data);
          }
        });
      });
    }
  }

  function _injectSvgSprite() {
    boomsvgloader.load('/app/themes/scb/assets/svgs/build/svgs-defs.svg'); 
  }

  function _plusButtons() {
    $('.plus-button.-expandable').on('click', function(e) {
      $(this).toggleClass('expanded');
    });
  }

  function _shrinkHeader() {
    if ($(document).scrollTop() > 100) {
      $('.site-header').addClass('shrink');
    }
    $(document).on("scroll", function(){
      if ($(document).scrollTop() > 100) {
        $('.site-header').addClass('shrink');
      } else {
        $('.site-header').removeClass('shrink');
      }
    });
  }

  function _updatePostCollectionLinks(id,action) {
    $('article[data-id='+id+'] .collection-action').each(function() {
      if (action==='add') {
        $(this).removeClass('collection-add').addClass('collection-remove');
        $(this).find('.collection-text').data('action', 'remove').text('Remove from Collection');
      } else {
        $(this).removeClass('collection-remove').addClass('collection-add');
        $(this).find('.collection-text').data('action', 'add').text('Add to Collection');
      }
    });

  }

  function _showModal() {
    _showPageOverlay(); 
    $('body').addClass('modal-active');
    $modal.addClass('display');
    setTimeout(function() {
      $modal.addClass('active');
    }, 100);
    _scrollBody($('body'), 250);
    if ($modal.find('.modal-content').is(':empty')) {
      $modal.addClass('empty');
    } else {
      $modal.removeClass('empty');
    }
  }

  function _hideModal() {
    _hidePageOverlay();
    $('body').removeClass('modal-active');
    $modal.removeClass('active');
    setTimeout(function() {
      $modal.removeClass('display');
    }, 500);
    _scrollBody($('body'), 250);
  }

  function _showCollection() {
    _hideModal();
    _showPageOverlay(); 
    $('body').addClass('collection-active');
    $collection.addClass('display');
    setTimeout(function() {
      $collection.addClass('active');
    }, 100);
    _scrollBody($('body'), 250);
    if (!$collection.find('article').length) {
      $collection.addClass('empty');
    } else {
      $collection.removeClass('empty');
    }
    // Hide mobile nav
    if($('.site-nav').is('.active')) {
      _hideMobileNav();
    }
  }

  function _hideCollection() {
    _hidePageOverlay();
    $('body').removeClass('collection-active');
    $collection.removeClass('active');
    setTimeout(function() {
      $collection.removeClass('display');
    }, 500);
  }

  // Show collection message dialog
  function _collectionMessage(messageType) {
    var message,
        feedbackTimer;

    if (messageType==="remove") {
      message = "Your selection has been removed from the collection.";
    } else if (messageType==="add") {
      message = "Your selection has been added to the collection.";
    } else {
      message = messageType;
    }

    function _hideFeedback() {
      $collection.find('.feedback-container').removeClass('show-feedback');
      $collection.find('.feedback-container .feedback p').text('');

    }

    $collection.find('.feedback-container .feedback p').text(message);
    setTimeout(function(){
      $collection.find('.feedback-container').addClass('show-feedback');
    },250);

    feedbackTimer = setTimeout(_hideFeedback, 3000);

    $collection.find('.feedback-container').on('mouseenter', function() {
      clearTimeout(feedbackTimer);
    }).on('mouseleave', function() {
      setTimeout(_hideFeedback, 1000);
    });
    
  }

  // Init collection sorting, title editing, etc
  function _initCollectionBehavior() {
    // Email collection
    $('.email-collection').on('click', function(e) {
      e.preventDefault();
      _showEmailForm();
    });
    $('#email-collection-form form').on('submit', function(e) {
      e.preventDefault();
      $.ajax({
          url: wp_ajax_url,
          method: 'post',
          dataType: 'json',
          data: $(this).serialize()
      }).done(function(response) {
        if (response.success) {
          _hideEmailForm();
          _scrollBody($('.collection .feedback-container'), 250);
          _collectionMessage('Your email was sent successfully.');
        } else {
          _scrollBody($('.collection .feedback-container'), 250);
          _collectionMessage('There was an error sending your email: ' + response.data.message);
        }
      }).fail(function(response) {
        _scrollBody($('.collection .feedback-container'), 250);
        _collectionMessage('There was an error sending your email.');
      });


    });
    // Update collection title
    $('.collection-title').on('blur', function() {
      var title = $('.collection-title').text();
      var collection_id = $('.collection-title').data('id');
      $.ajax({
          url: wp_ajax_url,
          method: 'post',
          dataType: 'json',
          data: {
            action: 'collection_action',
            do: 'title',
            title: title,
            collection_id: collection_id
          }
      }).done(function(response) {
        if (response.success) {
          $('.collection-title').addClass('updated');
          setTimeout(function() { $('.collection-title').addClass('updated'); }, 1500);
        }
      });
    });

    // Sort collection objects
    $('.sortable').each(function() {
      var collection_sort = $(this).sortable({
        containerSelector: 'div.sortable',
        itemSelector: 'article',
        placeholder: '<article class="placeholder"><div class="placeholder-inner"></div></article>',
        // vertical: false,
        onDragStart: function ($item, container, _super) {
          var offset = $item.offset(),
              pointer = container.rootGroup.pointer;

          adjustment = {
            left: pointer.left - offset.left,
            top: pointer.top - offset.top
          };

          _super($item, container);
        },
        onDrag: function ($item, position) {
          $item.css({
            left: position.left - adjustment.left,
            top: position.top - adjustment.top
          });
        },
        onDrop: function ($item, container, _super) {
          var data = collection_sort.sortable('serialize').get();
          var collection_id = $(container.el[0]).data('id');
          $.ajax({
              url: wp_ajax_url,
              method: 'post',
              dataType: 'json',
              data: {
                action: 'collection_sort',
                collection_id: collection_id,
                post_array: data[0]
              }
          }).done(function(response) {
            $(container.el[0]).addClass('updated');
            setTimeout(function() { $(container.el[0]).addClass('updated'); }, 1500);
          }).fail(function(response) {
            alert(response.data.message);
          });

          _super($item, container);
        }
      });
    });
  }

  function _initPostModals() {
    $(document).on('click', '.show-post-modal', function(e) {
      var $thisTarget = $(e.target);
      // Ignore links inside that do something else
      if ($thisTarget.is('.no-ajaxy') || $thisTarget.parents('.no-ajaxy').length) {
        return;
      }
      e.preventDefault();

      var post_id = $(this).data('id'),
          modal_type = $(this).data('modal-type'),
          $postModal = $modal.addClass('post-modal ' + modal_type);

      // Hide the collection if it's open
      _hideCollection();

      $postModal.find('.modal-content').empty();

      $.ajax({
        url: wp_ajax_url,
        method: 'post',
        dataType: 'html',
        data: {
            'action': 'load_post_modal',
            'post_id': post_id
        },
        success: function(response) {
          var $postData = $(response);
          $('.post-modal .modal-content').append($postData);
          _showModal();
        },
        error: function(error){
          console.log(error);
        }
      }); 

    });       
  }

  function _showPageOverlay() {
    if (!$('#page-over').length) {
      $('body').prepend('<div id="page-overlay"></div>');
    }
    setTimeout(function() {
      $('#page-overlay').addClass('active');
    },50);
  }

  function _hidePageOverlay() {
    $('#page-overlay').remove();
  }

  function _initBigClicky() {
    $(document).on('click', '.bigclicky', function(e) {
      if (!$(e.target).is('a')) {
        e.preventDefault();
        var link = $(this).find('h1:first a,h2:first a');
        var href = link.attr('href');
        if (href) {
          if (e.metaKey || link.attr('target')) {
            window.open(href);
          } else {
            location.href = href;
          }
        }
      }
    });
  }

  function _scrollBody(element, duration, delay) {
    if ($('#wpadminbar').length) {
      wpOffset = $('#wpadminbar').height();
    } else {
      wpOffset = 0;
    }
    element.velocity("scroll", {
      duration: duration,
      delay: delay,
      offset: -wpOffset
    }, "easeOutSine");
  }

  function _initSearch() {
    $('.search-form:not(.mobile-search) .search-submit').on('click', function (e) {
      if ($('.search-form').hasClass('active')) {

      } else {
        e.preventDefault();
        $('.search-form').addClass('active');
        $('.search-field:first').focus();
      }
    });
    $('.search-form .close-button').on('click', function() {
      _hideSearch();
      _hideMobileNav();
    });
  }

  function _hideSearch() {
    $('.search-form').removeClass('active');
  }

  // Handles main nav
  function _initNav() {
    // SEO-useless nav toggler
    $('<button class="menu-toggle"><span class="lines"></span></button>')
      .prependTo('.site-header .wrap')
      .on('click', function(e) {
        if($('.site-nav').is('active')) {
          _hideMobileNav();
          _hidePageOverlay();
        } else {
          _showMobileNav();
          _showPageOverlay();
        }
      });

    $('<button class="plus-button close hide-nav"><div class="plus"></div></button>')
      .prependTo('.site-nav')
      .on('click', function(e) {
        _hideMobileNav();
        _hidePageOverlay();
      });
  }

  function _showMobileNav() {
    _hideModal();
    _hideCollection();
    $('body, .menu-toggle').addClass('menu-open');
    $('.site-nav').addClass('active');
  }

  function _hideMobileNav() {
    $('body, .menu-toggle').removeClass('menu-open');
    $('.site-nav').removeClass('active');
  }

  function _initMasonry(){
    if (breakpoint_medium) {
      $('.masonry').masonry({
        itemSelector: 'article,.masonry-me',
        transitionDuration: '.35s'
      });
    }
  }

  function _initLoadMore() {
    $document.on('click', '.load-more a', function(e) {
      e.preventDefault();
      var $load_more = $(this).closest('.load-more');
      var post_type = $load_more.attr('data-post-type') ? $load_more.attr('data-post-type') : 'news';
      var page = parseInt($load_more.attr('data-page-at'));
      var per_page = parseInt($load_more.attr('data-per-page'));
      var category = $load_more.attr('data-category');
      var more_container = $load_more.parents('section,main').find('.load-more-container');
      loadingTimer = setTimeout(function() { more_container.addClass('loading'); }, 500);

      $.ajax({
          url: wp_ajax_url,
          method: 'post',
          data: {
              action: 'load_more_posts',
              post_type: post_type,
              page: page+1,
              per_page: per_page,
              category: category
          },
          success: function(data) {
            var $data = $(data);
            if (loadingTimer) { clearTimeout(loadingTimer); }
            more_container.append($data).removeClass('loading');
            // if (breakpoint_medium) {
            //   more_container.masonry('appended', $data, true);
            // }
            $load_more.attr('data-page-at', page+1);

            // Hide load more if last page
            if ($load_more.attr('data-total-pages') <= page + 1) {
                $load_more.addClass('hide');
            }
          }
      });
    });
  }

  // Track ajax pages in Analytics
  function _trackPage() {
    if (typeof ga !== 'undefined') { ga('send', 'pageview', document.location.href); }
  }

  // Track events in Analytics
  function _trackEvent(category, action) {
    if (typeof ga !== 'undefined') { ga('send', 'event', category, action); }
  }

  // Called in quick succession as window is resized
  function _resize() {
    screenWidth = document.documentElement.clientWidth;
    breakpoint_small = (screenWidth > breakpoint_array[0]);
    breakpoint_medium = (screenWidth > breakpoint_array[1]);
    breakpoint_large = (screenWidth > breakpoint_array[2]);
  }

  // Called on scroll
  // function _scroll(dir) {
  //   var wintop = $(window).scrollTop();
  // }

  // Public functions
  return {
    init: _init,
    resize: _resize,
    scrollBody: function(section, duration, delay) {
      _scrollBody(section, duration, delay);
    }
  };

})(jQuery);

// Fire up the mothership
jQuery(document).ready(SCB.init);

// Zig-zag the mothership
jQuery(window).resize(SCB.resize);
