<?php

use Model\SingleVariables;

/**
 * @var SingleVariables $variables
 */
?>
<h2>Globale Variabelen</h2>
<form method='post'>
<div class='form-group'>
	<label for='edit_checks'>Minimale aantal checks nodig voor plaatsen</label>
	<input id='edit_checks' name='edit_checks' class='form-control' type='number' value='<?php echo $variables->min_checks; ?>' />
</div>
<div class='form-group'>
	<label for='edit_mail'>Mail adres voor het sturen van foto's</label>
	<input id='edit_mail' name='edit_mail' class='form-control' type='email' value='<?php echo $variables->mail_address; ?>' />
</div>
<div class='form-group'>
	<label for='edit_schrijfregels'>Schrijfregels</label>
	<textarea id='edit_schrijfregels' class='form-control text input' name='edit_schrijfregels'><?php echo $variables->schrijfregels; ?></textarea>
</div>
<input type='submit' class='btn btn-primary' value='Aanpassen' />
</form>
