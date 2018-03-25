<?php
if (!$page_loaded) {return;}
?>
<script src='code/draft.js'></script>
<script src='code/editor.js'></script>
<?php
if (isset($_POST['draftid'])) 
	{
	$Stukjes->plaatsdraft($_POST['draftid'], 'Nieuw', null, $Error);
	}

function printButtons($chars) {
	foreach ($chars as $char) {
	    echo "<input type='button' class='px-4 btn btn-secondary' value='" . $char . "' onclick='insertChar(this)' />";
	}
}

$Error->printAll();
?>

<script>
$(function() {
	/* Draft stuff */
	$('.input').on('input', function() {
		Draft.draft();
		setInterval(function() {
			Draft.draft();
		}, 10000);
		$('.input').off('input');
	});

	$('#text').on('input', function() {
		$('#charcount').text(this.value.length + " tekens");
	});
	$('#text').trigger('input');
});
</script>

<h2>Nieuw stukje</h2>

<form method='post' onSubmit='return Draft.plaats(this)'>
<input type='hidden' name='draftid' id='draftid' />

<div class='form-group'>
	<label for='title'>Titel</label>
	<input type='text' class='form-control input' name='title' id='title' />
</div>

<div class='form-group'>
	<label for='user'>Auteur</label>
	<input type='text' class='form-control' id='user' value='<?php echo htmlspecialchars($Session->username); ?>' disabled />
</div>

<div class='form-group'>
	<label for='category'>Categorie</label>
	<select name='category' id='category' class='form-control'>
		<?php
		var_dump($Categories->getCats($Error));
		foreach ($Categories->getCats($Error) as $id => $category)
		    {
		    echo "<option value='" . $id . "'>" . htmlspecialchars($category['name']) . "</option>\n";
		    }
		?>
	</select>
</div>

<div class='form-group'>
	<textarea id='text' class='form-control text input' name='text'></textarea>
	<small class='float-right' id='charcount'></small>
</div>


<div class="btn-group my-1" role="group" aria-label="Basic example">
	<?php
	printButtons(array('&euml;','&eacute;','&egrave'));
	?>
</div>
<div class="btn-group my-1" role="group" aria-label="Basic example">
	<?php
	printButtons(array('&iuml;','&auml;','&ouml;','&uuml;'));
	?>
</div>

<div class='mt-3 form-group'>
	<label class="custom-control custom-checkbox">
	  <input type='checkbox' name='done' value='1' class="custom-control-input" />
	  <span class="custom-control-indicator"></span>
	  <span class="custom-control-description">Dit stukje is klaar</span>
	</label>
</div>

<input class='btn btn-primary' type='submit' value='Plaats' /><br />
<span id='info'></span>

</form>
