<?php
if (!$page_loaded) {return;}

$stukje = $Stukjes->getDraft($_GET['stukje'], $Error);
$cat = $Categories->getCat($stukje['categorie'], $Error);
$Error->printAll();
?>
<h2 class='mb-2'>Draft lezen</h2>

<div class='row'>
	<div class='col-sm-6'><b>Titel</b></div><div class='col-sm-6'><?php echo htmlspecialchars($stukje['titel']); ?></div>
	<div class='col-sm-6'><b>Auteur</b></div><div class='col-sm-6'><?php echo htmlspecialchars($stukje['user']); ?></div>
	<div class='col-sm-6'><b>Categorie</b></div><div class='col-sm-6'><?php echo htmlspecialchars($cat['name']);?></div>
	<div class='col-12'><?php echo nl2br(htmlspecialchars($stukje['tekst'])); ?></div>
	<div class='col-sm-6'><b>Klaar</b></div><div class='col-sm-6'><?php echo (($stukje['klaar'] == 1) ? 'Ja' : 'Nee'); ?></div>
</div>

<center>
	<a class='btn btn-info mt-3 px-5' href='?action=drafts'>Terug</a>
</center>
