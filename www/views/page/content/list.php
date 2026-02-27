<?php

use App\Model\Article;
use App\Model\Category;
use App\Util\Singleton\Session;
use App\Util\ViewRenderer;

/**
 * @var Article[] $articles
 * @var int       $role
 * @var string    $title
 * @var string    $list_type
 * @var int       $checks
 */
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$catFilter = Session::instance()->getFilter();
$categories = Category::getAll();
$filterMode = Session::instance()->getFilterMode();
$filterCategories = Session::instance()->getFilterCategories();
?>

    <h2 class='mb-4'><?php echo $title; ?></h2>
<?php
function cap(string $text, int $len): string
{
    return substr($text, 0, $len).(strlen($text) > $len ? '...' : '');
}

if ($role > 1) {
    /* 0 - alle stukjes
     * 1 - alle stukjes die klaar zijn
     * 2 - alle stukjes die klaar zijn & nog niet nagekeken
     * 3 - alle stukjes die klaar zijn & nagekeken
     */
    echo "<div class='w-100 text-center'>";
    if ('list' == $action) {
        echo "<a class='btn m-1 ".(0 == $filterMode ? 'btn-success' : 'btn-secondary')."' href='?action=list&filter_mode=0'>Alles</a>";
        echo "<a class='btn m-1 ".(1 == $filterMode ? 'btn-success' : 'btn-secondary')."' href='?action=list&filter_mode=1'>Klaar</a>";
        if (2 == $role) {
            echo "<a class='btn m-1 ".(2 == $filterMode ? 'btn-success' : 'btn-secondary')."' href='?action=list&filter_mode=2'>Klaar & kan ik nakijken</a>";
        }
        if (3 == $role) {
            echo "<a class='btn m-1 ".(3 == $filterMode ? 'btn-success' : 'btn-secondary')."' href='?action=list&filter_mode=3'>Klaar & nagekeken</a>";
        }
    }
    if (3 == $role) {
        echo "<a class='btn m-1 ".($filterCategories ? 'btn-success' : 'btn-secondary')."' href='?action=$action&filter_categories=".(1 == $filterCategories ? 0 : 1)."'>Filter op categorie</a>";
    }
    echo "</div>\n";
}

if (1 == $role) {
    echo "<div class='w-100 text-center'>";
    echo "<form class='form-group' method='post' action='?action=$action'>";
    echo "<select onchange=submit() class='form-drop' id='filtercat' name='filters[]'>";
    echo "<option value='0'>Geen Filter</option>";
    foreach ($categories as $cat) {
        echo '<option '.($catFilter[0] == $cat->id ? 'selected' : '')." value='$cat->id'>$cat->name</option>";
    }
    echo '</select> ';
    echo '</form>';
    echo "</div>\n";
}
?>

<?php
if (3 == $role && $filterCategories) {
    echo "<div class='w-100 text-center pt-2'>";
    echo "<form method='post' action='?action=$action'>";
    echo "<div class='form-group'>";
    echo "<input type='hidden' name='filters[]' value='0'>";
    $first = true;
    foreach ($categories as $cat) {
        if (!$first) {
            echo '&nbsp;&nbsp;';
        }
        $first = false;
        echo "<input id='cat-filter-$cat->id' onchange=submit() type='checkbox' name='filters[]' value='$cat->id'",
        in_array($cat->id, $catFilter) ? ' checked' : '', '/>';
        echo "&nbsp;<label for='cat-filter-$cat->id'> $cat->name </label> ";
    }
    echo '</form>';
    echo '</div>';
    echo "</div>\n";
}
?>

<?php
if (0 == count($articles)) {
    echo "<div class='text-center text-grey'><i>Hier zijn op dit moment (nog) geen stukjes.</div>\n";
}

$n = 0;
foreach ($articles as $article) {
    $filtered = false;
    if (3 == $role && $filterCategories) {
        $filtered = !in_array($article->category_id, $catFilter);
    }
    if (1 == $role && count($catFilter) > 0) {
        $filtered = $filtered || !in_array($article->category_id, $catFilter);
    }
    if ($role > 1 && 'list' == $action) {
        if ($filterMode >= 1) {
            $filtered = $filtered || false === $article->ready;
        }
        if (2 == $filterMode) {
            $user = Session::instance()->getUser();
            $filtered = $filtered || count($article->checkers) >= $checks || in_array($user, $article->checkers) || in_array($user, $article->authors);
        }
        if (3 == $filterMode) {
            $filtered = $filtered || count($article->checkers) < $checks;
        }
    }
    if (!$filtered) {
        ++$n;
        echo ViewRenderer::render_view('partial.article_list_item', [
            'article' => $article,
            'role' => $role,
            'list_type' => $list_type,
        ]);
    }
}
if (0 == $n && count($articles) > 0) {
    echo "<div class='mt-3 text-center text-grey'><i>Er zijn geen stukjes die voldoen aan het huidige filter.</div>\n";
}
?>