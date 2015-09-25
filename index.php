<?php
include_once("php/lib/bootstrap.php");

//file upload
include_once('php/xml-review.php');
$File_Section = new File_Section;
$filesuccess = $File_Section->upload_file($_POST);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="viewport" content="initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>WPtranspo</title>
    <link href="http://fonts.googleapis.com/css?family=Raleway:700,300" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/prettify.css">
</head>

<body>
    <nav>
        <div class="container">
            <h1>WPtranspo</h1>
            <div id="menu">
                <ul class="toplinks">
                    <li><a href="https://github.com/dubrod/WPtranspo" target="_blank">Github Repo</a></li>
                </ul>
            </div>
            <a id="menu-toggle" href="#" class=" ">&#9776;</a> </div>
    </nav>
    <header>
        <div class="container">
            <h2 class="docs-header">Join the Revolution</h2>
        </div>
    </header>
    <section>
        <div class="container">
            <ul class="docs-nav" id="menu-left">
                <li>
                    <strong>Getting Started</strong>
                </li>
                <li><a href="#setup" class=" ">Setup</a></li>
                <li><a href="#review" class=" ">Review XML</a></li>
                <!--<li class="separator"></li>-->

            </ul>
            <div class="docs-content">
                <h2> Getting Started</h2>
                <h3 id="setup">Setup</h3>
                <p>In order for this to work the following must be <strong>TRUE</strong>:</p>
                <ul>
                    <li>You have installed MODX Revolution, and this package is on the server with it.</li>
                    <li>You have Database Credentials</li>
                    <li>You are logged in to the manager, or have an open session.</li>
                    <li>You have a WP XML file in the "<strong>xml</strong>" folder.</li>
                </ul>

                <hr>

                <h3 id="review">Review XML</h3>
                <p>Open your
                    <strong>Wordpress XML File</strong> and make note you have no errors and you do want to import ALL that data.</p>
                <p>If it needs trimming now would be the time. We will be importing or assigning the below to MODX:</p>
                <ul>
                    <li>Categories</li>
                    <li>Resources</li>
                    <li>Assigning Templates</li>
                </ul>

                <h4>Enter your XML filename and lets review it</h4>
                <div id="xml-review">
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>#xml-review">
                        <label for="xml-file">File Name: (wp-export.xml)</label><br>
                        <input type="text" name="xml-file" id="xml-file">
                        <button id="xmlreviewsubmit" name="xmlreviewsubmit">REVIEW</button>
                    </form>
                </div>
                <div id="xml-review-response" style="background:#f7f7f7;"><?php echo $filesuccess; ?></div>

                <hr>

                <h3 id="import-cate">Import Categories</h3>



            </div>
        </div>
    </section>
    <section class="vibrant centered">
        <div class="container">
            <!--<h4>Welcome to MODX!</h4>-->
        </div>
    </section>
    <script src="js/jquery.min.js"></script>

    <script type="text/javascript" src="js/prettify/prettify.js"></script>
    <script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js?lang=css&skin=sunburst"></script>
    <script src="js/layout.js"></script>
    <script src="js/jquery.localscroll-1.2.7.js" type="text/javascript"></script>
    <script src="js/jquery.scrollTo-1.4.3.1.js" type="text/javascript"></script>
</body>

</html>
