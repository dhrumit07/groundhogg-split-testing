<?php

namespace GroundhoggSplitTesting\Reports;

use Groundhogg\Broadcast;
use Groundhogg\Classes\Activity;
use Groundhogg\Contact_Query;
use Groundhogg\DB\DB;
use Groundhogg\Event;
use Groundhogg\Funnel;
use Groundhogg\Plugin;
use Groundhogg\Preferences;
use Groundhogg\Reporting\New_Reports\Chart_Last_Broadcast;
use function Groundhogg\get_db;
use function Groundhogg\get_request_var;
use function Groundhogg\isset_not_empty;
use function Groundhogg\key_to_words;

class Chart_Broadcast_email_B extends Chart_Last_Broadcast {


	protected function get_last_broadcast_details() {


		$broadcast = $this->get_broadcast();

		if ( $broadcast && $broadcast->exists() ) {

			if ( absint( $broadcast->get_meta( 'split_test_email' ) ) ) {

				$counts = $this->normalize_data( $broadcast->get_report_data(absint($broadcast->get_meta( 'split_test_email' ) )) );
			} else {

				$counts = $this->normalize_data( $broadcast->get_report_data() );
			}

			$data  = [];
			$label = [];
			$color = [];

			// normalize data
			foreach ( $counts as $key => $datum ) {

				$label [] = $datum ['label'];
				$data[]   = $datum ['data'];
				$color[]  = $datum ['color'];

			}

			return [
				'label' => $label,
				'data'  => $data,
				'color' => $color
			];


		}

		return [];

	}


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