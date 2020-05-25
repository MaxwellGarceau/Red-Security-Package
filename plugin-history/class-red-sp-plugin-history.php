<?php
class Plugin_History {

  private static $instance;
  public static $ph_options_name = 'ph_options';
  public static $ph_save_date_format = 'D M d, Y G:i';

  public static function get_instance() {
    if ( self::$instance == null ) {
      self::$instance = new Plugin_History();
    }
    return self::$instance;
  }

  /**
   * Hooks
   */
  public static function init() {
    add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
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
   * Options
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
      'active_plugin_set' => '',
    );
  }

  /**
   * Plugin Data
   */
  public static function save_plugin_data( $plugins = null ) {
    if ( $plugins === null ) {
      $plugins = self::get_plugins();
    }

    $ph_options = self::get_options();
    $today = self::get_todays_timestamp();
    $ph_options['plugin_reports'][$today] = $plugins;
    update_option( self::$ph_options_name, $ph_options );
  }

  public static function erase_plugin_history() {
    $ph_options = self::get_options();
    $ph_options['plugin_reports'] = array();
    update_option( self::$ph_options_name, $ph_options );
  }

  public static function delete_active_plugin_set( $timestamp = null ) {
    $ph_options = self::get_options();

    /* If $timestamp is not passed in get it from 'get_active_plugin_set' */
    if ( $timestamp === null ) {
      $timestamp = self::get_active_plugin_set();
    }

    /* Remove array entry and update all options */
    unset( $ph_options['plugin_reports'][$timestamp] );
    update_option( self::$ph_options_name, $ph_options );
  }

  public static function get_last_plugin_save_date() {
    $ph_options = self::get_options();
    if ( isset( $ph_options['plugin_reports'] ) && ! empty( $ph_options['plugin_reports'] ) ) {
      $dates = array_keys( $ph_options['plugin_reports'] );
      $dates = max( $dates );
    } else {
      $dates = null;
    }
    return $dates;
  }

  public static function get_last_plugin_save() {
    $ph_options = self::get_options();
    $latest_date = self::get_last_plugin_save_date();
    return $ph_options['plugin_reports'][$latest_date];
  }

  /**
   * Active Plugin Set Functions
   */
  public static function get_active_plugin_set() {
    // wp_die(var_dump(parse_url($_SERVER['REQUEST_URI'])));
    if ( isset( $_GET['timestamp'] ) && $_GET['timestamp'] !== '' ) {
      $timestamp = (int) $_GET['timestamp'];
    } else {
      $timestamp = self::get_last_plugin_save_date();
    }
    return $timestamp;
  }

  /**
   * Helpers
   */
  public static function get_todays_timestamp( $date = null ) {
    return strtotime( date( self::$ph_save_date_format ) );
  }

  public static function get_plugins() {
    $plugins = get_plugins(); // Start with core WP plugin info
    /* Add additional information to plugins */
    foreach ( $plugins as $folder_file => &$plugin ) {
      $path = WP_PLUGIN_DIR . '/' . $folder_file;
      $plugin['last_updated'] = filemtime( $path );
    }
    return $plugins;
  }

  public static function combine_plugin_groups( $current_plugins, $compare_plugins = array() ) {
    $combined_plugins = array();

    /* Merge plugins with same name into same array */
    foreach ( $current_plugins as $key => $current_plugin ) {
      if ( isset( $compare_plugins[$key] ) ) {
        $combined_plugins[$key] = array( 'current_plugin' => $current_plugin, 'compare_plugin' => $compare_plugins[$key] );
      } else {
        $combined_plugins[$key] = array( 'current_plugin' => $current_plugin );
      }
    }

    /* If there are no plugins to check bail early */
    if ( empty( $compare_plugins ) ) {
      return $combined_plugins;
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

  /**
   * Format for email
   */
  public static function get_plugin_changes_string( $current_plugins, $compare_plugins ) {
    $plugin_changes = array();
    $combined_plugins = self::combine_plugin_groups( $current_plugins, $compare_plugins );
    foreach ( $combined_plugins as $folder_file => $plugin ) {

      /**
       * Compare current and old plugin
       */
      $current_plugin = $plugin['current_plugin'];
      $compare_plugin = $plugin['compare_plugin'];
      $has_current_plugin = isset( $current_plugin ) && !empty( $current_plugin );
      $has_compare_plugin = isset( $compare_plugin ) && !empty( $compare_plugin );

      /* Plugin was deleted */
      if ( ! $has_current_plugin && $has_compare_plugin ) {
        $plugin_changes[] = $compare_plugin['Name'] . ' was removed';
      }

      /* Plugin was added */
      if ( $has_current_plugin && ! $has_compare_plugin ) {
        $plugin_changes[] = $current_plugin['Name'] . ' was added';
      }

      if ( $has_current_plugin && $has_compare_plugin ) {

        /* Plugin was upgraded */
        if ( version_compare( $current_plugin['Version'], $compare_plugin['Version'], '>' ) ) {
          $plugin_changes[] = $current_plugin['Name'] . ' ' . $compare_plugin['Version'] . ' to ' . $current_plugin['Version'];

        /* Plugin was downgraded */
        } else if ( version_compare( $current_plugin['Version'], $compare_plugin['Version'], '<' ) ) {
          $plugin_changes[] = $current_plugin['Name'] . ' was downgraded from version ' . $compare_plugin['Version'] . ' to ' . $current_plugin['Version'] . ' for technical stability';
        }

      }

    }

    return $plugin_changes;
  }

  /**
   * Dev
   */
  // DELETE AFTER TESTING (or move to another file)
  public static function add_differences_for_testing( &$current_plugins, &$compare_plugins ) {
    $current_plugins['new-test-plugin/new-test-plugin.php'] = array( 'Name' => 'New Plugin (was just added)', 'Version' => '1.2.6' ); // ADDED test
    $compare_plugins['old-test-plugin/old-test-plugin.php'] = array( 'Name' => 'Old Plugin (was deleted)', 'Version' => '7.5.6' ); // DELETED test
    $compare_plugins['akismet/akismet.php']['Version'] = '1.5.2'; // DOWNGRADED test
    $compare_plugins['hello.php']['Version'] = '100.1.2'; // UPGRADED test
  }

}
