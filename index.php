<?php
	// Character set adapted from http://ux.stackexchange.com/a/21078
	$name = substr(str_shuffle(str_repeat("abcdefghkmnoprstwxzABCDEFGHJKLMNPQRTWXY", 6)), 0, 6);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Groupme Analysis</title>
		<?php
			if (preg_match("/(iPhone|iPod|iPad|Android|BlackBerry|Mobile)/i", $_SERVER['HTTP_USER_AGENT'])) {
				?><meta name="viewport" content="width=500"><?php
			}
		?>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

		<script src="js/main.js"></script>
		<link type="text/css" rel="stylesheet" href="build/home.css" />
	</head>
	<body>
		<header>
			<img src="assets/header.png" alt="header" />
		</header>
		<div class="cta">
			<div>
				<h1>Get all the statistics on your GroupMe messages!</h1>
				<a class="button" href="#steps">Get Started</a>
			</div>
		</div>
		<div class="steps" id="steps">
			<ul>
				<div class="row">
					<li>
						<h3>Step 1</h3>
						<div>
							Add user from menu.
							<img src="assets/add0.jpg" />
						</div>
					</li>
					<li>
						<h3>Step 2</h3>
						<div>
							Use the phone number:
							<div class="well">
								619-432-4317
							</div>
							and click 'Add new phone number'
							<img src="assets/add1.jpg" />
						</div>
					</li>
				</div>
				<div class="row">
					<li>
						<h3>Step 3</h3>
						<div>
							Set the user's name to:
							<div class="well">
								<?php echo $name; ?>
							</div>
							and uncheck 'Save to your Address Book'
							<img src="assets/add2.jpg" />
						</div>
					</li>
					<li>
						<h3>Step 4</h3>
						<span id="alert"></span>
						<div>
							Click this button once the user has been added to the group!
							<button type="submit" id="added" data-name="<?php echo $name; ?>">Added!</button>
						</div>
					</li>
				</div>
			</ul>
		</div>
	</body>
</html>