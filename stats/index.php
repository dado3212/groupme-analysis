<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	define('API', TRUE);
	include_once('../php/groupme.php');

	$groupID = "16897222";
	$info = analyze($groupID);

	$names = $info["total"]["names"];
	$topics = $info["total"]["topics"];

	usort($names, function($a, $b) { return $a["time"] < $b["time"]; });
	usort($topics, function($a, $b) { return $a["time"] < $b["time"]; });
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
		<script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>

		<link type="text/css" rel="stylesheet" href="../build/stats.css">
		<script src="../js/stats.js"></script>
	</head>
	<body>
		<header>
			<h1><?php echo $names[0]["name"] ?></h1>
			<h3><?php echo $topics[0]["name"] ?></h3>
		</header>
		<div id="group" style="display: none;">
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
			<div id="comments">
				<ul>
				<?php for ($i = 0; $i < count($info['total']['popular']); $i++) {
					$post = $info['total']['popular'][$i];
					$author = ($post["sender_type"] == "user") ? $info["individuals"][$post["sender_id"]]["name"] : "<code>System</code>";
					?>
					<li>
						<div class="prefix">
						<?php
							if ($i == 0) {
								echo "<img class='gold' src='../assets/gold.png' />";
							} else if ($i == 1) {
								echo "<img class='silver' src='../assets/silver.png' />";
							} else if ($i == 2) {
								echo "<img class='bronze' src='../assets/bronze.png' />";
							} else {
								echo $i + 1;
							}
						?>
						</div>
						<div class="content">
							<?php
							if (count($post["attachments"]) == 0)
								echo "<div><q>" . $post["text"] . "</q><span class='author'>- $author</span></div>";
							else {
								echo "<img src='" . $post["attachments"][0]["url"] . "'/><div>";
								if (strlen($post["text"]) > 0)
									echo "<q>" . $post["text"] . "</q>";
								echo "<span class='author'>- $author</span></div>";
							}
						?>
						</div>
					</li>
				<?php } ?>
				</ul>
			</div>
		</div>
		<div id="individuals">
			<div class="members">
				<table id="members" class="display">
					<thead>
						<tr>
							<th>User</th>
							<th>Comments</th>
							<th>Words</th>
							<th>Likes Received</th>
							<th>Likes Given</th>
							<th>Likes Given/Likes Received</th>
							<th>Likes Received/Comment</th>
							<th>Self Likes</th>
							<th>Best Comment</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($info["individuals"] as $member) { ?>
						<tr>
							<td><?php echo $member["name"]; ?></td>
							<td><?php echo $member["total_number"]; ?></td>
							<td><?php echo $member["total_words"]; ?></td>
							<td><?php echo $member["total_likes_received"]; ?></td>
							<td><?php echo $member["total_likes_given"]; ?></td>
							<td><?php echo ($member["total_likes_received"] > 0) ? round($member["total_likes_given"]/$member["total_likes_received"], 2) : 0; ?></td>
							<td><?php echo ($member["total_number"] > 0) ? round($member["total_likes_received"]/$member["total_number"], 2) : 0; ?></td>
							<td><?php echo $member["self_likes"]; ?></td>
							<td><?php echo $member["best_comment"] ? $member["best_comment"]["text"] . " (<span>" . $member["max_likes"] . "</span> likes)" : "No liked comments. :( <span style='display: none;'>0</span>"; ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php echo "<pre>" . print_r($info, true) . "</pre>"; ?>
	</body>
</html>