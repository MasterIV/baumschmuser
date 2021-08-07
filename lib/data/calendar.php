<?php


class data_calendar {
	public function data() {
		$locations = db()->calendar_locations->all()->relate();

		$month = (!isset($_GET['mon'])) ? date("n") : (int)$_GET['mon'];
		$month_begin = mktime(0, 0, 0, $month, 1);
		$wochenanfang = date("w", $month_begin);
		if ($wochenanfang == 0) $wochenanfang = 7;
		$monatstage = date("t", $month_begin);
		$today = mktime(0, 0, 0);

		$days = array();
		$day = 0;
		$i = 0;
		while ($day <= $monatstage || $i % 7 != 0) {
			if ($day > 0 || $i + 1 == $wochenanfang) $day++;

			if ($day > 0 && $day <= $monatstage) {
				$tstamp = $month_begin + ($day - 1) * 3600 * 24;

				$events = db()->get("calendar_events", "start between " . $tstamp . " and " . ($tstamp + 3600 * 24));
				foreach ($events as &$e) {
					$e['datum'] = date("H:i", $e['start']);
					$e['location'] = $locations[$e['target']];
				}

				$days[$i] = array(
					'current' => $tstamp == $today,
					'timestamp' => $tstamp,
					'tag' => $day,
					'events' => $events
				);
			} else $days[$i] = false;

			$i++;
		}

		return array(
			'monatsanfang' => $month_begin,
			'month' => $month,
			'current_month' => date("n"),
			'days' => $days
		);
	}
}