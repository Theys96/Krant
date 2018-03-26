<?php
/* INIT */
error_reporting(0);
session_start();
include 'code/code.php';
$Error = new ErrorHandler();
include 'config.php';
$Db = new Mysqli($mysql_host, $mysql_username, $mysql_password, $mysql_database);
if ($Db->connect_errno) {
    $Error->throwFatal("MySQL connection failed: " . $Db->connect_error);
}
$Session = new Session($Db);
$Error->session = $Session;
$Stukjes = new Stukjes($Db);
$Categories = new Categories($Db);
$Users = new Users($Db);
/* INIT end */

/* Login/logout */
if (isset($_POST['role']))
	{
	$Session->login($_POST);
	}

if ($_GET['action'] == 'logout')
	{
	$Session->logout();
	}
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

    <!-- jQuery -->
    <script src="assets/js/jquery-3.2.1.min.js"></script>
</head>
<body>

<div class="container">
<?php
if ($Session->logged == true)
	{
	include 'code/home.php';
	}
else
	{
	include 'code/login.php';
	}
?>
</div>

    <div id="footer">&copy; Thijs Havinga, 2018</div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
  </body>
</html>
<?php
$Db->close();
?>