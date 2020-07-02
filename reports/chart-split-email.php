<?php

namespace GroundhoggSplitTesting\Reports;

use Groundhogg\Broadcast;
use Groundhogg\Classes\Activity;
use Groundhogg\Email;
use Groundhogg\Reporting\New_Reports\Base_Chart_Report;
use Groundhogg\Reporting\New_Reports\Chart_Last_Broadcast;
use Groundhogg\Step;
use function Groundhogg\get_db;
use function Groundhogg\get_request_var;

abstract class Chart_Split_Email extends Base_Chart_Report {

	protected function get_type() {
		return 'doughnut';
	}

	protected function get_datasets() {

		if ( ! $this->get_step_id() ){
			return [];
		}

		$data = $this->get_email_stats();

		return [
			'labels'   => $data['label'],
			'datasets' => [
				[
					'data'            => $data['data'],
					'backgroundColor' => $data['color']
				]
			]
		];
	}

	protected function get_options() {
		return $this->get_pie_chart_options();
	}


	protected function get_step_id() {
		return absint( get_request_var( 'data' )['split_step'] );

	}


	abstract function get_step_email_id();

	protected function get_email_stats() {

		$email = new Email($this->get_step_email_id() );
		$stats = $email->get_email_stats( $this->start, $this->end, [absint($this->get_step_id())] );
		$counts = $this->normalize_data( $stats );


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
//			'url'   => add_query_arg(
//				[
//					'activity' => [
//						'activity_type' => Activity::EMAIL_OPENED,
//						'step_id'       => $stats['id'],
//						'funnel_id'     => Broadcast::FUNNEL_ID
//					]
//				],
//				admin_url( sprintf( 'admin.php?page=gh_contacts' ) )
//			),
			'color' => $this->get_random_color()
		);

		$dataset[] = array(
			'label' => _x( 'Clicked', 'stats', 'groundhogg' ),
			'data'  => $stats['clicked'],
//			'url'   => add_query_arg(
//				[
//					'activity' => [
//						'activity_type' => Activity::EMAIL_CLICKED,
//						'step_id'       => $stats['id'],
//						'funnel_id'     => Broadcast::FUNNEL_ID
//					]
//				],
//				admin_url( sprintf( 'admin.php?page=gh_contacts' ) )
//			),
			'color' => $this->get_random_color()
		);

		$dataset[] = array(
			'label' => _x( 'Unopened', 'stats', 'groundhogg' ),
			'data'  => $stats['sent'] - $stats['opened'],
//			'url'   => '#',
			'color' => $this->get_random_color()
		);

		return $dataset;
	}


}