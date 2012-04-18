(function ($) {

/**
 * Provide the summary information for the tracking settings vertical tabs.
 */
Drupal.behaviors.tabsSettingsSummary = {
  attach: function (context) {
    // Make sure this behavior is processed only if drupalSetSummary is defined.
    if (typeof jQuery.fn.drupalSetSummary == 'undefined') {
      return;
    }
    
    $('fieldset#edit-autodismiss', context).drupalSetSummary(function(context){
      var vals = [];
      $('input[type="checkbox"]:checked', context).each(function(){
        vals.push(Drupal.t($.trim(($(this).next('label').text().split(' '))[2])));
      });
      if (!vals.length) {
        return Drupal.t('Disabled');
      }
      else {
        return vals.join(', ');
      }
    });

    $('fieldset#edit-pages', context).drupalSetSummary(function(context){
      var $radio = $('input[name="absolute_messages_visibility_pages"]:checked', context);
      if ($radio.val() == 0) {
        if (!$('textarea[name="absolute_messages_pages"]', context).val()){
          return Drupal.t('Not restricted');
        }
        else {
          return Drupal.t('All pages with exceptions');
        }
      }
      else {
        return Drupal.t('Restricted to certain pages');
      }
    });

    $('fieldset#edit-roles', context).drupalSetSummary(function(context){
      var vals = [];
      $('input[type="checkbox"]:checked', context).each(function(){
        vals.push($.trim($(this).next('label').text()));
      });
      if (!vals.length) {
        return Drupal.t('Not restricted');
      }
      else if ($('input[name="absolute_messages_visibility_roles"]:checked', context).val() == 1) {
        return Drupal.t('Except: @roles', {'@roles' : vals.join(', ')});
      }
      else {
        return vals.join(', ');
      }
    });
    
    $('fieldset#edit-advanced', context).drupalSetSummary(function (context) {
      var vals = [];
      if ($('input[name="absolute_messages_no_js_check"]:checked', context).length == 1) {
        vals.push(Drupal.t('Ignore has_js cookie'));
      };
      return vals.join(', ');
    });
    
  }
};

})(jQuery);
