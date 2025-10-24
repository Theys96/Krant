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
$row = false;
echo "<div class='row table-color1'>\n";
echo "<div class='col-3'><b>categorie</b></div>";
echo "<div class='col-4'>beschrijving</div>";
echo "<div class='col-1'>stukjes</div>";
echo "<div class='col-1'>foto's</div>";
echo "<div class='col-1'>wjd</div>";
echo "<div class='col-2'></div>";
echo "</div>\n";
foreach ($categories as $category) {
    $color = $row ? 'table-color1' : 'table-color2';
    $row = !$row;

    echo "<div class='row ".$color."'>\n";
    echo "<div class='col-3'><b>".htmlspecialchars($category->name).'</b></div>';
    echo "<div class='col-4'>".htmlspecialchars($category->description).'</div>';
    echo "<div class='col-1'>".$category->article_amount.'</div>';
    echo "<div class='col-1'>".$category->picture_amount.'</div>';
    echo "<div class='col-1'>".$category->wjd_amount.'</div>';
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

<p class='mb-0'>Aantal blokjes in het overzicht voor:</p>
    <div class='d-flex justify-content-between'>
	    <div class='form-group mr-1'>
		    <label for='new_article_amount'>Stukjes</label>
		    <input type='number' id='new_article_amount' class='form-control' name='new_article_amount' required value='5'></input>
	    </div>
	    <div class='form-group mr-1'>
		    <label for='new_picture_amount'>Foto's</label>
		    <input type='number' id='new_picture_amount' class='form-control' name='new_picture_amount' required value='2'></input>
	    </div>
	    <div class='form-group'>
		    <label for='new_wjd_amount'>Wist je Datjes</label>
		    <input type='number' id='new_wjd_amount' class='form-control' name='new_wjd_amount' required value='7'></input>
	    </div>
    </div>
<input type='submit' class='btn btn-primary' value='Toevoegen' />
</form>
<?php
}
?>
