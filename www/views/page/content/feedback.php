<?php
/**
 * @var int $user_id
 */
?>
<h2 class='my-3'>Feedback</h2>

<p class='read-row'>Heb je suggesties over dit programma voor het schrijven van de stukjes? Geef het hier aan! Misschien doe ik er dan een keer iets mee.</p>

<form method='post'>
<input type='hidden' name='user' value='<?php echo $user_id; ?>' id='user' />

<div class='form-group'>
	<textarea id='text' class='form-control text input' name='text'></textarea>
</div>

<input class='btn btn-primary' type='submit' value='Verzenden' /><br />
<span id='info'></span>

</form>
