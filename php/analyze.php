<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	define('API', TRUE);
	include_once('groupme.php');

	if (isset($_POST['name']) && strlen($_POST['name']) == 6) {
		$group = findGroupByName($_POST['name']);
		if ($group) {
			$analysis = analyze($group);
			$password = bin2hex(openssl_random_pseudo_bytes(10));

			$PDO = createConnection();

			$stmt = $PDO->prepare("INSERT INTO groups (name, password, data) VALUES (:name, :password, :data)");
			$stmt->bindValue(":name", $_POST['name'], PDO::PARAM_STR);
			$stmt->bindValue(":password", $password, PDO::PARAM_STR);
			$stmt->bindValue(":data", json_encode($analysis), PDO::PARAM_STR);

			$stmt->execute();

			sendMessage($group, "Check out the analysis of this group at http://alexbeals.com/projects/groupme/stats?group=" . $_POST['name'] . "&password=" . $password);

			sendMessage($group, "Please remove me now. (If I remove myself I can never come back 😞)");

			// leaveGroup($group);

			$response = [
				"response" => "success",
				"code" => 3,
				"message" => "Check group for link!",
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