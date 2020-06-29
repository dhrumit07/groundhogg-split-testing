<?php

namespace GroundhoggSplitTesting\Reports;

use Groundhogg\Reporting\New_Reports\Chart_Last_Broadcast;

class Chart_Broadcast_A extends Chart_Last_Broadcast {

	protected function get_broadcast_id() {
//		return get_request_var( 'data' )['broadcast_id'];
		return 219;
	}

}