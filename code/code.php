<?php
include 'error.class.php';
include 'session.class.php';
include 'stukjes.class.php';
include 'categories.class.php';
include 'users.class.php';

function cap($text, $len) {
	return substr($text,0,$len) . (strlen($text) > $len ? "..." : "");
}
?>