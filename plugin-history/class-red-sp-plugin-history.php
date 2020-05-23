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
    $plugins = Plugin_History::get_plugins(); // Hardcoded for now
    // wp_die(var_dump($plugins));
    // $plugin_reports = self::get_options();
    // $plugin_reports = $plugin_reports['plugin_reports'];
    // $plugin_reports = array_values( $plugin_reports[0] )[0];


    $output = '<div class="wrap">';
      $output .= '<p>*Click column title to copy text.</p>';
      $output .= self::get_plugin_table( $plugins );


      // $output .= '<hr />';

      // $output .= '<h2>Last saved plugin table</h2>';

      // $output .= self::get_plugin_table( $plugin_reports ); // Previous plugin report

      $output .= self::get_save_plugins_button();

    $output.= '</div>';
    echo $output;
  }

  public static function get_plugin_table( $plugins ) {
    $output .= '<table id="plugin-history">';
    $output .= '<thead>
        <tr>
            <th title="Click to select column">
                Plugin Name
            </th>
            <th title="Click to select column">
                Plugin Version
            </th>
            <th title="Click to select column">
                Last Updated
            </th>
        </tr>
    </thead>';

    foreach ( $plugins as $plugin ) {
      $output .= '<tr>';
        $output .= '<td>' . $plugin['Name'] . '</td>';
        $output .= '<td>' . $plugin['Version'] . '</td>';
        $output .= '<td>' . date( 'm/d/Y', $plugin['last_updated'] ) . '</td>';
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
}
$plugin_history = Plugin_History::get_instance();
$plugin_history->execute_hooks();
