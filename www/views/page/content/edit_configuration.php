<?php

use App\Util\Singleton\Configuration;

/*
 * @var Configuration $variables
 */
?>
<h2>Configuratie aanpassen</h2>
<form method='post'>
<div class='form-group'>
	<label for='edit_checks'>Minimale aantal checks nodig voor plaatsen</label>
	<input id='edit_checks' name='edit_checks' class='form-control' type='number' required value='<?php echo $variables->min_checks; ?>' />
</div>
<div class='form-group'>
	<label for='edit_mail'>Mail adres voor het sturen van foto's</label>
	<input id='edit_mail' name='edit_mail' class='form-control' type='email' value='<?php echo $variables->mail_address; ?>' />
</div>
<input type='text' hidden name='edit_passwords[]' value='<?php echo null; ?>'></input>
<div class='form-group'>
	<label for='edit_password_1'>Wachtwoord voor schrijvers</label>
	<input type='text' id='edit_password_1' class='form-control text input' name='edit_passwords[]' value='<?php echo $variables->passwords[1]; ?>'></input>
</div>
<div class='form-group'>
	<label for='edit_passwords_2'>Wachtwoord voor nakijkers</label>
	<input type='text' id='edit_password_2' class='form-control text input' name='edit_passwords[]' value='<?php echo $variables->passwords[2]; ?>'></input>
</div>
<div class='form-group'>
	<label for='edit_password_3'>Wachtwoord voor beheerders</label>
	<input type='text' id='edit_password_3' class='form-control text input' name='edit_passwords[]' required value='<?php echo $variables->passwords[3]; ?>'></input>
</div>
<div class='form-group'>
	<label for='edit_schrijfregels'>Schrijfregels</label>
	<textarea id='edit_schrijfregels' class='form-control text input' name='edit_schrijfregels'><?php echo $variables->schrijfregels; ?></textarea>
</div>
<input type='submit' class='btn btn-primary' value='Aanpassen' />
</form>
