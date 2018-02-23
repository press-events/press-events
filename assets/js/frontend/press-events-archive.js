/* global pe_archive_event_params */
(function( $ ) {
	'use strict';

	if ( typeof pe_archive_event_params === 'undefined' ) {
		return false;
	}

	/**
	 * pe_event_calendar_handler class.
	 */
	var pe_event_calendar_handler = {

		init: function() {

			$( '.press-events-archive' )
				.find('select').chosen({disable_search_threshold: 10, width: '100%'});

			$( '.press-events-archive' ).on( 'click', '.filters-item.tags > a', this.tagFilter );

			if ( pe_archive_event_params.ajax_archive === 'on' ) {
				$( '.press-events-archive' )
					.on( 'click', '#pe-archive-navigation > a', this.changeMonth )
					.on( 'change', 'select[name="archive-type"]', this.triggerUpdate )
					.on( 'submit', 'form.archive-event-filters', this.fromSubmit );
			}
		},

		/**
		 * Toggle filter
		 */
		tagFilter: function(e) {
			e.preventDefault();
			e.stopPropagation();

			var $filterItem = $(this).closest('.filters-item');
			$filterItem.toggleClass('active');

			$filterItem.find('.panel-dropdown-content').on( 'click', function(e) {
				e.stopPropagation();
			}).find('button').on( 'click', function() {
				$(this).closest('.filters-item').removeClass('active');
			});

			$('body').on( 'click', function() {
				$filterItem.removeClass('active');
			});
		},

		/**
		 * Change target month and trigger from submit
		 */
		changeMonth: function(e) {
			var $thisbutton = $( this );
			var $calendar = $( '.press-events-archive' );

			if ( ! $thisbutton.data( 'target-month' ) ) {
			    return false;
			}

			e.preventDefault();

			// Update archive data
			var month = $thisbutton.data( 'target-month' );
			$calendar.find( 'input#target-month' ).val( month );

			// Trigger form submit
			pe_event_calendar_handler.triggerUpdate();
		},

		/**
		 * Trigger the calendar to update
		 */
		triggerUpdate: function() {
			// Trigger form submit
			$('form.archive-event-filters').trigger('submit');
		},

		/**
		 * Handle form submit
		 */
		fromSubmit: function(e) {
			e.preventDefault();

			var data = $(this).serialize(),
				$calendar = $( '.press-events-archive' );

			$calendar.addClass('loading');

			$.post( pe_archive_event_params.pe_ajax_url.toString().replace( '%%endpoint%%', 'get_calendar' ), data, function( response ) {
				if ( ! response ) {
					return;
				}

				if ( response.error ) {
					location.reload();
					return;
				}

				var data = response.data;

				// Update data
				$calendar.find( '.archive-event-header .archive-title h1' ).text( data.title );
				$calendar.find( '#pe-archive-navigation .previous' ).data( 'target-month', data.navigation.previous );
				$calendar.find( '#pe-archive-navigation .next' ).data( 'target-month', data.navigation.next );
				$calendar.find( 'input[name="archive_month"]' ).val( data.month );

				// Replace calendar HTML
				$calendar.find('.archive-event-wrapper').replaceWith( response.data.html );

				// Remove loading
				$calendar.removeClass('loading');
			});
		}

	};

	/**
	 * Init pe_event_calendar_handler.
	 */
	pe_event_calendar_handler.init();

})( jQuery );
