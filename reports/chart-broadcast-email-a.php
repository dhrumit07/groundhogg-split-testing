<?php

namespace GroundhoggSplitTesting\Reports;

use Groundhogg\Broadcast;
use Groundhogg\Classes\Activity;
use Groundhogg\Reporting\New_Reports\Chart_Last_Broadcast;
use function Groundhogg\get_db;
use function Groundhogg\get_request_var;

class Chart_Broadcast_Email_A extends Chart_Last_Broadcast {




	protected function normalize_data( $stats ) {

		if ( empty( $stats ) ) {
			return $stats;
		}

		/*
		* create array  of data ..
		*/
		$dataset = array();

		$dataset[] = array(
			'label' => _x( 'Opened', 'stats', 'groundhogg' ),
			'data'  => $stats['opened'] - $stats['clicked'],
			'url'   => add_query_arg(
				[
					'activity' => [
						'activity_type' => Activity::EMAIL_OPENED,
						'step_id'       => $stats['id'],
						'funnel_id'     => Broadcast::FUNNEL_ID,
						'email_id'      => $stats['email_id']
					]
				],
				admin_url( sprintf( 'admin.php?page=gh_contacts' ) )
			),
			'color' => $this->get_random_color()
		);

		$dataset[] = array(
			'label' => _x( 'Clicked', 'stats', 'groundhogg' ),
			'data'  => $stats['clicked'],
			'url'   => add_query_arg(
				[
					'activity' => [
						'activity_type' => Activity::EMAIL_CLICKED,
						'step_id'       => $stats['id'],
						'funnel_id'     => Broadcast::FUNNEL_ID,
						'email_id'      => $stats['email_id']
					]
				],
				admin_url( sprintf( 'admin.php?page=gh_contacts' ) )
			),
			'color' => $this->get_random_color()
		);

		$dataset[] = array(
			'label' => _x( 'Unopened', 'stats', 'groundhogg' ),
			'data'  => $stats['unopened'],
			'url'   => '#',
			'color' => $this->get_random_color()
		);

		return $dataset;
	}

}