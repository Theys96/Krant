<?php
session_start();
include 'code/code.php';
include 'config.php';
$Db = new Mysqli($mysql_host, $mysql_username, $mysql_password, $mysql_database);
$Session = new Session($Db);
$Stukjes = new Stukjes($Db);
$Error = new ErrorHandler();

switch($_REQUEST['action']) {

	case 'createdraft':
		if ($Session->logged == true)
			{
			$stukje = $Stukjes->draft($Session->username, $_REQUEST['titel'], $_REQUEST['cat'], $_REQUEST['tekst'], $_REQUEST['context'], $_REQUEST['klaar'], $Error);
			$json = $Error->arrayAll();
			$json['draftID'] = $stukje;
			echo json_encode($json);
			}
	break;
	
	case 'updatedraft':
		if ($Session->logged == true)
			{
			$stukje = $Stukjes->updatedraft($_REQUEST['id'], $_REQUEST['titel'], $_REQUEST['cat'], $_REQUEST['tekst'], $_REQUEST['context'], $_REQUEST['klaar'], $Error);
			$json = $Error->arrayAll();
			echo json_encode($json);
			}
	break;
	}
?>