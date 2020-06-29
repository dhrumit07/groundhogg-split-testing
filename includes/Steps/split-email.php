<?php

namespace GroundhoggSplitTesting\Steps;

use Groundhogg\Classes\Activity;
use Groundhogg\Email;
use Groundhogg\Reporting\New_Reports\Chart_Draw;
use Groundhogg\Reporting\Reporting;
use Groundhogg\Steps\Actions\Action;
use Groundhogg\Utils\Graph;
use function Groundhogg\get_array_var;
use Groundhogg\Preferences;
use Groundhogg\Contact;
use Groundhogg\Contact_Query;
use Groundhogg\Event;
use function Groundhogg\get_db;
use function Groundhogg\get_request_var;
use function Groundhogg\isset_not_empty;
use Groundhogg\HTML;
use function Groundhogg\html;
use Groundhogg\Plugin;
use function Groundhogg\percentage;
use function Groundhogg\search_and_replace_domain;
use Groundhogg\Step;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Send Email
 *
 * This will send an email to the contact using WP_MAIL
 *
 * @package     Elements
 * @subpackage  Elements/Actions
 * @author      Adrian Tobey <info@groundhogg.io>
 * @copyright   Copyright (c) 2018, Groundhogg Inc.
 * @license     https://opensource.org/licenses/GPL-3.0 GNU Public License v3
 * @see         WPGH_Email::send()
 * @since       File available since Release 0.9
 */
class Split_Email extends Action {


	/**
	 * @return string
	 */
	public function get_help_article() {
//		return 'https://docs.groundhogg.io/docs/builder/actions/send-email/';
	}

	/**
	 * Get the element name
	 *
	 * @return string
	 */
	public function get_name() {
		return _x( 'Split Email', 'step_name', 'groundhogg' );
	}

	/**
	 * Get the element type
	 *
	 * @return string
	 */
	public function get_type() {
		return 'split_email';
	}

	/**
	 * Get the description
	 *
	 * @return string
	 */
	public function get_description() {
		return _x( 'Send an split emails to contact.', 'step_description', 'groundhogg' );
	}

	/**
	 * Get the icon URL
	 *
	 * @return string
	 */
	public function get_icon() {
		return GROUNDHOGG_ASSETS_URL . '/images/funnel-icons/send-email.png';
	}

	public function admin_scripts() {
//		wp_enqueue_script( 'groundhogg-funnel-email' );
//		wp_localize_script( 'groundhogg-funnel-email', 'EmailStep', array(
//			'edit_email_path'     => admin_url( 'admin.php?page=gh_emails&action=edit' ),
//			'add_email_path'      => admin_url( 'admin.php?page=gh_emails&action=add' ),
//			'save_changes_prompt' => _x( "You have changes which have not been saved. Are you sure you want to exit?", 'notice', 'groundhogg' ),
//		) );
	}

	/**
	 * Display the settings
	 *
	 * @param $step Step
	 */
	public function settings( $step ) {

		$html = Plugin::$instance->utils->html;

		$email_id = $this->get_setting( 'email_id' );
		$email    = Plugin::$instance->utils->get_email( $email_id );

		$html->start_form_table();

		$html->start_row();

		$html->th( __( 'Select an email to send:', 'groundhogg' ) );
		$html->td( [
			// EMAIL ID DROPDOWN
			$html->dropdown_emails( [
				'name'     => $this->setting_name_prefix( 'email_id' ),
				'id'       => $this->setting_id_prefix( 'email_id' ),
				'selected' => $this->get_setting( 'email_id' ),
			] ),
			'&nbsp',
			$html->button( [
				'title' => 'set as Winner',
				'text'  => _x( 'Set as Winner', 'action', 'groundhogg' ),
				'class' => 'button button-secondary',
				'name'  => $this->setting_name_prefix( 'winner_a' ),
				'id'    => $this->setting_id_prefix( 'winner_a' ),
				'type'  => 'submit'
			] ),
			// ROW ACTIONS
			"<div class=\"row-actions\">",
			// EDIT EMAIL
			$html->button( [
				'title' => 'Edit Email',
				'text'  => _x( 'Edit Email', 'action', 'groundhogg' ),
				'class' => 'button button-primary edit-email',
			] ),
			'&nbsp;',
			// ADD NEW EMAIL
			$html->button( [
				'title' => 'Create New Email',
				'text'  => _x( 'Create New Email', 'action', 'groundhogg' ),
				'class' => 'button button-secondary add-email',
			] ),
			"</div>",

		] );

		$html->end_row();


		$html->start_row();

		$html->th( __( 'Split Test email:', 'groundhogg' ) );
		$html->td( [
			// EMAIL ID DROPDOWN
			$html->dropdown_emails( [
				'name'     => $this->setting_name_prefix( 'split_email_id' ),
				'id'       => $this->setting_id_prefix( 'split_email_id' ),
				'selected' => $this->get_setting( 'split_email_id' ),
			] ),
			'&nbsp',
			$html->button( [
				'title' => 'set as Winner',
				'text'  => _x( 'Set as Winner', 'action', 'groundhogg' ),
				'class' => 'button button-secondary',
				'name'  => $this->setting_name_prefix( 'winner_b' ),
				'id'    => $this->setting_id_prefix( 'winner_b' ),
				'value' => 'winner_b',
				'type'  => 'submit'
			] ),

			"<div class=\"row-actions\">",
			// EDIT EMAIL
			$html->button( [
				'title' => 'Edit Email',
				'text'  => _x( 'Edit Email', 'action', 'groundhogg' ),
				'class' => 'button button-primary edit-email',
			] ),
			'&nbsp;',
			// ADD NEW EMAIL
			$html->button( [
				'title' => 'Create New Email',
				'text'  => _x( 'Create New Email', 'action', 'groundhogg' ),
				'class' => 'button button-secondary add-email',
			] ),
			"</div>",

		] );

		$html->end_row();

		$html->end_form_table();
		$html->end_form_table();
	}


	/**
	 * Save the settings
	 *
	 * @param $step Step
	 */
	public function save( $step ) {

		wp_send_json( $_POST );

		$email_id = absint( $this->get_posted_data( 'email_id' ) );

		$this->save_setting( 'email_id', $email_id );

		$email = new Email( $this->get_setting( 'email_id' ) );


		if ( ! $email->exists() ) {
			$this->add_error( 'email_dne', __( 'You have not selected an email to send in one of your steps.', 'groundhogg' ) );
		}

		if ( ( $email->is_draft() && $step->get_funnel()->is_active() ) ) {
			$this->add_error( 'email_in_draft_mode', __( 'You still have emails in draft mode! These emails will not be sent and will cause automation to stop.' ) );
		}

		$this->save_setting( 'split_email_id', absint( $this->get_posted_data( 'split_email_id' ) ) );

		if ( $this->get_posted_data( 'winner_a' ) ) {
			$this->save_setting( 'winner', 'a' );
		}


		if ( $this->get_posted_data( 'winner_b' ) ) {
			$this->save_setting( 'winner', 'b' );
		}

	}

	/**
	 * Process the apply note step...
	 *
	 * @param $contact Contact
	 * @param $event Event
	 *
	 * @return bool|\WP_Error
	 */
	public function run( $contact, $event ) {


		$email_id = absint( $this->get_setting( 'email_id' ) );
//
		$email = Plugin::$instance->utils->get_email( $email_id );
//
		if ( absint( $this->get_setting( 'last_send' ) ) == absint( $email->get_id() ) ) {
			$email = Plugin::$instance->utils->get_email( absint( $this->get_setting( 'split_email_id' ) ) );
			//or
//			$email =  new Email(absint( $this->get_setting( 'split_email_id' ) )) ;
		}

		if ( ! $email ) {
			return new \WP_Error( 'email_dne', 'Invalid email ID provided.' );
		}

		$this->save_setting( 'last_send', $email->get_id() );

		return $email->send( $contact, $event );
	}


}