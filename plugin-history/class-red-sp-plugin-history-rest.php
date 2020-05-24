<?php
class Plugin_History_Rest extends Plugin_History {

  private static $instance;
  public static function get_instance() {
    if ( self::$instance == null ) {
      self::$instance = new Plugin_History_Rest();
    }
    return self::$instance;
  }

  public static function init() {
    add_action( 'rest_api_init', array( __CLASS__, 'plugin_history_rest_routes' ) );
  }

  public static function plugin_history_rest_routes() {
    register_rest_route('plugin-history/v1', 'get_plugin_data', array(
      'methods' => 'GET',
      'callback' => array( __CLASS__, 'get_plugin_data' )
    ));

    register_rest_route('plugin-history/v1', 'save_plugin_data', array(
      'methods' => 'POST',
      'callback' => array( __CLASS__, 'post_plugin_data' )
    ));
  }

  public static function get_plugin_data() {
    // Return plugin data
  }

  public static function post_plugin_data() {
    // Save plugin data
    parent::save_plugin_data();
    wp_send_json_success();
  }
}
