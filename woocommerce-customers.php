<?php

/*
  Plugin Name: WP Sheet Editor - WooCommerce Customers
  Description: WooCommerce Customers Spreadsheet and Bulk Editor
  Version: 1.0.9
  Author:      WP Sheet Editor
  Author URI:  https://wpsheeteditor.com
  Plugin URI: https://wpsheeteditor.com/extensions/woocommerce-customers-spreadsheet/
 */

require 'vendor/vg-plugin-sdk/index.php';
require 'vendor/wp-sheet-editor-users/users.php';

if (!class_exists('WP_Sheet_Editor_WooCommerce_Customers')) {

	/**
	 * Filter rows in the spreadsheet editor.
	 */
	class WP_Sheet_Editor_WooCommerce_Customers {

		static private $instance = false;
		var $users_plugin_exists = null;

		private function __construct() {
			
		}

		function init() {
			$this->users_plugin_exists = file_exists(WP_CONTENT_DIR . '/plugins/bulk-edit-user-profiles-in-spreadsheet/users.php');
			add_filter('wpse_users_allowed_roles', array($this, 'allow_customers_role'));
			add_filter('vg_sheet_editor/user_path/event_data', array($this, 'filter_event_data'));
		}

		function filter_event_data($event_data) {
			if (!$this->users_plugin_exists) {
				$event_data['eventCategory'] = 'WC Customers - ' . $event_data['eventCategory'];
			}

			return $event_data;
		}

		function allow_customers_role($roles) {
			if (!$this->users_plugin_exists) {
				$roles = array();
			}
			$roles['customer'] = __('Customers', 'woocommerce');
			return $roles;
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WP_Sheet_Editor_WooCommerce_Customers::$instance) {
				WP_Sheet_Editor_WooCommerce_Customers::$instance = new WP_Sheet_Editor_WooCommerce_Customers();
				WP_Sheet_Editor_WooCommerce_Customers::$instance->init();
			}
			return WP_Sheet_Editor_WooCommerce_Customers::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}


add_action('after_setup_theme', 'vgse_woocommerce_customers', 99);

if (!function_exists('vgse_woocommerce_customers')) {

	function vgse_woocommerce_customers() {
		return WP_Sheet_Editor_WooCommerce_Customers::get_instance();
	}

}