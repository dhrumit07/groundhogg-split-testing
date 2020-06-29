<?php

namespace GroundhoggSplitTesting\Reports;


use Groundhogg\Broadcast;
use Groundhogg\Classes\Activity;
use Groundhogg\Event;
use Groundhogg\Plugin;
use Groundhogg\Reporting\New_Reports\Base_Table_Report;
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

class Table_Broadcast_Stats_Compare extends Base_Table_Report {

	/**
	 * @return mixed
	 */
	protected function get_broadcast_id_a() {
//		$id = absint( get_array_var( get_request_var( 'data', [] ), 'broadcast_id' ) );
//
//		if ( ! $id ) {
//
//			$broadcasts = get_db( 'broadcasts' )->query( [
//				'status'  => 'sent',
//				'orderby' => 'send_time',
//				'order'   => 'desc',
//				'limit'   => 1
//			] );
//
//			if ( ! empty( $broadcasts ) ) {
//				$id = absint( array_shift( $broadcasts )->ID );
//			}
//		}
//
//		return $id;

		return 219;
	}

	/**
	 * @return mixed
	 */
	protected function get_broadcast_id_b() {
//		$id = absint( get_array_var( get_request_var( 'data', [] ), 'broadcast_id' ) );
//
//		if ( ! $id ) {
//
//			$broadcasts = get_db( 'broadcasts' )->query( [
//				'status'  => 'sent',
//				'orderby' => 'send_time',
//				'order'   => 'desc',
//				'limit'   => 1
//			] );
//
//			if ( ! empty( $broadcasts ) ) {
//				$id = absint( array_shift( $broadcasts )->ID );
//			}
//		}
//		return $id;

		return 219;
	}

	protected function get_table_data() {

		$broadcast_a = new Broadcast( $this->get_broadcast_id_a() );
		$stats_a     = $broadcast_a->get_report_data();

		$title_a = $broadcast_a->is_email() ? $broadcast_a->get_object()->get_subject_line() : $broadcast_a->get_title();


		$broadcast_b = new Broadcast( $this->get_broadcast_id_b() );
		$stats_b     = $broadcast_b->get_report_data();

		$title_b = $broadcast_b->is_email() ? $broadcast_b->get_object()->get_subject_line() : $broadcast_b->get_title();


		return [
			[
				'label' => __( 'Subject', 'groundhogg' ),
				'data'  => html()->wrap( $title_a, 'a', [
					'href'  => admin_page_url( 'gh_reporting', [
						'tab'       => 'broadcasts',
						'broadcast' => $broadcast_a->get_id()
					] ),
					'title' => $title_a,
//					'class' => 'number-total'
				] ),
				'data1' => html()->wrap( $title_b, 'a', [
					'href'  => admin_page_url( 'gh_reporting', [
						'tab'       => 'broadcasts',
						'broadcast' => $broadcast_b->get_id()
					] ),
					'title' => $title_b,
//					'class' => 'number-total'
				] )
			],
			[
				'label' => __( 'Sent', 'groundhogg' ),
				'data'  => date_i18n( get_date_time_format(), convert_to_local_time( $broadcast_a->get_send_time() ) ),
				'data1' => date_i18n( get_date_time_format(), convert_to_local_time( $broadcast_b->get_send_time() ) ),
			],
			[
				'label' => __( 'Total Delivered', 'groundhogg' ),
				'data'  => html()->wrap( _nf( $stats_a['sent'] ), 'a', [
					'href'  => add_query_arg(
						[
							'report' => [
								'type'   => Event::BROADCAST,
								'step'   => $broadcast_a->get_id(),
								'status' => Event::COMPLETE
							]
						],
						admin_url( sprintf( 'admin.php?page=gh_contacts' ) )
					),
					'class' => 'number-total'
				] ),
				'data1' => html()->wrap( _nf( $stats_b['sent'] ), 'a', [
					'href'  => add_query_arg(
						[
							'report' => [
								'type'   => Event::BROADCAST,
								'step'   => $broadcast_b->get_id(),
								'status' => Event::COMPLETE
							]
						],
						admin_url( sprintf( 'admin.php?page=gh_contacts' ) )
					),
					'class' => 'number-total'
				] )
			],
			[
				'label' => __( 'Opens', 'groundhogg' ),
				'data'  => html()->wrap( _nf( $stats_a['opened'] ) . ' (' . percentage( $stats_a['sent'], $stats_a['opened'] ) . '%)', 'a', [
					'href'  => add_query_arg(
						[
							'activity' => [
								'activity_type' => Activity::EMAIL_OPENED,
								'step_id'       => $broadcast_a->get_id(),
								'funnel_id'     => $broadcast_a->get_funnel_id()
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
								'step_id'       => $broadcast_b->get_id(),
								'funnel_id'     => $broadcast_b->get_funnel_id()
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
								'step_id'       => $broadcast_a->get_id(),
								'funnel_id'     => $broadcast_a->get_funnel_id()
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
								'step_id'       => $broadcast_b->get_id(),
								'funnel_id'     => $broadcast_b->get_funnel_id()
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
			[
				'label' => __( 'Unopened', 'groundhogg' ),
				'data'  => _nf( $stats_a['unopened'] ) . ' (' . percentage( $stats_a['sent'], $stats_a['unopened'] ) . '%)',
				'data1' => _nf( $stats_b['unopened'] ) . ' (' . percentage( $stats_b['sent'], $stats_b['unopened'] ) . '%)'
			],
			[
				'label' => __( 'Unsubscribed', 'groundhogg' ),
				'data'  => html()->wrap( _nf( $stats_a['unsubscribed'] ) . ' (' . percentage( $stats_a['sent'], $stats_a['unsubscribed'] ) . '%)', 'a', [
					'href'  => add_query_arg(
						[
							'activity' => [
								'activity_type' => Activity::UNSUBSCRIBED,
								'step_id'       => $broadcast_a->get_id(),
								'funnel_id'     => $broadcast_a->get_funnel_id()
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
								'step_id'       => $broadcast_b->get_id(),
								'funnel_id'     => $broadcast_b->get_funnel_id()
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
			__( 'Broadcast A' ),
			__( 'Broadcast B' ),
		];
	}
}