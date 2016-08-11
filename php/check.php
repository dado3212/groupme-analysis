<?php
	define("API", TRUE);
	include_once("groupme.php");

	if (isset($_POST["name"]) && strlen($_POST["name"]) == 6) {
		$group = findGroupByName($_POST["name"]);
		if ($group) {
			$response = [
				"response" => "success",
				"code" => 3,
				"message" => "",
			];
		} else {
			$response = [
				"response" => "error",
				"code" => 1,
				"message" => "Group not found.  Make sure the name of the added member matches exactly, along with the phone #.",
			];
		}
	} else {
		$response = [
			"response" => "error",
			"code" => 2,
			"message" => "Invalid name.  Please contact site admin.",
		];
	}

	echo json_encode($response);
?>