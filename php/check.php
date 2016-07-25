<?php
error_reporting(E_ALL);
	ini_set('display_errors', 1);

	define('API', TRUE);
	include_once('groupme.php');

	// $_POST['name'] = 'eRxwez';

	if (isset($_POST['name']) && strlen($_POST['name']) == 6) {
		$group = findGroupByName($_POST['name']);
		if ($group) {
			$response = [
				"response" => "success",
				"code" => 3,
				"message" => json_encode(["group" => $group]),
			];
		} else {
			$response = [
				"response" => "error",
				"code" => 1,
				"message" => "Not added.",
			];
		}
	} else {
		$response = [
			"response" => "error",
			"code" => 2,
			"message" => "Invalid name.",
		];
	}

	echo json_encode($response);
?>