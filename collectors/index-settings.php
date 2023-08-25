<?php
/**
 * Query Monitor Algolia Status Collector class.
 *
 * @package qmalgolia
 * @since   1.0.0
 */

namespace tw2113\qmalgolia;

use Algolia_Plugin;
use QM_Collector;
use QueryMonitor;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to collect data for the Algolia Index Settings section.
 *
 * @since 1.0.0
 */
class Query_Monitor_Algolia_Collector_Index_Settings extends QM_Collector {

	/**
	 * ID for our collector instance.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $id = 'qmalgolia-index-settings';

	/**
	 * Array of data to display for this section.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $data;

	/**
	 * Algolia_Plugin instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Algolia_Plugin
	 */
	private $wpswa;

	/**
	 * Query_Monitor_Algolia_Collector_Index_Settings constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->data  = [];
		$this->wpswa = \Algolia_Plugin_Factory::create();
	}

	/**
	 * Sets a usable name for our collector.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function name() {
		return 'WPSwA';
	}

	/**
	 * Collect data to make available for the HTML output.
	 *
	 * @since 1.0.0
	 */
	public function process() {
		$this->data['index-settings'] = [];

		$client  = $this->wpswa->get_api()->get_client();
		$indices = $client->listIndices();
		$prefix  = $this->wpswa->get_settings()->get_index_name_prefix();

		foreach ( $indices['items'] as $index ) {
			if ( false === strpos( $index['name'], $prefix ) ) {
				continue;
			}
			$settings = get_transient( 'qmalgolia_indices_settings_cache' );

			if ( false !== $settings ) {
				if ( ! defined( 'SCRIPT_DEBUG' ) || ( defined( 'SCRIPT_DEBUG' ) && false === SCRIPT_DEBUG ) ) {
					$this->data['index-settings'][ $index['name'] ] = $settings;

					return;
				}
			}

			$settings = $client->initIndex( $index['name'] )->getSettings();
			set_transient( 'qmalgolia_indices_settings_cache', $settings, 30 * MINUTE_IN_SECONDS );
			$this->data['index-settings'][ $index['name'] ] = $settings;
		}
	}
}

/**
 * Initiate an instance for Collector class for the index settings section.
 *
 * @since 1.0.0
 *
 * @param array        $collectors Array of current instantiated collectors.
 * @param QueryMonitor $qm         Query Monitor instance.
 *
 * @return array
 */
function register_qmalgolia_collectors_index_settings( array $collectors, QueryMonitor $qm ) {
	$collectors['qmalgolia-index-settings'] = new Query_Monitor_Algolia_Collector_Index_Settings;

	return $collectors;
}
