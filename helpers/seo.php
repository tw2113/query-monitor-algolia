<?php
/**
 * Query Monitor Algolia SEO Integration.
 *
 * @package qmalgolia
 * @since   1.1.0
 */

namespace tw2113\qmalgolia;

/**
 * SEO Integration Detection.
 *
 * Most specifically good for the SEO integrations with WP Search with Algolia Pro.
 *
 * @since 1.1.0
 */
class Query_Monitor_Algolia_Pro_SEO_Integration {

	/**
	 * @var \WP_Post $object
	 */
	private \WP_Post $object;

	/**
	 * Constructor
	 *
	 * @param \WP_Post $object Post object.
	 */
	public function __construct( \WP_Post $object ) {
		$this->object = $object;
	}

	/**
	 * Main indexable status method.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function indexable_status() {
		if ( $this->yoast_available() ) {
			return $this->yoast();
		}

		if ( $this->aioseo_available() ) {
			return $this->aioseo();
		}

		if ( $this->rankmath_available() ) {
			return $this->rankmath();
		}

		if ( $this->seopress_available() ) {
			return $this->seopress();
		}

		// Always available, run this last.
		return $this->wpswa_pro();
	}

	/**
	 * Return the result of our general noindex metabox check.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	private function wpswa_pro() {
		$result = esc_html__( 'Indexable - WPSWA Pro', 'query-monitor-algolia' );
		$indexable = get_post_meta( $this->object->ID, 'wpswa_pro_should_not_index', true );

		if ( 'yes' === $indexable ) {
			$result = esc_html__( 'Not indexable - WPSWA Pro', 'query-monitor-algolia' );
		}

		return $result;
	}

	/**
	 * Whether or not Yoast SEO is available.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	private function yoast_available() {
		return defined( 'WPSEO_VERSION' );
	}

	/**
	 * Whether or not the post is indexable based on Yoast SEO settings.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	private function yoast() {
		$result = esc_html__( 'Indexable - Yoast', 'query-monitor-algolia' );
		$maybe_should_index = get_post_meta(
			$this->object->ID,
			'_yoast_wpseo_meta-robots-noindex',
			true
		);

		// 1 is saved to meta when "No, don't show" is chosen.
		if ( ! empty( $maybe_should_index ) && '1' === $maybe_should_index ) {
			$result = esc_html__( 'Not indexable - Yoast', 'query-monitor-algolia' );
		}

		// 2 is saved to meta when "Yes, show" is chosen.
		if ( ! empty( $maybe_should_index ) && '2' === $maybe_should_index ) {
			$result = esc_html__( 'Indexable - Yoast', 'query-monitor-algolia' );
		}

		// We will reach here if `_yoast_wpseo_meta-robots-noindex` meta doesn't exist.
		if ( true === \WPSEO_Options::get( 'noindex-' . $this->object->post_type, false ) ) {
			$result = esc_html__( 'Not indexable - Yoast', 'query-monitor-algolia' );
		}

		return $result;
	}

	/**
	 * Whether or not All In One SEO is available.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	private function aioseo_available() {
		return function_exists( '\aioseo' );
	}

	/**
	 * Whether or not the post is indexable based on All In One SEO Settings.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	private function aioseo() {
		$result = esc_html__( 'Indexable - AIOSEO', 'query-monitor-algolia' );

		if ( true === aioseo()->meta->metaData->getMetaData( $this->object )->robots_noindex ) {
			$result = esc_html__( 'Not indexable - AIOSEO', 'query-monitor-algolia' );
		}

		// Global option is true for post type.
		if ( true === aioseo()->dynamicOptions->searchAppearance->postTypes->{$this->object->post_type}->advanced->robotsMeta->noindex ) {
			$result = esc_html__( 'Not indexable - AIOSEO', 'query-monitor-algolia' );
		}

		return $result;
	}

	/**
	 * Whether or not Rank Math SEO is available.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	private function rankmath_available() {
		return function_exists( '\rank_math' );
	}

	/**
	 * Whether or not the post is indexable based on Rank Math SEO settings.
	 * @since 1.1.0
	 *
	 * @return string
	 */
	private function rankmath() {
		$result = esc_html__( 'Not indexable - RankMath', 'query-monitor-algolia' );
		$data = get_post_meta( $this->object->ID, 'rank_math_robots', true );
		if ( ! empty( $data ) && is_array( $data ) ) {
			if ( ! in_array( 'noindex', $data, true ) ) {
				$result = esc_html__( 'Indexable - RankMath', 'query-monitor-algolia' );
			}
		}

		$settings = rank_math()->settings->get( "titles.pt_{$this->object->post_type}_robots", [] );

		if ( ! in_array( 'noindex', $settings, true ) ) {
			$result = esc_html__( 'Indexable - RankMath', 'query-monitor-algolia' );
		}

		return $result;
	}

	/**
	 * Whether or not SEOPress is available.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	private function seopress_available() {
		return defined( 'SEOPRESS_VERSION' );
	}

	/**
	 * Whether or not the post is indexable based on SEOPress settings.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	private function seopress() {
		$result = esc_html__( 'Indexable - SEOPress', 'query-monitor-algolia' );

		$maybe_should_index = get_post_meta(
			$this->object->ID,
			'_seopress_robots_index',
			true
		);

		if ( ! empty( $maybe_should_index ) && 'yes' === $maybe_should_index ) {
			$result = esc_html__( 'Not indexable - SEOPress', 'query-monitor-algolia' );
		}

		// Returns null or '1' so casting should convert to appropriate boolean value.
		if ( true === (bool) seopress_get_service( 'TitleOption' )->getSingleCptNoIndex( $this->object ) ) {
			$result = esc_html__( 'Not indexable - SEOPress', 'query-monitor-algolia' );
		}

		return $result;
	}
}
