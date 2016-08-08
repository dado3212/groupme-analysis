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

	$people = $info["individuals"];

	usort($people, function($a, $b) { return $a["name"] > $b["name"]; });

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
					height: 300,
					width: 700,
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

			// Instantiate dump
			<?php
				echo "var people = " . json_encode($people) . ";\n";
			?>
		</script>

		<!-- Google Charts -->
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<script type="text/javascript">
		  google.charts.load('current', {packages: ['corechart']});
		  google.charts.setOnLoadCallback(function(){ changePerson(people[0], null); });
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
						<h2>Best Comments</h2>
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
				<div class="all">
					<div class="people">
					<?php
						for ($i = 0; $i < count($people); $i++) {
							$person = $people[$i]; ?>
							<div data-id='<?php echo $i; ?>' <?php if ($i == 0) echo "class='active'"; ?> onclick="changePerson(people[<?php echo $i; ?>], this)">
								<div class="profile" style="background-image: url(<?php echo $person['image']; ?>)"></div>
								<span><?php echo $person['name']; ?></span>
							</div>
						<?php }
					?>
					</div>
					<div class="detail">
						<h1></h1>
						<div class="image">
						</div>
						<div class="main-stats">
							<div class="comments">
								<h4>
									<img src="../assets/groupme.png" />
									Comments
								</h4>
								<span></span>
							</div>
							<div class="words">
								<h4>
									<i class="fa fa-book" aria-hidden="true"></i>
									Words
								</h4>
								<span></span>
							</div>
							<div class="likes-received">
								<h4>
									<img src="../assets/heart.png" />
									Likes Received
								</h4>
								<span></span>
							</div>
							<div class="likes-given">
								<h4>
									<img src="../assets/shared.png" />
									Likes Given
								</h4>
								<span></span>
							</div>
							<div class="self-likes">
								<h4>
									<img src="../assets/smiley.png" />
									Self Likes
								</h4>
								<span></span>
							</div>
						</div>
						<div id="histogram"></div>
						<div class="secondary-stats">
							<div class="shared">
							</div>
							<div class="loved">
							</div>
						</div>
					</div>
				</div>
				<div id="sort">
					<h3>Sort</h3>
					<div class="type" data-sort="name">
						Name
						<span>↑</span>
					</div>
					<div class="type" data-sort="comments">
						Comments
						<span></span>
					</div>
					<div class="type" data-sort="words">
						Words
						<span></span>
					</div>
					<div class="type" data-sort="likes_received">
						Likes Received
						<span></span>
					</div>
					<div class="type" data-sort="likes_given">
						Likes Given
						<span></span>
					</div>
					<div class="type" data-sort="self_likes">
						Self Likes
						<span></span>
					</div>
				</div>
			</div>
		</div>
		<?php // echo "<pre>" . print_r($info, true) . "</pre>"; ?>
	</body>
</html>