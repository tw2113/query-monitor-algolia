<?php
/**
 * Query Monitor Algolia Constants HTML class.
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
 * Class to construct HTML output for Constants section.
 *
 * @since 1.0.0
 */
class Query_Monitor_Algolia_HTML_Constants extends \QM_Output_Html {

	/**
	 * Query_Monitor_Algolia_HTML_Constants constructor.
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
	 * Construct the output for the Query Monitor content section for Algolia constants.
	 *
	 * @since 1.0.0
	 */
	public function output() {
		$data = $this->collector->get_data();

		?>
		<div id="<?php echo esc_attr( $this->collector->id() ); ?>" class="qm qm-third">
			<table>
				<thead>
				<tr class="qm-items-shown">
					<th><?php printf( esc_html__( '%s Constant Name', 'query-monitor-algolia' ), $this->collector->name() ); ?></th>
					<th><?php printf( esc_html__( '%s Constant Value', 'query-monitor-algolia' ), $this->collector->name() ); ?></th>
				</tr>
				</thead>
				<tfoot>
				<tr class="qm-items-shown qm-hide">
					<td><?php printf( esc_html__( '%s Constant Name', 'query-monitor-algolia' ), $this->collector->name() ); ?></td>
					<td><?php printf( esc_html__( '%s Constant Value', 'query-monitor-algolia' ), $this->collector->name() ); ?></td>
				</tr>
				</tfoot>
				<tbody>
				<?php
				if ( ! empty( $data['constants'] ) && is_array( $data['constants'] ) ) {
					foreach ( $data['constants'] as $key => $value ) {
						?>
						<tr>
							<td><?php echo esc_html( $key ); ?></td>
							<td><?php echo esc_html( $value ); ?></td>
						</tr>
						<?php
					}
				} else {
					?>
					<tr>
						<td colspan="2" style="text-align:center !important;"><em>none</em></td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</div>

		<?php
	}

	/**
	 * Add our constants section to the Query Monitor dropdown from admin bar.
	 *
	 * @since 1.0.0
	 *
	 * @param array $menu Array of menu items to render.
	 * @return array
	 */
	public function admin_menu( array $menu ) {
		$menu[] = $this->menu( [
			'title' => sprintf( esc_html__( '%s Constants', 'query-monitor-algolia' ), 'WP Search with Algolia' ),
		] );

		return $menu;
	}
}

/**
 * Initiate an instance for HTML output for the constants section.
 *
 * @since 1.0.0
 *
 * @param array          $output     Array of HTML output instances to render.
 * @param QM_Collectors $collectors Collector object.
 * @return array
 */
function register_qmalgolia_output_html_constants( array $output, QM_Collectors $collectors ) {
	$collector = QM_Collectors::get( 'qmalgolia-constants' );
	if ( $collector ) {
		$output['qmalgolia-constants'] = new Query_Monitor_Algolia_HTML_Constants( $collector );
	}

	return $output;
}
