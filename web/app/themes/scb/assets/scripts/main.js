// SCB - Firebelly 2016
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
      $image_modal,
      $category_nav,
      modal_animating = false,
      scrollbar_width = _getScrollbarWidth(), // Get scrollbar width on page load
      History = window.History,
      State,
      root_url = History.getRootUrl(),
      relative_url,
      original_url,
      page_cache = {},
      modal_timer,
      collection_message_timer;

  function _init() {
    // Touch-friendly fast clicks
    FastClick.attach(document.body);

    // Set screen size vars
    _resize();

    // Cache some common DOM queries
    $document = $(document);
    $body = $('body').addClass('loaded');
    $collection = $('.collection.mini');
    $modal = $('.global-modal');

    // Highly questionable disabling of default browser behavior to avoid "stealing" images
    $body.on('contextmenu', '.modal-content img, main img', function(e) { e.preventDefault(); });
    $body.on('dragstart', '.modal-content img, main img', function(e) { e.preventDefault(); });

    // Fit them vids!
    $('main').fitVids();

    // Are we on a page with project category nav?
    if ($('.project-categories').length) {
      _initProjectCategories();
    }

    // Show/hide project details
    $document.on('click', '.details-toggle', function() {
      $(this).closest('.show-details').next('.project-meta-content').slideToggle(350);
    });

    // Esc handlers
    $document.keyup(function(e) {
      if (e.keyCode === 27) {
        if ($body.is('.modal-active, .collection-active')) {
          History.back();
        } else {
          _hideSearch();
          _hideModal();
          _hideImageModal();
          _hideMobileNav();
          _hideEmailForm();
        }
      }
    });

    // Smoothscroll links
    $('a.smoothscroll').click(function(e) {
      e.preventDefault();
      var href = $(this).attr('href'),
          navHeight = 96;
      _scrollBody($(href), 500, 0, navHeight);
    });

    // Scroll down to hash after page load
    $(window).load(function() {
      if (window.location.hash) {
        _scrollBody($(window.location.hash), 250, 0);
      }
    });

    _initNav();
    _initSearch();
    _initImageModals();
    _initLoadMore();
    _initPostModals();
    _initBigClicky();
    _initMasonryGrid();
    _injectSvgSprite();
    _initApplicationForms();
    _initCollectionLinks();
    _initCollectionBehavior();
    _plusButtons();
    _shrinkHeader();

    // Init state
    State = History.getState();
    relative_url = '/' + State.url.replace(root_url,'');
    original_url = State.url;

    // Cache initial project grid to avoid flash of loading class after closing first project modal
    if (relative_url==='/') {
      page_cache[encodeURIComponent(State.url)] = $('section.main-project-grid').clone()[0];
    }

    // Single pages with forms: inject .feedback-container at top for non-modal feedback messages
    if (relative_url.match(/(position|internships)/)) {
      $('<div class="feedback-container"><div class="feedback"><p></p></div></div></div>').prependTo('main.main');
    }

    _initStateHandling();

    // Handle some faux-link redirects e.g. /collection/, /careers/submit-portfolio/ (see .htaccess)
    if (window.location.hash && window.location.hash === '#collection') {

      // Collection redirect
      original_url = root_url;
      History.replaceState({ignore_change: true}, null, '##');
      History.replaceState({}, document.title, location.href);
      setTimeout(History.pushState({ modal: true }, 'Collection – SCB', '/collection/'), 150);

    } else if (window.location.hash && window.location.hash === '#submit-portfolio') {

      // Submit Portfolio redirect
      original_url = root_url + 'careers/';
      History.replaceState({ignore_change: true}, null, '##');
      History.replaceState({}, document.title, location.href);
      setTimeout(History.pushState({ modal: true }, 'Submit Portfolio – SCB', '/careers/submit-portfolio/'), 250);

    }

  } // end init()

  // Bind to state changes and handle back/next
  function _initStateHandling() {
    $(window).bind('statechange',function(){
      State = History.getState();
      relative_url = '/' + State.url.replace(root_url,'');

      if (State.data.ignore_change) {
        return;
      }

      if (State.url !== original_url && relative_url.match(/^\/(project|person|position|office|\d{0,4}|careers\/internships)\//)) {

        // Standard post modals
        if (page_cache[encodeURIComponent(State.url)]) {
          _updateModal();
        } else {
          _loadModal();
        }

      } else if ($category_nav && (relative_url==='/' || relative_url.match(/^\/projects\//))) {

        // Project category navigation
        _hideModal();
        _hideImageModal();
        _hideCollection();

        if (page_cache[encodeURIComponent(State.url)]) {
          _updateProjects();
        } else {
          _loadProjects();
        }

      } else if (relative_url.match(/^\/collection\//)) {

        // Collection
        _showCollection();

      } else if (relative_url.match(/^\/careers\/submit-portfolio\//)) {

        // Submit Portfolio
        _showApplicationForm();

      } else {

        // URL isn't handled as a modal or isn't project category (or is page without $category_nav)
        if (State.url !== original_url) {
          // Just load URL if isn't original_url
          location.href = State.url;
        } else {
          // ..otherwise just hide all modals
          _hideModal();
          _hideCollection();
          _hideImageModal();
        }

      }

      // Track AJAX URL change in analytics
      _trackPage();

      // Update Facebook tags for any share buttons on the page
      _updateOGTags();
    });
  }

  // Get scrollbar width for open modal offset
  function _getScrollbarWidth() {
    var outer = document.createElement('div');
    outer.style.visibility = 'hidden';
    outer.style.width = '100px';
    outer.style.msOverflowStyle = 'scrollbar'; // needed for WinJS apps

    document.body.appendChild(outer);

    var widthNoScroll = outer.offsetWidth;
    // Force scrollbars
    outer.style.overflow = 'scroll';

    // Add innerdiv
    var inner = document.createElement('div');
    inner.style.width = '100%';
    outer.appendChild(inner);

    var widthWithScroll = inner.offsetWidth;

    // Remove divs
    outer.parentNode.removeChild(outer);

    return widthNoScroll - widthWithScroll;
  }

  // Collapse category nav?
  function _checkCatScrollPos() {
    if($(window).scrollTop() >= $('.main .projects').offset().top - $('.site-header').outerHeight()) {
      $category_nav.addClass('fixed');
      if ($('.categories-toggle').is('.expanded') && !$category_nav.is('.expanded')) {
        $('.categories-toggle').removeClass('expanded');
      }
    } else if ($(window).scrollTop() <= $('.main').offset().top + $('.site-header').outerHeight()) {
      $category_nav.removeClass('fixed expanded');
      $('.categories-toggle').addClass('expanded');
    }
  }

  // Hide "Load More" if there are no more pages
  function _checkLoadMore() {
    $('.load-more').toggleClass('hide', parseInt($('.load-more').attr('data-page-at')) >= parseInt($('.load-more').attr('data-total-pages')));
  }

  // Update og: tags after state change
  function _updateOGTags() {
    $('meta[property="og:url"]').attr('content', State.url);
    $('meta[property="og:title"]').attr('content', document.title);
    $('meta[property="og:type"]').attr('content', ($body.is('.moda-active') ? 'article' : 'website') );
    if ($('#og-updates').length) {
      $('meta[property="og:description"]').attr('content', $('#og-updates').attr('data-description'));
      $('meta[property="og:image"]').attr('content', $('#og-updates').attr('data-image'));
    }
  }

  // Function to update document title after state change
  function _updateTitle() {
    var title;
    if ($modal.is('.active')) {
      title = $('.modal-content [data-page-title]').first().attr('data-page-title');
    } else {
      title = $('[data-page-title]').first().attr('data-page-title');
    }
    if (title === '') {
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

  // Show email form in Collection
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
    // Global application form, shown (in a modal of course!) when clicking "Submit Portfolio"
    $document.on('click', 'a.submit-portfolio', function(e) {
      e.preventDefault();
      History.pushState({ modal: true }, 'Submit Portfolio – SCB', '/careers/submit-portfolio/');
    });
    // Handle application form submissions
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
                if (response.success) {
                  _feedbackMessage('Your application was submitted successfully!');
                  form.reset();
                } else {
                  _feedbackMessage('Sorry, there was an error submitting your application: ' + response.data.message);
                }
              },
              error: function(response) {
                _feedbackMessage('Sorry, there was an error submitting your application: ' + response.data.message);
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

  // Shrink header as you scroll down
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

  // Update all Collection add/remove links on page after action
  function _updatePostCollectionLinks(id, action) {
    $.each(page_cache, function(url, block) {
        _updatePostCollectionLinksInBlock(block, id, action);
    });
    _updatePostCollectionLinksInBlock('body', id, action);
  }
  function _updatePostCollectionLinksInBlock(block, id, action) {
    $(block).find('a.collection-action[data-id='+id+']').each(function() {
      if (action==='add') {
        $(this).removeClass('collection-add').addClass('collection-remove').attr('data-action', 'remove');
        $(this).find('.collection-text').text('Remove from Collection');
      } else {
        $(this).removeClass('collection-remove').addClass('collection-add').attr('data-action', 'add');
        $(this).find('.collection-text').text('Add to Collection');
      }
    });
  }

  // Everybody loves a modal!
  function _showModal() {
    if (modal_timer) { clearTimeout(modal_timer); }
    _hideCollection();
    _showPageOverlay();
    $body.addClass('modal-active');
    // Offset body for scrollbar width
    $('body, .site-header').css('margin-right', scrollbar_width);
    $modal.addClass('active');
    $modal.find('.modal-content').scrollTop(0);
    if ($modal.find('.modal-content').is(':empty')) {
      $modal.addClass('empty');
    } else {
      $modal.removeClass('empty');
    }
  }
  function _hideModal() {
    modal_animating = true;
    _hidePageOverlay();
    $('body, .site-header').css('margin-right', 0);
    $body.removeClass('modal-active');
    $modal.removeClass('active');
    if (modal_timer) { clearTimeout(modal_timer); }
    modal_timer = setTimeout(function() {
      modal_animating = false;
      $modal.removeClass('news-modal post-modal office-modal project-modal person-modal application-modal position-modal'); // clear out section-specific styles
    }, 750);
  }

  // Show Collection modal
  function _showCollection() {
    _hideModal();
    _showPageOverlay();
    $body.addClass('collection-active');
    // Offset body for scrollbar width
    $('body, .site-header').css('margin-right', scrollbar_width);
    $collection.addClass('active');

    $collection.toggleClass('empty', !$collection.find('article').length);

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
  }

  // Show Application form! In a modal!
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
    // History.pushState({ modal: true }, 'Submit Portfolio – SCB', '/careers/submit-portfolio/');
  }

  // Show collection message dialog
  function _feedbackMessage(messageType) {
    var message;

    if ($collection.is('.active')) {
      _scrollBody($('.modal .feedback-container'), 250, 0, 0, $('.overflow-wrapper'));
    } else if ($modal.is('.active')) {
      _scrollBody($('article.single'), 250, 0, 0, $('.modal-content'));
    } else {
      // Single page, not a modal!
      _scrollBody($body, 250, 0);
    }

    if (messageType === 'remove') {
      message = 'Your selection has been removed from the collection.';
    } else if (messageType === 'add') {
      message = 'Your selection has been added to the collection.';
    } else {
      message = messageType;
    }

    $('.feedback-container .feedback p').text(message);
    setTimeout(function(){
      $('.feedback-container').addClass('show-feedback');
    }, 250);

    if (collection_message_timer) { clearTimeout(collection_message_timer); }
    collection_message_timer = setTimeout(_hideCollectionMessage, 3000);

    $('.feedback-container').on('mouseenter', function() {
      if (collection_message_timer) { clearTimeout(collection_message_timer); }
    }).on('mouseleave', function() {
      if (collection_message_timer) { clearTimeout(collection_message_timer); }
      collection_message_timer = setTimeout(_hideCollectionMessage, 1000);
    });

  }
  function _hideCollectionMessage() {
    $('.feedback-container').removeClass('show-feedback');
    setTimeout(function() { $('.feedback-container .feedback p').text(''); }, 250);
  }

  function _initCollectionLinks() {
    // Show/hide mini collection in nav
    $document.on('click', '.show-collection', function(e) {
      e.preventDefault();
      if ($collection.hasClass('active')) {
        History.back();
      } else {
        History.pushState({ modal: true }, 'Collection – SCB', '/collection/');
      }
    });
    $document.on('click', '.hide-collection', function(e) {
      e.preventDefault();
      if ($collection.hasClass('active')) {
        History.back();
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

        // If add/remove, repopulate collection & reinit behavior
        if (action.match(/add|remove/)) {

          _updatePostCollectionLinks(id,action);
          // Repopulate all collections
          $('section.collection').html(response.data.collection_html);
          _initCollectionBehavior();
          // Just show empty message if removing last item to avoid confusing, stacked feedback
          if (!$collection.hasClass('active') && !response.data.collection_html.match(/empty/)) {
            _feedbackMessage(action);
          }
          // Push /collection/ to History to trigger opening Collection
          History.pushState({ modal: true }, 'Collection – SCB', '/collection/');

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
          setTimeout(function() { $('.collection-title').removeClass('updated'); }, 1500);
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
            setTimeout(function() { $(container.el[0]).removeClass('updated'); }, 1500);
          }).fail(function(response) {
            _feedbackMessage(response.data.message);
          });

          _super($item, container);
        }
      });
    });
  }

  // Ajaxify .show-post-modal links
  function _initPostModals() {
    // Show/hide global modal in nav
    $document.on('click', '.show-modal', function(e) {
      e.preventDefault();
      if ($modal.hasClass('active')) {
        History.back();
      } else {
        _showModal();
      }
    });
    $document.on('click', '.hide-modal', function(e) {
      e.preventDefault();
      if ($modal.hasClass('active')) {
        History.back();
      }
    });

    // Hide page overlay when clicked
    $document.on('click', '#page-overlay', function() {
      _hidePageOverlay();
      if ($body.is('.menu-open')) {
        _hideMobileNav();
      } else {
        History.back();
      }
    });

    // AJAXified links that load content in a modal and slide it out
    $document.on('click', '.show-post-modal', function(e) {
      if (modal_animating) {
        return false;
      }
      var $target = $(e.target);
      if (!$target.is('.no-ajaxy') && !$target.parents('.collection-action').length) {
        e.preventDefault();
        History.pushState({ modal: true }, '', $(this).attr('href') || $(this).attr('data-page-url'));
      }
    });
  }

  // Load AJAX content to show in a modal & store in page_cache array
  function _loadModal() {
    $.ajax({
      url: wp_ajax_url,
      method: 'post',
      dataType: 'html',
      data: {
        'action': 'load_post_modal',
        'post_url': State.url
      },
      success: function(response) {
        page_cache[encodeURIComponent(State.url)] = $.parseHTML(response);
        _updateModal();
      }
    });
  }

  // Update modal with cached content for current URL and show it
  function _updateModal() {
    $postModal = $(page_cache[encodeURIComponent(State.url)]);
    $modal.removeClass('news-modal post-modal office-modal project-modal person-modal application-modal position-modal').addClass($postModal.attr('data-modal-type') + '-modal');
    $modal.find('.modal-content').html('<div class="feedback-container"><div class="feedback"><p></p></div></div></div>' + page_cache[encodeURIComponent(State.url)][0].outerHTML);
    if ($modal.find('.modal-content .collection-action').length) {
      $modal.find('.collection-action-clone').remove();
      $modal.find('.modal-content .collection-action').clone().prependTo($modal).addClass('collection-action-clone');
    }
    _trackPage();
    _showModal();
    _updateTitle();
  }

  // Load AJAX content for projects (triggered when clicking category nav)
  function _loadProjects() {
    var product_category = '';
    if (State.url !== '/') {
      project_category = relative_url.split('/')[2];
    }

    $.ajax({
        url: wp_ajax_url,
        method: 'post',
        data: {
            action: 'load_more_projects',
            page: 1,
            // per_page: 6,
            project_category: project_category
        },
        success: function(response) {
          page_cache[encodeURIComponent(State.url)] = $.parseHTML(response);
          $('.page-intro,.projects').addClass('loading');
          setTimeout(_updateProjects, 150);
        }
    });
  }

  function _initProjectCategories() {
    $category_nav = $('.project-categories');

    // Modal project category links
    $document.on('click', '.categories a', function(e) {
      e.preventDefault();
      _scrollBody($body, 250, 0);
      History.pushState({ category: true }, '', this.href);
    });

    // Set initial active state (if on category page)
    _updateProjectCategoryNavByURL();

    // Category nav scrolling behavior
    _checkCatScrollPos();
    $(window).on('scroll', function() {
      _checkCatScrollPos();
    });

    // Toggle category filters when scrolling
    $document.on('click', '.categories-toggle', function(e) {
      $category_nav.toggleClass('expanded');
    });

    // Project category filters
    $category_nav.on('click', 'a', function(e) {
      e.preventDefault();
      var $li = $(this).parent('li'),
          url = this.href;
      if ($li.is('.active')) {
        if ($li.parents('li:first').length) {
          url = $li.parents('li:first').find('a:first').attr('href');
        } else {
          url = '/';
        }
      }

      _scrollBody($body, 250, 0);

      // Push active category URL
      History.pushState({ category: true }, '', url);
    });
  }

  // Populate Project grid with category output
  function _updateProjects() {
    var $data = $(page_cache[encodeURIComponent(State.url)]),
        $category_link = $('.project-categories a[href="' + relative_url + '"]');

    // Show active category in nav
    _updateProjectCategoryNavByURL();

    // Build array of selected category slugs
    var project_categories = [];
    $('.project-categories li.active>a').each(function() {
      var slug = $($category_link).attr('href').split('/')[2];
      project_categories.push(slug);
    });

    // Pull last category to use for filtering
    project_category = project_categories.slice(-1).pop();
    // No categories selected?
    if (typeof project_category === 'undefined') {
      project_category = '';
    }

    // Remove load-more DOM elements from returned HTML
    var new_load_more = $data.find('.load-more').detach();

    // Update load more container & empty load-more container
    $('.load-more').replaceWith(new_load_more);

    // Clear out any existing Masonry
    $('.masonry-grid').masonry('destroy');

    // Populate new projects in grid
    $('section.projects .initial-section').html( $data.find('.initial-section').html() ).removeClass('loading');
    _initMasonryGrid();

    // Pull intro and replace on page
    $('.page-intro').html( $data.find('.page-intro').html() );

    $('.page-intro,.projects').removeClass('loading');

    _checkLoadMore();
  }

  // Set active state in category nav based on current URL (used in statechange & on page load)
  function _updateProjectCategoryNavByURL() {
    $category_nav.find('ul, li').removeClass('active grandchildren-active');
    $('.bar').removeClass('active');
    if (relative_url !== '/') {
      var $li = $category_nav.find('a[href="' + relative_url + '"]').parent('li').addClass('active');
      $li.parents('ul,li').addClass('active');

      // Add grandchildren class if more than one active column
      if ($category_nav.find('li.active>ul').length > 1) {
        $('.bar').addClass('active');
        $('.categories-parent').addClass('grandchildren-active');
      } else {
        $('.bar.-one, .bar.-two').addClass('active');
      }
      $category_nav.find('.-inner').outerHeight($category_nav.find('li.active:last ul:first,ul.active').maxHeight() + 20);
    } else {
      $('.bar.-one').addClass('active');
      $category_nav.find('.-inner').outerHeight($category_nav.find('ul.categories-parent') .outerHeight() + 20);
    }
    _setCategoryPageClass();
  }

  function _setCategoryPageClass() {
    var active_cat = $('.project-categories li.active:first>a');
    if (active_cat.length) {
      $body.attr('data-pageClass', active_cat.attr('href').split('/')[2]);
    }
  }

  // Gray out page contents under modal
  function _showPageOverlay() {
    if (!$('#page-overlay').length) {
      $('<div id="page-overlay"></div>').prependTo($body);
      $('#page-overlay').addClass('active');
    }
  }
  function _hidePageOverlay() {
    $('#page-overlay').remove();
  }

  // Larger clicker areas ftw (w/ support for target and ctrl/cmd+click)
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

  // Scroll to location in body or container element
  function _scrollBody(element, duration, delay, offset, container) {
    if (typeof container === 'undefined' && $('#wpadminbar').length) {
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

  // Search handler to show form in overlay
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

  // Handler for image modals (currently map images)
  function _initImageModals() {
    // Append image modal markup
    $image_modal = $('<div class="image-modal"><button class="plus-button close hide-image-modal"><div class="plus"></div></button><div class="image-wrap"><img src=""></div></div>').prependTo('.site-footer');

    // All map links open up in modal
    $document.on('click', '.show-image-modal', function(e) {
      e.preventDefault();

      // Hide the collection/modal if it's open
      _hideCollection();
      _hideModal();

      $image_modal.find('img').attr('src', $(this).attr('href'));
      $image_modal.imagesLoaded(function() {
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

  // Load More handler: loads AJAX content and updates various pagination/category attributes in Load More
  function _initLoadMore() {
    $document.on('click', '.load-more a', function(e) {
      e.preventDefault();
      var $load_more = $(this).closest('.load-more');
      var post_type = $load_more.attr('data-post-type') ? $load_more.attr('data-post-type') : 'news';
      var page = parseInt($load_more.attr('data-page-at'));
      var per_page = parseInt($load_more.attr('data-per-page'));
      var category = $load_more.attr('data-category');
      var more_container = $load_more.parents('section,main').find('.load-more-container');

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
            $('.masonry-grid').append($data).removeClass('loading');
            $('.masonry-grid').masonry('appended', $data);
            $load_more.attr('data-page-at', page+1);
            SCB.checkLoadMore();
          }
      });
    });
  }

  // Track AJAX pages in Analytics
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

  // Public functions
  return {
    init: _init,
    resize: _resize,
    checkLoadMore: _checkLoadMore,
    scrollBody: function(section, duration, delay, offset, container) {
      return _scrollBody(section, duration, delay, offset, container);
    },
    feedbackMessage: function(message) {
      return _feedbackMessage(message);
    }
  };

})(jQuery);

// Fire up the mothership
jQuery(document).ready(SCB.init);

// Zig-zag the mothership
jQuery(window).resize(SCB.resize);

// returns max height of elements passed in
jQuery.fn.maxHeight = function(){
    var maxHeight = Math.max.apply(null, jQuery(this).map(function() {
        return jQuery(this).outerHeight();
    }).get());
    return maxHeight;
};
