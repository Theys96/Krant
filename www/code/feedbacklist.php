<?php
if (!$page_loaded) {return;}
?>
<?php
$query = "SELECT * FROM feedback ORDER BY timestamp DESC";
$result = $Db->query($query);
if (!$result) {
	$Error->throwError("Er is iets misgegaan bij het ophalen van de feedback. Controleer de query: ");
	$Error->throwError($query);
	exit();
}
$Error->printAll();
?>

<h2 class='my-3'>Feedback</h2>

<div class='row'>
<?php
while ($f = $result->fetch_assoc()) {
	echo "<div class='col-12'>";
	echo "[".$f['timestamp']."] <b>" . nl2br(htmlspecialchars($f['user'])) . "</b> - " . nl2br(htmlspecialchars($f['text']));
	echo "</div>";
}
?>
</div>