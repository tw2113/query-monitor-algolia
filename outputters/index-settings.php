<?php
/**
 * Query Monitor Algolia Status HTML class.
 *
 * @package qmalgolia
 * @since   1.0.0
 */

namespace tw2113\qmalgolia;

use QM_Collector;
use QM_Collectors;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to construct HTML output for Index Settings section.
 *
 * @since 1.0.0
 */
class Query_Monitor_Algolia_HTML_Index_Settings extends \QM_Output_Html {

	/**
	 * Query_Monitor_Algolia_HTML_Index_Settings constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param QM_Collector $collector
	 */
	public function __construct( QM_Collector $collector ) {
		parent::__construct( $collector );

		add_filter( 'qm/output/menus', [ $this, 'admin_menu' ], 101 );
	}

	/**
	 * Construct the output for the Query Monitor content section for WP Search with Algolia index settings.
	 *
	 * @since 1.0.0
	 */
	public function output() {
		$data = $this->collector->get_data();

		?>
		<div id="<?php echo esc_attr( $this->collector->id() ); ?>" class="qm qm-non-tabular qm-panel-show">
			<div class="qm-boxed">
				<h2 class="qm-screen-reader-text" id="qm-algolia-index-settings-caption">Index Settings</h2>
				<?php
				ob_start();

				foreach( $data['index-settings'] as $index_name => $index ) {
					$count = 0;
					$class = ( $count & 1 ) ? 'qm-odd' : '';
					?>
					<section>
						<h3><?php echo esc_html( $index_name ); ?></h3>
						<table>
							<tbody>
								<?php foreach( $index as $key => $value ) {
									?>
									<tr class="<?php echo esc_attr( $class ) ?>">
										<th scope="row"><?php echo esc_html( $key ); ?></th>
										<td><?php
											if ( is_array( $value ) ) {
												echo '<pre>'. implode( ",\n", $value ) . '</pre>';
											} else {
												echo esc_html( $value );
											}
											?>
										</td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</section>
					<?php
					++$count;
				}

				echo ob_get_clean();
				?>
			</div>
		</div>

		<?php
	}

	/**
	 * Add our status section to the Query Monitor dropdown from admin bar.
	 *
	 * @since 1.0.0
	 *
	 * @param array $menu Array of menu items to render.
	 *
	 * @return array
	 */
	public function admin_menu( array $menu ) {
		$menu[] = $this->menu( [
			'title' => esc_html__( 'WP Search with Algolia Index Settings', 'query-monitor-algolia' )
		] );

		return $menu;
	}
}

/**
 * Initiate an instance for HTML output for the index settings section.
 *
 * @since 1.0.0
 *
 * @param array         $output     Array of HTML output instances to render.
 * @param QM_Collectors $collectors Collector object.
 *
 * @return array
 */
function register_qmalgolia_output_html_index_settings( array $output, QM_Collectors $collectors ) {
	$collector = QM_Collectors::get( 'qmalgolia-index-settings' );
	if ( $collector ) {
		$output['qmalgolia-index-settings'] = new Query_Monitor_Algolia_HTML_Index_Settings( $collector );
	}

	return $output;
}
