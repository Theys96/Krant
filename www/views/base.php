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

        <!-- jQuery -->
        <script src="assets/js/jquery-3.2.1.min.js"></script>
    </head>
    <body>

        <div class="container">
            <?php echo $body; ?>
        </div>

        <div id="footer">&copy; <?php echo date("Y"); ?> Thijs Havinga</div>

        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="assets/js/popper.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
    </body>
</html>
