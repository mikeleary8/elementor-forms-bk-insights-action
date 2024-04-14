<?php
/**
 * Plugin Name: Elementor Forms Brandkrew Insights Action
 * Description: Custom addon which adds new subscriber to Brandkrew Insights after form submission.
 * Plugin URI:  https://elementor.com/
 * Version:     1.0.0
 * Author:      Elementor Developer
 * Author URI:  https://developers.elementor.com/
 * Text Domain: elementor-forms-bk-action
 *
 * Requires Plugins: elementor
 * Elementor tested up to: 3.20.0
 * Elementor Pro tested up to: 3.20.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Add new subscriber to Brandkrew Insights.
 *
 * @since 1.0.0
 * @param ElementorPro\Modules\Forms\Registrars\Form_Actions_Registrar $form_actions_registrar
 * @return void
 */

function add_new_bk_insights_form_action( $form_actions_registrar ) {

	include_once( __DIR__ .  '/form-actions/bk-insights.php' );

	$form_actions_registrar->register( new Bk_Insights_Action_After_Submit() );

}

add_action( 'elementor_pro/forms/actions/register', 'add_new_bk_insights_form_action' );