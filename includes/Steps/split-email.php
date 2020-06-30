<?php

namespace GroundhoggSplitTesting\Steps;

use Groundhogg\Classes\Activity;
use Groundhogg\Email;
use Groundhogg\Reporting\New_Reports\Chart_Draw;
use Groundhogg\Reporting\Reporting;
use Groundhogg\Steps\Actions\Action;
use Groundhogg\Steps\Actions\Send_Email;
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
class Split_Email extends Send_Email {


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


	/**
	 * Display the settings
	 *
	 * @param $step Step
	 */
	public function settings( $step ) {
		$html = Plugin::$instance->utils->html;

		if ( $this->get_setting( 'winner' ) ) {
			parent::settings( $step );

			$html->start_form_table();

			$html->start_row();

			$html->th( __( 'Enable split email', 'groundhogg' ) );
			$html->td( [
				// EMAIL ID DROPDOWN
				$html->checkbox( [
					'name'     => $this->setting_name_prefix( 'enable_split' ),
					'id'       => $this->setting_id_prefix( 'enable_split' ),
					'label'     => __('Enabled')
				] ),

			] );

			$html->end_row();
			$html->end_form_table();


		} else {


			$html->start_form_table();

			$html->start_row();

			$html->th( __( 'Split Email (Email A):', 'groundhogg' ) );
			$html->td( [
				// EMAIL ID DROPDOWN
				$html->dropdown_emails( [
					'name'     => $this->setting_name_prefix( 'email_a_id' ),
					'id'       => $this->setting_id_prefix( 'email_a_id' ),
					'selected' => $this->get_setting( 'email_a_id' ),
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
				$html->wrap( [
					$html->input( [

						'name'  => $this->setting_name_prefix( 'winner' ),
						'id'    => $this->setting_id_prefix( 'winner' ),
						'type'  => 'radio',
						'value' => 'winner_a',
					] ),
					__( 'Declare this email as winner' ),
				], 'p' ),

			] );

			$html->end_row();


			$html->start_row();

			$html->th( __( 'Split Email (Email B):', 'groundhogg' ) );
			$html->td( [
				// EMAIL ID DROPDOWN
				$html->dropdown_emails( [
					'name'     => $this->setting_name_prefix( 'email_b_id' ),
					'id'       => $this->setting_id_prefix( 'email_b_id' ),
					'selected' => $this->get_setting( 'email_b_id' ),
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

				$html->wrap( [
					$html->input( [

						'name'  => $this->setting_name_prefix( 'winner' ),
						'id'    => $this->setting_id_prefix( 'winner' ),
						'type'  => 'radio',
						'value' => 'winner_b',
					] ),
					__( 'Declare this email as winner' )
				], 'p' ),

			] );

			$html->end_row();

			$html->end_form_table();
		}

	}


	/**
	 * Save the settings
	 *
	 * @param $step Step
	 */
	public function save( $step ) {

		if ( $this->get_setting( 'winner' ) ) {
			parent::save( $step );
			if ( $this->get_posted_data( 'enable_split' ) ){
				$this->save_setting( 'winner' , '' );
			}

		} else {


			$email_a_id = absint( $this->get_posted_data( 'email_a_id' ) );

			$this->save_setting( 'email_a_id', $email_a_id );

			$email = new Email( $this->get_setting( 'email_a_id' ) );


			if ( ! $email->exists() ) {
				$this->add_error( 'email_dne', __( 'You have not selected an email to send in one of your steps.', 'groundhogg' ) );
			}

			if ( ( $email->is_draft() && $step->get_funnel()->is_active() ) ) {
				$this->add_error( 'email_in_draft_mode', __( 'You still have emails in draft mode! These emails will not be sent and will cause automation to stop.' ) );
			}


			$email_b_id = absint( $this->get_posted_data( 'email_b_id' ) );

			$this->save_setting( 'email_b_id', $email_b_id );

			$email = new Email( $this->get_setting( 'email_b_id' ) );


			if ( ! $email->exists() ) {
				$this->add_error( 'email_dne', __( 'You have not selected an email to send in one of your steps.', 'groundhogg' ) );
			}

			if ( ( $email->is_draft() && $step->get_funnel()->is_active() ) ) {
				$this->add_error( 'email_in_draft_mode', __( 'You still have emails in draft mode! These emails will not be sent and will cause automation to stop.' ) );
			}


			if ( $this->get_posted_data( 'winner' ) ) {

				if ( $this->get_posted_data( 'winner' ) == 'winner_a' ) {
					$this->save_setting( 'email_id', $this->get_setting( 'email_a_id' ) );
					$this->save_setting( 'winner', $this->get_setting( 'email_a_id' ) );
				} elseif ( $this->get_posted_data( 'winner' ) == 'winner_b' ) {

					$this->save_setting( 'email_id', $this->get_setting( 'email_b_id' ) );
					$this->save_setting( 'winner', $this->get_setting( 'email_b_id' ) );
				}
			}
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

		if ( $this->get_setting( 'winner' ) ) {
			return parent::run( $contact, $event );
		} else {


			$sent       = absint( $this->get_setting( 'sent' ) );
			$email_a_id = absint( $this->get_setting( 'email_a_id' ) );
			$email_b_id = absint( $this->get_setting( 'email_b_id' ) );
			$email_id   = $sent % 2 === 0 ? $email_b_id : $email_a_id;
			$email      = new Email( $email_id );
			$this->save_setting( 'sent', $sent + 1 );

			return $email->send( $contact, $event );
		}


	}


}