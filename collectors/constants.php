<?php
/**
 * Query Monitor Algolia Constants Collector class.
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
 * Class to collect data for the WP Search with Algolia Constants section.
 *
 * @since 1.0.0
 */
class Query_Monitor_Algolia_Collector_Constants extends QM_Collector {

	/**
	 * ID for our collector instance.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $id = 'qmalgolia-constants';

	/**
	 * Data to be used in our output.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $data;

	/**
	 * Query_Monitor_Algolia_Collector_Constants constructor.
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
		return 'WP Search with Algolia';
	}

	/**
	 * Collect data to make available for the HTML output.
	 *
	 * @since 1.0.0
	 */
	public function process() {
		$this->data['constants'] = [];

		/**
		 * Filters the constants to check for with Query Monitor Algolia.
		 *
		 * @since 1.0.0
		 *
		 * @param array $value Array of constants to check for.
		 */
		$constants = apply_filters( 'qmalgolia_constants', [
			'ALGOLIA_HIDE_HELP_NOTICES',
			'ALGOLIA_SPLIT_POSTS',
			'ALGOLIA_CONTENT_MAX_SIZE',
			'ALGOLIA_INDEX_NAME_PREFIX',
		] );

		foreach( $constants as $constant ) {
			if ( defined( $constant ) ) {
				$this->data['constants'][ $constant ] = (
					is_bool( constant( $constant ) )
				)
					? self::format_bool_constant( $constant )
					: constant( $constant );
			}
		}
	}
}

/**
 * Initiate an instance for Collector class for the constants section.
 *
 * @since 1.0.0
 *
 * @param array        $collectors Array of current instantiated collectors.
 * @param QueryMonitor $qm         Query Monitor instance.
 * @return array
 */
function register_qmalgolia_collectors_constants( array $collectors, QueryMonitor $qm ) {
	$collectors['qmalgolia-constants'] = new Query_Monitor_Algolia_Collector_Constants;
	return $collectors;
}
