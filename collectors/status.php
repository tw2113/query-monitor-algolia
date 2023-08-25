<?php
/**
 * Query Monitor Algolia Status Collector class.
 *
 * @package qmalgolia
 * @since   1.0.0
 */

namespace tw2113\qmalgolia;

use Algolia_Plugin;
use Exception;
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

	/**
	 * Template path for our loaded autocomplete or instantsearch file.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $found_template_path;

	/**
	 * Data container.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $data;

	/**
	 * Algolia_Plugin instance.
	 *
	 * @since 1.0.0
	 * @var Algolia_Plugin
	 */
	private $wpswa;

	/**
	 * Query_Monitor_Algolia_Collector_Status constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->data = [];
		$this->wpswa = \Algolia_Plugin_Factory::create();
	}

	/**
	 * Set_up method.
	 *
	 * @since 1.0.0
	 */
	public function set_up() {
		parent::set_up();

		add_filter( 'template_include', [ $this, 'get_algolia_template_path' ], PHP_INT_MAX );
	}

	/**
	 * Get template path used.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Template path.
	 * @return mixed
	 */
	function get_algolia_template_path( $template ) {
		if ( ! in_array( basename( $template ), [ 'autocomplete.php', 'instantsearch.php' ] ) ) {
			return $template;
		}

		$template_path = $template;

		$this->found_template_path = str_replace( WP_CONTENT_DIR, '', $template_path );

		return $template;
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
		$this->data['template']         = [];
		$this->data['indexable-status'] = [];
		$this->data['indices']          = [];
		$this->data['setting-status']   = [];

		$client = $this->wpswa->get_api()->get_client();

		$searchable_types = get_post_types( [
			'exclude_from_search' => false
		] );

		$object = get_queried_object();
		if ( ! empty( $object ) ) {
			if ( ! is_admin() ) {
				switch ( get_class( $object ) ) {
					case 'WP_Post' :
						$this->data['current'][] = [
							'title' => esc_html__( 'Is a:', 'query-monitor-algolia' ),
							'value' => $object->post_type,
						];
						$this->data['current'][] = [
							'title' => esc_html__( 'Is search indexable?', 'query-monitor-algolia' ),
							'value' => ( in_array( $object->post_type, $searchable_types ) ) ? 'true' : 'false',
						];
						$the_index = $this->wpswa->get_index( 'searchable_posts' );
						$remote_index = $client->initIndex( $the_index->get_name() );
						// All objects get the suffix regardless of size. Safe to use.
						$objID = $object->ID . '-0';
						try {
							$found = $remote_index->getObject( $objID );
							if ( ! empty( $found ) ) {
								$this->data['current'][] = [
									'title' => esc_html__( 'Is currently indexed?', 'query-monitor-algolia' ),
									'value' => 'true',
								];
							} else {
								$this->data['current'][] = [
									'title' => esc_html__( 'Is currently indexed?', 'query-monitor-algolia' ),
									'value' => 'false',
								];
							}
						} catch ( Exception $e ) {
							$this->data['current'][] = [
								'title' => esc_html__( 'Is currently indexed?', 'query-monitor-algolia' ),
								'value' => 'false - ' . $e->getMessage(),
							];
						}

						$h ='';
						break;
					case 'WP_Term' :
						$this->data['current'][] = [
							'title' => esc_html__( 'Is a:', 'query-monitor-algolia' ),
							'value' => 'Term archive'
						];
						break;
					case 'WP_User':
						$this->data['current'][] = [
							'title' => esc_html__( 'Is a:', 'query-monitor-algolia' ),
							'value' => 'User archive'
						];
						break;
				}

			}
		}

		if ( ! empty( $this->found_template_path ) ) {
			$this->data['template'][] = [
				'title' => esc_html__( 'Found path', 'query-monitor-algolia' ),
				'value' => $this->found_template_path
			];
		}

		$this->data['indexable-status'][] = [
			'title' => esc_html__( 'Searchable post index enabled?', 'query-monitor-algolia' ),
			'value' => ( $this->wpswa->get_index( 'searchable_posts' )->is_enabled() ) ? 'true' : 'false',
		];

		$this->data['indexable-status'][] = [
			'title' => esc_html__( 'Indexable Post Types:', 'query-monitor-algolia' ),
			'value' => implode( ', ', $searchable_types ),
		];

		$this->data['indices'] = $this->flatten_indices();

		$this->data['setting-status'][] = [
			'title' => esc_html__( 'API is reachable?', 'query-monitor-algolia' ),
			'value' => ( $this->wpswa->get_settings()->get_api_is_reachable() ) ? 'true' : 'false'
		];
		$this->data['setting-status'][] = [
			'title' => esc_html__( 'Autocomplete enabled?', 'query-monitor-algolia' ),
			'value' => $this->wpswa->get_settings()->get_autocomplete_enabled()
		];
		$this->data['setting-status'][] = [
			'title' => esc_html__( 'Autocomplete config(s):', 'query-monitor-algolia' ),
			'value' => $this->flatten_autocomplete_config()
		];
		$this->data['setting-status'][] = [
			'title' =>  esc_html__( 'Search style:', 'query-monitor-algolia' ),
			'value' => ucfirst( $this->wpswa->get_settings()->get_override_native_search() ),
		];
		$this->data['settings-status'][] = [
			'title' => esc_html__( 'Prefix:', 'query-monitor-algolia' ),
			'value' => $this->wpswa->get_settings()->get_index_name_prefix(),
		];
		$this->data['setting-status'][] = [
			'title' =>  esc_html__( 'Powered by enabed?', 'query-monitor-algolia' ),
			'value' => ( $this->wpswa->get_settings()->is_powered_by_enabled() ) ? 'true' : 'false'
		];
	}

	private function flatten_autocomplete_config() {
		$configs = $this->wpswa->get_settings()->get_autocomplete_config();
		return json_encode( $configs );
	}

	private function flatten_indices() {
		$result = get_transient( 'qmalgolia_indices_cache' );

		if ( false !== $result ) {
			if ( ! defined( 'SCRIPT_DEBUG' ) || ( defined( 'SCRIPT_DEBUG' ) && false === SCRIPT_DEBUG ) ) {
				return json_encode( $result );
			}
		}

		$indices = $this->wpswa->get_api()->get_client()->listIndices();
		$prefix = $this->wpswa->get_settings()->get_index_name_prefix();
		$result = [];
		foreach( $indices['items'] as $index ) {
			if ( false === strpos( $index['name'], $prefix ) ) {
				continue;
			}
			$result[] = [
				'name'        => $index['name'],
				'found_count' => $index['entries'],
				'last_update' => date( 'Y-m-d h:i:s', strtotime( $index['updatedAt'] ) )
			];
		}
		set_transient( 'qmalgolia_indices_cache', $result, 30 * MINUTE_IN_SECONDS );
		return json_encode( $result );
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
