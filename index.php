<?php
  ob_start();
  include("php/code.php");
  ob_end_clean();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Groupme Analysis</title>

    <!-- SEO -->
    <meta name="robots" content="index, follow, archive">
    <meta charset="utf-8" />
    <meta http-equiv="Cache-control" content="public">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:creator" content="@alex_beals">

    <meta property="og:type" content="website">
    <meta property="og:title" content="Groupme Analysis">
    <meta property="og:image" content="http://groupmeanalysis.com/assets/images/summary.png">
    <meta property="og:image:type" content="image/png">
      <meta property="og:image:width" content="1200">
      <meta property="og:image:height" content="630">
    <meta property="og:url" content="http://groupmeanalysis.com">
    <meta property="og:description" content="Analyze any of your groupme's at the click of a button.">

    <meta name="description" content="Analyze any of your groupme's at the click of a button.">
    
    <meta name="format-detection" content="telephone=no">
    <meta name="google-site-verification" content="YIEgaynlS5pGb3Iw0EK4MqnNR5kcOX5GSgSq_30vQok" />

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="assets/favicon/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="assets/favicon/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="assets/favicon/manifest.json">
    <link rel="mask-icon" href="assets/favicon/safari-pinned-tab.svg" color="#00aff0">
    <link rel="shortcut icon" href="assets/favicon/favicon.ico">
    <meta name="msapplication-config" content="assets/favicon/browserconfig.xml">
    <meta name="theme-color" content="#00aff0">

    <?php
      // Respect 'Request Desktop'
      if (preg_match("/(iPhone|iPod|iPad|Android|BlackBerry|Mobile)/i", $_SERVER['HTTP_USER_AGENT'])) {
        ?><meta name="viewport" content="width=650"><?php
      }
    ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

    <script src="js/main.js"></script>
    <link type="text/css" rel="stylesheet" href="build/home.css" />
  </head>
  <body>
    <header>
      <img src="assets/images/header.png" alt="header" />
    </header>
    <div class="cta">
      <div>
        <h1>Get all the statistics on your GroupMe messages!</h1>
        <div class="button-wrapper">
          <a class="button" href="#steps">Get Started</a>
        </div>
      </div>
      <img src="assets/images/phone.png" alt="phone" />
    </div>
    <div class="demo">
      <h2>Demo Statistics</h2>
      <div class="group">
        <img src="assets/images/group_demo.png" alt="Group Stats" />

        <div class="details">
          <div>
            <span class="tab active" data-name="group">Group</span>
            <span class="tab" data-name="individual">Individual</span>
          </div>
          <ul>
            <li>Most mentioned individuals</li>
            <li>Number of comments</li>
            <li>Number of likes</li>
            <li>Best comments</li>
            <li>More!</li>
          </ul>
        </div>
      </div>
      <div class="individual" style="display: none;">
        <img src="assets/images/individual_demo.png" alt="Individual Stats" />

        <div class="details">
          <div>
            <span class="tab" data-name="group">Group</span>
            <span class="tab active" data-name="individual">Individual</span>
          </div>
          <ul>
            <li>Likes received</li>
            <li>Likes given</li>
            <li>Number of comments</li>
            <li>Best comment</li>
            <li>Activity by time</li>
            <li>Who likes your posts</li>
            <li>More!</li>
          </ul>
        </div>
      </div>
    </div>
    <div class="steps" id="steps">
      <h2>Steps</h2>
      <ul>
        <div class="row">
          <li>
            <h3>Step 1</h3>
            <div>
              Add user on Groupme to the group you want to analyze.
            </div>
            <img src="assets/images/add0.jpg" alt="Add User" />
          </li>
          <li>
            <h3>Step 2</h3>
            <div>
              Use the phone number:
              <div class="well">
                619-432-4317
              </div>
              and click 'Add new phone number'
            </div>
            <img src="assets/images/add1.jpg" alt="Use Phone #" />
          </li>
        </div>
        <div class="row">
          <li>
            <h3>Step 3</h3>
            <div>
              Set the user's name to:
              <div class="well">
                <?php echo $code; ?>
              </div>
              and uncheck 'Save to your Address Book'
            </div>
            <img src="assets/images/add2.jpg" alt="Set Name" />
          </li>
          <li>
            <h3>Step 4</h3>
            <span id="alert"></span>
            <div>
              Click this button once the user has been added to the group!<br>Note: each code can only be used once.
              <div class="button-wrapper">
                <button type="submit" id="added" data-name="<?php echo $code; ?>">
                  Added!
                  <div id="spinner">
                  </div>
                </button>
              </div>
            </div>
            <img src="assets/images/add3.jpg" alt="Stats Comment" />
          </li>
        </div>
      </ul>
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