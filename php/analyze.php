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

			$stmt = $PDO->prepare("INSERT INTO groups (name, password, group_id, data) VALUES (:name, :password, :group_id, :data)");
			$stmt->bindValue(":name", $_POST['name'], PDO::PARAM_STR);
			$stmt->bindValue(":password", $password, PDO::PARAM_STR);
			$stmt->bindValue(":group_id", $group, PDO::PARAM_STR);
			$stmt->bindValue(":data", json_encode($analysis), PDO::PARAM_STR);

			$stmt->execute();

			sendMessage($group, "Check out the analysis of this group at http://groupmeanalysis.com/stats?group=" . $_POST['name'] . "&password=" . $password);

			sendMessage($group, "Please remove me now. (If I remove myself I can never come back 😞)");

			// leaveGroup($group);

			$response = [
				"response" => "success",
				"code" => 3,
				"message" => "Successfully analyzed! Check group for link.",
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