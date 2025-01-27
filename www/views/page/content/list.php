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
 */
$catFilter = Session::instance()->getFilter();
$categories = Category::getAll();
if(sizeof($catFilter) == sizeof($categories)) {
    $catFilter = [];
}
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
?>

    <h2 class='mb-4'><?php echo $title; ?></h2>
<?php
function cap($text, $len)
{
    return substr($text, 0, $len) . (strlen($text) > $len ? "..." : "");
}

if ($role > 1) {
    /* 0 - alle stukjes
     * 1 - alle stukjes die klaar zijn
     * 2 - alle stukjes die klaar zijn & nog niet nagekeken
     */
    $filter = isset($_GET['filter']) ? intval($_GET['filter']) : 1;
    echo "<div class='w-100 text-right'>";
    if ($action == "list"){
        echo "<a class='" . ($filter == 0 ? 'text-success' : '') . "' href='?action=list&filter=0'>alles</a> | ";
        echo "<a class='" . ($filter == 1 ? 'text-success' : '') . "' href='?action=list&filter=1'>klaar</a> | ";
        if ($role == 2) {
            echo "<a class='" . ($filter == 2 ? 'text-success' : '') . "' href='?action=list&filter=2'>klaar & kan ik nakijken</a> ";
        }
        if ($role == 3) {
            echo "<a class='" . ($filter == 3 ? 'text-success' : '') . "' href='?action=list&filter=3'>klaar & nagekeken</a> | ";
        }
    }
    if ($role == 3) {
        echo "<a class='" . (!empty($catFilter) ? 'text-success' : '') ."' href='?action=$action&filter=$filter&filtercat=1'>Filter op categorie</a>";
    }
    echo "</div>\n";
}

if ($role == 1) {
    echo "<div class='w-100 text-right'>";
    $filtercat = isset($_GET['filtercat']) ? intval($_GET['filtercat']) : 0;
    echo "<form class='form-group'>";
    echo "<input hidden id='action' name='action' value='$action'>";
    echo "<select class='form-drop' id='filtercat' name='filtercat'>";
    echo "<option value='0'>Geen Filter</option>";
    foreach ($categories as $cat) {
        echo "<option " . ($filtercat == $cat->id ? 'selected' : '') . " value='$cat->id'>$cat->name</option>";
    }
    echo "</select> ";
    echo "<input class='btn btn-secondary' type='submit'value='Filter op categorie'>";
    echo "</form>";
    echo "</div>\n";
}
?>

<?php
if ($role == 3 && isset($_GET['filtercat'])) {
    echo "<div class='w-100 text-right pt-2'>";
    echo "<form method='post' action='?action=$action", isset($filter) ? "&filter=$filter" : "", "'>";
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
    echo "&nbsp;&nbsp;<input class='btn btn-secondary py-1' type='submit' value='Filter'/> ";
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
    if ($role == 1 && $filtercat != 0) {
        $filtered = $filtered || $article->category_id != $filtercat;
    }
    if (isset($filter) && $action == "list") {
        if ($filter >= 1) {
            $filtered = $filtered || $article->ready === false;
        }
        if ($filter == 2) {
            $user =  Session::instance()->getUser();
            $filtered = $filtered || count($article->checkers) > 2 || in_array($user, $article->checkers) ||  in_array($user, $article->authors);
        }
        if ($filter == 3) {
            $filtered = $filtered || count($article->checkers) < 3;
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
if ((isset($filter) || isset($filtercat)) && $n == 0 && count($articles) > 0) {
    echo "<div class='mt-3 text-center text-grey'><i>Er zijn geen stukjes die voldoen aan het huidige filter.</div>\n";
}
?>