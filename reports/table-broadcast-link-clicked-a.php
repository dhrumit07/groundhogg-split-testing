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

class Table_Broadcast_Link_Clicked_A extends Table_Broadcast_Link_Clicked {

	protected function get_broadcast_id() {
		return get_array_var( get_request_var( 'data', [] ), 'broadcast_id' );
//		return 219;
	}


}