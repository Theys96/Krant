<?php
$mysql_host = '';
$mysql_username = '';
$mysql_password = '';
$mysql_database = '';
$Error->throwFatal("MySQL settings are not set!"); // Comment out this line when deploying.

$passwords = array(
	1 => null,
	2 => null,
	3 => "printer");
$pagechars = 4500;

$roles = array(
	1 => 'schrijver',
	2 => 'nakijker',
	3 => 'beheerder');

$actions = array(
	'schrijf' => array(
		'include' => array('code/create.php'),
		'level' => array(1,3)),
	'edit' => array(
		'include' => array('code/edit.php'),
		'level' => array(1,3)),
	'read' => array(
		'include' => array('code/read.php'),
		'level' => array(1,2,3)),
	'readdraft' => array(
		'include' => array('code/readdraft.php'),
		'level' => array(3)),
	'check' => array(
		'include' => array('code/check.php'),
		'level' => array(2,3)),
	'lijst' => array(
		'include' => array('code/list.php'),
		'level' => array(1,2,3)),
	'cats' => array(
		'include' => array('code/cats.php'),
		'level' => array(1,2,3)),
	'users' => array(
		'include' => array('code/admin.php','code/users.php'),
		'level' => array(3)),
	'admin' => array(
		'include' => array('code/admin.php'),
		'level' => array(3)),
	'plaats' => array(
		'include' => array('code/plaats.php'),
		'level' => array(3)),
	'drafts' => array(
		'include' => array('code/admin.php','code/drafts.php'),
		'level' => array(3)),
	'bin' => array(
		'include' => array('code/admin.php','code/bin.php'),
		'level' => array(3)),
	'versionctrl' => array(
		'include' => array('code/admin.php','code/versionctrl.php'),
		'level' => array(3)),
	'versions' => array(
		'include' => array('code/admin.php','code/versions.php'),
		'level' => array(3)),
	'archive' => array(
		'include' => array('code/admin.php','code/archive.php'),
		'level' => array(3)),
	'feedback' => array(
		'include' => array('code/feedback.php'),
		'level' => array(1,2,3)),
	'feedbacklist' => array(
		'include' => array('code/feedbacklist.php'),
		'level' => array(1,2,3)),
		);
?>
