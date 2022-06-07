<?php
if (!$page_loaded) {return;}
?>
<script src='code/draft.js'></script>
<script src='code/editor.js'></script>
<?php
if (isset($_POST['draftid']))
	{
	$Stukjes->plaatsdraft($_POST['draftid'], "Nagekeken", $_GET['stukje'], $Error);
	echo "<script>window.location = '?action=lijst';</script>";
	}

function printButtons($chars) {
	foreach ($chars as $char) {
	    echo "<input type='button' class='px-4 btn btn-secondary' value='" . $char . "' onclick='insertChar(this)' />";
	}
}

$stukje = $Stukjes->getStukje($_GET['stukje'], null, $Error);
$checks = $Stukjes->getChecks($_GET['stukje'], $Error);
$author = $Stukjes->getAuthor($stukje['stukje'], 'stukjes', $Error);
$Error->printAll();

if ($author == $Session->username) {
	$Error->throwError("Je mag je eigen stukje niet nakijken.");
	$Error->printAll();
	return;
}
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
	charCounter("#text", "#charcount");
	});
</script>

<h2>Stukje nakijken</h2>
<?php if (count($checks) > 0 ) : ?>
<div class='row'>
	<div class='col-sm-12 text-right'>Nagekeken door <?php echo implode(", ", $checks); ?>.</div>
</div>
<?php endif; ?>

<form id='form' method='post' onSubmit='return Draft.plaats(this)' action='?action=check&stukje=<?php echo $_GET['stukje']; ?>'>
<input type='hidden' name='draftid' id='draftid' />

<div class='form-group'>
	<label for='title'>Titel</label>
	<input type='text' id='title' class='form-control' name='title' value="<?php echo htmlspecialchars($stukje['titel']); ?>" />
</div>

<div class='form-group'>
	<label for='user'>Auteur</label>
	<input type='text' class='form-control' id='user' value='<?php echo htmlspecialchars($author); ?>' disabled />
</div>

<div class='form-group'>
	<label for='category'>Categorie</label>
	<select name='category' id='category' class='form-control'>
		<?php
		foreach ($Categories->getCats($Error) as $id => $category)
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
		<small class='float-right' id='charcount'></small>
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
	  <input type='checkbox' name='done' value='1' class="custom-control-input" disabled checked />
	  <span class="custom-control-indicator"></span>
	  <span class="custom-control-description">Dit stukje is klaar</span>
	</label>
</div>

<input class='btn btn-primary' type='submit' value='Nagekeken' /> 
<a class='btn btn-primary' href='?action=lijst'>Niet nagekeken</a> <br />
<span id='info'></span>

</form>
