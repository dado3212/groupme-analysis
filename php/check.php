<?php
error_reporting(E_ALL);
	ini_set('display_errors', 1);

	define('API', TRUE);
	include_once('groupme.php');

	$_POST['name'] = 'eRxwez';

	if (isset($_POST['name']) && strlen($_POST['name']) == 6) {
		$group = findGroupByName($_POST['name']);
		if ($group) {
			echo 'Found group: ' . $group;
			echo "<pre>" . print_r(json_decode(analyze($group), true), true) . "</pre>";
		} else {
			echo 'Not added.';
		}
	} else {
		echo 'Invalid name.';
	}
?>