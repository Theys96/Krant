<?php
if (!$page_loaded) {return;}

if (isset($_POST['addcat'])) {
	$Categories->addCat($_POST['addcat'], $_POST['catdescr'], $Error);
}
if (isset($_GET['delcat'])) {
	$Categories->delCat($_GET['delcat'], $Error);
}

$cats = $Categories->getCats($Error);
$Error->printAll();

echo "<h2>Categorieën</h2>\n";
if ($Session->role == 3) :
?>
<form method='post' action='index.php?action=cats'>
<h3>Categorie toevoegen</h3>
<div class='form-group'>
	<label for='name'>Naam</label>
	<input id='name' name='addcat' class='form-control' type='text' />
</div>
<div class='form-group'>
	<label for='name'>Beschrijving</label>
	<textarea id='name' name='catdescr' class='form-control'></textarea>
</div>
<input type='submit' class='btn btn-primary' value='Toevoegen' />
</form>

<?php
endif;
?>
<br />

<div class='px-3 mx-auto'>
<?php
$row = true;

foreach ($cats as $cat)
	{
	$color = $row ? '#AAAAAA' : '#DDDDDD';
	$row = !$row;
	
	echo "<div style='background-color: " . $color . "' class='row'>\n";
	echo "<div class='col-4'><b>" . htmlspecialchars($cat['name']) . "</b></div>";
	echo "<div class='col-6'>" . htmlspecialchars($cat['description']) . "</div>";
	echo "<div class='col-2'>";
	if ($Session->role == 3 && $cat['id'] != 1)
		echo "<a class='text-danger' href='?action=cats&delcat=" . $cat['id'] . "'>X</a>";
	echo "</div>";
	echo "</div>\n";
	}
?>
</div>
