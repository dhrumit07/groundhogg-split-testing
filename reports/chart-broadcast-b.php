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

class Chart_Broadcast_B extends Chart_Last_Broadcast {



	protected function get_broadcast_id() {
//		return get_request_var( 'data' )['broadcast_id'];
		return 219;
	}

}