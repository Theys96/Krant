<?php
if (!$page_loaded) {return;}

if (isset($_GET['undo']))
	{
	$Stukjes->undoPlaatsStukje($_GET['undo'], $Error);
	}
$list = $Stukjes->getGeplaatsteStukjes(null, $Error);
$Error->printAll();
?>
<h2 class='mb-3'>Geplaatst</h2>
<?php
if (count($list) == 0) {
	echo "<div class='text-center text-grey'><i>Er zijn nog geen stukjes geplaatst.</i></div>\n";
}

foreach ($list as $stukje)
	{
	echo "<div class='stukje my-2 mx-1 row pb-1 pt-2'>\n";
		echo "<div class='col-md-6'><div class='row'>";
			echo "<div class='col-sm-7'><h4><b>" . htmlspecialchars($stukje['titel']) . "</b></h4></div>";
			echo "<div class='col-sm-5 text-right'>" . htmlspecialchars($Stukjes->getAuthor($stukje['stukje'], 'geplaatst', $Error)) . "</div>";
		echo "</div></div><div class='col-md-6'><div class='row'>";
			echo "<div class='col-7'>" . htmlspecialchars($Categories->getCatValue($stukje['categorie'], 'name', $Error)) . "</div>";
			echo "<div class='col-5 text-right'>" . (($stukje['klaar'] == 1) ? "klaar" : "niet klaar") . "</div>";
		echo "</div></div>";
		echo "<div class='col-12 mb-2 text-center text-grey'><i>" . htmlspecialchars(substr($stukje['tekst'],0,75) . (strlen($stukje['tekst']) > 75 ? "..." : "")) . "</i></div>";
		echo "<div class='col-sm-3'></div>";
		echo "<div class='col-sm-6 text-right'><a class='btn btn-primary w-100 my-1 py-1' href='?action=archive&undo=" . $stukje['stukje'] . "'>Terugplaatsen</a></div>";
		echo "<div class='col-sm-3'></div>";
	echo "</div>";
	}
?>
