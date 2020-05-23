<?php

// Inline JavaScript
function ph_add_js_variables() { ?>
  <script>
    // Set the "ajax_url" variable available globally
    ph_ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
    ph_site_url = "<?php echo get_site_url(); ?>";
  </script>

  <?php
}
add_action( 'wp_footer', 'ph_add_js_variables' );
add_action( 'admin_footer', 'ph_add_js_variables' );
