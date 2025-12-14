<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Dynamic Tag - Property Field
 *
 * Elementor dynamic tag that returns a Property Field.
 *
 * @since 1.0.0
 */
class REM_Elementor_Dynamic_Tag_Property_Field extends \Elementor\Core\DynamicTags\Tag {

	/**
	 * Get dynamic tag name.
	 *
	 * Retrieve the name of the Property Field tag.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Dynamic tag name.
	 */
	public function get_name() {
		return 'rem-property-field';
	}

	/**
	 * Get dynamic tag title.
	 *
	 * Returns the title of the Property Field tag.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Dynamic tag title.
	 */
	public function get_title() {
		return esc_html__( 'Property Field', 'real-estate-manager' );
	}

	/**
	 * Get dynamic tag groups.
	 *
	 * Retrieve the list of groups the Property Field tag belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Dynamic tag groups.
	 */
	public function get_group() {
		return [ 'rem-dynamic-tags' ];
	}

	/**
	 * Get dynamic tag categories.
	 *
	 * Retrieve the list of categories the Property Field tag belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Dynamic tag categories.
	 */
	public function get_categories() {
		return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
	}

	/**
	 * Register dynamic tag controls.
	 *
	 * Add input fields to allow the user to customize the Property Field tag settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return void
	 */
	protected function register_controls() {
		$options = [];
        global $rem_ob;
        $fields = $rem_ob->single_property_fields();

		foreach ( $fields as $field ) {
            if (isset($field['key'])) {
                $options[ $field['key'] ] = $field['title'];
            }
		}

		$this->add_control(
			'user_selected_field',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => esc_html__( 'Field Title', 'real-estate-manager' ),
				'options' => $options,
			]
		);
	}

	/**
	 * Render tag output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function render() {
		$user_selected_field = $this->get_settings( 'user_selected_field' );

		if ( ! $user_selected_field ) {
			return;
		}

		$value = rem_get_field_value($user_selected_field);
		echo wp_kses_post( $value );
	}

}
?>