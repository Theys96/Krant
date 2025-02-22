<?php

use App\Model\Article;
use App\Model\Category;

/**
 * @var Category[] $categories
 */
?>
<h2>Overzicht</h2>

<div class='px-3 mx-auto my-5'>
    <div style='background-color: #AAAAAA' class='row'>
        <div class='col-3'><b>Categorie</b></div>
        <div class='col-6'><b>Ongeplaatst</b></div>
        <div class='col-3'><b>Geplaatst</b></div>
    </div>
    <div style='background-color: #DDDDDD' class='row'>
        <div class='col-3'></div>
        <div class='col-3'><i>Niet klaar</i></div>
        <div class='col-3'><i>Klaar</i></div>
        <div class='col-3'></div>
    </div>
<?php
$row = true;
foreach ($categories as $category) {

    $counts = [
        'open_not_ready' => [0, 0],
        'open_ready' => [0, 0],
        'placed' => [0, 0],
    ];
    foreach (Article::getAllByCategory($category) as $article) {
        $bin = null;
        if ($article->status == Article::STATUS_OPEN) {
            $bin = $article->ready ? 'open_ready' : 'open_not_ready';
        } elseif ($article->status == Article::STATUS_PLACED) {
            $bin = 'placed';
        }
        if ($bin !== null) {
            $counts[$bin][0]++;
            $counts[$bin][1] += strlen($article->contents);
        }
    }

    $color = $row ? '#AAAAAA' : '#DDDDDD';
    $row = !$row;
    echo "<div style='background-color: " . $color . "' class='row'>\n";
    echo "<div class='col-3'>" . htmlspecialchars($category->name) . "</div>";
    foreach (['open_not_ready', 'open_ready', 'placed'] as $bin) {
        echo "<div class='col-3'><span data-toggle='tooltip' data-placement='top' title='" . $counts[$bin][0] . " stukje(s), " . $counts[$bin][1] . " teken(s)' >" . $counts[$bin][0] . " / " . $counts[$bin][1] . "</span></div>";
    }
    echo "</div>\n";
}
?>
</div>
