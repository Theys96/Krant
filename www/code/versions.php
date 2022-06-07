<?php
if (!$page_loaded) {return;}

if (isset($_GET['replace'])) {
	$Stukjes->replaceVersion($_GET['stukje'], $_GET['replace'], $Error);
	}
$versions = $Stukjes->getVersions($_GET['stukje'], $Error);
$Error->printAll();
?>
<h2 class='mb-3'>Versies</h2>

<?php
$current = true;
foreach ($versions as $stukje) {

	if ($current) {
	echo "<div class='stukje bg-info my-2 mx-1 row pt-2'>\n";
	$current = false;
	} else {
	echo "<div class='stukje my-2 mx-1 row pt-1'>\n";
	}
		echo "<div class='col-md-6'><div class='row'>";
			echo "<div class='col-sm-7'><h4><b>" . htmlspecialchars( cap($stukje['titel'], 40) ) . "</b></h4></div>";
			echo "<div class='col-sm-5 text-right'>" . htmlspecialchars($stukje['user']) . "</div>";
		echo "</div></div><div class='col-md-6'><div class='row'>";
			echo "<div class='col-7'>" . htmlspecialchars($Categories->getCatValue($stukje['categorie'], 'name', $Error)) . "</div>";
			echo "<div class='col-5 text-right'>" . (($stukje['klaar'] == 1) ? "klaar" : "niet klaar") . "</div>";
		echo "</div></div>";

		echo "<div class='col-12 text-center text-grey'><i>" . htmlspecialchars(substr($stukje['tekst'],0,75) . (strlen($stukje['tekst']) > 75 ? "..." : "")) . "</i></div>";
		echo "<div class='col-6'>versie <b>" . $stukje['version'] . "</b> (" . $stukje['type'] . ")</div>";
		echo "<div class='col-6 text-right'><b>" . $stukje['lengte'] . "</b> teken(s)</div>";

		echo "<div class='col-md-3'></div>";
		echo "<div class='col-md-3'><a class='py-1 my-1 w-100 btn btn-primary' href='?action=read&stukje=" . $stukje['stukje'] . "&version=" . $stukje['version'] . "'>Lezen</a></div>";
		echo "<div class='col-md-3'><a class='py-1 my-1 w-100 btn btn-primary' href='?action=versions&stukje=" . $stukje['stukje'] . "&replace=" . $stukje['version'] . "'>Terugplaatsen</a></div>";
		echo "<div class='col-md-3'></div>";
	echo "</div>";
	}
?>

<center>
	<a class='btn btn-info px-5 mt-3' href='?action=versionctrl'>Terug</a>
</center>
