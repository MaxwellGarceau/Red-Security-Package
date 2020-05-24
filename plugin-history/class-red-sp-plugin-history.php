<?php
class Plugin_History {

  private static $instance;
  public static $ph_options_name = 'ph_options';
  public static function get_instance() {
    if ( self::$instance == null ) {
      self::$instance = new Plugin_History();
    }
    return self::$instance;
  }

  /**
   * Hooks
   */
  public function execute_hooks() {
    add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
  }

  public static function admin_enqueue_scripts() {
    /* Styles */
    wp_enqueue_style( 'ph-style', self::get_directory_url() . '/css/ph-style.css' );

    /* Scripts */
    wp_enqueue_script( 'ph-get-table-column', self::get_directory_url() . '/js/get-table-column.js', array( 'jquery' ) );
    wp_enqueue_script( 'ph-plugin-data', self::get_directory_url() . '/js/plugin-data.js', array( 'jquery' ) );
  }

  /**
   * Getters
   */
  public static function get_directory_url() {
    return RED_SP_URL . 'plugin-history';
  }

  /**
   * Admin
   */
  public function add_admin_menu() {
    add_submenu_page( 'options-general.php', 'Plugin History', 'Plugin History', 'manage_options', 'plugin-history', array( $this, 'output_admin_menu' ) );
  }

  /**
   * Data
   */
  public static function get_options() {
    /* NOTE: Calls get_option twice. Need to rethink. */
    if ( get_option( self::$ph_options_name ) === false ) {
      add_option( self::$ph_options_name, self::get_defaults() );
    }
    return get_option( self::$ph_options_name );
  }

  public function get_defaults() {
    return array(
      'plugin_reports' => array(
        // Key is date, value is plugin data
      ),
    );
  }

  public static function save_plugin_data( $plugins = null ) {
    if ( $plugins === null ) {
      $plugins = Plugin_History::get_plugins();
    }

    $ph_options = self::get_options();
    $today = self::get_todays_date();
    $ph_options['plugin_reports'][] = array( $today => $plugins );
    update_option( self::$ph_options_name, $ph_options );
  }

  /**
   * HTML
   */
  public static function output_admin_menu() {
    $current_plugins = Plugin_History::get_plugins(); // Hardcoded for now
    // wp_die(var_dump($current_plugins));
    // $plugin_reports = self::get_options();
    // $plugin_reports = $plugin_reports['plugin_reports'];
    // $plugin_reports = array_values( $plugin_reports[0] )[0];
// wp_die(var_dump($current_plugins));
    $fake_old_plugins = $current_plugins;
    $fake_old_plugins['akismet/akismet.php']['Version'] = '2.21';
    self::combine_plugin_groups($current_plugins,$fake_old_plugins);

    $output .= '<div class="ph-wrap">';

      $output .= '<h1>Plugin History</h1>';

      $output .= '<p class="ph-notice">*Click column title to copy text</p>';

      $output .= '<div class="ph-all-tables-container">';

        $output .= '<div class="ph-table-container">';

          $output .= self::get_plugin_table( $current_plugins, $current_plugins );

          $output .= self::get_save_plugins_button();

        $output .= '</div>';

      $output .= '</div>'; // .ph-all-tables-container

    $output .= '</div>'; // .ph-wrap

    echo $output;
  }

  public static function get_plugin_table( $current_plugins, $compare_plugins = array() ) {
    $output .= '<table id="plugin-history" class="ph-plugin-table">';
    $output .= '<thead>
        <tr>
            <th class="ph-plugin-name-header" title="Current Plugins">
                Plugin Name
            </th>
            <th title="Click to select column">
                Plugin Version
            </th>
            <th title="Click to select column">
                Last Updated
            </th>';

      if ( ! empty( $compare_plugins ) ) {
        $output .= '<th class="ph-table-spacer"></th>'; // Space between last table and this one

        $output .= '<th class="ph-plugin-name-header" title="Last Saved Plugins">
            Plugin Name
        </th>
        <th title="Click to select column">
            Plugin Version
        </th>
        <th title="Click to select column">
            Last Updated
        </th>';
      }

    $output .= '</tr>
    </thead>';

    $combined_plugins = self::combine_plugin_groups( $current_plugins, $compare_plugins );
    foreach ( $combined_plugins as $folder_file => $plugin ) {
      /**
       * Compare current and old plugin
       */
      $current_plugin = $plugin['current_plugin'];
      $compare_plugin = $plugin['compare_plugin'];
      $plugin_row_classes = array();
      $has_current_plugin = isset( $current_plugin ) && !empty( $current_plugin );
      $has_compare_plugin = isset( $compare_plugin ) && !empty( $compare_plugin );

      /* Plugin was deleted */
      if ( ! $has_current_plugin && $has_compare_plugin ) {
        $plugin_row_classes[] = 'ph-deleted';
        $plugin_row_classes[] = 'ph-tooltip';
      }

      /* Plugin was added */
      if ( $has_current_plugin && ! $has_compare_plugin ) {
        $plugin_row_classes[] = 'ph-added';
        $plugin_row_classes[] = 'ph-tooltip';
      }


      if ( $has_current_plugin && $has_compare_plugin ) {

        /* Plugin was upgraded */
        if ( version_compare( $current_plugin['Version'], $compare_plugin['Version'], '>' ) ) {
          $plugin_row_classes[] = 'ph-upgraded';
          $plugin_row_classes[] = 'ph-tooltip';

        /* Plugin was downgraded */
        } else if ( version_compare( $current_plugin['Version'], $compare_plugin['Version'], '<' ) ) {
          $plugin_row_classes[] = 'ph-downgraded';
          $plugin_row_classes[] = 'ph-tooltip';
        }

      }

      $output .= '<tr class="' . implode( ' ', $plugin_row_classes ) . '">';
        /**
         * Current plugins
         */
        if ( $has_current_plugin ) {
          $output .= '<td>' . $current_plugin['Name'] . '</td>';
          $output .= '<td>' . $current_plugin['Version'] . '</td>';
          $output .= '<td>' . date( 'm/d/Y', $current_plugin['last_updated'] ) . '</td>';
        } else {
          /* Placeholders */
          $output .= '<td></td>';
          $output .= '<td></td>';
          $output .= '<td></td>';
        }

        // if ( $has_current_plugin && $has_compare_plugin ) {
          $output .= '<td class="ph-table-spacer"></td>'; // Space between last table and this one
        // }

        /**
         * Last Saved plugins
         */
        if ( $has_compare_plugin ) {
          $output .= '<td>' . $compare_plugin['Name'] . '</td>';
          $output .= '<td>' . $compare_plugin['Version'] . '</td>';
          $output .= '<td>' . date( 'm/d/Y', $compare_plugin['last_updated'] ) . '</td>';
        } else {
          /* Placeholders */
          $output .= '<td></td>';
          $output .= '<td></td>';
          $output .= '<td></td>';
        }

      $output .= '</tr>';
    }

    $output .= '</table>';
    return $output;
  }

  public static function get_save_plugins_button() {
    return '<button id="ph-save-plugin-data" class="button button-primary">Save Plugin Data</button>';
  }

  public static function get_previous_plugin_report() {

    return '<button id="ph-save-plugin-data" class="button button-primary">Save Plugin Data</button>';
  }

  /**
   * Helpers
   */
  public static function get_todays_date() {
    return date( 'D M d, Y G:i' );
  }

  public static function get_plugins() {
    $plugins = get_plugins(); // Start with core WP plugin info
    // wp_die(var_dump($plugins));
    /* Add additional information to plugins */
    foreach ( $plugins as $folder_file => &$plugin ) {
      $path = WP_PLUGIN_DIR . '/' . $folder_file;
      $plugin['last_updated'] = filemtime( $path );
    }
    return $plugins;
  }

  public static function combine_plugin_groups( $current_plugins, $compare_plugins ) {
    /* Merge plugins with same name into same array */
    $combined_plugins = array();

    // DELETE AFTER TESTING
    $compare_plugins['old-test-plugin/old-test-plugin.php'] = array( 'Name' => 'Old Plugin (was deleted)', 'Version' => '7.5.6' ); // DELETED test
    $current_plugins['new-test-plugin/new-test-plugin.php'] = array( 'Name' => 'New Plugin (was just added)', 'Version' => '1.2.6' ); // ADDED test
    $compare_plugins['akismet/akismet.php']['Version'] = '1.5.2'; // DOWNGRADED test
    $compare_plugins['hello.php']['Version'] = '100.1.2'; // UPGRADED test

    foreach ( $current_plugins as $key => $current_plugin ) {
      if ( isset( $compare_plugins[$key] ) ) {
        $combined_plugins[$key] = array( 'current_plugin' => $current_plugin, 'compare_plugin' => $compare_plugins[$key] );
      } else {
        $combined_plugins[$key] = array( 'current_plugin' => $current_plugin );
      }
    }

    /* Dump all non merged plugins instances into the new array */
    foreach ( $compare_plugins as $key => $compare_plugin ) {
      if ( ! isset( $combined_plugins[$key] ) ) {
        $combined_plugins[$key] = array( 'compare_plugin' => $compare_plugin );
      }
    }

    /* Sort by alphabetical order */
    ksort( $combined_plugins );
    return $combined_plugins;
  }

}
$plugin_history = Plugin_History::get_instance();
$plugin_history->execute_hooks();
