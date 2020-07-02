<?php

namespace GroundhoggSplitTesting\Reports;


use Groundhogg\Broadcast;
use Groundhogg\Classes\Activity;
use Groundhogg\Plugin;
use Groundhogg\Reporting\New_Reports\Table_Broadcast_Link_Clicked;
use function Groundhogg\generate_referer_hash;
use function Groundhogg\get_array_var;
use function Groundhogg\get_db;
use function Groundhogg\get_request_var;
use function Groundhogg\html;
use function Groundhogg\percentage;

class Table_Broadcast_Link_Clicked_B extends Table_Broadcast_Link_Clicked {


	protected function get_table_data() {

		$broadcast = new Broadcast( $this->get_broadcast_id() );
		$split_email_id = absint(  $broadcast->get_meta( 'split_test_email' ) ) ;

		$activity = get_db( 'activity' )->query( [
			'funnel_id'     => $broadcast->get_funnel_id(),
			'step_id'       => $broadcast->get_id(),
			'activity_type' => Activity::EMAIL_CLICKED,
			'email_id'      => $split_email_id
		] );

		$links = [];

		foreach ( $activity as $event ) {

			if ( ! isset( $links[ $event->referer_hash ] ) ) {
				$links[ $event->referer_hash ] = [
					'referer'  => $event->referer,
					'hash'     => $event->referer_hash,
					'contacts' => [],
					'uniques'  => 0,
					'clicks'   => 0,
				];
			}

			$links[ $event->referer_hash ]['clicks'] ++;
			$links[ $event->referer_hash ]['contacts'][] = $event->contact_id;
			$links[ $event->referer_hash ]['uniques']    = count( array_unique( $links[ $event->referer_hash ]['contacts'] ) );
		}

		if ( empty( $links ) ) {
			return [];
		}


		$data = [];
		foreach ( $links as $hash => $link ) {
			$data[] = [
				'label'   => html()->wrap( $link['referer'], 'a', [
					'href'   => $link['referer'],
					'class'  => 'number-total',
					'title'  => $link['referer'],
					'target' => '_blank',
				] ),
				'uniques' => html()->wrap( $link['uniques'], 'a', [
					'href'  => add_query_arg(
						[
							'activity' => [
								'activity_type' => Activity::EMAIL_CLICKED,
								'step_id'       => $broadcast->get_id(),
								'funnel_id'     => $broadcast->get_funnel_id(),
								'referer_hash'  => $hash,
								'email_id'      => $split_email_id
							]
						],
						admin_url( sprintf( 'admin.php?page=gh_contacts' ) )
					),
					'class' => 'number-total'
				] ),
				'clicks'  => html()->wrap( $link['clicks'], 'span', [ 'class' => 'number-total' ] ),
			];
		}

		return $data;


	}



}