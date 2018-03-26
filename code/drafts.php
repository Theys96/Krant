<?php
if (!$page_loaded) {return;}

$list = $Stukjes->getDrafts(null, $Error);

$Error->printAll();
?>
<h2 class='mb-2'>Drafts</h2>
<?php
if (count($list) == 0) {
	echo "<div class='text-center text-grey'><i>Er zijn geen drafts.</i></div>\n";
}

foreach ($list as $draft) {
	echo "<div class='stukje my-2 mx-1 row pb-1 pt-2'>\n";
		echo "<div class='col-md-6'><div class='row'>";
			echo "<div class='col-sm-7'><h4><b>" . htmlspecialchars( cap($draft['titel'], 40) ) . "</b></h4></div>";
			echo "<div class='col-sm-5 text-right'>" . htmlspecialchars($draft['user']) . "</div>";
		echo "</div></div><div class='col-md-6'><div class='row'>";
			echo "<div class='col-7'>" . htmlspecialchars($Categories->getCatValue($draft['categorie'], 'name', $Error)) . "</div>";
			echo "<div class='col-5 text-right'>" . (($draft['klaar'] == 1) ? "klaar" : "niet klaar") . "</div>";
		echo "</div></div>";
		echo "<div class='col-12 mb-2 text-center text-grey'><i>" . htmlspecialchars(substr($draft['tekst'],0,75) . (strlen($draft['tekst']) > 75 ? "..." : "")) . "</i></div>";
		echo "<div class='col-sm-3'></div>";
		echo "<div class='col-sm-6 mb-2 text-right'><a class='btn btn-primary py-1 w-100' href='?action=readdraft&stukje=" . $draft['id'] . "'>Lezen</a></div>";
		echo "<div class='col-sm-3'></div>";
	echo "</div>";
	}
?>
