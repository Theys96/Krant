<?php

use Model\Article;
use Model\Category;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * @var Article[] $articles
 * @var int $role
 * @var string $title
 * @var string $list_type
 * @var int $checks
 */
$catFilter = Session::instance()->getFilter();
?>

    <h2 class='mb-4'><?php echo $title; ?></h2>
<?php
function cap($text, $len)
{
    return substr($text, 0, $len) . (strlen($text) > $len ? "..." : "");
}

if ($role > 1) {
    $filter = isset($_GET['filter']) ? intval($_GET['filter']) : 1;
    /* 0 - alle stukjes
     * 1 - alle stukjes die klaar zijn
     * 2 - alle stukjes die klaar zijn & nog niet nagekeken
     */
    echo "<div class='w-100 text-right'>";
    echo "<a class='" . ($filter == 0 ? 'text-success' : '') . "' href='?action=list&filter=0'>alles</a> | ";
    echo "<a class='" . ($filter == 1 ? 'text-success' : '') . "' href='?action=list&filter=1'>klaar</a> | ";
    if ($role == 2) {
        echo "<a class='" . ($filter == 2 ? 'text-success' : '') . "' href='?action=list&filter=2'>klaar & kan ik nakijken</a> ";
    }
    if ($role == 3) {
        echo "<a class='" . ($filter == 3 ? 'text-success' : '') . "' href='?action=list&filter=3'>klaar & nagekeken</a> | ";
        echo "<a class='" . (!empty($catFilter) ? 'text-success' : '') ."' href='?action=list&filter=$filter&filtercat=1'>Filter op categorie</a>";
    }
    echo "</div>\n";
}
?>

<?php
if (isset($_GET['filtercat'])) {
    $categories = Category::getAll();
    echo "<div class='w-100 text-right pt-2'>";
    echo "<form method='post' action='?action=list", isset($filter) ? "&filter=$filter" : "", "'>";
    echo "<div class='form-group'>";
    echo "<input type='hidden' name='filters[]' value='0'>";
    $first = true;
    foreach ($categories as $cat) {
        if (!$first) {
            echo "&nbsp;&nbsp;";
        }
        $first = false;
        echo "<input id='cat-filter-$cat->id' type='checkbox' name='filters[]' value='$cat->id'",
        (in_array($cat->id, $catFilter)) || empty($catFilter) ? ' checked' : '', "/>";
        echo "&nbsp;<label for='cat-filter-$cat->id'> $cat->name </label> ";
    }
    echo "&nbsp;&nbsp;<input class='btn btn-primary py-1' type='submit' value='Filter'/> ";
    echo "</form>";
    echo "</div>\n";
}
?>

<?php
if (count($articles) == 0) {
    echo "<div class='text-center text-grey'><i>Hier zijn op dit moment (nog) geen stukjes.</div>\n";
}

$n = 0;
foreach ($articles as $article) {
    $filtered = false;
    if (!empty($catFilter)) {
        $filtered = $filtered || !(in_array($article->category_id, $catFilter));
    }
    if (isset($filter)) {
        if ($filter >= 1) {
            $filtered = $filtered || $article->ready === false;
        }
        if ($filter == 2) {
            $user =  Session::instance()->getUser();
            $filtered = $filtered || count($article->checkers) >= $checks || in_array($user, $article->checkers) ||  in_array($user, $article->authors);
        }
        if ($filter == 3) {
            $filtered = $filtered || count($article->checkers) < $checks;
        }

    }
    if (!$filtered) {
        $n++;
        echo ViewRenderer::render_view('partial.article_list_item', [
            'article' => $article,
            'role' => $role,
            'list_type' => $list_type
        ]);
    }
}
if (isset($filter) && $n == 0 && count($articles) > 0) {
    echo "<div class='mt-3 text-center text-grey'><i>Er zijn geen stukjes die voldoen aan het huidige filter.</div>\n";
}
?>