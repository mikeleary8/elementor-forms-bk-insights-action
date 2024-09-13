<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor form TrackYourLeads.co action.
 *
 * Custom Elementor form action which adds new subscriber to TrackYourLeads.co SaaS after form submission.
 *
 * @since 1.0.0
 */
class Tyl_Insights_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base {

	/**
	 * Get action name.
	 *
	 * Retrieve TrackYourLeads.co action name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'TrackYourLeads.co';
	}

	/**
	 * Get action label.
	 *
	 * Retrieve TrackYourLeads.co action label.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'TrackYourLeads.co', 'elementor-forms-tyl-action' );
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
			'section_tyl',
			[
				'label' => esc_html__( 'TrackYourLeads.co', 'elementor-forms-tyl-action' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			'tyl_url',
			[
				'label' => esc_html__( 'Webhook URL', 'elementor-forms-tyl-action' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'https://webhooks.trackyourleads.co/elementor/...',
				'description' => esc_html__( 'Enter the Webhook URL of your current Dashboard.', 'elementor-forms-tyl-action' ),
			]
		);

		$widget->add_control(
			'tyl_email_field',
			[
				'label' => esc_html__( 'Email Field ID', 'elementor-forms-tyl-action' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'Enter the email field ID',
			]
		);

		$widget->add_control(
			'tyl_name_field',
			[
				'label' => esc_html__( 'Name Field ID', 'elementor-forms-tyl-action' ),
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

		//  Make sure that there is a Webhook URL
		if ( empty( $settings['tyl_url'] ) ) {
			return;
		}

		// Make sure that there is an email field ID
		if ( empty( $settings['tyl_email_field'] ) ) {
			return;
		}

		// Get submitted form data.
		$raw_fields = $record->get( 'fields' );

		// Normalize form data.
		$fields = [];
		foreach ( $raw_fields as $id => $field ) {
			$fields[ $id ] = $field['value'];
		}

		// Make sure the user entered an email
		if ( empty( $fields[ $settings['tyl_email_field'] ] ) ) {
			return;
		}

		// Request data for the Webhook
		$tyl_data = [
			'Name' => $fields[ $settings['tyl_name_field'] ],
			'Email' => $fields[ $settings['tyl_email_field'] ],
			'Page URL' => isset( $_POST['referrer'] ) ? $_POST['referrer'] : '',
		];

		// Add name if field is mapped.
		if ( empty( $fields[ $settings['tyl_name_field'] ] ) ) {
			$tyl_data['name'] = $fields[ $settings['tyl_name_field'] ];
		}

		// Send the request.
		try {
			wp_remote_post(
				$settings['tyl_url'],
				[
					'body' => $tyl_data,
				]
			);
		} catch (Exception $e) {
			
		}

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
			$element['tyl_url'],
			$element['tyl_email_field'],
			$element['tyl_name_field']
		);

		return $element;

	}

}