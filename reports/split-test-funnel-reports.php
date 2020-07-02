<?php

namespace GroundhoggSplitTesting\Reports;

use Groundhogg\Funnel;
use Groundhogg\Plugin;
use Groundhogg\Plugin as Groundhogg;
use Groundhogg\Reports;
use function Groundhogg\get_array_var;
use function Groundhogg\get_cookie;
use function Groundhogg\get_post_var;
use function Groundhogg\get_request_var;
use function Groundhogg\get_url_var;
use function Groundhogg\html;

class Split_Test_Funnel_Reports {

	public function __construct() {

		add_filter( 'groundhogg/admin/reports/reports_to_load', [ $this, 'add_in_pages' ], 10 );

		add_action( 'groundhogg/admin/reports/pages/funnels/after_reports', [ $this, 'funnel_html' ], 10 );

		add_action( 'groundhogg/reports/setup_default_reports/after', [ $this, 'add_in_reports' ], 10, 1 );

		add_action( 'wp_ajax_groundhogg_refresh_split_steps', [ $this, 'refresh_split_steps' ] );

	}


	function refresh_split_steps() {

		$funnel = new Funnel( absint( get_post_var( 'funnel_id' ) ) );

		if ( $funnel && $funnel->get_steps( [ 'step_type' => 'split_email' ] ) ) {
			foreach ( $funnel->get_steps( [ 'step_type' => 'split_email' ] ) as $split_step ) {
				$options[ $split_step->get_id() ] = $split_step->get_title();
			}
			wp_send_json( [
				'options' => $options
			] );
		}

		wp_send_json( [
			'options' => [
				0 => __( 'No Spilt Email Action Found' )
			]
		] );


	}


	/**
	 * @param $reports array
	 *
	 * @return array
	 */
	function add_in_pages( $reports ) {

		$reports ['funnels'] = array_merge( [ 'table_email_stats_compare' ], get_array_var( $reports, 'funnels' ) );
		$reports ['funnels'] = array_merge( [ 'chart_split_email_a' ], get_array_var( $reports, 'funnels' ) );
		$reports ['funnels'] = array_merge( [ 'chart_split_email_b' ], get_array_var( $reports, 'funnels' ) );

		return $reports;

	}


	function get_funnel_id() {
//		if ( get_request_var( 'funnel' ) ) {
//			return absint( get_request_var( 'funnel' ) );
//		}

		return Plugin::$instance->reporting->get_report( 'complete_funnel_activity' )->get_funnel_id();

	}

	function funnel_html() {

		wp_enqueue_script( 'groundhogg-admin-reporting-split-testing' );


		$funnel = new Funnel( $this->get_funnel_id() );

		$options = [];
		foreach ( $funnel->get_steps( [ 'step_type' => 'split_email' ] ) as $split_step ) {
			$options[ $split_step->get_id() ] = $split_step->get_title();
		}

		?>

        <div style="margin-top: 10px">
            <div style="float: left">
                <h1><?php _e( 'Split Email Stats', 'groundhogg' ); ?></h1>

            </div>

            <div class="actions" style="float: right; margin-block-start: 0.83em; margin-block-end: 0.83em;">
				<?php
				echo html()->dropdown( [
					'name'        => 'split_step',
					'id'          => 'split-step',
					'class'       => 'post-data',
					'options'     => $options,
					'option_none' => false,
				] );
				?>

            </div>
        </div>
        <div style="clear: both;"></div>

        <div class="groundhogg-chart-wrapper">
            <div class="groundhogg-chart">
                <h2 class="title"><?php _e( 'Split Email A', 'groundhogg' ); ?></h2>
                <div style="width: 100%; padding: ">
                    <div class="float-left" style="width:60%">
                        <canvas id="chart_split_email_a"></canvas>
                    </div>
                    <div class="float-left" style="width:40%">
                        <div id="chart_split_email_a_legend" class="chart-legend"></div>
                    </div>
                </div>
            </div>
            <div class="groundhogg-chart">
                <h2 class="title"><?php _e( 'Split Email B', 'groundhogg' ); ?></h2>
                <div style="width: 100%; padding: ">
                    <div class="float-left" style="width:60%">
                        <canvas id="chart_split_email_b"></canvas>
                    </div>
                    <div class="float-left" style="width:40%">
                        <div id="chart_split_email_b_legend" class="chart-legend"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="groundhogg-chart-wrapper">
            <div class="groundhogg-chart-no-padding" style="width: 100% ; margin-right: 0px;">
                <h2 class="title"><?php _e( 'Email stats compare split email', 'groundhogg' ); ?></h2>
                <div id="table_email_stats_compare"></div>
            </div>
        </div>
		<?php
	}


	/**
	 * @param $reports Reports
	 */
	function add_in_reports( $reports ) {
		$report_compare = new Table_Email_Stats_Compare( $reports->start, $reports->end );
		$reports->add( 'table_email_stats_compare', [ $report_compare, 'get_data' ] );


		$report_email_a = new Chart_Split_Email_A( $reports->start, $reports->end );
		$reports->add( 'chart_split_email_a', [ $report_email_a, 'get_data' ] );

		$report_email_b = new Chart_Split_Email_B( $reports->start, $reports->end );
		$reports->add( 'chart_split_email_b', [ $report_email_b, 'get_data' ] );

	}


}
