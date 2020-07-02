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

class Chart_Split_Email_B extends Chart_Split_Email {

	function get_step_email_id() {
		if (!$this->get_step_id() ){
			return  [];
		}

		$step = new Step( $this->get_step_id() );
		return  absint($step->get_meta( 'email_b_id' ));

	}
}