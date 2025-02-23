<?php

use App\Model\Category;

/**
 * @var Category[] $categories
 * @var int        $role
 */
?>
<h2>Categorieën</h2>

<div class='px-3 mx-auto my-5'>
<?php
$row = true;
foreach ($categories as $category) {
    $color = $row ? '#AAAAAA' : '#DDDDDD';
    $row = !$row;

    echo "<div style='background-color: ".$color."' class='row'>\n";
    echo "<div class='col-4'><b>".htmlspecialchars($category->name).'</b></div>';
    echo "<div class='col-6'>".htmlspecialchars($category->description).'</div>';
    echo "<div class='col-2'>";
    if (3 === $role) {
        echo "<a href='?action=edit_category&category=".$category->id."'>Aanpassen</a>";
    }
    echo '</div>';
    echo "</div>\n";
}
?>
</div>

<?php
if (3 == $role) {
    ?>
<form method='post'>
<h3>Categorie toevoegen</h3>
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
<?php
}
?>
