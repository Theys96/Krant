<?php
if (!$page_loaded) {return;}

if (isset($_GET['delstukje']))
	{
	$Stukjes->delStukje($_GET['delstukje'], $Error);
	}
if (isset($_GET['plaatsstukje']))
	{
	$Stukjes->plaatsStukje($_GET['plaatsstukje'], $Error);
	}
$list = $Stukjes->getStukjes(null, $Error);
$Error->printAll();
?>

<h2 class='mb-4'>Welkom</h2>
<?php
if ($Session->role == 2) {
	if ($_GET['filter'] && $_GET['filter'] == "1")
		echo "<div class='w-100 text-right'><a href='?action=lijst'>alle stukjes tonen</a></div>";
	else
		echo "<div class='w-100 text-right'><a href='?action=lijst&filter=1'>alleen stukjes tonen die klaar zijn en nog niet nagekeken zijn</a></div>";	
}
?>


<?php
if (count($list) == 0) {
	echo "<div class='text-center text-grey'><i>Er zijn op dit moment (nog) geen stukjes.</div>\n";
}

foreach ($list as $stukje) {
	$checks = $Stukjes->numChecks($stukje['stukje'], $Error);
	if (isset($_GET['filter']) && $_GET['filter'] == "1" && ($stukje['klaar'] != 1 || $checks > 0)) {
		//NOPE
		}
	else {
		echo "<div class='stukje my-2 mx-1 row pt-1'>\n";
			echo "<div class='col-md-6'><div class='row'>";
				echo "<div class='col-sm-7'><h4><b>" . htmlspecialchars($stukje['titel']) . "</b></h4></div>";
				echo "<div class='col-sm-5 text-right'>" . htmlspecialchars($Stukjes->getAuthor($stukje['stukje'], 'stukjes', $Error)) . "</div>";
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
?>
</table>
