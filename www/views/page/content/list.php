<?php
use Model\Article;
use Model\User;

/**
 * @var Article[] $articles
 * @var int $role
 */
?>

<h2 class='mb-4'>Stukjes</h2>
<?php
function cap($text, $len) {
	return substr($text,0,$len) . (strlen($text) > $len ? "..." : "");
}

if ($role == 2) {
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
if (count($articles) == 0) {
	echo "<div class='text-center text-grey'><i>Er zijn op dit moment (nog) geen stukjes.</div>\n";
}

$n = 0;
foreach ($articles as $article) {
	$filtered = false;
	if (isset($filter)) {
		if ($filter >= 1) {
			$filtered = $filtered || $article->ready === false;
		}
		if ($filter >= 2) {
			$filtered = $filtered || $article->checkers > 0;
		}
	}
	if (!$filtered) {
		$n++;
		$authors = htmlspecialchars(implode(', ', array_map(
            static function (User $author): string {
                return $author->username;
            },
            $article->authors
        )));
        $checkers = htmlspecialchars(implode(', ', array_map(
            static function (User $author): string {
                return $author->username;
            },
            $article->checkers
        )));

		echo "<div class='stukje my-2 mx-1 row pt-1'>\n";
			echo "<div class='col-md-6'><div class='row'>";
				echo "<div class='col-sm-7'><h4><b>" . htmlspecialchars( cap($article->title, 40) ) . "</b></h4></div>";
				echo "<div class='col-sm-5 text-right'>" . $authors . "</div>";
			echo "</div></div><div class='col-md-6'><div class='row'>";
				echo "<div class='col-7'>" . htmlspecialchars($article->category->description) . "</div>";
				echo "<div class='col-5 text-right'>" . (($article->ready === true) ? "klaar" : "niet klaar") . "</div>";
			echo "</div></div>";

			echo "<div class='col-12 mb-2 text-center text-grey'><i>" . htmlspecialchars( cap($article->contents, 75) ) . "</i></div>";
			echo "<div class='col-6'><b>" . $lengte = strlen($article->contents) . "</b> tekens</div>";
			echo "<div class='col-6 text-right'><b>" . count($article->checkers) . "</b> check(s)" . (count($article->checkers) == 0 ? "" : ": ") . $checkers . "</div>";
			echo "<div class='col-12'><div class='row justify-content-center'>";
				if ($role != 2)
					echo "<div class='col-4 px-1 text-center'><a class='btn btn-warning py-1 my-1 w-100' href='?action=edit&stukje=" . $article->id . "'>Wijzigen</a></div>";
				else if ($role == 2) {
					if ($article->ready === true)
						echo "<div class='col-4 px-1 text-center'><a class='btn btn-warning py-1 my-1 w-100' href='?action=check&stukje=" . $article->id . "'>Nakijken</a></div>";
					}
				if ($role == 3)
					echo "<div class='col-4 px-1 text-center'><a class='btn btn-danger py-1 my-1 w-100' href='?action=list&remove_article=" . $article->id . "'>Verwijderen</a></div>";
				if ($role == 3)
					{
					if ($article->ready === true)
						echo "<div class='col-4 px-1 text-center'><a class='btn btn-primary py-1 my-1 w-100' href='?action=plaats&stukje=" .$article->id . "'>Lezen</a></div>";
					else
						echo "<div class='col-4 px-1 text-center'><a class='btn btn-primary py-1 my-1 w-100' href='?action=read&stukje=" . $article->id . "'>Lezen</a></div>";
					}
				else {
					echo "<div class='col-4 px-1 text-center'><a class='btn btn-primary py-1 my-1 w-100' href='?action=read&stukje=" . $article->id . "'>Lezen</a></div>";
				}
			echo "</div></div>";
		echo "</div>\n";
	}
}
if (isset($filter) && $n == 0 && count($articles) > 0) {
	echo "<div class='mt-3 text-center text-grey'><i>Er zijn geen stukjes die voldoen aan het huidige filter.</div>\n";
}
?>