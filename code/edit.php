<?php
if (!$page_loaded) {return;}
?>
<script src='code/draft.js'></script>
<?php
if (isset($_POST['draftid']))
	{
	$Stukjes->plaatsdraft($_POST['draftid'], "Edit", $_GET['stukje'], $Error);
	}

function printButtons($chars) {
	foreach ($chars as $char) {
	    echo "<input type='button' class='px-4 btn btn-secondary' value='" . $char . "' onclick='insertChar(this)' />";
	}
}

$stukje = $Stukjes->getStukje($_GET['stukje'], null, $Error);
$Error->printAll();
?>

<script>
function insertChar(button)
    {
    document.getElementById('text').value = document.getElementById('text').value + button.value;
    }

$(function() {
	setInterval(function() {
		Draft.draft();
		}, 10000);
	});
</script>

<h2>Stukje wijzigen</h2>

<form id='form' method='post' onSubmit='return Draft.plaats(this)' action='?action=edit&stukje=<?php echo $_GET['stukje']; ?>'>
<input type='hidden' name='draftid' id='draftid' />

<div class='form-group'>
	<label for='title'>Titel</label>
	<input type='text' id='title' class='form-control' name='title' value="<?php echo htmlspecialchars($stukje['titel']); ?>" />
</div>

<div class='form-group'>
	<label for='user'>Auteur</label>
	<input type='text' class='form-control' id='user' value='<?php echo htmlspecialchars($Session->username) ?>' disabled />
</div>

<div class='form-group'>
	<label for='category'>Categorie</label>
	<select name='category' id='category' class='form-control'>
		<?php
		foreach ($Categories->getCats() as $id => $category)
		    {
			if ($id == $stukje['categorie']) {
			echo "<option value='" . $id . "' selected>" . htmlspecialchars($category['name']) . "</option>\n";
			} else {
			echo "<option value='" . $id . "'>" . htmlspecialchars($category['name']) . "</option>\n";
			}
		    }
		?>
	</select>
</div>

<div class='row'>
	<div class='col-sm-6'>
		<br />
		<textarea class='form-control text' id='text' name='text'><?php echo htmlspecialchars($stukje['tekst']); ?></textarea>
	</div>
	<div class='col-sm-6'>
		<b>Origineel:</b><br />
		<textarea class='form-control text' readonly><?php echo htmlspecialchars($stukje['tekst']); ?></textarea>
	</div>
</div>

<div class="btn-group my-2" role="group" aria-label="Basic example">
	<?php
	printButtons(array('&euml;','&eacute;','&egrave'));
	?>
</div>
<div class="btn-group my-2" role="group" aria-label="Basic example">
	<?php
	printButtons(array('&iuml;','&auml;','&ouml;','&uuml;'));
	?>
</div>

<div class='mt-3 form-group'>
	<label class="custom-control custom-checkbox">
	  <input type='checkbox' name='done' value='1' class="custom-control-input" <?php echo ($stukje['klaar'] ? 'checked' : ''); ?> />
	  <span class="custom-control-indicator"></span>
	  <span class="custom-control-description">Dit stukje is klaar</span>
	</label>
</div>

<input class='btn btn-primary' type='submit' value='Plaats' /><br />
<span id='info'></span>

</form>
