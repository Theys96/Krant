<?php

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

        echo "<div style='background-color: " . $color . "' class='row'>\n";
        echo "<div class='col-4'><b>" . htmlspecialchars($edition->name) . "</b></div>";
        echo "<div class='col-6'>" . htmlspecialchars($edition->description) . "</div>";
        echo "<div class='col-2'>";
        if ($role === 3) {
            echo "<a href='?action=edit_edition&edition=" . $edition->id . "'>Aanpassen</a>";
        }
        echo "</div>";
        echo "</div>\n";
    }
    ?>
</div>

<?php
if ($role == 3) :
?>
<form method='post'>
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
<form method = 'post'>
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
