Drupal.absoluteMessages = Drupal.absoluteMessages || {};
Drupal.settings.absoluteMessages = Drupal.settings.absoluteMessages || {};

(function ($) {

  /**
   * Attaches absolute messages behavior.
   */
  Drupal.behaviors.absoluteMessages = {
    attach: function (context, settings) {
      $("body", context).once('absolute-messages', function(){
        Drupal.absoluteMessages.initialize(settings.absoluteMessages);
      });
    }
  };

  Drupal.absoluteMessages.initialize = function(settings){
    $.extend(Drupal.absoluteMessages, settings);

    Drupal.absoluteMessages.timeouts = new Array();
    if ($("#absolute-messages-messages").length > 0) {
      // Move messages from closure to right after opening of body tag.
      $("body").prepend($("#absolute-messages-messages"));

      if (Drupal.absoluteMessages.max_lines) {
        Drupal.absoluteMessages.checkHeights();
      }

      // Check AM div position.
      Drupal.absoluteMessages.checkTopOffset();
      // Update position on page scroll.
      if ($("#absolute-messages-messages").css("position") == "fixed") {
        $(window).scroll(Drupal.absoluteMessages.checkTopOffset);
      }

      // Bind all required events.
      Drupal.absoluteMessages.bindEvents();

      // Show all messages.
      $(".absolute-messages-message").not(".absolute-messages-dismiss-all").slideDown(600);
      Drupal.absoluteMessages.checkIcons();
    }

    // Move any additional messages addad by Ajax calls
    // after the page was generated.
    $(document).ajaxComplete(function(){
      Drupal.absoluteMessages.checkNewMessages();
    });

  };

  Drupal.absoluteMessages.checkHeights = function(){
    var line_height;
    var current_height;
    // display-none elements do not have height, so we need to display
    // them first  (although hidden) to be able to get their height.
    // Also, force-set height to avoid "jumpy" animation.
    $(".absolute-messages-message").each(function(){
      if ($(this).css("display") == "none") {
        $(this).css({'visibility':'hidden', 'display':'block'})
               .css("height", $(this).height());
        line_height = parseInt($(".absolute-messages-message .content").css("line-height"));
        current_height = $(".content", this).height();
        if (current_height > line_height * Drupal.absoluteMessages.max_lines) {
          $(".content", this).css("max-height", line_height * Drupal.absoluteMessages.max_lines)
                             .addClass("collapsed")
                             .parents(".absolute-messages-message")
                             .addClass("collapsible")
                             .attr("title", "Click to see the whole message");
        }
        // And hide them again so we still can manage them using jQuery sliding.
        $(this).removeAttr('style');
      }
    });
  };

  Drupal.absoluteMessages.checkIcons = function(){
    var visible_messages = $(".absolute-messages-message:visible").not(".absolute-messages-dismiss-all").size();
    // If no messages are displayed, remove "Dismiss all" icon
    // and show "Show dismissed messages" icon.
    if (visible_messages == 0) {
      $("div.absolute-messages-dismiss-all").hide();
      $("#absolute-messages-show").show();
    };
    // Show "Dismiss all messages" icon if number of visible
    // messages is higher that configured in module settings.
    if (Drupal.absoluteMessages.dismiss_all_count && visible_messages > Drupal.absoluteMessages.dismiss_all_count) {
      $("div.absolute-messages-dismiss-all").show();
    }
  };

  Drupal.absoluteMessages.checkTopOffset = function(){
    var topOffset = 0;
    // Admin Menu offset.
    if (Drupal.admin != undefined) {
      // Let's wait for fully generated #admin-menu if it's not ready yet.
      if ($("#admin-menu").height() == null) {
        setTimeout("Drupal.absoluteMessages.checkTopOffset()", 100);
        return;
      }
      // Overlay is opened.
      if ($("body").hasClass("overlay")) {
        // When AM has fixed position and overlay is opened, parent admin menu
        // is not scrolling together with window when overlay is being scrolled,
        // and is always visible, therefore AM needs to be always displayed below.
        if ($("#absolute-messages-messages").css("position") == "fixed") {
          topOffset = $("#admin-menu").height();
        }
        // But if its position is not fixed, it will be moved additional
        // 20px downwards, as admin menu adds additional 20px top margin
        // to overlay body, so we need to account for it here.
        else {
          topOffset = -parseInt($("body").css("margin-top"));
        }
      }
      // When there is no overlay, admin menu is scrolling together with
      // page content, so AM needs to move to the top of the visible area
      // when admin menu disappears from view.
      else if ($("#absolute-messages-messages").css("position") == "fixed") {
        if ($(window).scrollTop() < $("#admin-menu").height()) {
          topOffset = $("#admin-menu").height() - $(window).scrollTop();
        }
      }
    }
    // Toolbar offset.
    else if ($("body").hasClass("toolbar")) {
      // When overlay is displayed, we need to take toolbar height from parent.
      if ($("body").hasClass("overlay")) {
        // When AM has fixed position, it just needs to be moved below
        // the toolbar.
        if ($("#absolute-messages-messages").css("position") == "fixed") {
          topOffset = parent.Drupal.toolbar.height();
        }
        // But if its position is not fixed, it will be moved additional
        // 20px downwards, as overlay body has 20px top padding, so we need
        // to account for it here.
        else {
          topOffset = -parseInt($("body").css("padding-top"));
        }
      }
      // No overlay is displayed, so just move AM below toolbar.
      else if ($("#absolute-messages-messages").css("position") == "fixed") {
        topOffset = Drupal.toolbar.height();
      }
    }
    // Put AM where it should be.
    $("#absolute-messages-messages").css("top", topOffset + "px");
  };

  Drupal.absoluteMessages.bindEvents = function(){

    // Dismiss single message.
    $("a.absolute-messages-dismiss").once("absolute-messages").click(function(event){
      // Stop propagation to avoid expanding/collapsing parent div.
      event.stopPropagation();
      $(this).parents(".absolute-messages-message").slideUp(300, function(){
        Drupal.absoluteMessages.checkIcons();
      });
    });

    // Dismiss all messages.
    $("a.absolute-messages-dismiss-all").once("absolute-messages").bind("click", function(){
      $(".absolute-messages-message").slideUp(300, function(){
        Drupal.absoluteMessages.checkIcons();
      });
    });

    // Show cursor as pointer when hovering over 'show dismissed messages' icon.
    // This is mainly for IE, as it does not want to change the cursor through CSS
    // over the whole element when containing element has width and height of 0px.
    $("#absolute-messages-show").once("absolute-messages").bind("mouseenter", function(){
      $(this).css("cursor", "pointer");
    }).bind("mouseleave", function(){
      $(this).css("cursor", "auto");
    })
    // Show all previously dismissed messages after clicking on 'show dismissed' icon.
    .bind("click", function(){
      $(this).hide();
      $(".absolute-messages-message").not(".absolute-messages-dismiss-all").slideDown(300, function(){
        Drupal.absoluteMessages.checkIcons();
      });
    });

    // Clear all timeouts on mouseover and set them again on mouseout.
    $("#absolute-messages-messages").once("absolute-messages").bind("mouseenter", function(){
      Drupal.absoluteMessages.clearTimeouts();
    }).bind("mouseleave", function(){
      Drupal.absoluteMessages.setTimeouts();
    });

    // Expand/collapse long messages.
    $(".absolute-messages-message.collapsible").once("absolute-messages").bind("click", function(){
      if ($(".content", this).hasClass("collapsed")) {
        Drupal.absoluteMessages.messageExpand($(".content", this));
      }
      else {
        Drupal.absoluteMessages.messageCollapse($(".content", this));
      }
    });

    // Automatic dismiss messages after specified time.
    $.each(Drupal.absoluteMessages.dismiss, function(index, value){
      if (value == 1) {
        Drupal.absoluteMessages.timeouts[index] = setTimeout(function(){
          $(".absolute-messages-"+index).slideUp(600, function(){
            Drupal.absoluteMessages.checkIcons();
          });
        }, Drupal.absoluteMessages.dismiss_time[index] * 1000);
      }
    });

  };

  Drupal.absoluteMessages.setTimeouts = function(){
    $.each(Drupal.absoluteMessages.dismiss, function(index, value){
      if (value == 1) {
        Drupal.absoluteMessages.timeouts[index] = setTimeout(function(){
          $(".absolute-messages-"+index).slideUp(600, function(){
            Drupal.absoluteMessages.checkIcons();
          });
        }, Drupal.absoluteMessages.dismiss_time[index] * 1000);
      }
    });
  };

  Drupal.absoluteMessages.clearTimeouts = function(){
    $.each(Drupal.absoluteMessages.dismiss, function(index, value){
      clearTimeout(Drupal.absoluteMessages.timeouts[index]);
    });
  };

  Drupal.absoluteMessages.messageCollapse = function(element){
    $(element).css("max-height", parseInt($(element).css("line-height")) * Drupal.absoluteMessages.max_lines)
              .removeClass("expanded")
              .addClass("collapsed");
  };

  Drupal.absoluteMessages.messageExpand = function(element){
    $(element).css("max-height", "")
              .removeClass("collapsed")
              .addClass("expanded");
  };

  Drupal.absoluteMessages.checkNewMessages = function(){
    Drupal.absoluteMessages.clearTimeouts();
    // Search for any #absolute-messages-messages divs
    // which are not direct children of <body> tag.
    $("body").children().find("#absolute-messages-messages").each(function(index){
      if (Drupal.absoluteMessages.max_lines) {
        Drupal.absoluteMessages.checkHeights();
      }
      // If #absolute-messages-messages dix does not exist yet right after
      // the body tag, let's just move the freshly created one there.
      if ($("body > #absolute-messages-messages").length == 0) {
        $("body").prepend($(this));
        $(".absolute-messages-message").not(".absolute-messages-dismiss-all").slideDown(600);
      }
      // On the other hand, if it already exists there, let's move all
      // new messages only and add them right before "Dismiss all messages"
      // icon, then remove the new #absolute-messages-messages div completely.
      else {
        $("#absolute-messages-show").hide();
        $("div.absolute-messages-dismiss-all", "body > #absolute-messages-messages").before($(".absolute-messages-message", this)
                                                                                    .not(".absolute-messages-dismiss-all", this)
                                                                                    .slideDown(600));
        $(this).remove();
      }
      Drupal.absoluteMessages.bindEvents();
      Drupal.absoluteMessages.checkIcons();
    });
  };

})(jQuery);
