<?php
/**
 * Press Events Meta Box Functions
 *
 * @package PressEvents/Classes/Admin
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Output a text input box.
 *
 * @param array $field
 */
function bm_pe_text_input( $field ) {
	global $post;

	$field['placeholder'] = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field['class'] = isset( $field['class'] ) ? $field['class'] : '';
	$field['style'] = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : 'field-wrapper';
	$field['value'] = isset( $field['value'] ) ? $field['value'] : get_post_meta( $post->ID, $field['id'], true );
	$field['value'] = isset( $field['default_value'] ) && !$field['value'] ? $field['default_value'] : $field['value'];
	$field['name'] = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['type'] = isset( $field['type'] ) ? $field['type'] : 'text';
	$field['description'] = isset( $field['description'] ) ? $field['description'] : false;
	$field['bare'] = isset( $field['bare'] ) ? $field['bare'] : false;

	// Custom attribute handling
	$custom_attributes = array();
	if ( !empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	if ( !$field['bare'] ) {
		echo '<div class="' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">';

		echo '<div class="label-wrapper"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label></div>';

		echo '<div class="input-wrapper">';
	}

    echo '<input type="' . esc_attr( $field['type'] ) . '" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" ' . implode( ' ', $custom_attributes ) . ' /> ';

	if ( !$field['bare'] ) {
		if ( !empty( $field['description'] ) ) {
			echo '<div class="description">'. $field['description'] .'</div>';
		}

		echo '</div>'; // Input Wrapper

		echo '</div>'; // Wrapper
	}
}

/**
 * Output a text input box.
 *
 * @param array $field
 */
function bm_pe_hidden_input( $field ) {
	global $post;

	$field['value'] = isset( $field['value'] ) ? $field['value'] : get_post_meta( $post->ID, $field['id'], true );
	$field['name'] = isset( $field['name'] ) ? $field['name'] : $field['id'];

    echo '<input type="hidden" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" />';
}

/**
 * Output a checkbox input.
 *
 * @param array $field
 */
function bm_pe_checkbox( $field ) {
	global $post;

	$field['class'] = isset( $field['class'] ) ? $field['class'] : '';
	$field['style'] = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : 'field-wrapper';
	$field['value'] = isset( $field['value'] ) ? $field['value'] : get_post_meta( $post->ID, $field['id'], true );
	$field['checkbox_value'] = isset( $field['checkbox_value'] ) ? $field['checkbox_value'] : 'yes';
	$field['name'] = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['type'] = isset( $field['type'] ) ? $field['type'] : 'text';
	$field['description'] = isset( $field['description'] ) ? $field['description'] : false;
	$field['bare'] = isset( $field['bare'] ) ? $field['bare'] : false;

	// Custom attribute handling
	$custom_attributes = array();
	if ( !empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	if ( !$field['bare'] ) {
		echo '<div class="' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">';

		echo '<div class="label-wrapper"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label></div>';

		echo '<div class="input-wrapper">';
	}

    echo '<input type="checkbox" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . $field['checkbox_value'] . '" ' . checked( $field['value'], $field['checkbox_value'], false ) . ' ' . implode( ' ', $custom_attributes ) . ' />';

	if ( !$field['bare'] ) {
		if ( !empty( $field['description'] ) ) {
			echo '<div class="description">'. $field['description'] .'</div>';
		}

		echo '</div>'; // Input Wrapper

		echo '</div>'; // Wrapper
	}
}

/**
 * Output a select input.
 *
 * @param array $field
 */
function bm_pe_select_input( $field ) {
	global $post;

	$field['class'] = isset( $field['class'] ) ? $field['class'] : '';
	$field['style'] = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : 'field-wrapper';
	$field['value'] = isset( $field['value'] ) ? $field['value'] : get_post_meta( $post->ID, $field['id'], true );
	$field['value'] = isset( $field['default_value'] ) && !$field['value'] ? $field['default_value'] : $field['value'];
	$field['name'] = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['description'] = isset( $field['description'] ) ? $field['description'] : false;
	$field['bare'] = isset( $field['bare'] ) ? $field['bare'] : false;

	// Custom attribute handling
	$custom_attributes = array();
	if ( !empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	if ( !$field['bare'] ) {
		echo '<div class="' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">';

		echo '<div class="label-wrapper"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label></div>';

		echo '<div class="input-wrapper">';
	}

	echo '<select id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['name'] ) . '" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" ' . implode( ' ', $custom_attributes ) . '>';

	foreach ( $field['options'] as $key => $option ) {

		// Custom attribute handling
	 	$custom_attributes = array();
	 	if ( isset( $option['custom_attributes'] ) && ! empty( $option['custom_attributes'] ) && is_array( $option['custom_attributes'] ) ) {
	 		foreach ( $option['custom_attributes'] as $attribute => $value ) {
	 			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
	 		}
	 	}

		echo '<option value="' . esc_attr( $key ) . '" ' . selected( esc_attr( $field['value'] ), esc_attr( $key ), false ) . ' ' . implode( ' ', $custom_attributes ) . '>' . esc_html( $option[0] ) . '</option>';

	}

	echo '</select> ';

	if ( !$field['bare'] ) {
		if ( !empty( $field['description'] ) ) {
			echo '<div class="description">'. $field['description'] .'</div>';
		}

		echo '</div>'; // Input Wrapper

		echo '</div>'; // Wrapper
	}
}

/**
 * Output a textarea input.
 *
 * @param array $field
 */
function bm_pe_textarea_input( $field ) {
 	global $post;

 	$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
 	$field['class'] = isset( $field['class'] ) ? $field['class'] : '';
 	$field['style'] = isset( $field['style'] ) ? $field['style'] : '';
 	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : 'field-wrapper';
 	$field['value'] = isset( $field['value'] ) ? $field['value'] : get_post_meta( $post->ID, $field['id'], true );
 	$field['name'] = isset( $field['name'] ) ? $field['name'] : $field['id'];
 	$field['type'] = isset( $field['type'] ) ? $field['type'] : 'text';
 	$field['description'] = isset( $field['description'] ) ? $field['description'] : false;
 	$field['bare'] = isset( $field['bare'] ) ? $field['bare'] : false;
	$field['rows'] = isset( $field['rows'] ) ? $field['rows'] : 2;
	$field['cols'] = isset( $field['cols'] ) ? $field['cols'] : 20;

 	// Custom attribute handling
 	$custom_attributes = array();
 	if ( !empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
 		foreach ( $field['custom_attributes'] as $attribute => $value ) {
 			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
 		}
 	}

 	if ( !$field['bare'] ) {
 		echo '<div class="' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">';

 		echo '<div class="label-wrapper"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label></div>';

 		echo '<div class="input-wrapper">';
 	}

	echo '<textarea class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '"  name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" rows="' . esc_attr( $field['rows'] ) . '" cols="' . esc_attr( $field['cols'] ) . '" ' . implode( ' ', $custom_attributes ) . '>' . esc_textarea( $field['value'] ) . '</textarea> ';

 	if ( !$field['bare'] ) {
 		if ( !empty( $field['description'] ) ) {
 			echo '<div class="description">'. $field['description'] .'</div>';
 		}

 		echo '</div>'; // Input Wrapper

 		echo '</div>'; // Wrapper
	}
}
