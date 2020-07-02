<?php

namespace GroundhoggSplitTesting;

use Groundhogg\Admin\Admin_Menu;
use Groundhogg\DB\Manager;
use Groundhogg\Extension;
use GroundhoggSplitTesting\Reports\Split_Test_Funnel_Reports;
use GroundhoggSplitTesting\Reports\Split_Testing_Reports;
use GroundhoggSplitTesting\Steps\Split_Email;

class Plugin extends Extension {


	/**
	 * Override the parent instance.
	 *
	 * @var Plugin
	 */
	public static $instance;

	/**
	 * Include any files.
	 *
	 * @return void
	 */
	public function includes() {
//        require  GROUNDHOGG_SPLIT_TESTING_PATH . '/includes/functions.php';
	}

	/**
	 * Init any components that need to be added.
	 *
	 * @return void
	 */
	public function init_components() {

		new Split_Test();
		new Split_Testing_Reports();
		new Split_Test_Funnel_Reports();
	}

	/**
	 * Get the ID number for the download in EDD Store
	 *
	 * @return int
	 */
	public function get_download_id() {
		// TODO: Implement get_download_id() method.
	}


	/**
	 * register the new Action.
	 *
	 * @param \Groundhogg\Steps\Manager $manager
	 */
	public function register_funnel_steps( $manager )
	{
		$manager->add_step( new Split_Email());
	}

	public function register_admin_scripts($is_minified, $IS_MINIFIED)
	{
		wp_register_script( 'groundhogg-admin-reporting-split-testing', GROUNDHOGG_SPLIT_TESTING_ASSETS_URL . 'js/reports.js', [
			'jquery',
			'moment-js',
			'chart-js',
			'baremetrics-calendar',
			'groundhogg-admin',
			'groundhogg-admin-reporting'
		], GROUNDHOGG_SPLIT_TESTING_VERSION, true );


	}

	/**
	 * Get the version #
	 *
	 * @return mixed
	 */
	public function get_version() {
		return GROUNDHOGG_SPLIT_TESTING_VERSION;
	}

	/**
	 * @return string
	 */
	public function get_plugin_file() {
		return GROUNDHOGG_SPLIT_TESTING__FILE__;
	}

	/**
	 * Register autoloader.
	 *
	 * Groundhogg autoloader loads all the classes needed to run the plugin.
	 *
	 * @since 1.6.0
	 * @access private
	 */
	protected function register_autoloader() {
		require GROUNDHOGG_SPLIT_TESTING_PATH . 'includes/autoloader.php';
		Autoloader::run();
	}
}

Plugin::instance();