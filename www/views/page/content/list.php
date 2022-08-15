<?php

use Model\Article;
use Model\User;
use Util\Singleton\Session;
use Util\ViewRenderer;

/**
 * @var Article[] $articles
 * @var int $role
 * @var string $title
 * @var string $list_type
 */
?>

    <h2 class='mb-4'><?php echo $title; ?></h2>
<?php
function cap($text, $len)
{
    return substr($text, 0, $len) . (strlen($text) > $len ? "..." : "");
}

if ($role == 2) {
    $filter = isset($_GET['filter']) ? intval($_GET['filter']) : 1;
    /* 0 - alle stukjes
     * 1 - alle stukjes die klaar zijn
     * 2 - alle stukjes die klaar zijn & nog niet nagekeken
     */
    echo "<div class='w-100 text-right'>Filter: ";
    echo "<a class='" . ($filter == 0 ? 'text-success' : '') . "' href='?action=list&filter=0'>alles</a> | ";
    echo "<a class='" . ($filter == 1 ? 'text-success' : '') . "' href='?action=list&filter=1'>klaar</a> | ";
    echo "<a class='" . ($filter == 2 ? 'text-success' : '') . "' href='?action=list&filter=2'>klaar & nog niet nagekeken</a>";
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
    if (isset($filter)) {
        if ($filter >= 1) {
            $filtered = $filtered || $article->ready === false;
        }
        if ($filter >= 2) {
            $filtered = $filtered || count($article->checkers) > 0;
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