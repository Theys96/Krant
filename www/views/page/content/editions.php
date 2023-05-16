<?php

use Model\Article;
use Model\Edition;

/**
 * @var Edition[] $editions
 * @var int $role
 */
?>
<h2>Edities</h2>

<div class='px-3 mx-auto my-5'>
    <?php
    $row = true;
    foreach ($editions as $edition) {
        $color = $row ? '#AAAAAA' : '#DDDDDD';
        $row = !$row;

        $article_count = $edition->countArticles(Article::STATUS_OPEN);
        echo "<div style='background-color: " . $color . "' class='row'>\n";
        echo "<div class='col-2'><b>" . htmlspecialchars($edition->name) . "</b></div>";
        echo "<div class='col-3'>" . htmlspecialchars($edition->description) . "</div>";
        echo "<div class='col-3'><b>" . $article_count . "</b> ongeplaatste stukje(s)</div>";
        echo "<div class='col-4'>";
        if ($role === 3) {
            echo "<a class='btn btn-primary py-1 ml-1 my-1 float-right' href='?action=edit_edition&edition=" . $edition->id . "'>Aanpassen</a>";
        }
        if (!$edition->active && $article_count > 0) {
            echo "<a class='btn btn-info py-1 ml-1 my-1 float-right' href='?action=migrate_edition&edition=" . $edition->id . "'>Stukjes overzetten</a>";
        } elseif ($edition->active) {
            echo "<span class='btn btn-success py-1 ml-1 my-1 float-right'>Actief</span>";
        }
        echo "</div>";
        echo "</div>\n";
    }
    ?>
</div>

<?php
if ($role == 3) :
?>
<form method='post' action='?action=editions'>
    <h3>Editie toevoegen</h3>
    <div class='form-group'>
        <label for='name'>Naam</label>
        <input id='name' name='new_name' class='form-control' type='text' />
    </div>
    <div class='form-group'>
        <label for='description'>Beschrijving</label>
        <input id='description' name='new_description' class='form-control' type='text' />
    </div>
    <input type='submit' class='btn btn-primary' value='Toevoegen' />
</form>
<form method='post' action='?action=editions'>
    <h3 class="mt-5">Huidige editie</h3>
    <div class='form-group'>
        <select name='active_edition' id='active_edition' class='form-control'>
            <?php
            foreach ($editions as $edition) {
                echo "<option value='" . $edition->id . ($edition->active ? "' selected" : "'") . ">" . htmlspecialchars($edition->name) . "</option>\n";
            }
            ?>
        </select>
    </div>
    <input type='submit' class='btn btn-primary' value='Wijzigen' />
</form>
<?php
endif;
?>
