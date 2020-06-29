<?php

use Groundhogg\Broadcast;
use Groundhogg\Plugin;
use function Groundhogg\get_db;
use function Groundhogg\get_url_var;

wp_enqueue_script( 'groundhogg-admin-reporting-split-testing' );

$broadcasts = get_db( 'broadcasts' );
$broadcasts = $broadcasts->query( [ 'status' => 'sent' ] );

$options = [];

foreach ( $broadcasts as $broadcast ) {
	$broadcast                       = new Broadcast( absint( $broadcast->ID ) );
	$options[ $broadcast->get_id() ] = $broadcast->get_title();
}
?>

<div class="groundhogg-chart-wrapper">
    <div class="groundhogg-chart">
        <h2 class="title"><?php _e( 'Broadcast A', 'groundhogg' ); ?></h2>

	    <?php
	    $args = array(
		    'name'        => 'broadcast_a',
		    'id'          => 'broadcast-a',
		    'class'       => 'post-data',
		    'selected'    => absint( get_url_var( 'broadcast_a' ) ),
		    'options'     => $options,
		    'option_none' => false,
	    );
	    echo Plugin::$instance->utils->html->dropdown( $args );
	    ?>
    </div>
    <div class="groundhogg-chart">
        <h2 class="title"><?php _e( 'Broadcast B', 'groundhogg' ); ?></h2>

	    <?php
	    $args = array(
		    'name'        => 'broadcast_b',
		    'id'          => 'broadcast-b',
		    'class'       => 'post-data',
		    'selected'    => absint( get_url_var( 'broadcast_b' ) ),
		    'options'     => $options,
		    'option_none' => false,
	    );
	    echo Plugin::$instance->utils->html->dropdown( $args );
	    ?>
    </div>
</div>

<div class="groundhogg-chart-wrapper">
    <div class="groundhogg-chart">
        <h2 class="title"><?php _e( 'Broadcast A', 'groundhogg' ); ?></h2>
        <div style="width: 100%; padding: ">
            <div class="float-left" style="width:60%">
                <canvas id="chart_broadcast_a"></canvas>
            </div>
            <div class="float-left" style="width:40%">
                <div id="chart_broadcast_a_legend" class="chart-legend"></div>
            </div>
        </div>
    </div>
    <div class="groundhogg-chart">
        <h2 class="title"><?php _e( 'Broadcast B', 'groundhogg' ); ?></h2>
        <div style="width: 100%; padding: ">
            <div class="float-left" style="width:60%">
                <canvas id="chart_broadcast_b"></canvas>
            </div>
            <div class="float-left" style="width:40%">
                <div id="chart_broadcast_b_legend" class="chart-legend"></div>
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
        <h2 class="title"><?php _e( 'Broadcast A Link Clicked', 'groundhogg' ); ?></h2>
        <div id="table_broadcast_link_clicked_a"></div>
    </div>
</div>
<div class="groundhogg-chart-wrapper">
    <div class="groundhogg-chart-no-padding" style="width: 100% ; margin-right: 0px;">
        <h2 class="title"><?php _e( 'Broadcast B Link Clicked', 'groundhogg' ); ?></h2>
        <div id="table_broadcast_link_clicked_b"></div>
    </div>
</div

