<?php
	define('API', TRUE);
	include_once('groupme.php');

	$_POST['name'] = 'eRxwez';

	if (isset($_POST['name']) && strlen($_POST['name']) == 6) {
		$group = findGroupByName($_POST['name']);
		if ($group) {
			echo 'Found group: ' . $group;
			analyze($group);
		} else {
			echo 'Not added.';
		}
	} else {
		echo 'Invalid name.';
	}
?>