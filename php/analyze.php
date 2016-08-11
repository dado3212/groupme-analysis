<?php
	define("API", TRUE);
	include_once("groupme.php");

	// If it's a valid name
	if (isset($_POST["name"]) && strlen($_POST["name"]) == 6) {
		$group = findGroupByName($_POST["name"]);

		// If the group matches (redundant check in case accessed directly)
		if ($group) {
			// Run full analysis, and generate random password
			$analysis = analyze($group);
			$password = bin2hex(openssl_random_pseudo_bytes(10));

			// Insert info into database for quick access
			$PDO = createConnection();

			$stmt = $PDO->prepare("INSERT INTO groups (name, password, group_id, data) VALUES (:name, :password, :group_id, :data)");
			$stmt->bindValue(":name", $_POST["name"], PDO::PARAM_STR);
			$stmt->bindValue(":password", $password, PDO::PARAM_STR);
			$stmt->bindValue(":group_id", $group, PDO::PARAM_STR);
			$stmt->bindValue(":data", json_encode($analysis), PDO::PARAM_STR);

			$stmt->execute();

			// Inform the group
			sendMessage($group, "Check out the analysis of this group at http://groupmeanalysis.com/stats?group=" . $_POST["name"] . "&password=" . $password);

			sendMessage($group, "Please remove me now. (If I remove myself I can never come back 😞)");

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