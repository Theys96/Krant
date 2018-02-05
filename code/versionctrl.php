<?php
if (!$page_loaded) {return;}

$list = $Stukjes->getStukjes(null, $Error);
$Error->printAll();
?>
<h2 class='py-2'>Versiebeheer</h2>
<?php
if (count($list) == 0) {
	echo "<div class='text-center text-grey'><i>Er zijn op dit moment (nog) geen stukjes.</div>\n";
}

foreach ($list as $stukje) {
	echo "<div class='stukje my-2 mx-1 row pb-1 pt-2'>\n";
		echo "<div class='col-md-6'><div class='row'>";
			echo "<div class='col-sm-7'><h4><b>" . htmlspecialchars($stukje['titel']) . "</b></h4></div>";
			echo "<div class='col-sm-5 text-right'>" . htmlspecialchars($Stukjes->getAuthor($stukje['stukje'], 'stukjes', $Error)) . "</div>";
		echo "</div></div><div class='col-md-6'><div class='row'>";
			echo "<div class='col-7'>" . htmlspecialchars($Categories->getCatValue($stukje['categorie'], 'name', $Error)) . "</div>";
			echo "<div class='col-5 text-right'>" . (($stukje['klaar'] == 1) ? "klaar" : "niet klaar") . "</div>";
		echo "</div></div>";
		echo "<div class='col-12 mb-2 text-center text-grey'><i>" . htmlspecialchars(substr($stukje['tekst'],0,75) . (strlen($stukje['tekst']) > 75 ? "..." : "")) . "</i></div>";
		echo "<div class='col-sm-3'><b>" . ($Stukjes->getVersion($stukje['stukje'], $Error)+1) . "</b> versies</div>";
		echo "<div class='col-sm-6 mb-2 text-right'><a class='btn btn-primary py-1 w-100' href='?action=versions&stukje=" . $stukje['stukje'] . "'>Versies</a></div>";
		echo "<div class='col-sm-3'></div>";
	echo "</div>";
}
?>
