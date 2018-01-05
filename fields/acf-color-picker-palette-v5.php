<?php

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// check if class already exists
if ( ! class_exists( 'ACF_Field_Color_Picker_Palette' ) ) :


	class ACF_Field_Color_Picker_Palette extends acf_field {


		/*
		*  __construct
		*
		*  This function will setup the field type data
		*
		*  @type	function
		*  @date	5/03/2014
		*  @since	5.0.0
		*
		*  @param	n/a
		*  @return	n/a
		*/

		function __construct( $settings ) {

			/*
			*  name (string) Single word, no spaces. Underscores allowed
			*/

			$this->name = 'color_picker_palette';


			/*
			*  label (string) Multiple words, can include spaces, visible when selecting a field type
			*/

			$this->label = __( 'Color Picker Palette', 'acf-color-picker-palette' );


			/*
			*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
			*/

			$this->category = 'jquery';


			/*
			*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
			*/

			$this->defaults = array(
				'color_picker_palette' => '',
				'choices'              => array(),
			);


			/*
			*  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
			*  var message = acf._e('color-picker-palette', 'error');
			*/

			$this->l10n = array(
				'error' => __( 'Error! Please enter a valid value', 'acf-color-picker-palette' ),
			);


			/*
			*  settings (array) Store plugin settings (url, path, version) as a reference for later use with assets
			*/

			$this->settings = $settings;


			// do not delete!
			parent::__construct();

		}


		/*
		*  render_field_settings()
		*
		*  Create extra settings for your field. These are visible when editing a field
		*
		*  @type	action
		*  @since	3.6
		*  @date	23/01/13
		*
		*  @param	$field (array) the $field being edited
		*  @return	n/a
		*/

		function render_field_settings( $field ) {

			/*
			*  acf_render_field_setting
			*
			*  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
			*  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
			*
			*  More than one setting can be added by copy/paste the above code.
			*  Please note that you must also have a matching $defaults value for the field name (color_picker_palette)
			*/

			$field['choices'] = acf_encode_choices( $field['choices'] );

			acf_render_field_setting( $field, array(
				'label'        => __( 'Color Options', 'acf-color-picker-palette' ),
				'instructions' => __( 'Enter each choice on a new line.', 'acf-color-picker-palette' ) . '<br /><br />' . __( 'For more control, you may specify both a value and label like this:', 'acf-color-picker-palette' ) . '<br /><br />' . __( 'black : #000', 'acf-color-picker-palette' ),
				'type'         => 'textarea',
				'name'         => 'choices',
			) );

			acf_render_field_setting( $field, array(
				'label'         => __( 'Allow Custom', 'acf-color-picker-palette' ),
				'name'          => 'allow_custom',
				'default_value' => 1,
				'type'          => 'true_false',
				'ui'            => 1,
				'message'       => __( 'Allow \'custom\' colors to be added', 'acf-color-picker-palette' ),
			) );

		}


		/*
		*  render_field()
		*
		*  Create the HTML interface for your field
		*
		*  @param	$field (array) the $field being rendered
		*
		*  @type	action
		*  @since	3.6
		*  @date	23/01/13
		*
		*  @param	$field (array) the $field being edited
		*  @return	n/a
		*/

		function render_field( $field ) {

			/*
			*  Create a simple text input using the 'color_picker_palette' setting.
			*/

			// Have to reformat the choices array in order to yield a properly formatted JSON string.
			$nested_array = [];
			if ( ! empty( $field['choices'] ) ) {
				foreach ( (array) $field['choices'] as $value => $label ) {
					$nested_array[] = [
						$value => $label,
					];
				}
			}

			$data_palette = json_encode( $nested_array );

			?>
			<input type="text" name="<?php echo esc_attr( $field['name'] ) ?>" data-palette='<?php echo $data_palette; ?>' value="<?php echo esc_attr( $field['value'] ) ?>" />
			<?php
		}


		/*
		*  input_admin_enqueue_scripts()
		*
		*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
		*  Use this action to add CSS + JavaScript to assist your render_field() action.
		*
		*  @type	action (admin_enqueue_scripts)
		*  @since	3.6
		*  @date	23/01/13
		*
		*  @param	n/a
		*  @return	n/a
		*/

		function input_admin_enqueue_scripts() {

			$url     = $this->settings['url'];
			$version = $this->settings['version'];

			// Enqueue required JS
			wp_enqueue_script( 'acf-input-color-picker-palette', "{$url}assets/js/palette-color-picker.min.js", array( 'jquery' ), $version );
			wp_enqueue_script( 'acf-input-color-picker-palette-init', "{$url}assets/js/init-color-picker-palette.js", array( 'acf-input' ), $version );

			// Enqueue required CSS
			wp_enqueue_style( 'acf-color-palette-picker', "{$url}assets/css/palette-color-picker.css", array( 'acf-input' ), $version );
			wp_enqueue_style( 'acf-input-color-picker-palette', "{$url}assets/css/input.css", array( 'acf-input' ), $version );

		}


		/*
		*  update_field()
		*
		*  This filter is applied to the $field before it is saved to the database
		*
		*  @type	filter
		*  @date	23/01/2013
		*  @since	3.6.0
		*
		*  @param	$field (array) the field array holding all the field options
		*  @return	$field
		*/

		function update_field( $field ) {

			$field['choices'] = acf_decode_choices( $field['choices'] );

			return $field;

		}

		/**
		 * Validate the color picker value.
		 *
		 * @since 1.1.0
		 *
		 * @param bool   $valid Whether the value is valid or not.
		 * @param string $value Color value.
		 * @param array  $field ACF Color Picker Palette field.
		 * @param string $input ACF field ID.
		 * @return bool
		 */
		public function validate_value( $valid, $value, $field, $input ) {
			$allow_custom = isset( $field['allow_custom'] ) ? $field['allow_custom'] : true;

			// Bail early if empty or custom values are allowed.
			if ( empty( $value ) || $allow_custom ) {
				return $valid;
			}

			// Ensure the value is in the list of choices.
			$valid = in_array( $value, array_keys( $field['choices'] ), true );

			if ( ! $valid ) {
				$valid = __( 'Please choose from the available colors', 'acf-color-picker-palette' );
			}

			return $valid;
		}

	}


	// initialize
	new ACF_Field_Color_Picker_Palette( $this->settings );


	// class_exists check
endif;

?>
