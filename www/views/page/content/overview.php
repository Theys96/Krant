<?php

use App\Model\Article;
use App\Model\Category;

/**
 * @var Category[] $categories
 */
$max_articles = 10;
$max_pictures = 4;
$max_wjd = 10;
$maxes = [
    'articles' => $max_articles,
    'pictures' => $max_pictures,
    'wjd' => $max_wjd,
];
?>
<h2>Overzicht</h2>
<p class="text-center">Geplaatst: donkergroen, Klaar: groen, Niet klaar: geel</p>
<div class='px-3 mx-auto my-5'>
    <div style='background-color: #AAAAAA' class='row'>
        <div class='col-3'><b>Categorie</b></div>
        <div class='col-3'><b>Stukjes</b></div>
        <div class='col-3'><b>Foto's</b></div>
        <div class='col-3'><b>Wist je Datjes</b></div>
    </div>
<?php
$row = true;
foreach ($categories as $category) {
    $counts = [
        'articles' => [0, 0, 0, 0],
        'pictures' => [0, 0, 0, 0],
        'wjd' => [0, 0, 0, 0],
    ];
    foreach (Article::getAllByCategory($category) as $article) {
        $bin = null;
        if (Article::STATUS_OPEN == $article->status) {
            $bin = $article->ready ? 1 : 0;
        } elseif (Article::STATUS_PLACED == $article->status) {
            $bin = 2;
        }
        if (null !== $bin) {
            if ($article->wjd) {
                $counts['wjd'][$bin] += substr_count(rtrim($article->contents), "\n") + 1;
                $counts['wjd'][3] += strlen($article->contents);
            } else {
                ++$counts['articles'][$bin];
                $counts['articles'][3] += strlen($article->contents);
            }
            if ($article->picture) {
                ++$counts['pictures'][$bin];
                ++$counts['pictures'][3];
            }
        }
    }

    $color = $row ? '#DDDDDD' : '#AAAAAA';
    $row = !$row;
    echo "<div style='background-color: ".$color."' class='row'>\n";
    echo "<div class='col-3'>".htmlspecialchars($category->name).'</div>';
    foreach (['articles', 'pictures', 'wjd'] as $bin) {
        echo "<div data-toggle='tooltip' data-placement='top' title='".$counts[$bin][3].''.('pictures' == $bin ? ' foto(s)' : ' tekens')."' class='col-3 align-self-start d-flex flex-wrap mb-2'>";
        for ($i = 0; $i < $maxes[$bin]; ++$i) {
            if ($counts[$bin][2] > 0) {
                --$counts[$bin][2];
                echo "<div class='box' style='background-color: green;'></div>";
            } elseif ($counts[$bin][1] > 0) {
                --$counts[$bin][1];
                echo "<div class='box' style='background-color:limegreen'></div>";
            } elseif ($counts[$bin][0] > 0) {
                --$counts[$bin][0];
                echo "<div class='box' style='background-color:gold'></div>";
            } else {
                echo "<div class='box' style='background-color:white'></div>";
            }
        }
        echo '</div>';
    }
    echo "</div>\n";
}
?>
</div>
