<?php
/*
 Plugin Name: Red Security Package
 Plugin URI: https://redearthdesign.com/
 description: >-
a plugin to create awesomeness and spread joy
 Version: 1.0.0
 Author: Red Earth Design
 Author URI: https://redearthdesign.com/
 License: Trade Secret
 */

/* NOTE: Copied from Beaer Builder core plugin. Modified to fit needs. */
if ( ! class_exists( 'Red_Security_Package' ) ) {

	/**
	 * Responsible for setting up builder constants, classes and includes.
	 */
	final class Red_Security_Package {

		/**
		 * Load the builder if it's not already loaded, otherwise
		 * show an admin notice.
		 *
		 * @since 1.8
		 * @return void
		 */
		static public function init() {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			self::define_constants();
			self::load_files();
		}

		/**
		 * Define builder constants.
     *
		 * @return void
		 */
		static private function define_constants() {
			define( 'RED_SP_VERSION', '1.0.0' );
			define( 'RED_SP_FILE', trailingslashit( dirname( dirname( __FILE__ ) ) ) . 'red-security-package/red-security-package.php' );
			define( 'RED_SP_DIR', plugin_dir_path( RED_SP_FILE ) );
			define( 'RED_SP_URL', plugins_url( '/', RED_SP_FILE ) );
		}

		/**
		 * Loads classes and includes.
		 *
		 * @return void
		 */
		static private function load_files() {

			/**
       * Classes
       */

      /* Plugin History */
			require_once RED_SP_DIR . 'plugin-history/class-red-sp-plugin-history.php';
      require_once RED_SP_DIR . 'plugin-history/class-red-sp-plugin-history-rest.php';
      require_once RED_SP_DIR . 'plugin-history/inc/js-variables.php';
		}
	}
}

Red_Security_Package::init();
