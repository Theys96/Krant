<?php
use Util\Singleton\Session;

/**
 * @var string $body
 * @var bool|null $show_easter_egg
 */

$alt_css = Session::instance()->getUser()?->alt_css;
$gold = Session::instance()->getGold();
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
        <?php if (!$gold && $alt_css > 0): ?>
        <?php echo "<link href='assets/css/alt_style${alt_css}.css' rel='stylesheet'>"?>
        <?php endif; ?>
        <?php if ($gold): ?>
        <link href="assets/css/gold.css" rel="stylesheet">
        <?php endif; ?>
        <!-- jQuery -->
        <script src="assets/js/jquery-3.2.1.min.js"></script>
        <script>
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
        </script>
        <script type="module" src="assets/js/emoji-picker-element-1.26.0/index.js"></script>
        <script src='assets/js/emoji-react.js'></script>
    </head>
    <body>

    <?php echo $body; ?>

<?php
if ($show_easter_egg) {
    echo "<a target=\"_blank\" href=\"?action=minigame\"><img src=\"assets/img/MHN.png\" alt=\"MHN Minigame\" class=\"easter-egg\"></a>";
}
?>

        <div id="footer">
            &copy; <?php echo date("Y"); ?> Thijs Havinga & Foppe Dijkstra
        </div>

        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="assets/js/popper.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
    </body>
</html>
