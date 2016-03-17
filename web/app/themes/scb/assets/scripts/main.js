// SCB - Firebelly 2015
/*jshint latedef:false*/

// Good Design for Good Reason for Good Namespace
var SCB = (function($) {

  var screen_width = 0,
      breakpoint_small = false,
      breakpoint_medium = false,
      breakpoint_large = false,
      breakpoint_array = [480,1000,1200],
      $body,
      $document,
      $sidebar,
      $collection,
      $modal,
      $mapModal,
      modal_animating = false,
      loadingTimer,
      // Get scrollbar width on page load
      scrollbarWidth = _getScrollbarWidth(),
      History = window.History,
      rootUrl = History.getRootUrl(),
      page_cache = [],
      category_cache = [],
      collection_message_timer,
      page_at;

  function _init() {
    // Touch-friendly fast clicks
    FastClick.attach(document.body);

    // Init state
    State = History.getState();

    // Cache some common DOM queries
    $document = $(document);
    $body = $('body');
    $collection = $('.collection.mini');
    $modal = $('.global-modal');
    $body.addClass('loaded');

    // Barf-o-rama
    $body.on('contextmenu', '.modal-content img, main img', function(e) { e.preventDefault(); });
    $body.on('dragstart', '.modal-content img, main img', function(e) { e.preventDefault(); });


    // Set screen size vars
    _resize();

    // Fit them vids!
    $('main').fitVids();

    // Global application form, shown (in a modal of course!) when clicking "Submit Portfolio"
    $document.on('click', 'a.submit-portfolio', function(e) {
      e.preventDefault();
      _showApplicationForm();
    });

    // Bind to state changes (unused right now)
    $(window).bind('Xstatechange',function(){
      var State = History.getState(),
          url = State.url,
          relative_url = url.replace(rootUrl,'');

      if (State.data.ignore_change) {
        return;
      }
    });

    // Are we on a page with project category nav?
    if ($('.project-categories').length) {

      // Category nav scrolling behavior
      _checkCatScrollPos();
      $(window).on('scroll', function() {
        _checkCatScrollPos();
      });

      // Toggle Categories filter
      $document.on('click', '.categories-toggle', function(e) {
        $('.project-categories').toggleClass('expanded');
      });

      // Init active nav categories on page load
      $('.project-categories').find('.current-cat-parent>a, .current-cat>a').each(function() {
        _updateProjectCategoryNav(this);
      });

      // Active grandparent doesn't get a .current-cat class of any sort
      $('.project-categories .current-cat-parent').parents('li').find('>a').each(function() {
        _updateProjectCategoryNav(this);
      });

      // Project category filter
      $('.project-categories').on('click', 'a', function(e) {
        e.preventDefault();

        var $category = $(this);
        _updateProjectCategoryNav(this);

        // Build array of selected category slugs
        var project_categories = [];
        $('.project-categories li.active>a').each(function() {
          var slug = $(this).attr('href').split('/')[2];
          project_categories.push(slug);
        });

        // Pull last category to use for filtering
        project_category = project_categories.slice(-1).pop();

        if (typeof project_category === 'undefined') {
          project_category = '';
        }

        // Set data-pageClass to parent category (first in array) for color theme styling
        $body.attr('data-pageClass', project_categories[0]);

        // Cached?
        if (!category_cache['category-' + project_category]) {
          $.ajax({
              url: wp_ajax_url,
              method: 'post',
              data: {
                  action: 'load_more_projects',
                  page: 1,
                  per_page: 6,
                  project_category: project_category
              },
              success: function(data) {
                if (loadingTimer) { clearTimeout(loadingTimer); }
                // Cache ajax return
                category_cache['category-' + project_category] = data;
                _updateProjects(data);
                $body.addClass('term-' + project_category);
                _scrollBody($body, 250, 0);
              }
          });
        } else {
          _updateProjects(category_cache['category-' + project_category]);
          $body.addClass('term-' + project_category);
        }
      });
    }

    // Show/hide project details
    $document.on('click', '.details-toggle', function() {
      $(this).closest('.show-details').next('.project-meta-content').slideToggle(350);
    });

    // Show/hide mini collection in nav
    $document.on('click', '.show-collection', function(e) {
      e.preventDefault();
      if ($collection.hasClass('active')) {
        _hideCollection();
      } else {
        _showCollection();
      }
    });
    $document.on('click', '.hide-collection', function(e) {
      e.preventDefault();
      if ($collection.hasClass('active')) {
        _hideCollection();
      }
    });

    // Show/hide global modal in nav
    $document.on('click', '.show-modal', function(e) {
      e.preventDefault();
      if ($modal.hasClass('active')) {
        _hideModal();
      } else {
        _showModal();
      }
    });
    $document.on('click', '.hide-modal', function(e) {
      e.preventDefault();
      if ($modal.hasClass('active')) {
        _hideModal();
        _hidePageOverlay();
      }
    });

    // Add/Remove from collection links
    $document.on('click', '.collection-action', function(e) {
      e.preventDefault();
      var $link = $(this);
      var id = $link.attr('data-id') || '';
      var collection_id = $link.parents('section.collection:first').attr('data-id');
      var action = $link.attr('data-action');

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
          // Just show empty message if removing last item to avoid confusing, stacked feedback
          if (!$collection.hasClass('active') && !response.data.collection_html.match(/empty/)) {
            _feedbackMessage(action);
          }
        } else if (action.match(/pdf/)) {
          var buttonText = $(e.target).text();
          if (response.success) {
            if (buttonText.match('print')) {
              // Make tmp iframe with PDF and trigger print()
              $('<iframe id="pdf-print"></iframe>').appendTo('body')
                .attr('src', response.data.pdf.url)
                .hide()
                .on('load', function(){
                  var frm = this.contentWindow;
                  setTimeout(function() {
                    frm.focus();
                    frm.print();
                  }, 500);
                 });
            } else {
              // Make tmp link to trigger download of PDF (from http://stackoverflow.com/a/27563953/1001675)
              var link = document.createElement('a');
              if (typeof link.download === 'undefined') {
                // Old browsers just open the pdf
                window.location = response.data.pdf.url;
              } else {
                link.href = response.data.pdf.url;
                link.download = response.data.pdf.name;
                link.click();
              }
            }
          } else {
            _feedbackMessage(response.data.message);
          }
        }
      });
    });

    // Hide page overlay when clicked
    $document.on('click', '#page-overlay', function() {
      _hidePageOverlay();
      _hideCollection();
      _hideModal();
      _hideMobileNav();
    });

    // Esc handlers
    $document.keyup(function(e) {
      if (e.keyCode === 27) {
        _hideSearch();
        _hideImageModal();
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
      var href = $(this).attr('href'),
          navHeight = 96;
      _scrollBody($(href), 500, 0, navHeight);
    });

    // Scroll down to hash afer page load
    $(window).load(function() {
      if (window.location.hash) {
        _scrollBody($(window.location.hash)); 
      }
    });

    _initNav();
    _initSearch();
    _initImageModals();
    _initLoadMore();
    _initPostModals();
    _initBigClicky();
    _initMasonryGrid();

    // AJAX form submissions
    _initApplicationForms();

    // Drag-sorting of Collections
    _initCollectionBehavior();

    // Init SVG Injection
    _injectSvgSprite();

    _plusButtons();
    _shrinkHeader();

  } // end init()

  // Get scrollbar width for open modal offset
  function _getScrollbarWidth() {
    var outer = document.createElement("div");
    outer.style.visibility = "hidden";
    outer.style.width = "100px";
    outer.style.msOverflowStyle = "scrollbar"; // needed for WinJS apps

    document.body.appendChild(outer);

    var widthNoScroll = outer.offsetWidth;
    // force scrollbars
    outer.style.overflow = "scroll";

    // add innerdiv
    var inner = document.createElement("div");
    inner.style.width = "100%";
    outer.appendChild(inner);        

    var widthWithScroll = inner.offsetWidth;

    // remove divs
    outer.parentNode.removeChild(outer);

    return widthNoScroll - widthWithScroll;
  }

  // Collapse category nav?
  function _checkCatScrollPos() {
    if($(window).scrollTop() >= $('.main .projects').offset().top - $('.site-header').outerHeight()) {
      $('.project-categories').addClass('fixed');
      if ($('.categories-toggle').is('.expanded') && !$('.project-categories').is('.expanded')) {
        $('.categories-toggle').removeClass('expanded');
      }
    } else if ($(window).scrollTop() <= $('.main').offset().top + $('.site-header').outerHeight()) {
      $('.project-categories').removeClass('fixed expanded');
      $('.categories-toggle').addClass('expanded');
    }
  }

  // Custom AJAX function to pull in new Projects (first two pages strange grids)
  function _updateProjects(data) {
    $data = $(data);
    // Remove load-more DOM elements from returned HTML
    var new_load_more = $data.find('.load-more').detach();

    // Update load more container & empty load-more container
    $('.load-more').replaceWith(new_load_more);

    $('.masonry-grid').masonry('destroy');

    // Update page classes
    var termClasses = $('body').attr('class').match(/\bterm-\S+/g);
    if (termClasses) {    
      $.each(termClasses, function(){
        $body.removeClass(this.toString());
      });
    }

    // Populate new projects in grid
    $('section.projects .initial-section').html( $data.find('.initial-section').html() ).removeClass('loading');
    _initMasonryGrid();

    // Pull intro and replace on page
    $('.page-intro').html( $data.find('.page-intro').html() );

    _checkLoadMore();
  }

  // Should we hide "Load More"?
  function _checkLoadMore() {
    $('.load-more').toggleClass('hide', parseInt($('.load-more').attr('data-page-at')) >= parseInt($('.load-more').attr('data-total-pages')));
  }

  // Update active state in hierarchical category nav (used on page load, and interacting w/ categories)
  function _updateProjectCategoryNav(el) {
    var thisUrl = $(el).attr('href'),
        $li = $(el).parent('li'),
        $activeSiblings = $li.siblings('.active');

    // Match height of absolutely-positioned children lists
    if ($li.find('ul.children').length && !$li.is('.active')) {
      var childHeight = $li.find('ul.children').outerHeight();
      $('.project-categories .-inner').outerHeight(childHeight + 20);
    } else {
      $('.project-categories .-inner').outerHeight($li.closest('ul').outerHeight() + 20);
    }

    // Toggle corresponding header bars
    if ($li.parent('ul').is('.categories-parent')) {
      $('.bar.-two, .bar.-three').removeClass('active');
      if (!$li.is('.active')) {
        $('.bar.-two').addClass('active');
      }
    } else if ($li.parent('ul').is('.children')) {
      if ($li.is('.active') && $('.bar.-three').is('.active') || !$li.find('ul').length && $('.bar.-three').is('.active')) {
        $('.bar.-three').removeClass('active');
      } else if ($li.find('ul').length) {
        $('.bar.-three').addClass('active');
      }
    }

    // Toggle active status
    if (!$li.hasClass('active')) {
      $li.addClass('active');
    } else {
      $li.removeClass('active');
      $li.find('ul, li').removeClass('active');
    }

    // Activate/deactivate the parent ul
    var $parentUl = $li.parent('ul');
    if (!$parentUl.is('.active')) {
      $li.parent('ul').addClass('active');
    } else if ($parentUl.hasClass('active') && !$activeSiblings.length) {
      $parentUl.removeClass('active');
    }

    // If toggling a child, give the whole thing a relative class
    if ($li.closest('ul').is('.children') && $li.find('.children').length) {
      $('.categories-parent').toggleClass('grandchildren-active');
    }

    // If there are active siblings, deactivate them and their children
    if ($activeSiblings.length) {
      $activeSiblings.find('.active').removeClass('active');
      $activeSiblings.removeClass('active');
    }

    // Update state
    if ($('.project-categories li.active').length) {
      $('.project-categories li.active:last>a').each(function() {
        History.replaceState({'ignore_change': true}, $(this).text() + ' – SCB', $(this).attr('href'));
        _trackPage();
      });
    } else {
      // No active projects, default to homepage
      History.replaceState({}, 'SCB – ' + $('.site-header .description').text(), '/');
      _trackPage();
    }

  }

  // Function to update document content (and og: tags) after state change (unused right now)
  function _updateContent() {
    var State = History.getState();

    // track page view in Analytics
    _trackPage();

    setTimeout(function() {
      _updateTitle();
      // Update meta tags
      if ($('#og-updates').length) {
        $('meta[property="og:url"]').attr('content', State.url);
        $('meta[property="og:title"]').attr('content', document.title);
        $('meta[property="og:description"]').attr('content', $('#og-updates').attr('data-description'));
        $('meta[property="og:image"]').attr('content', $('#og-updates').attr('data-image'));
      }
    }, 150);
  }

  // Function to update document title after state change (unused right now)
  function _updateTitle() {
    var title = $content.find('.content:first').attr('data-post-title');
    if (title === '' || title === 'Main') {
      title = 'SCB';
    } else {
      title = title + ' – SCB'; 
    }
    // this bit also borrowed from Ajaxify
    document.title = title;
    try {
      document.getElementsByTagName('title')[0].innerHTML = document.title.replace('<','&lt;').replace('>','&gt;').replace(' & ',' &amp; ');
    } catch (Exception) {}
  }

  function _showEmailForm() {
    $('#email-collection-form').addClass('active');
    $('#email-collection-form input:first').focus();
    $('.collection .collection-actions').velocity('scroll', { 
      container: $('.modal.active .overflow-wrapper'),
      duration: 250,
      delay: 0
    });
    _scrollBody($('.collection .collection-actions'), 250, 0, 0, $('.overflow-wrapper'));
  }

  function _hideEmailForm() {
    $('#email-collection-form').removeClass('active');
  }

  // AJAX Application form submissions
  function _initApplicationForms() {
    $document.on('click', '.application-form input[type=submit]', function(e) {
      var $form = $(this).closest('form');

      $form.validate({
        messages: {
          application_first_name: "Please leave us your first name",
          application_last_name: "Please leave us your last name",
          application_email: "We will need a valid email to contact you at",
          application_phone: "In case we need to call you"
        },
        submitHandler: function(form) {
          // only AJAXify if browser supports FormData (necessary for file uploads via AJAX, <IE10 = no go)
          if( window.FormData !== undefined ) {
            var formData = new FormData(form);
            $.ajax({
              url: wp_ajax_url,
              method: 'POST',
              data: formData,
              dataType: 'json',
              mimeType: 'multipart/form-data',
              processData: false,
              contentType: false,
              cache: false,
              success: function(response) {
                form.reset();
                _feedbackMessage('Your application was submitted successfully!');
                _scrollBody($('.modal.active .modal-content .feedback-container'), 250,0,0,$('.modal.active .modal-content'));
              },
              error: function(response) {
                _feedbackMessage('Sorry, there was an error submitting your application: ' + response.data.message);
                _scrollBody($('.modal.active .modal-content .feedback-container'), 250,0,0,$('.modal.active .modal-content'));
              }
            });
          } else {
            form.submit();
          }
        }
      });
    });
  }

  function _injectSvgSprite() {
    boomsvgloader.load('/app/themes/scb/assets/svgs/build/svgs-defs.svg'); 
  }

  function _plusButtons() {
    $document.on('click', '.plus-button.-expandable', function(e) {
      $(this).toggleClass('expanded');
    });
  }

  function _shrinkHeader() {
    if ($document.scrollTop() > 100) {
      $('.site-header').addClass('shrink');
    }
    $document.on('scroll', function(){
      if ($document.scrollTop() > 100) {
        $('.site-header').addClass('shrink');
      } else {
        $('.site-header').removeClass('shrink');
      }
    });
  }

  function _updatePostCollectionLinks(id,action) {
    $('article[data-id='+id+'] .collection-action').each(function() {
      if (action==='add') {
        $(this).removeClass('collection-add').addClass('collection-remove').attr('data-action', 'remove');
        $(this).find('.collection-text').text('Remove from Collection');
      } else {
        $(this).removeClass('collection-remove').addClass('collection-add').attr('data-action', 'add');
        $(this).find('.collection-text').text('Add to Collection');
      }
    });
  }

  function _showModal() {
    _showPageOverlay(); 
    $body.addClass('modal-active');
    // Offset body for scrollbar width
    $('body, .site-header').css('margin-right', scrollbarWidth);
    $modal.addClass('display');
    $modal.find('.modal-content').scrollTop(0);
    setTimeout(function() {
      $modal.addClass('active');
    }, 100);
    // _scrollBody($body, 250, 0, 0);
    if ($modal.find('.modal-content').is(':empty')) {
      $modal.addClass('empty');
    } else {
      $modal.removeClass('empty');
    }
  }

  function _hideModal() {
    modal_animating = true;
    State = History.getState();
    _hidePageOverlay();
    $('body, .site-header').css('margin-right', 0);
    $body.removeClass('modal-active');
    $modal.removeClass('active');
    setTimeout(function() {
      modal_animating = false;
      $modal.removeClass('display');
      $modal.removeClass('news-modal post-modal project-modal person-modal application-modal position-modal'); // clear out section-specific styles
    }, 500);
    if (State.data.previousURL) {
      History.replaceState({}, State.data.previousTitle, State.data.previousURL);
      _trackPage();
    }
  }

  function _showCollection() {
    _hideModal();
    _showPageOverlay(); 
    $body.addClass('collection-active');
    // Offset body for scrollbar width
    $('body, .site-header').css('margin-right', scrollbarWidth);
    $collection.addClass('display');
    setTimeout(function() {
      $collection.addClass('active');
    }, 100);
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
    $body.removeClass('collection-active');
    $collection.removeClass('active');
    $('body, .site-header').css('margin-right', 0);
    setTimeout(function() {
      $collection.removeClass('display');
    }, 500);
  }

  function _showApplicationForm() {
    $modal.find('.modal-content').empty().prepend('<div class="feedback-container"><div class="feedback"><p></p></div></div></div>');
    var $app_form = $('.application-form-template');
    if ($app_form.length) {
      if ($modal.find('.application-form').length===0) {
        $app_form.clone(true).addClass('active').appendTo($modal.find('.modal-content'));
      }
      $modal.addClass('application-modal');
      _showModal();
      $modal.find('.application-form input:first').focus();
    }
  }

  // Show collection message dialog
  function _feedbackMessage(messageType) {
    var message;

    if (messageType === 'remove') {
      message = 'Your selection has been removed from the collection.';
    } else if (messageType === 'add') {
      message = 'Your selection has been added to the collection.';
    } else {
      message = messageType;
    }

    $('.modal').find('.feedback-container .feedback p').text(message);
    setTimeout(function(){
      $('.modal').find('.feedback-container').addClass('show-feedback');
    }, 250);

    if (collection_message_timer) { clearTimeout(collection_message_timer); }
    collection_message_timer = setTimeout(_hideCollectionMessage, 3000);

    $('.modal').find('.feedback-container').on('mouseenter', function() {
      clearTimeout(collection_message_timer);
    }).on('mouseleave', function() {
      setTimeout(_hideCollectionMessage, 1000);
    });

    _scrollBody($('.collection .feedback-container'), 250, 0, 0, $('.overflow-wrapper'));
    
  }

  function _hideCollectionMessage() {
    $('.modal').find('.feedback-container').removeClass('show-feedback');
    $('.modal').find('.feedback-container .feedback p').text('');
  }

  // Init collection sorting, title editing, etc
  function _initCollectionBehavior() {
    // Email collection
    $('.email-collection').on('click', function(e) {
      e.preventDefault();
      _showEmailForm();
    });
    $('#email-collection-form form').validate({
      submitHandler: function(form) {
          $.ajax({
              url: wp_ajax_url,
              method: 'post',
              dataType: 'json',
              data: $(form).serialize()
          }).done(function(response) {
            if (response.success) {
              _hideEmailForm();
              _feedbackMessage('Your email was sent successfully!');
            } else {
              _feedbackMessage('Sorry, there was an error sending your email: ' + response.data.message);
            }
          }).fail(function(response) {
            _feedbackMessage('Sorry, there was an error sending your email.');
          });
        }
    });

    // Check for pressing enter, blur to update collection-title
    $('.collection-title').on('keydown', function(e) {  
      if(e.keyCode === 13) {
        e.preventDefault();
        this.blur();
      }
    });
    // Update collection title
    $('.collection-title').on('blur', function() {
      var title = $('.collection-title').text();
      var collection_id = $('.collection-title').attr('data-id');
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
          var collection_id = $(container.el[0]).attr('data-id');
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
            _feedbackMessage(response.data.message);
          });

          _super($item, container);
        }
      });
    });
  }

  function _initPostModals() {
    $document.on('click', '.show-post-modal', function(e) {
      var $thisTarget = $(e.target);
      // Ignore links inside that do something else
      if ($thisTarget.is('.no-ajaxy') || $thisTarget.parents('.no-ajaxy').length) {
        return;
      }
      if (modal_animating) { return false; }
      e.preventDefault();

      var post_id = $(this).attr('data-id'),
          modal_type = $(this).attr('data-modal-type');
          $modal.removeClass('news-modal post-modal project-modal person-modal application-modal position-modal'); // clear out section-specific styles
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
          $('.post-modal .modal-content').append($postData).prepend('<div class="feedback-container"><div class="feedback"><p></p></div></div></div>');
          History.replaceState({ previousTitle: document.title, previousURL: location.href }, $postData.attr('data-page-title') + ' – SCB', $postData.attr('data-page-url'));
          _trackPage();
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
      $body.prepend('<div id="page-overlay"></div>');
    }
    setTimeout(function() {
      $('#page-overlay').addClass('active');
    }, 50);
  }

  function _hidePageOverlay() {
    $('#page-overlay').remove();
  }

  function _initBigClicky() {
    $document.on('click', '.bigclicky', function(e) {
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

  function _initMasonryGrid() {
    // Add special classes for special styles
    $('.masonry-grid').find('.vertical').first().addClass('-first');
    var $grid = $('.masonry-grid').masonry({
      itemSelector: '.grid-item',
      columnWidth: '.grid-sizer',
      transitionDuration: '0.2s',
      hiddenStyle: { opacity: 0 }
    });
    $grid.imagesLoaded(function() {
      $grid.addClass('loaded');
    });
  }

  function _scrollBody(element, duration, delay, offset, container) {
    if ($('#wpadminbar').length) {
      wpOffset = $('#wpadminbar').height();
    } else {
      wpOffset = 0;
    }
    element.velocity("scroll", {
      duration: duration,
      delay: delay,
      offset: -wpOffset - (typeof offset !== 'undefined' ? offset : 0),
      container: (typeof container !== 'undefined' ? container : null)
    }, 'easeOutSine');
  }

  function _initSearch() {
    $('.show-search').on('click', function (e) {
      e.preventDefault();
      if ($('.search-modal').hasClass('active')) {

      } else {
        e.preventDefault();
        $('.search-modal').addClass('display');
        setTimeout(function() {
          $('.search-modal').addClass('active');
        }, 50);
        $('.search-field:first').focus();
      }
    });
    $('.search-modal .hide-search, .search-modal').on('click', function(e) {
      if (!$(e.target).is('.search-field, .search-submit')) {
        _hideSearch();
      }
    });

    // Stickify search column titles for single-column layout
    if($('body.search-results').length) {
      var $searchColumns = $('.search-column');

      $(window).on('scroll', function() {
        $searchColumns.each(function() {
          var $thisColumn = $(this);
          if ($(window).scrollTop() >= $thisColumn.offset().top && $(window).scrollTop() <= $thisColumn.offset().top + $thisColumn.outerHeight(true)) {
            $thisColumn.addClass('inView');
          } else if ($thisColumn.is('.inView') && $(window).scrollTop() >= $thisColumn.offset().top + $thisColumn.outerHeight(true) || $(window).scrollTop() <= $thisColumn.offset().top) {
            $thisColumn.removeClass('inView');
          }
        });
      });
    }
  }

  function _hideSearch() {
    $('.search-modal').removeClass('active');
    setTimeout(function() {
      $('.search-modal').removeClass('display');
    }, 500);
  }

  function _initImageModals() {
    // Append image modal markup
    $mapModal = $('<div class="image-modal"><button class="plus-button close hide-image-modal"><div class="plus"></div></button><div class="image-wrap"><img src=""></div></div>').prependTo('.site-footer');

    // All map links open up in modal
    $document.on('click', '.show-image-modal', function(e) {
      e.preventDefault();

      // Hide the collection/modal if it's open
      _hideCollection();
      _hideModal();

      $mapModal.find('img').attr('src', $(this).attr('href'));
      $mapModal.imagesLoaded(function() {
        _showImageModal();
      });
      // History.replaceState({ previousTitle: document.title, previousURL: location.href }, $postData.attr('data-page-title') + ' – SCB', $postData.attr('data-page-url'));
    });

    // Close it
    $document.on('click', '.hide-image-modal', _hideImageModal);
  }

  function _showImageModal() {
    $body.addClass('no-scroll');
    $('.image-modal').addClass('display');
    setTimeout(function() {
      $('.image-modal').addClass('active');
    },50);
  }

  function _hideImageModal() {
    State = History.getState();
    $body.removeClass('no-scroll');
    $('.image-modal').removeClass('active');
    setTimeout(function() {
      $('.image-modal').removeClass('display');
    }, 500);
    // if (State.data.previousURL) {
    //   History.replaceState({}, State.data.previousTitle, State.data.previousURL);
    // }
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
              project_category: category
          },
          success: function(data) {
            var $data = $(data);
            if (loadingTimer) { clearTimeout(loadingTimer); }
            $('.masonry-grid').append($data).removeClass('loading');
            $('.masonry-grid').masonry('appended', $data);
            $load_more.attr('data-page-at', page+1);
            SCB.checkLoadMore();
          }
      });
    });
  }

  // Track ajax pages in Analytics
  function _trackPage() {
    if (typeof ga !== 'undefined') {
      ga('send', 'pageview', location.pathname);
    }
  }

  // Track events in Analytics
  function _trackEvent(category, action) {
    if (typeof ga !== 'undefined') { 
      ga('send', 'event', category, action); 
    }
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
    checkLoadMore: _checkLoadMore,
    scrollBody: function(section, duration, delay, offset, container) {
      _scrollBody(section, duration, delay, offset, container);
    }
  };

})(jQuery);

// Fire up the mothership
jQuery(document).ready(SCB.init);

// Zig-zag the mothership
jQuery(window).resize(SCB.resize);
