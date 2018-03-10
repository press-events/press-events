<?php
/**
 * Press Events Settings
 *
 * @package PressEvents/Classes/Admin
 * @author Burn Media Ltd
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * BM_PE_Admin_Settings
 */
class BM_PE_Admin_Settings {

	/**
     * Settings tabs array
     *
     * @var array
     */
    protected $settings_tabs = array();

	/**
     * Settings sections array
     *
     * @var array
     */
    protected $settings_sections = array();

	/**
     * Settings fields array
     *
     * @var array
     */
    protected $settings_fields = array();

    public function __construct() {
		BM_PE_Settings_Page::load_settings();

		$this->set_tabs( BM_PE_Settings_Page::get_settings_tabs() );
		$this->set_sections( BM_PE_Settings_Page::get_settings_sections() );
		$this->set_fields( BM_PE_Settings_Page::get_settings_fields() );

		add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'main_settings_page') );
    }

	/**
	 * Register settings menu
	 */
	 public function main_settings_page() {
	     add_submenu_page(
			 'edit.php?post_type=pe_event',
		     __( 'Settings', 'press-events' ),
		     __( 'Settings', 'press-events' ),
		     'manage_options',
		     'pe-settings',
		     array( 'BM_PE_Settings_Page', 'output' )
		);
	}

    /**
     * Set settings tabs
     *
     * @param array   $tabs setting tabs array
     */
    function set_tabs( $tabs ) {
        $this->settings_tabs = $tabs;
        return $this;
    }

    /**
     * Set settings sections
     *
     * @param array   $sections setting sections array
     */
    function set_sections( $sections ) {
        $this->settings_sections = $sections;
        return $this;
    }

	/**
     * Set settings fields
     *
     * @param array   $fields settings fields array
     */
    function set_fields( $fields ) {
        $this->settings_fields = $fields;
        return $this;
    }

    /**
     * Initialize and registers the settings sections and fileds to WordPress
     */
    function admin_init() {

		// register settings sections
        foreach ( $this->settings_sections as $section ) {
            if ( false == get_option( $section['id'] ) ) {
                add_option( $section['id'] );
            }

            add_settings_section( $section['tab'] . '-' . $section['id'], $section['title'], function() use ($section) {
				if ( isset($section['desc']) ) {
					echo '<p class="section-description">'. $section['desc'] .'</p>';
				}
			}, $section['tab'] );

			// section has fields?
			if ( isset( $this->settings_fields[$section['id']] ) ) {

				// register this sections fields
				foreach ( $this->settings_fields[$section['id']] as $field ) {
	                $name = $field['name'];
	                $type = isset( $field['type'] ) ? $field['type'] : 'text';
	                $label = isset( $field['label'] ) ? $field['label'] : '';
	                $callback = isset( $field['callback'] ) ? $field['callback'] : array( $this, 'callback_' . $type );
	                $args = array(
	                    'id'                => $name,
	                    'class'             => isset( $field['class'] ) ? $option['class'] : $name,
	                    'label_for'         => "{$section['id']}[{$name}]",
	                    'help'              => isset( $field['help'] ) ? $field['help'] : '',
	                    'desc'              => isset( $field['desc'] ) ? $field['desc'] : '',
	                    'name'              => $label,
	                    'section'           => $section['id'],
	                    'size'              => isset( $field['size'] ) ? $field['size'] : null,
	                    'options'           => isset( $field['options'] ) ? $field['options'] : '',
	                    'std'               => isset( $field['default'] ) ? $field['default'] : '',
	                    'sanitize_callback' => isset( $field['sanitize_callback'] ) ? $field['sanitize_callback'] : '',
	                    'type'              => $type,
	                    'placeholder'       => isset( $field['placeholder'] ) ? $field['placeholder'] : '',
	                    'min'               => isset( $field['min'] ) ? $field['min'] : '',
	                    'max'               => isset( $field['max'] ) ? $field['max'] : '',
	                    'step'              => isset( $field['step'] ) ? $field['step'] : '',
						'multiple' 			=> isset( $field['multiple'] ) ? true : false
	                );
	                add_settings_field( $section['id'] ."[". $name ."]", $label, $callback, $section['tab'], $section['tab'] . '-' . $section['id'], $args );
	            }

			}

			// creates our settings in the options table
	        register_setting( $section['tab'], $section['id'], array( $this, 'sanitize_options' ) );
		}

    }

    /**
     * Get field help for display
     *
     * @param array   $args settings field args
     */
	public function get_field_help( $args ) {
        if ( ! empty( $args['help'] ) ) {
			$help = sprintf( '<span class="dashicons dashicons-editor-help press-tip" data-tip="%s"></span>', $args['help'] );
        } else {
            $help = '';
        }

        return $help;
    }

    /**
     * Get field description for display
     *
     * @param array   $args settings field args
     */
	public function get_field_description( $args ) {
        if ( ! empty( $args['desc'] ) ) {
			$desc = sprintf( '<div class="description">%s</div>', $args['desc'] );
        } else {
            $desc = '';
        }

        return $desc;
    }

    /**
     * Displays a text field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_text( $args ) {
        $value       = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size        = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
        $type        = isset( $args['type'] ) ? $args['type'] : 'text';
        $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';

		$html        = $this->get_field_help( $args );
        $html       .= sprintf( '<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder );
		$html	    .= $this->get_field_description( $args );

        echo $html;
    }

    /**
     * Displays a color picker field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_color( $args ) {
        $value       = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size        = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
        $type        = isset( $args['type'] ) ? $args['type'] : 'text';
        $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';

		$html        = $this->get_field_help( $args );
        $html       .= sprintf( '<input type="%1$s" class="%2$s-text press-color-picker" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder );
		$html	    .= $this->get_field_description( $args );

        echo $html;
    }

    /**
     * Displays a url field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_url( $args ) {
        $this->callback_text( $args );
    }

    /**
     * Displays a number field for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_number( $args ) {
        $value       = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size        = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
        $type        = isset( $args['type'] ) ? $args['type'] : 'number';
        $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';
        $min         = empty( $args['min'] ) ? '' : ' min="' . $args['min'] . '"';
        $max         = empty( $args['max'] ) ? '' : ' max="' . $args['max'] . '"';
        $step        = empty( $args['max'] ) ? '' : ' step="' . $args['step'] . '"';

        $html        = $this->get_field_help( $args );
        $html       .= sprintf( '<input type="%1$s" class="%2$s-number" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s%7$s%8$s%9$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder, $min, $max, $step );
		$html	  	.= $this->get_field_description( $args );

        echo $html;
    }

    /**
     * Displays a checkbox for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_checkbox( $args ) {
        $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

        $html  = '<fieldset>';
		$html .= $this->get_field_help( $args );
        $html .= sprintf( '<label for="%1$s[%2$s]">', $args['section'], $args['id'] );
        $html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id'] );
        $html .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="on" %3$s />', $args['section'], $args['id'], checked( $value, 'on', false ) );
        $html .= sprintf( '%1$s</label>', $args['desc'] );
		$html .= $this->get_field_description( $args );
        $html .= '</fieldset>';

        echo $html;
    }

    /**
     * Displays a multicheckbox for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_multicheck( $args ) {
        $value = $this->get_option( $args['id'], $args['section'], $args['std'] );
        $html  = '<fieldset>';
		$html .= $this->get_field_help( $args );
        $html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="" />', $args['section'], $args['id'] );

        foreach ( $args['options'] as $key => $label ) {
            $checked = isset( $value[$key] ) ? $value[$key] : '0';

            $html    .= sprintf( '<label for="%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key );
            $html    .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $checked, $key, false ) );
            $html    .= sprintf( '%1$s</label><br>',  $label );
        }

		$html .= $this->get_field_description( $args );
        $html .= '</fieldset>';

        echo $html;
    }

    /**
     * Displays a radio button for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_radio( $args ) {
        $value = $this->get_option( $args['id'], $args['section'], $args['std'] );
        $html  = '<fieldset>';
		$html .= $this->get_field_help( $args );

        foreach ( $args['options'] as $key => $label ) {
            $html .= sprintf( '<label for="%1$s[%2$s][%3$s]">',  $args['section'], $args['id'], $key );
            $html .= sprintf( '<input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $value, $key, false ) );
            $html .= sprintf( '%1$s</label><br>', $label );
        }

		$html .= get_field_description( $args );
        $html .= '</fieldset>';

        echo $html;
    }

    /**
     * Displays a selectbox for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_select( $args ) {
        $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size  = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
		$multiple = $args['multiple'] ? ' multiple' : null;

		$html  = $this->get_field_help( $args );
        $html  .= sprintf( '<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]"%4$s>', $size, $args['section'], $args['id'], $multiple );

        foreach ( $args['options'] as $key => $option ) {

			if ( is_array($option) ) {
				$html .= sprintf( '<optgroup label="%s">', $key, selected( $value, $key, false ), $option );
				foreach ( $option as $key => $label ) {
					$html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
				}
				$html .= sprintf( '</optgroup>' );
			} else {
            	$html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $option );
			}

        }

        $html .= sprintf( '</select>' );
		$html .= $this->get_field_description( $args );

        echo $html;
    }

    /**
     * Displays a textarea for a settings field
     *
     * @param array   $args settings field args
     */
    function callback_textarea( $args ) {
        $value       = esc_textarea( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
        $size        = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
        $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="'.$args['placeholder'].'"';

		$html        = $this->get_field_help( $args );
        $html       .= sprintf( '<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]"%4$s>%5$s</textarea>', $size, $args['section'], $args['id'], $placeholder, $value );
		$html       .= $this->get_field_description( $args );

        echo $html;
    }

    /**
     * Displays the html for a settings field
     *
     * @param array   $args settings field args
     * @return string
     */
    function callback_html( $args ) {
        echo $this->get_field_help( $args );
    }

    /**
     * Sanitize callback for Settings API
     *
     * @return mixed
     */
    function sanitize_options( $options ) {
        if ( !$options ) {
            return $options;
        }

        foreach( $options as $option_slug => $option_value ) {
            $sanitize_callback = $this->get_sanitize_callback( $option_slug );

            // If callback is set, call it
            if ( $sanitize_callback ) {
                $options[ $option_slug ] = call_user_func( $sanitize_callback, $option_value );
                continue;
            }
        }

        return $options;
    }

    /**
     * Get sanitization callback for given option slug
     *
     * @param string $slug option slug
     *
     * @return mixed string or bool false
     */
    function get_sanitize_callback( $slug = '' ) {
        if ( empty( $slug ) ) {
            return false;
        }

        // Iterate over registered fields and see if we can find proper callback
        foreach( $this->settings_fields as $section => $options ) {
            foreach ( $options as $option ) {
                if ( $option['name'] != $slug ) {
                    continue;
                }

                // Return the callback name
                return isset( $option['sanitize_callback'] ) && is_callable( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : false;
            }
        }

        return false;
    }

    /**
     * Get the value of a settings field
     *
     * @param string  $option  settings field name
     * @param string  $section the section name this field belongs to
     * @param string  $default default text if it's not found
     * @return string
     */
    function get_option( $option, $section, $default = '' ) {
        $options = get_option( $section );

        if ( isset( $options[$option] ) ) {
            return $options[$option];
        }

        return $default;
    }

}
new BM_PE_Admin_Settings();
