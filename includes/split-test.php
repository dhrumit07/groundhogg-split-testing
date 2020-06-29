<?php


namespace GroundhoggSplitTesting;

use Groundhogg\Broadcast;
use Groundhogg\Contact;
use Groundhogg\Email;
use Groundhogg\Event;
use Groundhogg\Plugin;
use function Groundhogg\get_array_var;
use function Groundhogg\get_request_var;
use function Groundhogg\html;

class Split_Test {
	public function __construct() {
		add_action( 'groundhogg/admin/broadcast/add/after/email_dropdown', [ $this, 'add_split_test_email_settings' ] );
		add_action( 'groundhogg/admin/broadcast/scheduled', [ $this, 'add_split_test_info' ], 10, 2 );
		add_filter( 'groundhogg/admin/bulkjobs/broadcast/schedule_broadcast/args', [ $this, 'modify_args' ], 10 );
		add_filter( 'groundhogg/broadcast/email/object', [ $this, 'email_object' ], 10, 4 );
	}

	/**
	 *
	 *
	 * @param $email Email
	 * @param $broadcst Broadcast
	 * @param $contact Contact
	 * @param $event Event

	 * @return Email
	 */
	public function email_object( $email, $broadcst, $contact, $event ) {

		return new Email( absint( $event->get_email_id() ) );
	}

	protected $count = 0;

	function modify_args( $args ) {

		$broadcast = new Broadcast( absint( get_array_var( $args, 'step_id' ) ) );

		$split_email = $broadcast->get_meta( 'split_test_email' );

		if ( $this->count % 2 === 0 ) {
			$args['email_id'] = $split_email;
		}
		$this->count ++;

		return $args;
	}


	/**
	 * Add split test email meta
	 *
	 * @param $broadcast_id
	 * @param $config
	 */
	function add_split_test_info( $broadcast_id, $config ) {
		if ( get_request_var( 'split_test' ) && get_request_var( 'split_test_email' ) ) {
			$broadcast = new Broadcast( $broadcast_id );
			$broadcast->add_meta( 'split_test_email', absint( get_request_var( 'split_test_email' ) ) );
		}
	}

	/**
	 * Add controls in the  broadcast page
	 */
	function add_split_test_email_settings() {
		?>
        <tr class="form-field term-email-wrap">
            <th scope="row"><label for="email_id"><?php _e( 'Split Test', 'groundhogg' ) ?></label></th>
            <td><?php

				echo html()->checkbox( [
					'label'    => _x( 'Enable', 'action', 'groundhogg' ),
					'name'     => 'split_test',
					'id'       => 'split-test',
					'value'    => '1',
					'checked'  => false,
					'required' => false
				] );

				?>
                <p class="description"><?php _e( 'Enabling this option will send two emails to contact.', 'groundhogg' ) ?></p>

            </td>
        </tr>
        <tr class="form-field term-email-wrap">
            <th scope="row"><label for="email_id"><?php _e( 'Split Test Email', 'groundhogg' ) ?></label></th>
            <td><?php
				echo Plugin::$instance->utils->html->dropdown_emails( [
					'id'   => 'split-test-email',
					'name' => 'split_test_email'
				] );
				?>
                <div class="row-actions">
                    <a target="_blank" class="button button-secondary"
                       href="<?php echo admin_url( 'admin.php?page=gh_emails&action=add' ); ?>"><?php _e( 'Create New Email', 'groundhogg' ); ?></a>
                </div>
                <p class="description"><?php _e( 'Email to test with the split test', 'groundhogg' ) ?></p>
            </td>
        </tr>
		<?php
	}


}