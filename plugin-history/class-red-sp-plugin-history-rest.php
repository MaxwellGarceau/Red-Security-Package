<?php
class Plugin_History_Rest extends Plugin_History {

  private static $instance;
  public static function get_instance() {
    if ( self::$instance == null ) {
      self::$instance = new Plugin_History_Rest();
    }
    return self::$instance;
  }

  public function execute_hooks() {
    add_action( 'rest_api_init', array( $this, 'plugin_history_rest_routes' ) );
  }

  public function plugin_history_rest_routes() {
    register_rest_route('plugin-history/v1', 'get_plugin_data', array(
      'methods' => 'GET',
      'callback' => array( $this, 'get_plugin_data' )
    ));

    register_rest_route('plugin-history/v1', 'save_plugin_data', array(
      'methods' => 'POST',
      'callback' => array( $this, 'post_plugin_data' )
    ));
  }

  public function get_plugin_data() {
    // Return plugin data
  }

  public function post_plugin_data() {
    // Save plugin data
    parent::save_plugin_data();
    wp_send_json_success();
  }
}
$plugin_history_rest = Plugin_History_Rest::get_instance();
$plugin_history_rest->execute_hooks();
