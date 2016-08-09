<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	define('API', TRUE);
	include_once('../php/groupme.php');

	// Pull information from database (analyze step)
	$PDO = createConnection();

	$stmt = $PDO->prepare("SELECT * FROM groups WHERE name=:name AND password=:password");
	$stmt->bindValue(":name", $_GET['group'], PDO::PARAM_STR);
	$stmt->bindValue(":password", $_GET['password'], PDO::PARAM_STR);

	$stmt->execute();

	$response = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($response) {

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

		<!-- SEO -->
		<meta name="robots" content="index, follow, archive">
		<meta charset="utf-8" />
		<meta http-equiv="Cache-control" content="public">

		<meta name="twitter:card" content="summary">
		<meta name="twitter:creator" content="@alex_beals">

		<meta property="og:type" content="website">
		<meta name="robots" content="index, follow, archive">
		<meta charset="utf-8" />
		<meta http-equiv="Cache-control" content="public">

		<meta name="twitter:card" content="summary">
		<meta name="twitter:creator" content="@alex_beals">

		<meta property="og:type" content="website">
		<meta property="og:title" content="<?php echo "Groupme Analysis | {$names[0]['name']}"; ?>">
		<meta property="og:image" content="http://groupmeanalysis.com/assets/images/header.png">
		<meta property="og:url" content="http://groupmeanalysis.com">
		<meta property="og:description" content="Analysis of the group <?php echo $names[0]['name']; ?>">

		<meta name="description" content="Analysis of the group <?php echo $names[0]['name']; ?>">

		<!-- Favicons -->
		<link rel="apple-touch-icon" sizes="180x180" href="/assets/favicon/apple-touch-icon.png">
		<link rel="icon" type="image/png" href="/assets/favicon/favicon-32x32.png" sizes="32x32">
		<link rel="icon" type="image/png" href="/assets/favicon/favicon-16x16.png" sizes="16x16">
		<link rel="manifest" href="/assets/favicon/manifest.json">
		<link rel="mask-icon" href="/assets/favicon/safari-pinned-tab.svg" color="#00aff0">
		<link rel="shortcut icon" href="/assets/favicon/favicon.ico">
		<meta name="msapplication-config" content="/assets/favicon/browserconfig.xml">
		<meta name="theme-color" content="#00aff0">

		<?php
			// Respect request desktop
			if (preg_match("/(iPhone|iPod|iPad|Android|BlackBerry|Mobile)/i", $_SERVER['HTTP_USER_AGENT'])) {
				?><meta name="viewport" content="width=700"><?php
			}
		?>

		<!-- Font Awesome -->
		<link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">

		<!-- jQuery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

		<!-- jQuery Widgets -->
		<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>

		<!-- Custom -->
		<link type="text/css" rel="stylesheet" href="../build/stats.css">
		<script src="../js/stats.js"></script>

		<script>
			var chartOptions = {
				title: '\'s Activity by Time',
				height: 300,
				width: 100,
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

			function drawChart(values) {
				var data = new google.visualization.DataTable();
				data.addColumn('timeofday', 'Time of Day');
				data.addColumn('number', 'Number of Messages');

				data.addRows(values);

				var chart = new google.visualization.ColumnChart(document.getElementById('histogram'));

				chartOptions.width = $(".all .detail").width();
				chart.draw(data, chartOptions);

				$(window).resize(function () {
					$("#histogram").hide();
					chartOptions.width = $(".all .detail").width();
					$("#histogram").show();
					chart.draw(data, chartOptions);
				});
			}

			// Dump of all people
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
							<img src="../assets/images/groupme.png" />
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
							<img src="../assets/images/heart.png" />
							Likes
						</h4>
						<?php echo number_format($info['total']['likes']); ?>
					</div>
				</div>
				<div id="mentions">
					<div class='wrapper'>
						<h2>Most Mentioned</h2>
						<?php foreach ($info['total']['mentions'] as $id => $number) {
							$author = $info["individuals"][$id];
							?>
							<div>
								<span class="profile" style="background-image: url('<?php echo $author["image"]; ?>');"></span>
								<span class="name"><?php echo $author["name"]; ?></span>
								<span class="number"><?php echo $number; ?></span>
							</div>
						<?php } ?>
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
								<span class="likes"><img src="../assets/images/heart.png" /><?php echo count($post["likes"]); ?></span>
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
								<div class="profile" style="background-image: url('<?php echo $person['image']; ?>')"></div>
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
									<img src="../assets/images/groupme.png" />
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
									<img src="../assets/images/heart.png" />
									Likes Received
								</h4>
								<span></span>
							</div>
							<div class="likes-given">
								<h4>
									<img src="../assets/images/shared.png" />
									Likes Given
								</h4>
								<span></span>
							</div>
							<div class="self-likes">
								<h4>
									<img src="../assets/images/smiley.png" />
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
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-82195594-1', 'auto');
		  ga('send', 'pageview');

		</script>
	</body>
</html>
<?php } else {
	header('Location: http://www.alexbeals.com/projects/groupme') ;
} ?>