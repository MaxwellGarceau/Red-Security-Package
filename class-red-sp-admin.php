<?php
class Red_Security_Package_Admin {
  /**
   * Hooks
   */
  public static function init() {
    add_action( 'admin_menu', array( __CLASS__, 'add_menu_page' ) );
  }

  /**
   * Admin menu
   */
  static public function add_menu_page() {
    /* Main admin menu */
    add_menu_page(
      'Red Security Package',
      'Security Package Tools',
      'manage_options',
      'red-security-package',
      array( __CLASS__, 'admin_page' )
    );

    /* Plugin History admin menu */
    add_submenu_page(
      'red-security-package',
      'Plugin History',
      'Plugin History',
      'manage_options',
      'plugin-history',
      array( 'Plugin_History', 'output_admin_menu' ) );
  }

  static public function admin_page() {
    echo '<h1>Red Security Package</h1>';
    echo '<p>Currently only Plugin History is active</p>';
  }
}
