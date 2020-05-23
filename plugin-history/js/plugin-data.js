(function ($) {
  $(document).ready(function() {
    // When button is clicked the css entered is transported to WordPress
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
  });
})(jQuery);
