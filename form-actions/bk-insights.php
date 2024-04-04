<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor form Brandkrew action.
 *
 * Custom Elementor form action which adds new subscriber to Brandkrew Insights SaaS after form submission.
 *
 * @since 1.0.0
 */
class Bk_Insights_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base {

	/**
	 * Get action name.
	 *
	 * Retrieve Brandkrew action name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'Brandkrew Insights';
	}

	/**
	 * Get action label.
	 *
	 * Retrieve Brandkrew action label.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Bk Insights', 'elementor-forms-bk-action' );
	}

	/**
	 * Register action controls.
	 *
	 * Add input fields to allow the user to customize the action settings.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param \Elementor\Widget_Base $widget
	 */
	public function register_settings_section( $widget ) {

		$widget->start_controls_section(
			'section_bk',
			[
				'label' => esc_html__( 'Brandkrew Insights', 'elementor-forms-bk-action' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			'bk_url',
			[
				'label' => esc_html__( 'Webhook URL', 'elementor-forms-bk-action' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'https://webhooks.brandkrew.com/elementor/...',
				'description' => esc_html__( 'Enter the Webhook URL of your current Insights Dashboard.', 'elementor-forms-bk-action' ),
			]
		);

		$widget->add_control(
			'bk_email_field',
			[
				'label' => esc_html__( 'Email Field ID', 'elementor-forms-bk-action' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'Enter the email field ID',
			]
		);

		$widget->add_control(
			'bk_name_field',
			[
				'label' => esc_html__( 'Name Field ID', 'elementor-forms-bk-action' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'Enter the name field ID',
			]
		);

		$widget->end_controls_section();

	}

	/**
	 * Run action.
	 *
	 * Runs the Brandkrew action after form submission.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param \ElementorPro\Modules\Forms\Classes\Form_Record  $record
	 * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
	 */
	public function run( $record, $ajax_handler ) {

		$settings = $record->get( 'form_settings' );

		//  Make sure that there is a Sendy installation URL.
		if ( empty( $settings['bk_url'] ) ) {
			return;
		}

		// Make sure that there is a Sendy email field ID (required by Sendy to subscribe users).
		if ( empty( $settings['bk_email_field'] ) ) {
			return;
		}

		// Get submitted form data.
		$raw_fields = $record->get( 'fields' );

		// Normalize form data.
		$fields = [];
		foreach ( $raw_fields as $id => $field ) {
			$fields[ $id ] = $field['value'];
		}

		// Make sure the user entered an email (required by Sendy to subscribe users).
		if ( empty( $fields[ $settings['bk_email_field'] ] ) ) {
			return;
		}

		// Request data based on the param list at https://sendy.co/api
		$bk_data = [
			'Name' => $fields[ $settings['bk_name_field'] ],
			'Email' => $fields[ $settings['bk_email_field'] ],
			'Page URL' => isset( $_POST['referrer'] ) ? $_POST['referrer'] : '',
		];

		// Add name if field is mapped.
		if ( empty( $fields[ $settings['bk_name_field'] ] ) ) {
			$bk_data['name'] = $fields[ $settings['bk_name_field'] ];
		}

		// Send the request.
		wp_remote_post(
			$settings['bk_url'],
			[
				'body' => $bk_data,
			]
		);

	}

	

	/**
	 * On export.
	 *
	 * Clears Brandkrew form settings/fields when exporting.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param array $element
	 */
	public function on_export( $element ) {

		unset(
			$element['bk_url'],
			$element['bk_email_field'],
			$element['bk_name_field']
		);

		return $element;

	}

}