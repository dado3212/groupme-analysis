<?php
	// Character set adapted from http://ux.stackexchange.com/a/21078
	$name = substr(str_shuffle(str_repeat("abcdefghkmnoprstwxzABCDEFGHJKLMNPQRTWXY", 6)), 0, 6);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Groupme Analysis</title>

		<link type="text/css" rel="stylesheet" href="build/home.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
		<script src="js/main.js"></script>
	</head>
	<body>
		<header>
			<img src="assets/header.png" alt="header" />
		</header>
		<div class="main">
			<ul class="steps">
				<li>
					<h3>Step 1</h3>
					Add user from menu.
				</li>
				<li>
					<h3>Step 2</h3>
					Use the phone number:
					<div class="well">
						+1 619-432-4317
					</div>
					and click 'Add new phone number'
					<img src="assets/add1.jpg" />
				</li>
				<li>
					<h3>Step 3</h3>
					Set the user's name to:
					<div class="well">
						<?php echo $name; ?>
					</div>
					<img src="assets/add2.jpg" />
				</li>
				<li>
					<h3>Step 4</h3>
					Click this button once the user has been added to the group!
					<button type="submit" id="added" class="cta" data-name="<?php echo $name; ?>">Added!</button>
				</li>
			</ul>
		</div>
	</body>
</html>