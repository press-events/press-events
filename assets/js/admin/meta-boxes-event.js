/* global pe_admin_meta_boxes_event, DateFormatter */
(function( $ ) {
	'use strict';

	/**
	 * Date & time actions
	 */
	var pe_event_meta_box_date_time = {

		/**
		 * Initialise date & time actions
		 */
		init: function() {

			this.loadPickers();

			$( '#press-events-event-details' )
				.on( 'change', 'input#_all_day_event', this.onlyShowTime )
				.on( 'change', 'input.date', this.syncPickers )
				.on( 'click', 'input.visible-date', this.showPicker )
				.on( 'click', 'a#timezone-toggle', this.showTimezoneInput );

			$( 'input#_all_day_event' ).change();

		},

		/**
		 * Load date & time pickers
		 */
		loadPickers: function() {

			$( '#date_time_event_details .date' ).datepicker({
				autoclose: true,
				format: 'yyyy-mm-dd',
				container: '#date_time_event_details'
			});

			$( '#date_time_event_details .time' ).timepicker({
				showDuration: true,
				timeFormat: pe_admin_meta_boxes_event.time_format
			});

			// initialise datepair
			$( '#date_time_event_details' ).datepair();

		},

		/**
		 * Sync the visible pickers with hidden values
		 */
		syncPickers: function() {
			var fmt = new DateFormatter({
			    dateSettings: {
					days: pe_admin_meta_boxes_event.date_vars.days,
				    daysShort: pe_admin_meta_boxes_event.date_vars.daysShort,
				    monthsShort: pe_admin_meta_boxes_event.date_vars.monthsShort,
				    months: pe_admin_meta_boxes_event.date_vars.months,
				    meridiem: pe_admin_meta_boxes_event.date_vars.meridiem,
				    ordinal: function (number) {
				        var n = number % 10, suffixes = {1: 'st', 2: 'nd', 3: 'rd'};
				        return Math.floor(number % 100 / 10) === 1 || !suffixes[n] ? 'th' : suffixes[n];
				    }
			    }
			});

			$( '#press-events-event-details .date' ).each(function() {
				$( '#' + $(this).data('input-mask') )
					.val( fmt.formatDate( $(this).datepicker('getDate'), pe_admin_meta_boxes_event.date_format ) );
			});
		},

		/**
		 * Show picker
		 */
		showPicker: function() {
			$( '#' + $(this).data('datepicker') ).datepicker( 'show' );
		},

		/**
		 * Hide time input if all day event
		 */
		onlyShowTime: function() {

			if( this.checked ){
                $( '#date_time_event_details .time' ).hide();
            } else {
                $( '#date_time_event_details .time' ).show();
            }

		},

		/**
		 * Show the timezone input
		 */
		showTimezoneInput: function() {
			$('.field-wrapper.event-timezone').addClass('active-edit');
		}

	};

	/**
	 * Location actions
	 */
	var pe_event_meta_box_location = {

		init: function() {
			$('#location_event_details')
				.on( 'change', 'select#_event_location_select', this.addLocation )
		        .on( 'click', '.location .top-bar .actions .remove', this.removeLocation )
		        .on( 'click', '.location .top-bar', this.toggleEdit )
		        .on( 'click', '.location .edit-wrapper .save', this.saveLocation )
		        .on( 'click', '.location .edit-wrapper .delete', this.deleteLocation )
				.on( 'saving-location', this.addLoading )
				.on( 'location-saved', this.updateLocation );

			$( document ).ready(function() {
				if ( $('#_event_location').val() ) {
					// Trigger update
					$('#location_event_details').trigger('update-location');
				}
			});
		},

		/**
		 * Add location
		 */
		addLocation: function() {
			// Get data
			var location_id = $(this).find(':selected').data('id');
			// Add to hidden input
			$('#_event_location').val(location_id);
			// Trigger update
			$('#location_event_details').trigger('update-location');
		},

		/**
		 * Toggle .edit-wrapper
		 */
		toggleEdit: function(e) {
		    e.preventDefault();
		    $(this).parents('.location').find('.edit-wrapper').slideToggle();
		},

		/**
		 * Remove location
		 */
		removeLocation: function(e) {
			if ( typeof e !== 'undefined' ) { e.preventDefault(); }
			// Remove ID from hidden input
			$('#_event_location').val('');
			// Reset select
			$('#_event_location_select').val('');
			// Remove .location
			$('#location-list-wrapper').empty();
		},

		/**
		 * Save location
		 */
		saveLocation: function(e) {
		    e.preventDefault();
			// Trigger saving
			$('#location_event_details').trigger( 'saving-location' );
			// Get location
			var $location = $(this).parents('.location');
			// Build location data
			var data = [{
				id: $location.data('id'),
		        title: $location.find( 'input[name="location_title"]' ).val(),
		        address: $location.find( 'input[name="location_address"]' ).val(),
		        city: $location.find( 'input[name="location_city"]' ).val(),
		        postcode: $location.find( 'input[name="location_postcode"]' ).val(),
		        county: $location.find( 'input[name="location_county"]' ).val(),
		        country: $location.find( 'input[name="location_country"]' ).val()
			}];
			// Trigger save
			$('#location_event_details').trigger( 'save-location', data );
		},

		/**
		 * Delete the location
		 */
		deleteLocation: function(e) {
		    e.preventDefault();

			// Get location id
			var id = $(this).parents('.location').data('id');

			if ( id === 0 ) {return;}

			if ( window.confirm('Are you sure you want to delete this location from all events?') ) {
				// Remove the location
				pe_event_meta_box_location.removeLocation();
				// Trigger delete
				$('#location_event_details').trigger( 'delete-location', id );
			}
		},

		/**
		 * Add saving loader and block input
		 */
		addLoading: function() {
			$('#location-list-wrapper').addClass('loading');
			// Remove any error
			$('#location-list-wrapper .location .edit-wrapper .small-error').remove();
		},

		/**
		 * Update location
		 */
		updateLocation: function(e, data) {
			// Remove loading
			$('#location-list-wrapper').removeClass('loading');

			if ( data.location.id !== 0 ) {
				// Update select
				$('#_event_location_select').replaceWith( data.selectHtml );
				// Add to hidden input
				$('#_event_location').val( data.location.id );
				// Select
				$('#_event_location_select').val( data.location.id );
			}
			// Trigger update
			$('#location_event_details').trigger('update-location');
		}

	};

	var pe_event_meta_box_location__ajax = {

		init: function() {
			$('#location_event_details')
				.on( 'update-location', this.updateLocation )
				.on( 'save-location', this.saveLocation )
				.on( 'delete-location', this.deleteLocation );
		},

		/**
		 * Update the location from AJAX call
		 */
		updateLocation: function() {
			// Add loading
			$('#location-list-wrapper').addClass('loading');

			// Get location from DB
			$.ajax({
		        url: pe_admin_meta_boxes_event.ajax_url,
		        data: {
		            action: 'press_events_get_location',
		            security: pe_admin_meta_boxes_event.get_location_nonce,
		            event_location_id: $('#_event_location').val()
		        },
		        type: 'POST',
		        success: function( response ) {
					// Remove loading
					$('#location-list-wrapper').removeClass('loading');
					// Remove .location
					$('#location-list-wrapper').empty();
					// Add new
					$('#location-list-wrapper').append( [response.data].map(function(item) {
						return $('script[data-template="location-item"]').text().split(/\$\{(.+?)\}/g).map(pe_event_meta_box_location__ajax.render(item)).join('');
					}) );
					// If new open edit
					if ( response.data.id === 0 ) {
						$('#location-list-wrapper .location[data-id="0"] .edit-wrapper').slideToggle();
					} else {
						// Select
						$('#_event_location_select').val(response.data.id);
					}

				}
			});
		},

		/**
		 * Save the data to WordPress
		 */
		saveLocation: function( e, data ) {
			// Add loading
			$('#location-list-wrapper').addClass('loading');
			// Save location from DB
			$.ajax({
		        url: pe_admin_meta_boxes_event.ajax_url,
		        data: {
		            action: 'press_events_update_location',
		            security: pe_admin_meta_boxes_event.update_location_nonce,
		            data: data
		        },
		        type: 'POST',
		        success: function( response ) {
					if ( response.success ) {
						// Trigger save
						$('#location_event_details').trigger( 'location-saved', response.data );
					} else {
						// Remove loading
						$('#location-list-wrapper').removeClass('loading');
						// Error
						$('#location-list-wrapper .location .edit-wrapper').prepend([{error:response.data}].map(function(item) {
							return $('script[data-template="location-error"]').text().split(/\$\{(.+?)\}/g).map(pe_event_meta_box_location__ajax.render(item)).join('');
						}));
					}
				}
			});
		},

		/**
		 * Delete the location
		 */
		deleteLocation: function( e, id ) {
			// Delete from DB
			$.ajax({
		        url: pe_admin_meta_boxes_event.ajax_url,
		        data: {
		            action: 'press_events_delete_location',
		            security: pe_admin_meta_boxes_event.delete_location_nonce,
		            location_id: id
		        },
		        type: 'POST',
		        success: function( response ) {
					// Remove option from select
					$('#_event_location_select').find('option[value="'+ response.data +'"]').remove();
					// Trigger deleted
					$('#location_event_details').trigger( 'location-deleted', response.data );
				}
			});
		},

		/**
		 * Render location item
		 */
		render: function( props ) {
		    return function(token, i) { return (i % 2) ? props[token] : token; };
		}

	};

	/**
	 * Organiser actions
	 */
	var pe_event_meta_box_organisers = {

		init: function() {
			// Sortable
			this.sortable();

			$('#organiser_event_details')
				.on( 'change', 'select#_event_organiser_select', this.addOrganiser )
		        .on( 'click', '.organiser .top-bar', this.toggleEdit )
		        .on( 'click', '.organiser .top-bar .actions .remove', this.removeOrganiser )
		        .on( 'click', '.organiser .edit-wrapper .save', this.saveOrganiser )
				.on( 'saving-organiser', this.addLoading );


			// Load organisers on load
			$( document ).ready(function() {
				if ( $('#_event_organisers').val() ) {
					// Trigger update
					$('#organiser_event_details').trigger('update-organisers');
				}
			});
		},

		/**
		 * Trigger jQuery sortable
		 */
		sortable: function() {
		    $( '#organisers-drag-drop-wrapper' ).sortable({
		        placeholder: 'ui-state-highlight',
		        handle: '.drag',
		        update: function() {
		            pe_event_meta_box_organisers.updateOrder();
		        },
		        stop: function() {
		            pe_event_meta_box_organisers.mainOrganiserFlag();
		        }
		    });
		},

		/**
		 * Append "main organiser" flag
		 */
		mainOrganiserFlag: function() {
		    var organisers_number = $( '#organisers-drag-drop-wrapper .organiser' ).length;
		    $( '#organisers-drag-drop-wrapper .organiser .top-bar .main' ).removeClass( 'active' );
		    if ( organisers_number > 1) {
		        $( '#organisers-drag-drop-wrapper .organiser:first-child .top-bar .main' ).addClass( 'active' );
		    }
		},

		/**
		 * Update organiser order input
		 */
		updateOrder: function() {
		    var order = [];
		    $( '#organisers-drag-drop-wrapper .organiser' ).each( function() {
		        order.push( $(this).data('id') );
		    });
		    var organisers = order.join(',');
		    $( 'input#_event_organisers' ).val( organisers );
		},

		/**
		 * Add location
		 */
		addOrganiser: function( e, user_id ) {
			if ( typeof user_id === 'undefined' ) {
				// Get data
				user_id = $(this).find(':selected').data('id');
			}
			var organisers = $('#_event_organisers').val();

			organisers = organisers === '' ? [] : organisers.split(',');
			organisers.push(user_id);

			// Comma list and add to value
			$('#_event_organisers').val( organisers.join(',') );

			// Trigger update
			$('#organiser_event_details').trigger('update-organisers');
		},

		/**
		 * Toggle .edit-wrapper
		 */
		toggleEdit: function(e) {
			e.preventDefault();
			$(this).parents('.organiser').find('.edit-wrapper').slideToggle();
		},

		/**
		 * Hide the edit wrapper
		 */
		hideEdit: function(e) {
			e.preventDefault();
			$(this).parents('.organiser').find('.edit-wrapper').hide();
		},

		/**
		 * Remove organiser
		 */
		removeOrganiser: function( e, user_id ) {
			if ( typeof e !== 'undefined' ) { e.preventDefault(); }

			if ( typeof user_id === 'undefined' ) { user_id = $(this).parents('.organiser').data('id'); }

			// Remove Organiser
			$(this).parents('.organiser').remove();

			var organisers = $('#_event_organisers').val();

			organisers = organisers === '' ? [] : organisers.split(',');
			organisers = $.grep(organisers, function(value) {
			  return value != user_id; // jshint ignore:line
			});

			// Comma list and add to value
			$('#_event_organisers').val( organisers.join(',') );

			// Trigger update
			$('#organiser_event_details').trigger('update-organisers');
		},

		/**
		 * Save organiser
		 */
		saveOrganiser: function(e) {
			e.preventDefault();
			// Trigger saving
			$('#organiser_event_details').trigger( 'saving-organiser' );
			// Get organiser
			var $organiser = $(this).parents('.organiser');
			// Build organiser data
			var data = {
				id: $organiser.data('id'),
		        display_name: $organiser.find( 'input[name="display_name"]' ).val(),
		        user_email: $organiser.find( 'input[name="user_email"]' ).val(),
		        user_url: $organiser.find( 'input[name="user_url"]' ).val(),
		        user_phone: $organiser.find( 'input[name="user_phone"]' ).val()
			};
			// Trigger save
			$('#organiser_event_details').trigger( 'save-organiser', [{organiser:data, $el: $organiser}] );
		},

		/**
		 * Add saving loader and block input
		 */
		addLoading: function() {
			$('#organisers-drag-drop-wrapper').addClass('loading');
			// Remove any error
			$('#organisers-drag-drop-wrapper .organiser .edit-wrapper .small-error').remove();
		}

	};

	/**
	 * Organiser ajax actions
	 */
	var pe_event_meta_box_organisers__ajax = {

		init: function() {
			$('#organiser_event_details')
				.on( 'update-organisers', this.updateOrganisers )
				.on( 'save-organiser', this.saveOrganiser );
		},

		/**
		 * Get given organisers from DB
		 */
		updateOrganisers: function() {
			// Get organisers from DB
			$.ajax({
		        url: pe_admin_meta_boxes_event.ajax_url,
		        data: {
		            action: 'press_events_get_organisers',
		            security: pe_admin_meta_boxes_event.get_organisers_nonce,
		            event_organiser_ids: $('#_event_organisers').val()
		        },
		        type: 'POST',
		        success: function( response ) {
					// Remove .organisers
					$('#organisers-drag-drop-wrapper').empty();
					// Add .organisers
					$.each( response.data.organisers, function( key, organiser ) {
						$('#organisers-drag-drop-wrapper').append( [organiser].map(function(item) {
							return $('script[data-template="organiser-item"]').text().split(/\$\{(.+?)\}/g).map(pe_event_meta_box_organisers__ajax.render(item)).join('');
						}) );
					} );
					// Toggle edit on new
					$('#organisers-drag-drop-wrapper').find('.organiser[data-id="0"] .top-bar').trigger('click');
					// Update organiser ids
					$('#_event_organisers').val(response.data.ids);
					// Update flag
				    pe_event_meta_box_organisers.mainOrganiserFlag();
					// Reset select
					$('#_event_organiser_select').val('');
				}
			});
		},

		/**
		 * Save organiser
		 */
		saveOrganiser: function( e, data ) {
			// Update or inset organiser
			$.ajax({
		        url: pe_admin_meta_boxes_event.ajax_url,
		        data: {
		            action: 'press_events_update_organiser',
		            security: pe_admin_meta_boxes_event.update_organiser_nonce,
		            data: data.organiser
		        },
		        type: 'POST',
		        success: function( response ) {
					if ( response.success ) {
						// Update select
						$('#_event_organiser_select').replaceWith( response.data.selectHtml );
						// Remove loading
						$('#organisers-drag-drop-wrapper').removeClass('loading');
						if ( data.organiser.id === 0 ) {
							// Remove ID from val
							pe_event_meta_box_organisers.removeOrganiser( e, 0 );
						}
						// Add new organiser ID
						pe_event_meta_box_organisers.addOrganiser( e, response.data.organiser.ID );
					} else {
						// Remove loading
						$('#organisers-drag-drop-wrapper').removeClass('loading');
						// Error
						data.$el.find('.edit-wrapper').prepend([{error:response.data}].map(function(item) {
							return $('script[data-template="organiser-error"]').text().split(/\$\{(.+?)\}/g).map(pe_event_meta_box_organisers__ajax.render(item)).join('');
						}));
					}
				}
			});
		},

		/**
		 * Render organiser item
		 */
		render: function( props ) {
		    return function(token, i) { return (i % 2) ? props[token] : token; };
		}

	};

	pe_event_meta_box_date_time.init();

	pe_event_meta_box_location.init();
	pe_event_meta_box_location__ajax.init();

	pe_event_meta_box_organisers.init();
	pe_event_meta_box_organisers__ajax.init();

})( jQuery );
