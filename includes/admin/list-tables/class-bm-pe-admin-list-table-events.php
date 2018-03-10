<?php
/**
 * List tables: pe_events.
 *
 * @package PressEvents/Classes/Admin/ListTables
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BM_PE_Admin_List_Table', false ) ) {
	include_once( 'abstract-class-bm-pe-admin-list-table.php' );
}

if ( ! class_exists( 'PE_Admin_List_Table_Events', false ) ) :

/**
 * BM_PE_Admin_List_Table_Events Class.
 */
class BM_PE_Admin_List_Table_Events extends BM_PE_Admin_List_Table {

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $list_table_type = 'pe_event';

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		add_filter( 'disable_months_dropdown', '__return_true' );
	}

	/**
	 * Pre-fetch any data for the row each column has access to it.
	 *
	 * @param int $post_id Post ID being shown.
	 */
	protected function prepare_row_data( $post_id ) {
		if ( empty( $this->object ) || $this->object->get_id() !== $post_id ) {
			$this->object = bm_pe_get_event( $post_id );
		}
	}

	/**
	 * Render blank state.
	 */
	protected function render_blank_state() {
		echo '<div class="press-events-no-posts">';
		echo '<div class="dashicons-before dashicons-calendar-alt"><br></div>';
		echo '<h2>'. __( 'Ready to create your first event?', 'press-events' ) .'</h2>';
		echo '<a class="button-primary button" href="'. esc_url( admin_url( 'post-new.php?post_type=pe_event&tutorial=true' ) ) .'">'. __( 'Create your first event', 'press-events' ) .'</a>';
		echo '</div>';
	}

	/**
	 * Define which columns are sortable.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public function define_sortable_columns( $columns ) {
		$custom = array(
			'event_time' => 'event_time',
		);

		return wp_parse_args( $custom, $columns );
	}

	/**
	 * Define which columns to show on this screen.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public function define_columns( $columns ) {
		if ( empty( $columns ) && ! is_array( $columns ) ) {
			$columns = array();
		}

		unset( $columns['comments'], $columns['date'] );

		$show_columns['event_time'] = __( 'Date & Time', 'press-events' );
		$show_columns['pe_event_category'] = __( 'Categories', 'press-events' );
		$show_columns['pe_event_tag'] = __( 'Tags', 'press-events' );
		$show_columns['featured'] = '<div class="featured-event press-tip" data-tip="' . esc_attr__( 'Featured', 'press-events' ) . '"><span class="dashicons dashicons-star-filled"></span></div>';
		$show_columns['date'] = __( 'Date', 'press-events' );

		return array_merge( $columns, $show_columns );
	}

	/**
	 * Render columm: event_start.
	 */
	protected function render_event_time_column() {
		// Event starts
		$start_day = bm_pe_format_datetime( $this->object->get_event_start(), bm_pe_date_format() );
		$start_time = bm_pe_format_datetime( $this->object->get_event_start(), bm_pe_time_format() );

		// Event ends
		$end_day = bm_pe_format_datetime( $this->object->get_event_end(), bm_pe_date_format() );
		$end_time = bm_pe_format_datetime( $this->object->get_event_end(), bm_pe_time_format() );

		// Timezone
		if ( bm_pe_timezone_offset() == bm_pe_event_timezone_offset( $this->object->get_id() ) ) {

			$tzstring = '';

		} else {

			// get timezone meta
			$current_offset = get_post_meta( $this->object->get_id(), '_event_gmt_offset', true );
			$tzstring = get_post_meta( $this->object->get_id(), '_event_timezone_string', true );

			if ( empty($tzstring) ) { // Create a UTC+- zone if no timezone string exists
				if ( 0 == $current_offset ) {
					$tzstring = 'UTC+0';
				} elseif ( $current_offset < 0 ) {
					$tzstring = 'UTC' . $current_offset;
				} else {
					$tzstring = 'UTC+' . $current_offset;
				}
			}

		}

		if ( $this->object->is_one_day_event() ) {
			echo $start_day;

			if ( ! $this->object->is_all_day_event() ) {
				echo '<div class="event-time">'. $start_time .' &ndash; '. $end_time .'</div>';
			}
		} else {
			if ( $this->object->is_all_day_event() ) {
				echo $start_day .' &ndash; '. $end_day;
			} else {
				echo '<div class="event-starts">'. $start_day .', '. $start_time .'</div>';
				echo '<div class="event-ends">'. $end_day .', '. $end_time .'</div>';
			}
		}

		if ( $tzstring ) {
			$timezone = $current_offset == '' ? $tzstring : bm_pe_offset_value_to_name( $current_offset );
			echo '<small><b>'. __( 'Timezone', 'press-events' ) .':</b> '. $timezone .'</small>';
		}
	}

	/**
	 * Render columm: pe_event_category.
	 */
	protected function render_pe_event_category_column() {
		if ( ! $terms = get_the_terms( $this->object->get_id(), 'pe_event_category' ) ) {
			echo '<span class="na">&ndash;</span>';
		} else {
			$termlist = array();
			foreach ( $terms as $term ) {
				$termlist[] = '<a href="' . esc_url( admin_url( 'edit.php?pe_event_category=' . $term->slug . '&post_type=pe_event' ) ) . '">'. esc_html( $term->name ) .'</a>';
			}
			echo apply_filters( 'press_events_admin_event_term_list', implode( ', ', $termlist ), 'pe_event_category', $this->object->get_id(), $termlist, $terms );
		}
	}

	/**
	 * Render columm: pe_event_tag.
	 */
	protected function render_pe_event_tag_column() {
		if ( ! $terms = get_the_terms( $this->object->get_id(), 'pe_event_tag' ) ) {
			echo '<span class="na">&ndash;</span>';
		} else {
			$termlist = array();
			foreach ( $terms as $term ) {
				$termlist[] = '<a href="' . esc_url( admin_url( 'edit.php?pe_event_tag=' . $term->slug . '&post_type=pe_event' ) ) . ' ">' . esc_html( $term->name ) . '</a>';
			}
			echo apply_filters( 'press_events_admin_event_term_list', implode( ', ', $termlist ), 'pe_event_tag', $this->object->get_id(), $termlist, $terms );
		}
	}

	/**
	 * Render columm: featured.
	 */
	protected function render_featured_column() {
		$url = wp_nonce_url( admin_url( 'admin-ajax.php?action=press_events_feature_event&event_id=' . $this->object->get_id() ), 'press-events-feature-event' );
		echo '<a href="' . esc_url( $url ) . '" class="toggle-featured">';

		if ( $this->object->is_featured() ) {
			echo '<span class="pe-featured press-tip" data-tip="' . esc_attr__( 'Yes', 'press-events' ) . '"><span class="dashicons dashicons-star-filled"></span></span>';
		} else {
			echo '<span class="pe-featured not-featured press-tip" data-tip="' . esc_attr__( 'No', 'press-events' ) . '"><span class="dashicons dashicons-star-empty"></span></span>';
		}

		echo '</a>';
	}

	/**
	 * Handle any custom filters.
	 *
	 * @param array $query_vars Query vars.
	 * @return array
	 */
	protected function query_filters( $query_vars ) {
		if ( isset( $query_vars['orderby'] ) ) {
			if ( 'event_time' === $query_vars['orderby'] ) {
				$query_vars = array_merge( $query_vars, array(
					'meta_key'  => '_event_starts',
					'orderby'   => 'meta_value',
				) );
			}
		}

		return $query_vars;
	}

}

endif;
