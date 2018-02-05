<?php
if (!isset($_GET['version'])) {
	$stukje = $Stukjes->getStukje($_GET['stukje'], null, $Error);
	$version = false;
	$checks = $Stukjes->getChecks($_GET['stukje'], $Error);
	}
else {
	$stukje = $Stukjes->getStukje($_GET['stukje'], $_GET['version'], $Error);
	$version = true;
	}
$cat = $Categories->getCat($stukje['categorie'], $Error);
$Error->printAll();
?>
<h2 class='mb-3'>Stukje lezen</h2>
<div class='row'>
	<?php
	if ($version) {
		echo "<div class='col-sm-4'><b>Versie</b></div><div class='col-sm-8'>" . $stukje['version'] . " (" . $stukje['type'] . ")</div>\n";
		}
	if (count($checks) > 0) {
		echo "<div class='col-sm-12'>Nagekeken door " . htmlspecialchars(implode(", ", $checks)) . ".</div>";
		}
	?>
	<div class='col-sm-4'><b>Titel</b></b></div><div class='col-sm-8'><?php echo htmlspecialchars($stukje['titel']); ?></div>
	<div class='col-sm-4'><b>Auteur</b></div><div class='col-sm-8'><?php echo htmlspecialchars($Stukjes->getAuthor($stukje['stukje'], 'stukjes', $Error)); ?></div>
	<div class='col-sm-4'><b>Categorie</b></div><div class='col-sm-8'><?php echo htmlspecialchars($cat['name']);?></div>
</div>
<?php echo nl2br(htmlspecialchars($stukje['tekst'])); ?>
<div class='row'>
	<div class='col-sm-4'><b>Klaar</b></div>
	<div class='col-sm-8'><?php echo ($stukje['klaar'] == 1) ? 'Ja' : 'Nee'; ?></div>
</div>

<center>
	<a class='btn btn-info px-5' href='?action=lijst'>Terug</a>
</center>
