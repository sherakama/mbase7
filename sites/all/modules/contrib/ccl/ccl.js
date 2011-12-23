(function ($) {

Drupal.behaviors.ccl = {
  attach: function (context, settings) {
    // This behavior attaches by ID, so is only valid once on a page.
    if ($('#ccl-add-form', context).size()) {
      $('#edit-link-type, #edit-node-options', context).buttonset();
    }
  }
};

})(jQuery);
