<?php
	define("API", TRUE);
	include_once("secret.php");

	$PDO = createConnection();

	$stmt = $PDO->query("SELECT name FROM groups");
	$used_codes = array_map(function ($code) {
		return $code["name"];
	}, $stmt->fetchAll(PDO::FETCH_ASSOC));

	do {
		// Character set adapted from http://ux.stackexchange.com/a/21078
		$code = substr(str_shuffle(str_repeat("abcdefghkmnoprstwxzABCDEFGHJKLMNPQRTWXY", 7)), 0, 7);
	} while (in_array($code, $used_codes));

	echo json_encode([
		"response" => "success",
		"code" => $code,
	]);
?>