<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	define('API', TRUE);
	include_once('../php/groupme.php');

	// Actual Group: 16897222
	// Test Group: 23376041
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
		<!-- Font Awesome -->
		<link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">

		<!-- jQuery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

		<!-- DataTables -->
		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
		<script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>

		<!-- Custom -->
		<link type="text/css" rel="stylesheet" href="../build/stats.css">
		<script src="../js/stats.js"></script>

		<script>
			function drawChart(values, name) {
				var data = new google.visualization.DataTable();
				data.addColumn('timeofday', 'Time of Day');
				data.addColumn('number', 'Number of Messages');

				data.addRows(values);

				var options = {
					title: name + '\'s Activity',
					height: 450,
					hAxis: {
						format: 'ha',
						viewWindow: {
							min: [0, 0, 0],
							max: [23, 59, 59]
						},
						gridlines: { count: 12 } 
					},
					legend: 'none',
				};

				var chart = new google.visualization.ColumnChart(document.getElementById('histogram'));

				chart.draw(data, options);
			}

			// Instantiate each individual's object
			<?php
				for ($i = 0; $i < count($info["individuals"]); $i++) {
					echo "var person$i = " . json_encode(array_slice($info["individuals"], $i, 1)[0]) . ";\n";
				}
			?>
		</script>

		<!-- Google Charts -->
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<script type="text/javascript">
		  google.charts.load('current', {packages: ['corechart']});
		  google.charts.setOnLoadCallback(function(){ changePerson(person0); });
		</script>
	</head>
	<body>
		<header>
			<h1><?php echo $names[0]["name"] ?></h1>
			<h3><?php echo $topics[0]["name"] ?></h3>
		</header>
		<div id="group" style="display: none;">
			<div class="main-stats">
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
							<th>Details</th>
						</tr>
					</thead>
					<tbody>
						<?php for ($i = 0; $i < count($info["individuals"]); $i++) {
							$member = array_slice($info["individuals"], $i, 1)[0];
						 ?>
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
							<td><?php echo "<button onclick=\"changePerson(person$i)\">Details</button>"; ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<div id="detail">
				<h1></h1>
				<img src="" />
				<div class="main-stats">
					<div class="comments">
						<img src="../assets/groupme.png" />
						<span></span>
					</div>
					<div class="words">
						<i class="fa fa-book" aria-hidden="true"></i>
						<span></span>
					</div>
					<div class="likes">
						<img src="../assets/heart.png" />
						<span></span>
					</div>
				</div>
				<div id="histogram"></div>
			</div>
		</div>
		<?php echo "<pre>" . print_r($info, true) . "</pre>"; ?>
	</body>
</html>