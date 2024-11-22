<?php
use Util\Singleton\Session;

/**
 * @var string $body
 */

$alt_css = Session::instance()->getUser()?->alt_css;
?>
<!DOCTYPE html>
<html lang="nl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>Krant</title>

        <!-- Bootstrap core CSS -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">

        <!-- Krant CSS -->
        <link href="assets/css/style.css" rel="stylesheet">
       	<?php if ($alt_css > 0): ?>
        <?php echo "<link href='assets/css/alt_style${alt_css}.css' rel='stylesheet'>"?>
        <?php endif; ?>
        <!-- jQuery -->
        <script src="assets/js/jquery-3.2.1.min.js"></script>
        <script>
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
        </script>
    </head>
    <body>

        <div class="container">
            <?php echo $body; ?>
        </div>

        <div id="footer">
            &copy; <?php echo date("Y"); ?> Thijs Havinga
        </div>

        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="assets/js/popper.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
    </body>
</html>
