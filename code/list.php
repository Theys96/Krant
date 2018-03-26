<?php
if (!$page_loaded) {return;}

if (isset($_GET['delstukje'])) {
	if ($Session->role == 3) {
		$Stukjes->delStukje($_GET['delstukje'], $Error);
	} else {
		$Error->throwWarning("Je moet beheerder zijn om stukjes te verwijderen.");
	}
}
if (isset($_GET['plaatsstukje'])) {
	if ($Session->role == 3) {
		$Stukjes->plaatsStukje($_GET['plaatsstukje'], $Error);
	} else {
		$Error->throwWarning("Je moet beheerder zijn om stukjes te plaatsen.");
	}
}
$list = $Stukjes->getStukjes(null, $Error);
$Error->printAll();
?>

<h2 class='mb-4'>Stukjes</h2>
<?php
if ($Session->role == 2) {
	$filter = isset($_GET['filter']) ? intval($_GET['filter']) : 1;
	/* 0 - alle stukjes
	 * 1 - alle stukjes die klaar zijn
	 * 2 - alle stukjes die klaar zijn & nog niet nagekeken
	 */
	echo "<div class='w-100 text-right'>Filter: ";
	echo "<a class='" . ($filter == 0 ? 'text-success' : '') . "' href='?action=lijst&filter=0'>alles</a> | ";
	echo "<a class='" . ($filter == 1 ? 'text-success' : '') . "' href='?action=lijst&filter=1'>klaar</a> | ";
	echo "<a class='" . ($filter == 2 ? 'text-success' : '') . "' href='?action=lijst&filter=2'>klaar & nog niet nagekeken</a>";
	echo "</div>\n";
}
?>


<?php
if (count($list) == 0) {
	echo "<div class='text-center text-grey'><i>Er zijn op dit moment (nog) geen stukjes.</div>\n";
}

$n = 0;
foreach ($list as $stukje) {
	$checks = $Stukjes->numChecks($stukje['stukje'], $Error);
	$filtered = false;
	if (isset($filter)) {
		if ($filter >= 1) {
			$filtered = $filtered || $stukje['klaar'] == 0;
		}
		if ($filter >= 2) {
			$filtered = $filtered || $checks > 0;
		}
	}
	if (!$filtered) {
		$n++;
		$author = $Stukjes->getAuthor($stukje['stukje'], 'stukjes', $Error);
		echo "<div class='stukje my-2 mx-1 row pt-1'>\n";
			echo "<div class='col-md-6'><div class='row'>";
				echo "<div class='col-sm-7'><h4><b>" . htmlspecialchars($stukje['titel']) . "</b></h4></div>";
				echo "<div class='col-sm-5 text-right'>" . htmlspecialchars($author) . "</div>";
			echo "</div></div><div class='col-md-6'><div class='row'>";
				echo "<div class='col-7'>" . htmlspecialchars($Categories->getCatValue($stukje['categorie'], 'name', $Error)) . "</div>";
				echo "<div class='col-5 text-right'>" . (($stukje['klaar'] == 1) ? "klaar" : "niet klaar") . "</div>";
			echo "</div></div>";

			echo "<div class='col-12 mb-2 text-center text-grey'><i>" . htmlspecialchars((substr($stukje['tekst'],0,75) . (strlen($stukje['tekst']) > 75 ? "..." : ""))) . "</i></div>";
			echo "<div class='col-6'><b>" . $lengte = $stukje['lengte'] . "</b> tekens</div>";
			echo "<div class='col-6 text-right'><b>" . $checks . "</b> check(s)</div>";
			echo "<div class='col-12'><div class='row justify-content-center'>";
				if ($Session->role != 2)
					echo "<div class='col-4 px-1 text-center'><a class='btn btn-warning py-1 my-1 w-100' href='?action=edit&stukje=" . $stukje['stukje'] . "'>Wijzigen</a></div>";
				else if ($Session->role == 2) {
					if ($stukje['klaar'] == 1 && $author != $Session->username)
						echo "<div class='col-4 px-1 text-center'><a class='btn btn-warning py-1 my-1 w-100' href='?action=check&stukje=" . $stukje['stukje'] . "'>Nakijken</a></div>";
					}
				if ($Session->role == 3)
					echo "<div class='col-4 px-1 text-center'><a class='btn btn-danger py-1 my-1 w-100' href='?action=lijst&delstukje=" . $stukje['stukje'] . "'>Verwijderen</a></div>";
				if ($Session->role == 3)
					{
					if ($stukje['klaar'] == 1)
						echo "<div class='col-4 px-1 text-center'><a class='btn btn-primary py-1 my-1 w-100' href='?action=plaats&stukje=" . $stukje['stukje'] . "'>Lezen</a></div>";
					else
						echo "<div class='col-4 px-1 text-center'><a class='btn btn-primary py-1 my-1 w-100' href='?action=read&stukje=" . $stukje['stukje'] . "'>Lezen</a></div>";
					}
				else {
					echo "<div class='col-4 px-1 text-center'><a class='btn btn-primary py-1 my-1 w-100' href='?action=read&stukje=" . $stukje['stukje'] . "'>Lezen</a></div>";
				}
			echo "</div></div>";
		echo "</div>\n";
	}
}
if (isset($filter) && $n == 0 && count($list) > 0) {
	echo "<div class='mt-3 text-center text-grey'><i>Er zijn geen stukjes die voldoen aan het huidige filter.</div>\n";
}
?>