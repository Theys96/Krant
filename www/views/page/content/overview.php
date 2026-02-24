<?php

use App\Model\Article;
use App\Model\Category;

/**
 * @var Category[] $categories
 */
$filter = [
    isset($_GET['notdone']) ? $_GET['notdone'] : 1,
    isset($_GET['done']) ? $_GET['done'] : 1,
    isset($_GET['placed']) ? $_GET['placed'] : 1,
];
?>
<h2>Overzicht</h2>
<?php
echo "<div class='w-100 text-center'>";
echo "<a class='btn m-1 ".(1 == $filter[2] ? 'btn-info' : 'btn-secondary')."' href='?action=overview&placed=".(1 == $filter[2] ? 0 : 1).'&done='.$filter[1].'&notdone='.$filter[0]."'>Geplaatst: donkergroen</a>";
echo "<a class='btn m-1 ".(1 == $filter[1] ? 'btn-info' : 'btn-secondary')."' href='?action=overview&placed=".$filter[2].'&done='.(1 == $filter[1] ? 0 : 1).'&notdone='.$filter[0]."'>Klaar: groen</a>";
echo "<a class='btn m-1 ".(1 == $filter[0] ? 'btn-info' : 'btn-secondary')."' href='?action=overview&placed=".$filter[2].'&done='.$filter[1].'&notdone='.(1 == $filter[0] ? 0 : 1)."'>Niet klaar: geel</a>";
echo '</div>';
?>
<div class='px-3 mx-auto my-5'>
    <div class='row table-color1'>
        <div class='col-3'><b>Categorie</b></div>
        <div class='col-3'><b>Stukjes</b></div>
        <div class='col-3'><b>Foto's</b></div>
        <div class='col-3'><b>Wist je Datjes</b></div>
    </div>
<?php
$row = true;
foreach ($categories as $category) {
    $maxes = [
        'articles' => $category->article_number,
        'pictures' => $category->picture_number,
        'wjd' => $category->wjd_number,
    ];
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
        if (null !== $bin && 0 == $filter[$bin]) {
            $bin = null;
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

    $titles = [
        'articles' => ($counts['articles'][0] + $counts['articles'][1] + $counts['articles'][2]).' stukje(s) | '.$counts['articles'][3].' tekens',
        'pictures' => $counts['pictures'][3].' foto(s)',
        'wjd' => ($counts['wjd'][0] + $counts['wjd'][1] + $counts['wjd'][2]).' wist je datje(s) | '.$counts['wjd'][3].' tekens',
    ];
    $color = !$row ? 'table-color1' : 'table-color2';
    $row = !$row;
    echo "<div class='row ".$color."'>\n";
    echo "<div class='col-3'>".htmlspecialchars($category->name).'</div>';
    foreach (['articles', 'pictures', 'wjd'] as $bin) {
        echo "<div data-toggle='tooltip' data-placement='top' title='".$titles[$bin]."' class='col-3 align-self-start d-flex flex-wrap mb-2'>";
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
