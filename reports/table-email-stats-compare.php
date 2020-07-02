<?php

namespace GroundhoggSplitTesting\Reports;


use Groundhogg\Broadcast;
use Groundhogg\Classes\Activity;
use Groundhogg\Email;
use Groundhogg\Event;
use Groundhogg\Plugin;
use Groundhogg\Reporting\New_Reports\Base_Email_Performance_Table_Report;
use Groundhogg\Reporting\New_Reports\Base_Table_Report;
use Groundhogg\Step;
use function Groundhogg\_nf;
use function Groundhogg\admin_page_url;
use function Groundhogg\convert_to_local_time;
use function Groundhogg\get_array_var;
use function Groundhogg\get_date_time_format;
use function Groundhogg\get_db;
use function Groundhogg\get_request_var;
use function Groundhogg\html;
use function Groundhogg\key_to_words;
use function Groundhogg\percentage;

class Table_Email_Stats_Compare extends Base_Email_Performance_Table_Report {


	protected function get_step_id() {
		return  absint( get_request_var( 'data' )['split_step'] )   ;

	}

	protected function get_table_data() {

		if ( ! $this->get_step_id() ) {
			return [];
		}


		$step = new Step( $this->get_step_id() );

		$email_a = new Email( absint( $step->get_meta( 'email_a_id' ) ) );
		$email_b = new Email( absint( $step->get_meta( 'email_b_id' ) ) );


		$stats_a = $email_a->get_email_stats( $this->start, $this->end, [$step->get_id()] );
		$stats_b = $email_b->get_email_stats( $this->start, $this->end, [$step->get_id()] );




		return [
			[
				'label' => __( 'Subject', 'groundhogg' ),
				'data'  => $email_a->get_subject_line(),
				'data1' => $email_b->get_subject_line(),
			],
			[
				'label' => __( 'Total Delivered', 'groundhogg' ),
				'data'  => _nf( $stats_a['sent'] ),
				'data1' => _nf( $stats_b['sent'] ),
			],
			[
				'label' => __( 'Opens', 'groundhogg' ),
				'data'  => html()->wrap( _nf( $stats_a['opened'] ) . ' (' . percentage( $stats_a['sent'], $stats_a['opened'] ) . '%)', 'a', [
					'href'  => add_query_arg(
						[
							'activity' => [
								'activity_type' => Activity::EMAIL_OPENED,
								'step_id'       => $step->get_id(),
								'funnel_id'     => $step->get_funnel_id(),
								'email_id'      => $email_a->get_id()
							]
						],
						admin_url( sprintf( 'admin.php?page=gh_contacts' ) )
					),
					'class' => 'number-total'
				] ),
				'data1' => html()->wrap( _nf( $stats_b['opened'] ) . ' (' . percentage( $stats_b['sent'], $stats_b['opened'] ) . '%)', 'a', [
					'href'  => add_query_arg(
						[
							'activity' => [
								'activity_type' => Activity::EMAIL_OPENED,
								'step_id'       => $step->get_id(),
								'funnel_id'     => $step->get_funnel_id(),
								'email_id'      => $email_b->get_id()
							]
						],
						admin_url( sprintf( 'admin.php?page=gh_contacts' ) )
					),
					'class' => 'number-total'
				] )
			],
			[
				'label' => __( 'Total Clicks', 'groundhogg' ),
				'data'  => html()->wrap( _nf( $stats_a['all_clicks'] ), 'span', [
					'class' => 'number-total'
				] ),
				'data1' => html()->wrap( _nf( $stats_b['all_clicks'] ), 'span', [
					'class' => 'number-total'
				] )
			],
			[
				'label' => __( 'Unique Clicks', 'groundhogg' ),
				'data'  => html()->wrap( _nf( $stats_a['clicked'] ) . ' (' . percentage( $stats_a['sent'], $stats_a['clicked'] ) . '%)', 'a', [
					'href'  => add_query_arg(
						[
							'activity' => [
								'activity_type' => Activity::EMAIL_CLICKED,
								'step_id'       => $step->get_id(),
								'funnel_id'     => $step->get_funnel_id(),
								'email_id'      => $email_a->get_id()
							]
						],
						admin_url( sprintf( 'admin.php?page=gh_contacts' ) )
					),
					'class' => 'number-total'
				] ),
				'data1' => html()->wrap( _nf( $stats_b['clicked'] ) . ' (' . percentage( $stats_b['sent'], $stats_b['clicked'] ) . '%)', 'a', [
					'href'  => add_query_arg(
						[
							'activity' => [
								'activity_type' => Activity::EMAIL_CLICKED,
								'step_id'       => $step->get_id(),
								'funnel_id'     => $step->get_funnel_id(),
								'email_id'      => $email_b->get_id()
							]
						],
						admin_url( sprintf( 'admin.php?page=gh_contacts' ) )
					),
					'class' => 'number-total'
				] )
			],
			[
				'label' => __( 'Click Thru Rate', 'groundhogg' ),
				'data'  => percentage( $stats_a['opened'], $stats_a['clicked'] ) . '%',
				'data1' => percentage( $stats_b['opened'], $stats_b['clicked'] ) . '%'
			],
//			[
//				'label' => __( 'Unopened', 'groundhogg' ),
//				'data'  => _nf( $stats_a['unopened'] ) . ' (' . percentage( $stats_a['sent'], $stats_a['unopened'] ) . '%)',
//				'data1' => _nf( $stats_b['unopened'] ) . ' (' . percentage( $stats_b['sent'], $stats_b['unopened'] ) . '%)'
//			],
			[
				'label' => __( 'Unsubscribed', 'groundhogg' ),
				'data'  => html()->wrap( _nf( $stats_a['unsubscribed'] ) . ' (' . percentage( $stats_a['sent'], $stats_a['unsubscribed'] ) . '%)', 'a', [
					'href'  => add_query_arg(
						[
							'activity' => [
								'activity_type' => Activity::UNSUBSCRIBED,
								'step_id'       => $step->get_id(),
								'funnel_id'     => $step->get_funnel_id(),
								'email_id'      => $email_a->get_id()
							]
						],
						admin_url( sprintf( 'admin.php?page=gh_contacts' ) )
					),
					'class' => 'number-total'
				] ),
				'data1' => html()->wrap( _nf( $stats_b['unsubscribed'] ) . ' (' . percentage( $stats_b['sent'], $stats_b['unsubscribed'] ) . '%)', 'a', [
					'href'  => add_query_arg(
						[
							'activity' => [
								'activity_type' => Activity::UNSUBSCRIBED,
								'step_id'       => $step->get_id(),
								'funnel_id'     => $step->get_funnel_id(),
								'email_id'      => $email_b->get_id()
							]
						],
						admin_url( sprintf( 'admin.php?page=gh_contacts' ) )
					),
					'class' => 'number-total'
				] )
			],

		];

	}

	protected function normalize_datum( $item_key, $item_data ) {
		// TODO: Implement normalize_datum() method.
	}

	function get_label() {
		return [
			__( 'States' ),
			__( 'Email A' ),
			__( 'Email B' ),
		];
	}

	protected function should_include( $sent, $opened, $clicked ) {
		// TODO: Implement should_include() method.
	}
}