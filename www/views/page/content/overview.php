<?php

use App\Model\Article;
use App\Model\Category;

/**
 * @var Category[] $categories
 */
?>
<h2>Overzicht</h2>

<div class='px-3 mx-auto my-5'>
    <div class='row table-color1'>
        <div class='col-3'><b>Categorie</b></div>
        <div class='col-6'><b>Ongeplaatst</b></div>
        <div class='col-3'><b>Geplaatst</b></div>
    </div>
    <div class='row table-color2'>
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
        if (Article::STATUS_OPEN == $article->status) {
            $bin = $article->ready ? 'open_ready' : 'open_not_ready';
        } elseif (Article::STATUS_PLACED == $article->status) {
            $bin = 'placed';
        }
        if (null !== $bin) {
            ++$counts[$bin][0];
            $counts[$bin][1] += strlen($article->contents);
        }
    }

    $color = $row ? 'table-color1' : 'table-color2';
    $row = !$row;
    echo "<div class='row ". $color ."'>\n";
    echo "<div class='col-3'>".htmlspecialchars($category->name).'</div>';
    foreach (['open_not_ready', 'open_ready', 'placed'] as $bin) {
        echo "<div class='col-3'><span data-toggle='tooltip' data-placement='top' title='".$counts[$bin][0].' stukje(s), '.$counts[$bin][1]." teken(s)' >".$counts[$bin][0].' / '.$counts[$bin][1].'</span></div>';
    }
    echo "</div>\n";
}
?>
</div>
