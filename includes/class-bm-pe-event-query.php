<?php
 /**
  * Class for parameter-based event querying.
  *
  * @package  PressEvents/Classes/Events
  * @author Burn Media Ltd
  * @since 1.0.0
  */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BM_PE_Event_Query Class.
 */
class BM_PE_Event_Query extends BM_PE_Object_Query {

	/**
	 * Valid query vars for events.
	 *
	 * @return array
	 */
	protected function get_default_query_vars() {
		return array_merge(
			parent::get_default_query_vars(),
			array(
				'status'        => array( 'draft', 'pending', 'private', 'publish' ),
				'limit'         => get_option( 'posts_per_page' ),
				'post_type'     => 'pe_event',
				'include'       => array(),
				'date_created'  => '',
				'date_modified' => '',
				'featured'      => '',
				'visibility'    => '',
				'category'      => array(),
				'tag'           => array(),
		        'timezone'      => '',
		        'event_start'   => '',
		        'event_end'     => '',
				'meta_key' => '_event_starts',
	            'orderby' => 'meta_value_num',
	            'order' => 'ASC'
			)
		);
	}

	/**
	 * Get events matching the current query vars.
	 *
	 * @return array|object of BM_PE_Event objects
	 */
	public function get_events() {
		$args = apply_filters( 'press_events_event_object_query_args', $this->get_query_vars() );

		$results = new WP_Query( $args );

		if ( $results->posts ) {
            $results->posts = array_map( 'bm_pe_get_event', $results->posts );
        }

		return apply_filters( 'press_events_event_object_query', $results->posts, $args );
	}

}
