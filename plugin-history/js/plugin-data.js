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
            location.reload(true);
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
            location.reload(true);
          }
      });
    });
  });
})(jQuery);
