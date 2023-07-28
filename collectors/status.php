<?php
/**
 * Query Monitor Algolia Status Collector class.
 *
 * @package qmalgolia
 * @since   1.0.0
 */

namespace tw2113\qmalgolia;

use QM_Collector;
use QueryMonitor;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to collect data for the Algolia Status section.
 *
 * @since 1.0.0
 */
class Query_Monitor_Algolia_Collector_Status extends QM_Collector {

	/**
	 * ID for our collector instance.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $id = 'qmalgolia-status';

	/**
	 * ID for the WP Search with Algolia post.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private $post_id = 0;

	public $data;

	/**
	 * Query_Monitor_Algolia_Collector_Status constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->data = [];
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
		$this->data['current']          = [];
		$this->data['indexable-status'] = [];
		$this->data['setting-status']   = [];

		$wpswa = \Algolia_Plugin_Factory::create();

		$searchable_types = get_post_types( [
			'exclude_from_search' => false
		] );

		$object = get_queried_object();
		if ( ! empty( $object ) ) {
			if ( ! is_admin() ) {
				switch ( get_class( $object ) ) {
					case 'WP_Post' :
						$this->data['current'][] = [
							'title' => 'Is a:',
							'value' => $object->post_type,
						];
						$this->data['current'][] = [
							'title' => 'Is indexable?',
							'value' => ( in_array( $object->post_type, $searchable_types ) ) ? 'true' : 'false',
						];
						break;
					case 'WP_Term' :
						$this->data['current'][] = [
							'title' => 'Is a:',
							'value' => 'Term archive'
						];
						break;
					case 'WP_User':
						$this->data['current'][] = [
							'title' => 'Is a:',
							'value' => 'User archive'
						];
						break;
				}

			}
		}

		$this->data['indexable-status'][] = [
			'title' => 'Searchable post index enabled?',
			'value' => ( $wpswa->get_index( 'searchable_posts' )->is_enabled() ) ? 'true' : 'false',
		];

		$this->data['indexable-status'][] = [
			'title' => 'Indexable Post Types:',
			'value' => implode( ', ', $searchable_types ),
		];

		$this->data['setting-status'][] = [
			'title' => 'API is reachable?',
			'value' => ( $wpswa->get_settings()->get_api_is_reachable() ) ? 'true' : 'false'
		];
		$this->data['setting-status'][] = [
			'title' => 'Autocomplete enabled?',
			'value' => $wpswa->get_settings()->get_autocomplete_enabled()
		];
		$this->data['setting-status'][] = [
			'title' => 'Autocomplete config(s):',
			'value' => $this->flatten_autocomplete_config( $wpswa )
		];
		$this->data['setting-status'][] = [
			'title' => 'Search style:',
			'value' => ucfirst( $wpswa->get_settings()->get_override_native_search() )
		];
		$this->data['setting-status'][] = [
			'title' => 'Powered by enabed?',
			'value' => ( $wpswa->get_settings()->is_powered_by_enabled() ) ? 'true' : 'false'
		];
	}

	private function flatten_autocomplete_config( $wpswa ) {
		$configs = $wpswa->get_settings()->get_autocomplete_config();
		$data = json_encode( $configs );
		return $data;
	}
}

/**
 * Initiate an instance for Collector class for the status section.
 *
 * @since 1.0.0
 *
 * @param array         $collectors Array of current instantiated collectors.
 * @param QueryMonitor $qm         Query Monitor instance.
 * @return array
 */
function register_qmalgolia_collectors_status( array $collectors, QueryMonitor $qm ) {
	$collectors['qmalgolia-status'] = new Query_Monitor_Algolia_Collector_Status;

	return $collectors;
}
