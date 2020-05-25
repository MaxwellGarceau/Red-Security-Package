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
    /**
     * Plugin Data
     */
    register_rest_route('plugin-history/v1', 'get_plugin_data', array(
      'methods' => 'GET',
      'callback' => array( __CLASS__, 'get_plugin_data' )
    ));

    register_rest_route('plugin-history/v1', 'save_plugin_data', array(
      'methods' => 'POST',
      'callback' => array( __CLASS__, 'post_plugin_data' )
    ));

    register_rest_route('plugin-history/v1', 'erase_plugin_history', array(
      'methods' => 'DELETE',
      'callback' => array( __CLASS__, 'erase_plugin_history' )
    ));
    register_rest_route('plugin-history/v1', 'rest_delete_active_plugin_set', array(
      'methods' => 'DELETE',
      'callback' => array( __CLASS__, 'rest_delete_active_plugin_set' )
    ));
  }

  /**
   * Plugin Data
   */
  public static function get_plugin_data() {
    // Return plugin data
  }

  public static function post_plugin_data() {
    // Save plugin data
    parent::save_plugin_data();
    wp_send_json_success();
  }

  public static function erase_plugin_history() {
    // Save plugin data
    parent::erase_plugin_history();
    wp_send_json_success();
  }

  public static function rest_delete_active_plugin_set( $data ) {
    if ( ! isset( $data ) || ! isset( $data['timestamp'] ) ) {
      wp_send_json_error();
    }

    $timestamp = $data['timestamp'];
    // Save plugin data
    parent::delete_active_plugin_set( $timestamp );
    wp_send_json_success();
  }
}
