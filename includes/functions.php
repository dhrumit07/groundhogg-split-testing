<?php

namespace GroundhoggSplitTesting;


use Groundhogg\Reports;
use GroundhoggSplitTesting\Reports\Table_Broadcast_Link_Clicked_B;
use GroundhoggSplitTesting\Reports\Table_Email_Stats_Compare;
use function Groundhogg\get_array_var;

/**
 * @param $reports array
 *
 * @return array
 */
function add_in_pages( $reports ) {

	$reports ['funnels'] = array_merge( [ 'table_email_stats_compare' ], get_array_var( $reports, 'funnels' ) );

	return $reports;

}

add_filter( 'groundhogg/admin/reports/reports_to_load', __NAMESPACE__ . '\add_in_pages', 10 );

function funnel_html() {
	?>
    <div class="groundhogg-chart-wrapper">
        <div class="groundhogg-chart-no-padding" style="width: 100% ; margin-right: 0px;">
            <h2 class="title"><?php _e( 'Email stats compare split email', 'groundhogg' ); ?></h2>
            <div id="table_email_stats_compare"></div>
        </div>
    </div
	<?php
}

add_action( 'groundhogg/admin/reports/pages/funnels/after_reports', __NAMESPACE__ . '\funnel_html', 10 );


/**
 * @param $reports Reports
 */
function add_in_reports( $reports ) {
	$report = new Table_Email_Stats_Compare( $reports->start, $reports->end );
	$reports->add( 'table_email_stats_compare', [ $report, 'get_data' ] );
}

add_action( 'groundhogg/reports/setup_default_reports/after', __NAMESPACE__ . '\add_in_reports', 10, 1 );

