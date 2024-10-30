(function ($) {
  "use strict";

  jQuery(document).on("submit", "#csf-admin-form", function (e) {
    e.preventDefault();

    // We inject some extra fields required for the security
    jQuery(this).append(
      '<input type="hidden" name="action" value="store_admin_data" />'
    );
    jQuery(this).append(
      '<input type="hidden" name="security" value="' +
        feedier_exchanger._nonce +
        '" />'
    );

    // We make our call
    jQuery.ajax({
      url: feedier_exchanger.ajax_url,
      type: "post",
      data: jQuery(this).serialize(),
      success: function (response) {
        location.reload();
      },
    });
  });

  jQuery(document).on("submit", "#csf-admin-form-facebook", function (e) {
    e.preventDefault();

    // We inject some extra fields required for the security
    jQuery(this).append(
      '<input type="hidden" name="action" value="store_admin_data" />'
    );
    jQuery(this).append(
      '<input type="hidden" name="security" value="' +
        feedier_exchanger._nonce +
        '" />'
    );

    // We make our call
    jQuery.ajax({
      url: feedier_exchanger.ajax_url,
      type: "post",
      data: jQuery(this).serialize(),
      success: function (response) {
        location.reload();
      },
    });
  });

  jQuery(document).on("submit", "#csf-admin-form-cron", function (e) {
    e.preventDefault();

    // We inject some extra fields required for the security
    jQuery(this).append(
      '<input type="hidden" name="action" value="store_admin_data" />'
    );
    jQuery(this).append(
      '<input type="hidden" name="security" value="' +
        feedier_exchanger._nonce +
        '" />'
    );

    // We make our call
    jQuery.ajax({
      url: feedier_exchanger.ajax_url,
      type: "post",
      data: jQuery(this).serialize(),
      success: function (response) {
        location.reload();
      },
    });
  });
})(jQuery);
