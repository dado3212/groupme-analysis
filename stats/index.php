<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	define('API', TRUE);
	include_once('../php/groupme.php');

	$groupID = "23376041";
	$info = analyze($groupID);

	$names = $info["total"]["names"];

	usort($names, function($a, $b) { return $a["time"] < $b["time"]; });
?>
<!DOCTYPE html>
<html lang="en">
	<head>

		<link type="text/css" rel="stylesheet" href="../build/stats.css" />
		<link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" />
	</head>
	<body>
		<header>
			<h1><?php echo $names[0]["name"] ?></h1>
		</header>
		<div class="group">
			<div id="main">
				<div>
					<img src="../assets/groupme.png" />
					<?php echo number_format($info['total']['comments']) . " comments"; ?>
				</div>
				<div>
					<i class="fa fa-book" aria-hidden="true"></i>
					<?php echo number_format($info['total']['words']) . " words"; ?>
				</div>
				<div>
					<img src="../assets/heart.png" />
					<?php echo number_format($info['total']['likes']) . " likes"; ?>
				</div>
				<div>
					<i class="fa fa-user" aria-hidden="true"></i>
					<?php echo number_format(count($info['individuals']) - 1) . " members"; ?>
				</div>
			</div>
		</div>
		<?php echo "<pre>" . print_r($info, true) . "</pre>"; ?>
	</body>
</html>