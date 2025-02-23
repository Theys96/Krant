<?php

use App\Model\Article;
use App\Model\Category;
use App\Model\Edition;
use App\Model\User;

/**
 * @var Edition   $from_edition
 * @var Edition   $to_edition
 * @var Article[] $articles
 */

/** @var Category[] $from_categories */
$from_categories = array_values(Category::getAll($from_edition));

/** @var Category[] $to_categories */
$to_categories = array_values(Category::getAll($to_edition));
?>
<h2>Stukjes overzetten</h2>

<?php
if (0 == count($to_categories)) {
    ?>
    <p class="text-danger">
        Editie <i><?php echo $to_edition->name; ?></i> moet tenminste &eacute;&eacute;n categorie hebben om stukjes over te kunnen zetten.
    </p>
    <a class='btn btn-info' href='?action=editions'>Terug</a>
<?php
} elseif (0 == count($articles)) {
    ?>
    <p class="text-danger">
        Editie <i><?php echo $to_edition->name; ?></i> moet tenminste &eacute;&eacute;n ongeplaatst stukje hebben om over te kunnen zetten.
    </p>
    <a class='btn btn-info' href='?action=editions'>Terug</a>
<?php
} else {
    ?>

<p>
    Stukjes overzetten van <i>"<?php echo $from_edition->name; ?>"</i> naar <i>"<?php echo $to_edition->name; ?>"</i>.
</p>
<p>
    Geef eerst aan hoe de categorie&euml;n moeten worden omgezet:
</p>

<form method='post' action="?action=editions">
    <?php
    foreach ($from_categories as $idx => $category) {
        $idx_to = $idx > (count($to_categories) - 1) ? 0 : $idx;
        echo "<div class='form-row'>";
        echo "<label for='category-".$idx."' class='col-md-3 col-form-label font-weight-bold'>";
        echo $category->name;
        echo '</label>';
        echo "<div class='col-md-1'>&rarr;</div>";
        echo "<div class='form-group mb-0 col-md-4'>";
        echo "<input type='hidden' value='".$category->id."' name='from_edition_categories[]'>";
        echo "<select id='category-".$idx."' name='to_edition_categories[]' class='form-control form-control-sm'>";
        foreach ($to_categories as $to_idx => $to_category) {
            echo "<option value='".$to_category->id."' ".($idx_to == $to_idx ? 'selected' : '').'>'.$to_category->name.'</option>';
        }
        echo '</select>';
        echo '</div>';
        echo '</div>';
    }
    ?>

    <p class="mt-4">Selecteer alle stukjes die overgezet moeten worden:</p>
    <div class='px-3 mx-auto mb-5'>
    <?php
    $row = true;
    foreach ($articles as $article) {
        $color = $row ? '#AAAAAA' : '#DDDDDD';
        $row = !$row;

        $authors = htmlspecialchars(implode(', ', array_map(
            static function (User $author): string {
                return $author->username;
            },
            $article->authors
        )));
        echo "<div style='background-color: ".$color."' class='row form-row'>\n";
        echo "<div class='col-1'><div class='form-check mb-0'><input name='migrate_articles[]' type='checkbox' value='".$article->id."' checked></div></div>";
        echo "<div class='col-3'><a data-toggle='tooltip' data-placement='top' title='Klik om te bekijken' target='_blank' href='?action=read&stukje=".$article->id."&source=editions'>".htmlspecialchars($article->title).'</a></div>';
        echo "<div class='col-3'>".htmlspecialchars($article->category->name).'</a></div>';
        echo "<div class='col-3'>".$authors.'</div>';
        echo "<div class='col-2'><b>".strlen($article->contents).'</b> tekens</div>';
        echo "</div\n>";
    }
    ?>
    </div>
    <input class='btn btn-primary' type='submit' value='Overzetten'/>
    <a class='btn btn-info' href='?action=editions'>Terug</a>
</form>

<?php
}
?>