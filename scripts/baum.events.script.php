<?php

$panel['colors'] = $GLOBALS['klassen_farben'];
$panel['classes'] = $GLOBALS['klassen'];
$panel['roles'] = $GLOBALS['rollen'];

if($_GET['event']) {
	$event = $panel['event'] = db()->raid_events->row($_GET['event'])->object();

	if($event) {
		if(current_user()) {
			$chars = db()->raid_chars->rows(current_user(), 'owner')->relate();

			if($_POST['character'] && isset($chars[intval($_POST['character'])])) {
				db()->raid_signup->insert([
						'user' => current_user(),
						'character' => $_POST['character'],
						'comment' => $_POST['comment'],
						'event' => $event->id,
						'time' => time(),
				], 'REPLACE');
			}

			$signup = db()->raid_signup->get("user = %d and event = %d", $event->id, current_user())->assoc();
			$form = $panel['form'] = new form_renderer(PAGE_SELF.'&event='.$event->id);
			$form->select('character', 'Character', $chars, $signup['character']);
			$form->text('comment', 'Kommentar', $signup['comment']);
		}

		$event->dungeon = $GLOBALS['raids'][intval($event->destination)];
		$panel['signups'] = db()->query("
			SELECT rc.*, rs.comment FROM raid_signup rs 
			JOIN raid_chars rc ON rc.id = rs.`character` 
			WHERE rs.event = %d", $event->id)->assocs();
	}
} else {
	$panel['raids'] = db()->query("
		SELECT re.*, 
			SUM(rc.rolle = 0) melee, 
			SUM(rc.rolle = 1) ranged, 
			SUM(rc.rolle = 2) tank, 
			SUM(rc.rolle = 3) heal,
			SUM(rc.owner = %d) joining
		FROM raid_events re
		LEFT JOIN raid_signup rs ON re.id = rs.event
		LEFT JOIN raid_chars rc ON rc.id = rs.`character` 
		WHERE re.start > UNIX_TIMESTAMP() - 86400
		GROUP BY re.id
		ORDER BY re.start ASC
	", current_user())->assocs();

	foreach ($panel['raids'] as &$r) {
		$r['url'] = PAGE_SELF . '&event=' . $r['id'];
		$r['dungeon'] = $GLOBALS['raids'][intval($r['destination'])];
	}
}
