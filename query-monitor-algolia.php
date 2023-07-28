<?php
/**
 * Query Monitor Algolia loader.
 *
 * @package qmalgolia
 * @since   1.0.0
 */

/*
 * Plugin Name: Query Monitor Algolia
 * Description: Add WP Search with Algolia to Query Monitor
 * Version: 1.0.0
 * Plugin URI: https://michaelbox.net
 * Author: Michael Beckwith
 * Contributors: tw2113
 * Requires at least: 6.2.2
 * Tested up to: 6.3
 * Requires PHP: 7.4
 * Stable tag: 1.0.0
 * Text Domain: query-monitor-algolia
 * License: MIT
 */

namespace tw2113\qmalgolia;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (
	( ! defined( 'QM_VERSION' ) ) ||
	( defined( 'QM_DISABLED' ) && QM_DISABLED )
) {
	return;
}

/**
 * Construct our plugin.
 *
 * @since 1.0.0
 */
class Query_Monitor_Algolia {

	/**
	 * Execute our hooks.
	 *
	 * @since 1.0.0
	 */
	public function do_hooks() {
		add_action( 'plugins_loaded', [ $this, 'includes' ], 0 );
		add_filter( 'qm/outputter/html', [ $this, 'include_outputters' ], 0 );
	}

	/**
	 * Wire up our collectiors and other includes.
	 *
	 * @since 1.0.0
	 */
	public function includes() {
		if ( class_exists( 'QM_Collector' ) ) {
			require 'collectors/constants.php';
			require 'collectors/status.php';
		}
		add_filter( 'qm/collectors', __NAMESPACE__ . '\register_qmalgolia_collectors_constants', 999, 2 );
		add_filter( 'qm/collectors', __NAMESPACE__ . '\register_qmalgolia_collectors_status', 999, 2 );

		require 'includes/conditionals.php';

		/**
		 * Fires at the end of our primary class includes method.
		 *
		 * @since 1.0.0
		 */
		do_action( 'qmalgolia_includes' );
	}

	/**
	 * Wire up our outputter data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $output Array of output for Query Monitor.
	 * @return array
	 */
	public function include_outputters( $output ) {
		if ( class_exists( 'QM_Output_Html' ) ) {
			require 'outputters/constants.php';
			require 'outputters/status.php';
		}
		add_filter( 'qm/outputter/html', __NAMESPACE__ . '\register_qmalgolia_output_html_constants', 999, 2 );
		add_filter( 'qm/outputter/html', __NAMESPACE__ . '\register_qmalgolia_output_html_status', 999, 2 );

		/**
		 * Fires at the end of our primary class include_outputters method.
		 *
		 * @since 1.0.0
		 *
		 * @param array $output Array of output for Query Monitor.
		 */
		do_action( 'qmalgolia_include_outputters', $output );

		return $output;
	}

}
$qmalgolia = new Query_Monitor_Algolia();
$qmalgolia->do_hooks();
