<?php

use Groundhogg\Broadcast;
use Groundhogg\Email;
use Groundhogg\Plugin;
use function Groundhogg\get_db;
use function Groundhogg\get_url_var;
//
//wp_enqueue_script( 'groundhogg-admin-reporting-split-testing' );

$broadcasts = get_db( 'broadcastmeta' );
$broadcasts = $broadcasts->query();

$options = [];

foreach ( $broadcasts as $broadcast ) {
	$broadcast                       = new Broadcast( absint( $broadcast->broadcast_id ) );
	$split_test_email                = new Email( absint( absint( $broadcast->get_meta( 'split_test_email' ) ) ) );
	$options[ $broadcast->get_id() ] = sprintf( '%s vs %s', $broadcast->get_object()->get_title(), $split_test_email->get_title() );
}
?>

<div class="actions" style="float: right">
	<?php
	$args = array(
		'name'        => 'broadcast_id',
		'id'          => 'broadcast-id',
		'class'       => 'post-data',
		'selected'    => absint( get_url_var( 'broadcast' ) ),
		'options'     => $options,
		'option_none' => false,
	);
	echo Plugin::$instance->utils->html->dropdown( $args );
	?>

</div>

<div style="clear: both;"></div>
<div class="groundhogg-chart-wrapper">
    <div class="groundhogg-chart">
        <h2 class="title"><?php _e( 'Email A', 'groundhogg' ); ?></h2>
        <div style="width: 100%; padding: ">
            <div class="float-left" style="width:60%">
                <canvas id="chart_broadcast_email_a"></canvas>
            </div>
            <div class="float-left" style="width:40%">
                <div id="chart_broadcast_email_a_legend" class="chart-legend"></div>
            </div>
        </div>
    </div>
    <div class="groundhogg-chart">
        <h2 class="title"><?php _e( 'Email B', 'groundhogg' ); ?></h2>
        <div style="width: 100%; padding: ">
            <div class="float-left" style="width:60%">
                <canvas id="chart_broadcast_email_b"></canvas>
            </div>
            <div class="float-left" style="width:40%">
                <div id="chart_broadcast_email_b_legend" class="chart-legend"></div>
            </div>
        </div>
    </div>
</div>
<div class="groundhogg-chart-wrapper">
    <div class="groundhogg-chart-no-padding" style="width: 100% ; margin-right: 0px;">
        <h2 class="title"><?php _e( 'Broadcast Stats', 'groundhogg' ); ?></h2>
        <div id="table_broadcast_stats_compare"></div>
    </div>
</div>
<div class="groundhogg-chart-wrapper">
    <div class="groundhogg-chart-no-padding" style="width: 100% ; margin-right: 0px;">
        <h2 class="title"><?php _e( 'Email A Link Clicked', 'groundhogg' ); ?></h2>
        <div id="table_broadcast_link_clicked_a"></div>
    </div>
</div>
<div class="groundhogg-chart-wrapper">
    <div class="groundhogg-chart-no-padding" style="width: 100% ; margin-right: 0px;">
        <h2 class="title"><?php _e( 'Email B Link Clicked', 'groundhogg' ); ?></h2>
        <div id="table_broadcast_link_clicked_b"></div>
    </div>
</div

