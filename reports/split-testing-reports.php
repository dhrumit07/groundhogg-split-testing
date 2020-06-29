<?php

namespace GroundhoggSplitTesting\Reports;

use Groundhogg\Plugin as Groundhogg;

class Split_Testing_Reports {

	protected $start;
	protected $end;

	public function __construct() {
		add_action( 'groundhogg/admin/init', [ $this, 'init' ] );
		add_action( 'groundhogg/reports/setup_default_reports/after', [ $this, 'register_new_reports' ] );
	}


	public function init() {
		Groundhogg::$instance->admin->reporting->add_custom_report_tab( [
			'name'     => __( 'Split Testing', 'groundhogg-edd' ),
			'slug'     => 'split-testing',
			'reports'  => [
				'chart_broadcast_a',
				'chart_broadcast_b',
				'table_broadcast_link_clicked_a',
				'table_broadcast_link_clicked_b',
				'table_broadcast_stats_compare'
			],
			'callback' => [ $this, 'view' ]
		] );
	}

	public function view() {
		include GROUNDHOGG_SPLIT_TESTING_PATH . 'reports/split-testing-reports-page.php';
	}

	/**
	 * Add the new reports
	 *
	 * @param $reports \Groundhogg\Reports
	 */
	public function register_new_reports( $reports ) {
		$this->start = $reports->start;
		$this->end   = $reports->end;
		$new_reports = [
			[
				'id'       => 'chart_broadcast_a',
				'callback' => [ $this, 'chart_broadcast_a' ]
			],
			[
				'id'       => 'chart_broadcast_b',
				'callback' => [ $this, 'chart_broadcast_b' ]
			],

			[
				'id'       => 'table_broadcast_link_clicked_a',
				'callback' => [ $this, 'table_broadcast_link_clicked_a' ]
			],

			[
				'id'       => 'table_broadcast_link_clicked_b',
				'callback' => [ $this, 'table_broadcast_link_clicked_b' ]
			],

			[
				'id'       => 'table_broadcast_stats_compare',
				'callback' => [ $this, 'table_broadcast_stats_compare' ]
			],


		];
		foreach ( $new_reports as $new_report ) {
			$reports->add( $new_report['id'], $new_report['callback'] );
		}
	}

	public function chart_broadcast_a() {
		$report = new Chart_Broadcast_A( $this->start, $this->end );

		return $report->get_data();
	}


	public function chart_broadcast_b() {
		$report = new Chart_Broadcast_B( $this->start, $this->end );

		return $report->get_data();
	}

	public function table_broadcast_link_clicked_a() {
		$report = new Table_Broadcast_Link_Clicked_A( $this->start, $this->end );

		return $report->get_data();
	}

	public function table_broadcast_link_clicked_b() {
		$report = new Table_Broadcast_Link_Clicked_B( $this->start, $this->end );

		return $report->get_data();
	}
public function table_broadcast_stats_compare() {
		$report = new Table_Broadcast_Stats_Compare( $this->start, $this->end );

		return $report->get_data();
	}


}