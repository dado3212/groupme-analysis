<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	define('API', TRUE);
	include_once('../php/groupme.php');

	$PDO = createConnection();

	$stmt = $PDO->prepare("SELECT * FROM groups WHERE name=:name AND password=:password");
	$stmt->bindValue(":name", $_GET['group'], PDO::PARAM_STR);
	$stmt->bindValue(":password", $_GET['password'], PDO::PARAM_STR);

	$stmt->execute();

	$response = $stmt->fetch(PDO::FETCH_ASSOC);

	$info = json_decode($response["data"], true);

	$names = $info["total"]["names"];
	$topics = $info["total"]["topics"];

	usort($names, function($a, $b) { return $a["time"] < $b["time"]; });
	usort($topics, function($a, $b) { return $a["time"] < $b["time"]; });
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?php echo "Groupme Analysis | {$names[0]['name']}"; ?></title>

		<!-- Font Awesome -->
		<link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">

		<!-- jQuery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

		<!-- jQuery Widgets -->
		<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>

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
					title: name + '\'s Activity by Time',
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
			<h6><?php echo "(As of " . date("M d, Y h:i a", strtotime($response['date'])) . ")"; ?></h6>
		</header>
		<div id="tabs">
			<ul>
				<li><a href="#group">Group Stats</a></li>
				<li><a href="#individuals">Individual Stats</a></li>
			</ul>
			<div id="group">
				<div class="main-stats">
					<div>
						<h4>
							<i class="fa fa-user" aria-hidden="true"></i>
							Members
						</h4>
						<?php echo number_format(count($info['individuals']) - 1); ?>
					</div>
					<div>
						<h4>
							<img src="../assets/groupme.png" />
							Comments
						</h4>
						<?php echo number_format($info['total']['comments']); ?>
					</div>
					<div>
						<h4>
							<i class="fa fa-book" aria-hidden="true"></i>
							Words
						</h4>
						<?php echo number_format($info['total']['words']); ?>
					</div>
					<div>
						<h4>
							<img src="../assets/heart.png" />
							Likes
						</h4>
						<?php echo number_format($info['total']['likes']); ?>
					</div>
				</div>
				<div id="comments">
					<ul>
					<?php for ($i = 0; $i < count($info['total']['popular']); $i++) {
						$post = $info['total']['popular'][$i];
						$author = ($post["sender_type"] == "user") ? $info["individuals"][$post["sender_id"]]["name"] : "<code>System</code>";
						$author_image = ($post["sender_type"] == "user") ? $info["individuals"][$post["sender_id"]]["image"] : "";
						?>
						<li>
							<div class="top">
								<span class="number"><?php echo $i + 1; ?></span>
								<span class="profile" style="background-image: url('<?php echo $author_image; ?>');"></span>
								<span class="name"><?php echo $author; ?></span>
								<span class="likes"><img src="../assets/heart.png" /><?php echo count($post["likes"]); ?></span>
							</div>
							<div class="content">
								<?php
								if (count($post["attachments"]) == 0)
									echo "<div><q>" . $post["text"] . "</q></div>";
								else {
									echo "<img src='" . $post["attachments"][0]["url"] . "'/><div>";
									if (strlen($post["text"]) > 0)
										echo "<q>" . $post["text"] . "</q>";
									echo "</div>";
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
								<td><?php echo number_format($member["total_number"]); ?></td>
								<td><?php echo number_format($member["total_words"]); ?></td>
								<td><?php echo number_format($member["total_likes_received"]); ?></td>
								<td><?php echo number_format($member["total_likes_given"]); ?></td>
								<td><?php echo ($member["total_likes_received"] > 0) ? round($member["total_likes_given"]/$member["total_likes_received"], 2) : 0; ?></td>
								<td><?php echo ($member["total_number"] > 0) ? round($member["total_likes_received"]/$member["total_number"], 2) : 0; ?></td>
								<td><?php echo $member["self_likes"]; ?></td>
								<td><?php echo $member["best_comment"] ? $member["best_comment"]["text"] . " (<span>" . $member["max_likes"] . "</span> likes)" : "No liked comments. :( <span style='display: none;'>0</span>"; ?></td>
								<td><?php echo "<button class='detail-button' onclick=\"changePerson(person$i)\">Details</button>"; ?></td>
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
						<div class="likes-received">
							<img src="../assets/heart.png" />
							<span></span>
						</div>
						<div class="likes-given">
							<img src="../assets/shared.png" />
							<span></span>
						</div>
						<div class="self-likes">
							<img src="../assets/smiley.png" />
							<span></span>
						</div>
					</div>
					<div id="histogram"></div>
				</div>
			</div>
		</div>
		<?php // echo "<pre>" . print_r($info, true) . "</pre>"; ?>
	</body>
</html>