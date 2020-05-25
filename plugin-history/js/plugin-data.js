(function ($) {
  $(document).ready(function() {
    /**
     * Save Plugin History
     */
    $('#ph-save-plugin-data').on('click', function() {

      $.ajax({
          // data: customCss,
          url: ph_site_url + '/wp-json/plugin-history/v1/save_plugin_data',
          method: 'POST',
          success: function(response) {
            var redirectUrl = ph_site_url + '/wp-admin/admin.php?page=plugin-history';
            location.assign(redirectUrl);
          }
      });
    });

    /**
     * Delete Active Plugin Set
     */
    $('#ph-delete-active-plugin-set').on('click', function() {

      /* Get timestamp param from query string */
      var urlParams = new URLSearchParams(window.location.search);
      var timestamp = urlParams.get('timestamp');

      $.ajax({
          // data: customCss,
          url: ph_site_url + '/wp-json/plugin-history/v1/rest_delete_active_plugin_set',
          method: 'DELETE',
          data: {
            timestamp: timestamp,
          },
          success: function(response) {
            var redirectUrl = ph_site_url + '/wp-admin/admin.php?page=plugin-history';
            location.assign(redirectUrl);
          }
      });
    });

    /**
     * Delete Plugin History
     */
    $('#ph-erase-plugin-history').on('click', function() {

      $.ajax({
          // data: customCss,
          url: ph_site_url + '/wp-json/plugin-history/v1/erase_plugin_history',
          method: 'DELETE',
          success: function(response) {
            var redirectUrl = ph_site_url + '/wp-admin/admin.php?page=plugin-history';
            location.assign(redirectUrl);
          }
      });
    });
  });
})(jQuery);
