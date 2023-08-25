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
 * Class to construct HTML output for Status section.
 *
 * @since 1.0.0
 */
class Query_Monitor_Algolia_HTML_Status extends \QM_Output_Html {

	/**
	 * Query_Monitor_Algolia_HTML_Status constructor.
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
	 * Construct the output for the Query Monitor content section for WP Search with Algolia status.
	 *
	 * @since 1.0.0
	 */
	public function output() {
		$data = $this->collector->get_data();

		?>
		<div id="<?php echo esc_attr( $this->collector->id() ); ?>" class="qm qm-non-tabular qm-panel-show">
			<div class="qm-boxed">

				<?php
				ob_start();

				if ( ! empty( $data['current'] ) ) :
					$this->get_current_displayed_content( $data );
				endif;

				$this->get_indexable_search_status( $data );

				if ( ! empty( $data['indices'] ) ) :
					$this->get_algolia_indexes( $data );
				endif;

				$this->get_settings_status( $data );

				echo ob_get_clean();
				?>
			</div>
		</div>

		<?php
	}

	private function get_indexable_search_status( $data ) {
		?>
		<section>
			<h3><?php esc_html_e( 'Indexable search status', 'query-monitor-algolia' ); ?></h3>
			<table>
				<tbody>
				<?php
				if ( ! empty( $data['indexable-status'] ) && is_array( $data['indexable-status'] ) ) {
					foreach ( $data['indexable-status'] as $item ) {
						?>
						<tr>
							<td><?php echo esc_html( $item['title'] ); ?></td>
							<td><?php echo esc_html( $item['value'] ); ?></td>
						</tr>
						<?php
					}
				} else {
					?>
					<tr>
						<td colspan="2" style="text-align:center !important;">
							<em><?php esc_html_e( 'none', 'query-monitor-algolia' ); ?></em></td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</section>
	<?php
	}

	private function get_algolia_indexes( $data ) {
		?>
		<section>
			<h3><?php esc_html_e( 'Indexes', 'query-monitor-algolia' ); ?></h3>
			<?php $indices = json_decode( $data['indices'] ); ?>

			<table>
				<thead>
				<tr>
					<th><?php esc_html_e( 'Index name', 'query-monitor-algolia' ); ?></th>
					<th><?php esc_html_e( 'Entries found count', 'query-monitor-algolia' ); ?></th>
					<th><?php esc_html_e( 'Last updated', 'query-monitor-algolia' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php
				if ( ! empty( $indices ) && is_array( $indices ) ) {
					foreach ( $indices as $index ) {
						?>
						<tr>
							<td><?php echo esc_html( $index->name ); ?></td>
							<td><?php echo esc_html( $index->found_count ); ?></td>
							<td><?php echo esc_html( $index->last_update ); ?></td>
						</tr>
						<?php
					}
				}
				?>
				</tbody>
			</table>
		</section>
	<?php
	}

	private function get_current_displayed_content( $data ) {
		?>
		<section>
			<h3><?php esc_html_e( 'Current item', 'query-monitor-algolia' ); ?></h3>
			<table>
				<tbody>
				<?php
				if ( is_array( $data['current'] ) ) {
					foreach ( $data['current'] as $item ) {
						?>
						<tr>
							<td><?php echo esc_html( $item['title'] ); ?></td>
							<td><?php echo esc_html( $item['value'] ); ?></td>
						</tr>
						<?php
					}
				} else {
					?>
					<tr>
						<td colspan="2" style="text-align:center !important;">
							<em><?php esc_html_e( 'none', 'query-monitor-algolia' ); ?></em></td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</section>
	<?php
	}

	private function get_settings_status( $data ) {
		?>
		<section>
			<h3><?php esc_html_e( 'Settings Status', 'query-monitor-algolia' ); ?></h3>
			<table>
				<tbody>
				<?php
				if ( ! empty( $data['setting-status'] ) && is_array( $data['setting-status'] ) ) {
					foreach ( $data['setting-status'] as $item ) {
						?>
						<tr>
							<td><?php echo esc_html( $item['title'] ); ?></td>
							<td><?php echo esc_html( $item['value'] ); ?></td>
						</tr>
						<?php
					}
				} else {
					?>
					<tr>
						<td colspan="2" style="text-align:center !important;">
							<em><?php esc_html_e( 'none', 'query-monitor-algolia' ); ?></em></td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</section>
	<?php
	}

	/**
	 * Add our status section to the Query Monitor dropdown from admin bar.
	 *
	 * @since 1.0.0
	 *
	 * @param array $menu Array of menu items to render.
	 * @return array
	 */
	public function admin_menu( array $menu ) {
		$menu[] = $this->menu( [
			'title' => sprintf( esc_html__( '%s Status', 'query-monitor-algolia' ), 'WP Search with Algolia' ),
		] );

		return $menu;
	}
}

/**
 * Initiate an instance for HTML output for the status section.
 *
 * @since 1.0.0
 *
 * @param array          $output     Array of HTML output instances to render.
 * @param QM_Collectors $collectors Collector object.
 * @return array
 */
function register_qmalgolia_output_html_status( array $output, QM_Collectors $collectors ) {
	$collector = QM_Collectors::get( 'qmalgolia-status' );
	if ( $collector ) {
		$output['qmalgolia-status'] = new Query_Monitor_Algolia_HTML_Status( $collector );
	}

	return $output;
}
