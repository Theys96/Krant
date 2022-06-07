<?php
if (!$page_loaded) {return;}
?>
<?php
if (isset($_POST['text'])) {
	$user = $Db->real_escape_string($_POST['user']);
	$text = $Db->real_escape_string($_POST['text']);
	$query = "INSERT INTO feedback (user, text) VALUES('".$user."', '".$text."')";
	$result = $Db->query($query);
	if ($result) {
		$Error->throwMessage("Feedback verzonden.");
	} else {
		$Error->throwError("Er is iets misgegaan bij het verzenden van de feedback. Controleer de query: ");
		$Error->throwError($query);
	}
}

$Error->printAll();
?>

<h2 class='my-3'>Feedback</h2>

<p>Heb je suggesties over dit programma voor het schrijven van de stukjes? Geef het hier aan! Misschien doe ik er dan een keer iets mee..</p>

<form method='post' onSubmit='return Draft.plaats(this)'>
<input type='hidden' name='user' value='<?php echo htmlspecialchars($Session->username); ?>' id='user' />

<div class='form-group'>
	<textarea id='text' class='form-control text input' name='text'></textarea>
</div>

<input class='btn btn-primary' type='submit' value='Verzenden' /><br />
<span id='info'></span>

</form>
